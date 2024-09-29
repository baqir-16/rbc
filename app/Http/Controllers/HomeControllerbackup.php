<?php

namespace App\Http\Controllers;

use App\Stream;
use DB;
use App;
use Response;
use ScanResults;
use Config;
use App\Pdfreport;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use DateTime;
use Auth;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
//        $client = new \GuzzleHttp\Client();
//        $response = $client->request('POST', 'http://localhost/cdp2/public/oauth/token', [
//            'form_params' => [
//                'client_id' => 2,
//                'client_secret' => 'nRDGq5CtwUA9eF3CeWJwTiiAfY4pYHJJvfWU8SE1',
//                'grant_type' => 'client_credentials',
//                'scope' => '*',
//            ]
//        ]);
//
//        $auth = json_decode((string)$response->getBody());
//
//        $response = $client->request('GET','http://localhost/cdp2/public/api/open', [
//            'headers' => [
//                'Authorization' => 'Bearer '.$auth->access_token,
//                'Content-Type' => 'application/json',
//                'Accept' => 'application/json',
//            ]
//        ]);
//
//        $data = json_decode((string)$response->getBody());
//        var_dump($data); exit;
////        $client = new \GuzzleHttp\Client();
//////        $res = $client->request('GET', 'http://localhost/cdp2/public/api/test');
////        $res = $client->request('GET', 'http://localhost/cdp2/public/api/aging_and_kpi', [
////                'headers' => [
////                    'Content-Type' => 'application/json',
////                    'Accept' => 'application/json',
////                ]
////            ]);
////
//////        echo $res->getStatusCode(); // 200
//////        echo $res->getHeaderLine('content-type');   // 'application/json; charset=utf8'
////        $data = json_decode((string)$res->getBody(), true);
////        dd($data);
//exit;

        if (Auth::user()->roles->contains('1')) {

        $updated_date = $critical_open = DB::connection('mongodb')->collection('db_num_of_hosts_by_cat')->select("updated_at")
            ->whereNotNull('updated_at')
            ->first();

        $timeTask = Carbon::parse(date('Y-m-d H:i:s', $updated_date['updated_at']->toDateTime()->format('U')));
        $Currentdate = Carbon::now();
        $totalTime = $Currentdate->diffInMinutes($timeTask);

        $kpi_opco_array = DB::connection('mysql')->table('opco')->where('status', Config::get('enums.opco_status.Primary'))->get()->toArray();
        $kpi_opco_array = json_decode(json_encode($kpi_opco_array), true);
        $aging_opco_array = json_decode(json_encode($kpi_opco_array), true);
        $kpi_opco_risk_array = [];
        $aging_opco_risk_array = [];

        $pdfinfo = Pdfreport::latest()->get();
//        dd($pdfinfo[0]['c_hours']);
//        dd($pdfinfo[0]['h_hours']);

        foreach ($kpi_opco_array as $opco) {
            $kpi_tmp_risk_array = [];

            $critical_open = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($critical_open  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $date = Carbon::now();

                $diff = $start->diffInHours($date);
                $critical_open[$key]['diff'] = $diff;
            }
            array_push($kpi_tmp_risk_array, $critical_open);

            $high_open = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($high_open  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $date = Carbon::now();

                $diff = $start->diffInHours($date);
                $high_open[$key]['diff'] = $diff;
            }
            array_push($kpi_tmp_risk_array, $high_open);

            $critical_close = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 1)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($critical_close as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

                $diff = $start->diffInHours($end);
                $critical_close[$key]['diff'] = $diff;
            }
            array_push($kpi_tmp_risk_array, $critical_close);

            $high_close = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 1)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($high_close  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

                $diff = $start->diffInHours($end);
                $high_close[$key]['diff'] = $diff;
            }
            array_push($kpi_tmp_risk_array, $high_close);
            array_push($kpi_opco_risk_array, $kpi_tmp_risk_array);
        }

        foreach ($aging_opco_array as $opco) {
            $aging_tmp_risk_array = [];

            $critical_opened = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($critical_opened  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $date = Carbon::now();

                $diff = $start->diffInHours($date);
                $critical_opened[$key]['diff'] = $diff;
            }

            array_push($aging_tmp_risk_array, $critical_opened);

            $critical_closed = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 1)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($critical_closed as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

                $diff = $start->diffInHours($end);
                $critical_closed[$key]['diff'] = $diff;
            }
            array_push($aging_tmp_risk_array, $critical_closed);


            $high_opened = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($high_opened  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $date = Carbon::now();

                $diff = $start->diffInHours($date);
                $high_opened[$key]['diff'] = $diff;
            }

            array_push($aging_tmp_risk_array, $high_opened);

            $high_closed = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 1)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($high_closed  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

                $diff = $start->diffInHours($end);
                $high_closed[$key]['diff'] = $diff;
            }

            array_push($aging_tmp_risk_array, $high_closed);

            array_push($aging_opco_risk_array, $aging_tmp_risk_array);
        }

