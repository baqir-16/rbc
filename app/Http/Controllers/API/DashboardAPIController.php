<?php

namespace App\Http\Controllers\API;

use App\Stream;
use App\Http\Controllers\Controller;
use Validator;
use DB;
use Config;
use Carbon\Carbon;
use App\Pdfreport;

// API call controller for fetching dashboard data to push to GCDP through S2S VPN
class DashboardAPIController extends Controller
{
    // Retrieve open findings to push to GCDP
    public function open() {
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

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $critical_risk = $encrypter->encrypt($critical_risk);
        $high_risk = $encrypter->encrypt($high_risk);
        $med_risk = $encrypter->encrypt($med_risk);
        $low_risk = $encrypter->encrypt($low_risk);
        $pending_rem_c = $encrypter->encrypt($pending_rem_c);
        $pending_rem_h = $encrypter->encrypt($pending_rem_h);
        $pending_rem_m = $encrypter->encrypt($pending_rem_m);
        $pending_rem_l = $encrypter->encrypt($pending_rem_l);

        return response()->json([
            'critical_risk' => $critical_risk,
            'high_risk' => $high_risk,
            'med_risk' => $med_risk,
            'low_risk' => $low_risk,
            'pending_rem_c' => $pending_rem_c,
            'pending_rem_h' => $pending_rem_h,
            'pending_rem_m' => $pending_rem_m,
            'pending_rem_l' => $pending_rem_l,
        ]);
    }


    // Retrieve number of hosts by category of findings to push to GCDP
    public function db_num_of_hosts_by_cat() {
        $db_num_of_hosts_by_cat = DB::connection('mongodb')->collection('db_num_of_hosts_by_cat')->where('department', null)->first();
        $cat_array = $db_num_of_hosts_by_cat['cat_array'];
        $hosts_per_cat_array = $db_num_of_hosts_by_cat['hosts_per_cat_array'];

        foreach($hosts_per_cat_array as $key=>$value){
            if($value[0]+$value[1]+$value[2]+$value[3] == 0){
                unset($cat_array[$key]);
                unset($hosts_per_cat_array[$key]);
            }
        }

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $cat_array = $encrypter->encrypt($cat_array);
        $hosts_per_cat_array = $encrypter->encrypt($hosts_per_cat_array);

        return response()->json([
            'cat_array' => $cat_array,
            'hosts_per_cat_array' => $hosts_per_cat_array,
        ]);
    }

    // Retrieve number of hosts by vulnerability name of findings to push to GCDP
    public function num_of_hosts_by_vuln_name() {
        $db_num_of_hosts_by_vuln_name = DB::connection('mongodb')->collection('db_num_of_hosts_by_vuln_name')->where('department', null)->first();
        $num_of_hosts_by_vuln_name = $db_num_of_hosts_by_vuln_name['num_of_hosts_by_vuln_name'];

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $num_of_hosts_by_vuln_name = $encrypter->encrypt($num_of_hosts_by_vuln_name);

        return response()->json([
            'num_of_hosts_by_vuln_name' => $num_of_hosts_by_vuln_name,
        ]);
    }


    // Retrieve the total number of open and close findings to push to GCDP
    public function total_open_close() {
        $db_open_close_findings = DB::connection('mongodb')->collection('db_open_close_findings')->where('department', null)->first();
        $open = $db_open_close_findings['open'];
        $close = $db_open_close_findings['close'];

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $open = $encrypter->encrypt($open);
        $close = $encrypter->encrypt($close);

        return response()->json([
            'open' => $open,
            'close' => $close,
        ]);
    }

    // Retrieve vulnerable findings from the past 9 weeks to push to GCDP (Inclusive of open and closed findings)
    public function vuln_exposure_past_four_months() {
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

        $open_array = [];
        array_push($open_array, $owk1);
        array_push($open_array, $owk2);
        array_push($open_array, $owk3);
        array_push($open_array, $owk4);
        array_push($open_array, $owk5);
        array_push($open_array, $owk6);
        array_push($open_array, $owk7);
        array_push($open_array, $owk8);

        $close_array = [];
        array_push($close_array, $cwk1);
        array_push($close_array, $cwk2);
        array_push($close_array, $cwk3);
        array_push($close_array, $cwk4);
        array_push($close_array, $cwk5);
        array_push($close_array, $cwk6);
        array_push($close_array, $cwk7);
        array_push($close_array, $cwk8);

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $open_array = $encrypter->encrypt($open_array);
        $close_array = $encrypter->encrypt($close_array);

        return response()->json([
            'open_array' => $open_array,
            'close_array' => $close_array,
        ]);
    }