//  ======================

        $w1 = Carbon::now()->subWeek(8);
        $w2 = Carbon::now()->subWeeks(7);
        $w3 = Carbon::now()->subWeeks(6);
        $w4 = Carbon::now()->subWeeks(5);
        $w5 = Carbon::now()->subWeeks(4);
        $w6 = Carbon::now()->subWeeks(3);
        $w7 = Carbon::now()->subWeeks(2);
        $w8 = Carbon::now()->subWeek(1);
        $w9 = Carbon::now();

        $owk1 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w1, $w2])->count();
        $owk2 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w2, $w3])->count();
        $owk3 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w3, $w4])->count();
        $owk4 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w4, $w5])->count();
        $owk5 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w5, $w6])->count();
        $owk6 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w6, $w7])->count();
        $owk7 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w7, $w8])->count();
        $owk8 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w8, $w9])->count();

        $cwk1 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w1, $w2])->count();
        $cwk2 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w2, $w3])->count();
        $cwk3 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w3, $w4])->count();
        $cwk4 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w4, $w5])->count();
        $cwk5 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w5, $w6])->count();
        $cwk6 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w6, $w7])->count();
        $cwk7 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w7, $w8])->count();
        $cwk8 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w8, $w9])->count();

//  ======================

        $vul_categories = DB::table('vul_categories')->get();

        $db_opco_findings = DB::connection('mongodb')->collection('db_opco_findings')->first();
        $opco_risk_array = $db_opco_findings['opco_risk_array'];
        $opco_array = $db_opco_findings['opco_array'];

        $db_open_close_findings = DB::connection('mongodb')->collection('db_open_close_findings')->first();
        $open = $db_open_close_findings['open'];
        $close = $db_open_close_findings['close'];

        $db_num_of_hosts_by_cat = DB::connection('mongodb')->collection('db_num_of_hosts_by_cat')->first();
        $cat_array = $db_num_of_hosts_by_cat['cat_array'];
        $hosts_per_cat_array = $db_num_of_hosts_by_cat['hosts_per_cat_array'];

        foreach($hosts_per_cat_array as $key=>$value){
            if($value[0]+$value[1]+$value[2]+$value[3] == 0){
                unset($cat_array[$key]);
                unset($hosts_per_cat_array[$key]);
            }
        }

        $db_num_of_hosts_by_vuln_name = DB::connection('mongodb')->collection('db_num_of_hosts_by_vuln_name')->first();
        $num_of_hosts_by_vuln_name = $db_num_of_hosts_by_vuln_name['num_of_hosts_by_vuln_name'];

        $info_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))
            ->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->count();

        $low_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Low'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $med_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $high_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))->count();

        $critical_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))->count();

        $pending_rem_c = DB::connection('mongodb')->collection('scan_results')->where('risk', (int)Config::get('enums.severity_status.Critical'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $pending_rem_h = DB::connection('mongodb')->collection('scan_results')->where('risk', (int)Config::get('enums.severity_status.High'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $pending_rem_m = DB::connection('mongodb')->collection('scan_results')->where('risk', (int)Config::get('enums.severity_status.Medium'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $pending_rem_l = DB::connection('mongodb')->collection('scan_results')->where('risk', (int)Config::get('enums.severity_status.Low'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $info_catg = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->whereNotNull('vul_category')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))
            ->select('name', 'host' , 'vul_category')
            ->groupby('name', 'desc')->get();

        $low_catg = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')->whereNotNull('vul_category')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Low'))
            ->select('name', 'host', 'vul_category')
            ->groupby('name', 'desc')->get();

        $med_catg = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')->whereNotNull('vul_category')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))
            ->select('name', 'host', 'vul_category')
            ->groupby('name', 'desc')->get();

        $high_catg = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')->whereNotNull('vul_category')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->select('name', 'host', 'vul_category')
            ->groupby('name', 'desc')
            ->get();

        $critical_catg = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')->whereNotNull('vul_category')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->select('name', 'host', 'vul_category')
            ->groupby('name', 'desc')->get();

        $streamscount = Stream::get();
        $st_complete = Stream::where('status', '11')->get();

        $pdfinfo = Pdfreport::latest()->with('user')->get();
    } else {

        $updated_date = $critical_open = DB::connection('mongodb')->collection('db_num_of_hosts_by_cat')->select("updated_at")
            ->whereNotNull('updated_at')
            ->first();

        $timeTask = Carbon::parse(date('Y-m-d H:i:s', $updated_date['updated_at']->toDateTime()->format('U')));
        $Currentdate = Carbon::now();
        $totalTime = $Currentdate->diffInMinutes($timeTask);

        $kpi_opco_array = DB::connection('mysql')->table('opco')->where('status', Config::get('enums.opco_status.Primary'))->get()->toArray();
        $kpi_opco_array = json_decode(json_encode($kpi_opco_array), true);
        $aging_opco_array = json_decode(json_encode($kpi_opco_array), true);
        $kpi_opco_risk_array = [];
        $aging_opco_risk_array = [];

        $pdfinfo = Pdfreport::latest()->get();
//        dd($pdfinfo[0]['c_hours']);
//        dd($pdfinfo[0]['h_hours']);

        foreach ($kpi_opco_array as $opco) {
            $kpi_tmp_risk_array = [];

            $critical_open = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($critical_open  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $date = Carbon::now();

                $diff = $start->diffInHours($date);
                $critical_open[$key]['diff'] = $diff;
            }
            array_push($kpi_tmp_risk_array, $critical_open);

            $high_open = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($high_open  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $date = Carbon::now();

                $diff = $start->diffInHours($date);
                $high_open[$key]['diff'] = $diff;
            }
            array_push($kpi_tmp_risk_array, $high_open);

            $critical_close = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 1)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($critical_close as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

                $diff = $start->diffInHours($end);
                $critical_close[$key]['diff'] = $diff;
            }
            array_push($kpi_tmp_risk_array, $critical_close);

            $high_close = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 1)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($high_close  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

                $diff = $start->diffInHours($end);
                $high_close[$key]['diff'] = $diff;
            }
            array_push($kpi_tmp_risk_array, $high_close);
            array_push($kpi_opco_risk_array, $kpi_tmp_risk_array);
        }

        foreach ($aging_opco_array as $opco) {
            $aging_tmp_risk_array = [];

            $critical_opened = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($critical_opened  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $date = Carbon::now();

                $diff = $start->diffInHours($date);
                $critical_opened[$key]['diff'] = $diff;
            }

            array_push($aging_tmp_risk_array, $critical_opened);

            $critical_closed = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 1)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($critical_closed as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

                $diff = $start->diffInHours($end);
                $critical_closed[$key]['diff'] = $diff;
            }
            array_push($aging_tmp_risk_array, $critical_closed);


            $high_opened = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($high_opened  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $date = Carbon::now();

                $diff = $start->diffInHours($date);
                $high_opened[$key]['diff'] = $diff;
            }

            array_push($aging_tmp_risk_array, $high_opened);

            $high_closed = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 1)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco['id'])
                ->get()->toArray();

            foreach ($high_closed  as $key => $issue) {
                $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
                $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

                $diff = $start->diffInHours($end);
                $high_closed[$key]['diff'] = $diff;
            }

            array_push($aging_tmp_risk_array, $high_closed);

            array_push($aging_opco_risk_array, $aging_tmp_risk_array);
        }

//  ======================

        $w1 = Carbon::now()->subWeek(8);
        $w2 = Carbon::now()->subWeeks(7);
        $w3 = Carbon::now()->subWeeks(6);
        $w4 = Carbon::now()->subWeeks(5);
        $w5 = Carbon::now()->subWeeks(4);
        $w6 = Carbon::now()->subWeeks(3);
        $w7 = Carbon::now()->subWeeks(2);
        $w8 = Carbon::now()->subWeek(1);
        $w9 = Carbon::now();

        $owk1 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w1, $w2])->count();
        $owk2 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w2, $w3])->count();
        $owk3 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w3, $w4])->count();
        $owk4 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w4, $w5])->count();
        $owk5 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w5, $w6])->count();
        $owk6 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w6, $w7])->count();
        $owk7 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w7, $w8])->count();
        $owk8 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('hod_signoff_date', [$w8, $w9])->count();

        $cwk1 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w1, $w2])->count();
        $cwk2 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w2, $w3])->count();
        $cwk3 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w3, $w4])->count();
        $cwk4 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w4, $w5])->count();
        $cwk5 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w5, $w6])->count();
        $cwk6 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w6, $w7])->count();
        $cwk7 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w7, $w8])->count();
        $cwk8 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->whereNotNull('hod_signoff_date')
            ->whereBetween('rem_pmo_closure_date', [$w8, $w9])->count();