    // Retrieve open findings of an OpCo by severity level to push to GCDP
    public function opco_findings()
    {
        $opco_risk_array = [];
        $tmp_risk_array = [];

        $critical = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->count();

        $high = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->count();

        $medium = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))
            ->count();

        $low = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Low'))
            ->count();

        array_push($tmp_risk_array, $critical);
        array_push($tmp_risk_array, $high);
        array_push($tmp_risk_array, $medium);
        array_push($tmp_risk_array, $low);
        array_push($opco_risk_array, $tmp_risk_array);

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $opco_risk_array = $encrypter->encrypt($opco_risk_array);

        return response()->json([
            'opco_risk_array' => $opco_risk_array,
        ]);
    }

    // Retrieve findings data for the aging and kpi chart to push to GCDP
    public function aging_and_kpi()
    {
        $pdfinfo = DB::connection('mysql')->table('pdfreports')->get()->toArray();
        $kpi_opco_risk_array = [];
        $aging_opco_risk_array = [];

// ===== KPI RISK START =====

        $kpi_tmp_risk_array = [];

        $kpi_tmp_critical_open = [];
        $critical_open = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->where('opco_id', (int)$_ENV['APP_OPCO_ID'])
            ->get()->toArray();

        foreach ($critical_open  as $key => $issue) {
            $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
            $date = Carbon::now();

            $diff = $start->diffInHours($date);
            $kpi_tmp_critical_open[$key]['diff'] = $diff;
        }
        array_push($kpi_tmp_risk_array, $kpi_tmp_critical_open);

        $kpi_tmp_high_open = [];
        $high_open = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->where('opco_id', (int)$_ENV['APP_OPCO_ID'])
            ->get()->toArray();

        foreach ($high_open  as $key => $issue) {
            $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
            $date = Carbon::now();

            $diff = $start->diffInHours($date);
            $kpi_tmp_high_open[$key]['diff'] = $diff;
        }
        array_push($kpi_tmp_risk_array, $kpi_tmp_high_open);

        $kpi_tmp_critical_close = [];
        $critical_close = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->where('opco_id', (int)$_ENV['APP_OPCO_ID'])
            ->get()->toArray();

        foreach ($critical_close as $key => $issue) {
            $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
            $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

            $diff = $start->diffInHours($end);
            $kpi_tmp_critical_close[$key]['diff'] = $diff;
        }
        array_push($kpi_tmp_risk_array, $kpi_tmp_critical_close);

        $kpi_tmp_high_close = [];
        $high_close = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->where('opco_id', (int)$_ENV['APP_OPCO_ID'])
            ->get()->toArray();

        foreach ($high_close  as $key => $issue) {
            $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
            $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

            $diff = $start->diffInHours($end);
            $kpi_tmp_high_close[$key]['diff'] = $diff;
        }
        array_push($kpi_tmp_risk_array, $kpi_tmp_high_close);
        array_push($kpi_opco_risk_array, $kpi_tmp_risk_array);

// ===== KPI RISK END =====

// ===== AGING RISK START =====
        $aging_tmp_risk_array = [];

        $tmp_critical_opened = [];
        $critical_opened = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->where('opco_id', (int)$_ENV['APP_OPCO_ID'])
            ->get()->toArray();

        foreach ($critical_opened  as $key => $issue) {
            $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
            $date = Carbon::now();

            $diff = $start->diffInHours($date);
            $tmp_critical_opened[$key]['diff'] = $diff;
        }

        array_push($aging_tmp_risk_array, $tmp_critical_opened);

        $tmp_critical_closed = [];
        $critical_closed = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->where('opco_id', (int)$_ENV['APP_OPCO_ID'])
            ->get()->toArray();

        foreach ($critical_closed as $key => $issue) {
            $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
            $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

            $diff = $start->diffInHours($end);
            $tmp_critical_closed[$key]['diff'] = $diff;
        }
        array_push($aging_tmp_risk_array, $tmp_critical_closed);

        $tmp_high_opened = [];
        $high_opened = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->where('opco_id', (int)$_ENV['APP_OPCO_ID'])
            ->get()->toArray();

        foreach ($high_opened  as $key => $issue) {
            $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
            $date = Carbon::now();

            $diff = $start->diffInHours($date);
            $tmp_high_opened[$key]['diff'] = $diff;
        }

        array_push($aging_tmp_risk_array, $tmp_high_opened);

        $tmp_high_closed = [];
        $high_closed = DB::connection('mongodb')->collection('scan_results')
            ->whereNotNull('hod_signoff_date')
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->where('opco_id', (int)$_ENV['APP_OPCO_ID'])
            ->get()->toArray();

        foreach ($high_closed  as $key => $issue) {
            $start = Carbon::parse(date('Y-m-d', $issue['hod_signoff_date']->toDateTime()->format('U')));
            $end = Carbon::parse(date('Y-m-d', $issue['rem_pmo_closure_date']->toDateTime()->format('U')));

            $diff = $start->diffInHours($end);
            $tmp_high_closed[$key]['diff'] = $diff;
        }

        array_push($aging_tmp_risk_array, $tmp_high_closed);
        array_push($aging_opco_risk_array, $aging_tmp_risk_array);

// ===== AGING RISK END =====

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $pdfinfo = $encrypter->encrypt($pdfinfo);
        $aging_opco_risk_array = $encrypter->encrypt($aging_opco_risk_array);
        $kpi_opco_risk_array = $encrypter->encrypt($kpi_opco_risk_array);

        return response()->json([
            'pdfinfo' => $pdfinfo,
            'aging_opco_risk_array' => $aging_opco_risk_array,
            'kpi_opco_risk_array' => $kpi_opco_risk_array,
        ]);
    }
}