//  ======================

        $vul_categories = DB::table('vul_categories')->get();

        $db_opco_findings = DB::connection('mongodb')->collection('db_opco_findings')->first();
        $opco_risk_array = $db_opco_findings['opco_risk_array'];
        $opco_array = $db_opco_findings['opco_array'];

        $db_open_close_findings = DB::connection('mongodb')->collection('db_open_close_findings')->first();
        $open = $db_open_close_findings['open'];
        $close = $db_open_close_findings['close'];

        $db_num_of_hosts_by_cat = DB::connection('mongodb')->collection('db_num_of_hosts_by_cat')->first();
        $cat_array = $db_num_of_hosts_by_cat['cat_array'];
        $hosts_per_cat_array = $db_num_of_hosts_by_cat['hosts_per_cat_array'];

        foreach($hosts_per_cat_array as $key=>$value){
            if($value[0]+$value[1]+$value[2]+$value[3] == 0){
                unset($cat_array[$key]);
                unset($hosts_per_cat_array[$key]);
            }
        }

        $db_num_of_hosts_by_vuln_name = DB::connection('mongodb')->collection('db_num_of_hosts_by_vuln_name')->first();
        $num_of_hosts_by_vuln_name = $db_num_of_hosts_by_vuln_name['num_of_hosts_by_vuln_name'];

        $info_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))
            ->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->count();

        $low_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Low'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->count();

        $med_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->count();

        $high_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.High'))->count();

        $critical_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))->count();

        $pending_rem_c = DB::connection('mongodb')->collection('scan_results')->where('risk', (int)Config::get('enums.severity_status.Critical'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->count();

        $pending_rem_h = DB::connection('mongodb')->collection('scan_results')->where('risk', (int)Config::get('enums.severity_status.High'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->count();

        $pending_rem_m = DB::connection('mongodb')->collection('scan_results')->where('risk', (int)Config::get('enums.severity_status.Medium'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->count();

        $pending_rem_l = DB::connection('mongodb')->collection('scan_results')->where('risk', (int)Config::get('enums.severity_status.Low'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->count();

        $info_catg = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->whereNotNull('vul_category')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))
            ->select('name', 'host' , 'vul_category')
            ->groupby('name', 'desc')->get();

        $low_catg = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')->whereNotNull('vul_category')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.Low'))
            ->select('name', 'host', 'vul_category')
            ->groupby('name', 'desc')->get();

        $med_catg = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')->whereNotNull('vul_category')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))
            ->select('name', 'host', 'vul_category')
            ->groupby('name', 'desc')->get();

        $high_catg = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')->whereNotNull('vul_category')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->select('name', 'host', 'vul_category')
            ->groupby('name', 'desc')
            ->get();

        $critical_catg = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')->whereNotNull('vul_category')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->select('name', 'host', 'vul_category')
            ->groupby('name', 'desc')->get();

        $streamscount = Stream::get();
        $st_complete = Stream::where('status', '11')->get();

        $pdfinfo = Pdfreport::latest()->with('user')->get();
       }

        return view('home', compact('totalTime','usercount', 'ticketscount','streamscount', 'st_complete',
            'med_risk', 'high_risk', 'info_risk', 'low_risk','critical_risk', 'pending_rem_c','pending_rem_h','pending_rem_m','pending_rem_l',
            'med_catg', 'high_catg', 'info_catg', 'low_catg','critical_catg', 'pdfinfo', 'opco_risk_array', 'opco_array', 'kpi_opco_risk_array', 'kpi_opco_array', 'vul_categories', 'open', 'close',
            'hosts_per_cat_array', 'cat_array', 'num_of_hosts_by_vuln_name', 'pdfinfo', 'open_array', 'close_array', 'pending_array', 'diff', 'aging_opco_risk_array', 'aging_opco_array', 'update_task',
            'w1', 'w2', 'w3', 'w4', 'w5', 'w6', 'w7', 'w8', 'w9',
            'owk1', 'owk2', 'owk3', 'owk4', 'owk5', 'owk6', 'owk7', 'owk8',
            'cwk1', 'cwk2', 'cwk3', 'cwk4', 'cwk5', 'cwk6', 'cwk7', 'cwk8'));
    }

    public function riskdetails($id) {
        $enums = array_flip(Config::get('enums.severity_status'));
        $opcos = array_flip(Config::get('enums.opco_switch'));

if (Auth::user()->roles->contains('1')) {
        if($id == 4)
            $low_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->where('risk', (int)Config::get('enums.severity_status.Low'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->get();
        elseif ($id == 3)
            $med_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->get();
        elseif ($id == 2)
            $high_risk_dt = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.High'))->get();
        elseif ($id == 1)
            $critical_risk_dt = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))->get();
        else
            $pending_rem_dt = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->get();
} else {
    if($id == 4)
            $low_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->where('risk', (int)Config::get('enums.severity_status.Low'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->get();
        elseif ($id == 3)
            $med_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->get();
        elseif ($id == 2)
            $high_risk_dt = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.High'))->where('department', Auth::user()->department)->get();
        elseif ($id == 1)
            $critical_risk_dt = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))->where('department', Auth::user()->department)->get();
        else
            $pending_rem_dt = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->get();

}
        return view('home.showdetails', compact('med_risk_dt', 'high_risk_dt', 'low_risk_dt','critical_risk_dt', 'pending_rem_dt', 'enums','id','opcos'));
    }


    public function opco_findings()
    {
        $opco_array = DB::connection('mysql')->table('opco')->where('status', Config::get('enums.opco_status.Primary'))->get()->toArray();
        $opco_array = json_decode(json_encode($opco_array), true);
        $opco_risk_array = [];

if (Auth::user()->roles->contains('1')) {
        foreach($opco_array as $opco){
            $tmp_risk_array = [];
            $critical = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco['id'])
                ->count();

            $high = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco['id'])
                ->count();

            $medium = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->where('opco_id', (int)$opco['id'])
                ->count();

            $low = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->where('opco_id', (int)$opco['id'])
                ->count();

            array_push($tmp_risk_array, $critical);
            array_push($tmp_risk_array, $high);
            array_push($tmp_risk_array, $medium);
            array_push($tmp_risk_array, $low);
            array_push($opco_risk_array, $tmp_risk_array);
        }
    } else {
        foreach($opco_array as $opco){
            $tmp_risk_array = [];
            $critical = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco['id'])
                ->count();

            $high = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco['id'])
                ->count();

            $medium = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->where('opco_id', (int)$opco['id'])
                ->count();

            $low = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->where('opco_id', (int)$opco['id'])
                ->count();

            array_push($tmp_risk_array, $critical);
            array_push($tmp_risk_array, $high);
            array_push($tmp_risk_array, $medium);
            array_push($tmp_risk_array, $low);
            array_push($opco_risk_array, $tmp_risk_array);
        }
    }
        $db_opco_findings = DB::connection('mongodb')->collection('db_opco_findings')->first();

        DB::connection('mongodb')->collection('db_opco_findings')
            ->where('_id', $db_opco_findings['_id'])
            ->update([
                'opco_risk_array' => $opco_risk_array,
                'opco_array' => $opco_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
            ]);
    }

    public function risk_levels()
    {
        if (Auth::user()->roles->contains('1')) {
        $info_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))->count();

        $low_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Low'))->groupby('name', 'host')->count();

        $med_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))->groupby('name', 'host')->count();

        $high_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))->count();

        $critical_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))->count();
        } else {
            $info_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))->count();

        $low_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.Low'))->groupby('name', 'host')->count();

        $med_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))->groupby('name', 'host')->count();

        $high_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.High'))->count();

        $critical_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))->count();
        }
        $db_risk_levels = DB::connection('mongodb')->collection('db_risk_levels')->first();

        DB::connection('mongodb')->collection('db_risk_levels')
            ->where('_id', $db_risk_levels['_id'])
            ->update([
                'info_risk' => $info_risk,
                'low_risk' => $low_risk,
                'med_risk' => $med_risk,
                'high_risk' => $high_risk,
                'critical_risk' => $critical_risk,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
            ]);
    }

    public function open_close()
    {
        if (Auth::user()->roles->contains('1')) {
        $close = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_pmo_closure_status', 1)->where('false_positive', 0)->whereNotNull('hod_signoff_date')->count();
        $open = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->whereNotNull('hod_signoff_date')->count();
        } else {
            $close = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_pmo_closure_status', 1)->where('false_positive', 0)->where('department', Auth::user()->department)->whereNotNull('hod_signoff_date')->count();
        $open = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->whereNotNull('hod_signoff_date')->count();
        }
        $db_open_close_findings = DB::connection('mongodb')->collection('db_open_close_findings')->first();

        DB::connection('mongodb')->collection('db_open_close_findings')
            ->where('_id', $db_open_close_findings['_id'])
            ->update([
                'close' => $close,
                'open' => $open,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
            ]);
    }

    public function num_of_hosts_by_cat()
    {
        $cat_array = DB::connection('mysql')->table('vul_categories')->get()->toArray();
        $cat_array = json_decode(json_encode($cat_array), true);
        $hosts_per_cat_array = [];

if (Auth::user()->roles->contains('1')) {
        foreach($cat_array as $key=>$cat){
            $temp_array = [];

            $critical_c = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->count();

            array_push($temp_array, $critical_c);

            $high_c = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk',  (int)Config::get('enums.severity_status.High'))
                ->count();
            array_push($temp_array, $high_c);

            $medium_c = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk',(int)Config::get('enums.severity_status.Medium'))
                ->count();
            array_push($temp_array, $medium_c);

            $low_c = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk',  (int)Config::get('enums.severity_status.Low'))
                ->count();
            array_push($temp_array, $low_c);

            array_push($hosts_per_cat_array, $temp_array);
        }
    } else {
        foreach($cat_array as $key=>$cat){
            $temp_array = [];

            $critical_c = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->count();

            array_push($temp_array, $critical_c);

            $high_c = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk',  (int)Config::get('enums.severity_status.High'))
                ->count();
            array_push($temp_array, $high_c);

            $medium_c = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk',(int)Config::get('enums.severity_status.Medium'))
                ->count();
            array_push($temp_array, $medium_c);

            $low_c = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('department', Auth::user()->department)
                ->where('risk',  (int)Config::get('enums.severity_status.Low'))
                ->count();
            array_push($temp_array, $low_c);

            array_push($hosts_per_cat_array, $temp_array);
        }       
    }
        $db_num_of_hosts_by_cat = DB::connection('mongodb')->collection('db_num_of_hosts_by_cat')->first();

        DB::connection('mongodb')->collection('db_num_of_hosts_by_cat')
            ->where('_id', $db_num_of_hosts_by_cat['_id'])
            ->update([
                'hosts_per_cat_array' => $hosts_per_cat_array,
                'cat_array' => $cat_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
            ]);
    }

    public function num_of_hosts_by_vuln_name()
    {
        $unique_vuln_names = DB::connection('mongodb')->collection('scan_results')->distinct('name')->get();
        $num_of_hosts_by_vuln_name = [];

if (Auth::user()->roles->contains('1')) {
        foreach($unique_vuln_names as $name) {
            $critical_n = DB::connection('mongodb')->collection('scan_results')
                ->distinct()
                ->select('host')
                ->where('name', $name)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->count();

            if($critical_n > 0) {
                $temp_array = [];
                array_push($temp_array, $name);
                array_push($temp_array, Config::get('enums.severity_status.Critical'));
                array_push($temp_array, $critical_n);
                array_push($num_of_hosts_by_vuln_name, $temp_array);
            }

            $high_n = DB::connection('mongodb')->collection('scan_results')
                ->distinct()
                ->select('host')
                ->where('name', $name)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->count();

            if($high_n > 0) {
                $temp_array = [];
                array_push($temp_array, $name);
                array_push($temp_array, Config::get('enums.severity_status.High'));
                array_push($temp_array, $high_n);
                array_push($num_of_hosts_by_vuln_name, $temp_array);
            }

            $medium_n = DB::connection('mongodb')->collection('scan_results')
                ->distinct()
                ->select('host')
                ->where('name', $name)
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->count();

            if($medium_n > 0) {
                $temp_array = [];
                array_push($temp_array, $name);
                array_push($temp_array, Config::get('enums.severity_status.Medium'));
                array_push($temp_array, $medium_n);
                array_push($num_of_hosts_by_vuln_name, $temp_array);
            }

            $low_n = DB::connection('mongodb')->collection('scan_results')
                ->distinct()
                ->select('host')
                ->where('name', $name)
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->count();

            if($low_n > 0) {
                $temp_array = [];
                array_push($temp_array, $name);
                array_push($temp_array, Config::get('enums.severity_status.Low'));
                array_push($temp_array, $low_n);
                array_push($num_of_hosts_by_vuln_name, $temp_array);
            }
        }
    } else {
        foreach($unique_vuln_names as $name) {
            $critical_n = DB::connection('mongodb')->collection('scan_results')
                ->distinct()
                ->select('host')
                ->where('name', $name)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->count();

            if($critical_n > 0) {
                $temp_array = [];
                array_push($temp_array, $name);
                array_push($temp_array, Config::get('enums.severity_status.Critical'));
                array_push($temp_array, $critical_n);
                array_push($num_of_hosts_by_vuln_name, $temp_array);
            }

            $high_n = DB::connection('mongodb')->collection('scan_results')
                ->distinct()
                ->select('host')
                ->where('name', $name)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->count();

            if($high_n > 0) {
                $temp_array = [];
                array_push($temp_array, $name);
                array_push($temp_array, Config::get('enums.severity_status.High'));
                array_push($temp_array, $high_n);
                array_push($num_of_hosts_by_vuln_name, $temp_array);
            }

            $medium_n = DB::connection('mongodb')->collection('scan_results')
                ->distinct()
                ->select('host')
                ->where('name', $name)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->count();

            if($medium_n > 0) {
                $temp_array = [];
                array_push($temp_array, $name);
                array_push($temp_array, Config::get('enums.severity_status.Medium'));
                array_push($temp_array, $medium_n);
                array_push($num_of_hosts_by_vuln_name, $temp_array);
            }

            $low_n = DB::connection('mongodb')->collection('scan_results')
                ->distinct()
                ->select('host')
                ->where('name', $name)
                ->where('department', Auth::user()->department)
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->count();

            if($low_n > 0) {
                $temp_array = [];
                array_push($temp_array, $name);
                array_push($temp_array, Config::get('enums.severity_status.Low'));
                array_push($temp_array, $low_n);
                array_push($num_of_hosts_by_vuln_name, $temp_array);
            }
        }
    }
        $db_num_of_hosts_by_vuln_name = DB::connection('mongodb')->collection('db_num_of_hosts_by_vuln_name')->first();

        DB::connection('mongodb')->collection('db_num_of_hosts_by_vuln_name')
            ->where('_id', $db_num_of_hosts_by_vuln_name['_id'])
            ->update([
                'num_of_hosts_by_vuln_name' => $num_of_hosts_by_vuln_name,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
            ]);
    }
}