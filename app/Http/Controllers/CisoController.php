<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stream;
use DB;
use Auth;
use App;
use Response;
use ScanResults;
use Config;

// CISO contorller for CISO dashboard
class CisoController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Returns analytical information for the CISO dashboard
    public function index(Request $request)
    {
        $opco = DB::table('users')->select('opco_id')->first();
        $user_opco = Auth::user()->opco_id;
//
//        $vul_categories = DB::connection('mysql')->table('vul_categories')->get();
//
        $db_opco_findings = DB::connection('mongodb')->collection('db_opco_findings')->first();
        foreach($db_opco_findings['opco_array'] as $key => $opco_arr){
            if($opco_arr['id'] == $user_opco) {
                $opco_array = $opco_arr;
                $opco_risk_array = $db_opco_findings['opco_risk_array'][$key];
            }
        }

        $close = DB::connection('mongodb')->collection('scan_results')
            ->where('opco_id', $user_opco)
            ->where('risk','!=',1)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->count();
        $open = DB::connection('mongodb')->collection('scan_results')
            ->where('opco_id', $user_opco)
            ->where('risk','!=',1)
            ->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->count();
//
        $cat_array = DB::connection('mysql')->table('vul_categories')->get()->toArray();
        $cat_array = json_decode(json_encode($cat_array), true);
        $hosts_per_cat_array = [];
//
        foreach($cat_array as $key=>$cat){
            $temp_array = [];

            $critical = DB::connection('mongodb')->collection('scan_results')
                ->where('opco_id', $opco->opco_id)
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', '=', (int)Config::get('enums.severity_status.Critical'))
                ->count();

            array_push($temp_array, $critical);

            $high = DB::connection('mongodb')->collection('scan_results')
                ->where('opco_id', $opco->opco_id)
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', '=', (int)Config::get('enums.severity_status.High'))
                ->count();
            array_push($temp_array, $high);

            $medium = DB::connection('mongodb')->collection('scan_results')
                ->where('opco_id', $opco->opco_id)
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', '=', (int)Config::get('enums.severity_status.Medium'))
                ->count();
            array_push($temp_array, $medium);


            $low = DB::connection('mongodb')->collection('scan_results')
                ->where('opco_id', $opco->opco_id)
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', '=', (int)Config::get('enums.severity_status.Low'))
                ->count();
            array_push($temp_array, $low);

            array_push($hosts_per_cat_array, $temp_array);
        }
//
        foreach($hosts_per_cat_array as $key=>$value){
            if($value[0]+$value[1]+$value[2]+$value[3] == 0){
                unset($cat_array[$key]);
                unset($hosts_per_cat_array[$key]);
            }
        }
//
//        $unique_vuln_names = DB::connection('mongodb')->collection('scan_results')->groupBy('name')->get();
//        $num_of_hosts_by_vuln_name = [];
//
//        foreach($unique_vuln_names as $name) {
//            $res = DB::connection('mongodb')->collection('scan_results')->distinct()->select('host')
//                ->where('opco_id', $user_opco)
//                ->where('risk','!=', 1)
//                ->where('rem_pmo_closure_status', 0)
//                ->where('false_positive', 0)
//                ->whereNotNull('hod_signoff_date')
//                ->where('name', $name['name'])->get();
//            array_push($num_of_hosts_by_vuln_name, $res->count());
//        }
//
//        foreach($num_of_hosts_by_vuln_name as $key=>$value){
//            if($value == 0){
//                unset($unique_vuln_names[$key]);
//                unset($num_of_hosts_by_vuln_name[$key]);
//            }
//        }

        $info_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('opco_id', $user_opco)
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))
            ->whereNotNull('hod_signoff_date')
            ->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->count();

        $low_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Low'))->where('opco_id', $user_opco)->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $med_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))->where('opco_id', $user_opco)->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $high_risk = DB::connection('mongodb')->collection('scan_results')->where('opco_id', $user_opco)->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))->count();

        $critical_risk = DB::connection('mongodb')->collection('scan_results')->where('opco_id', $user_opco)->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))->count();

        $pending_rem_c = DB::connection('mongodb')->collection('scan_results')->where('opco_id', $user_opco)->where('risk', (int)Config::get('enums.severity_status.Critical'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $pending_rem_h = DB::connection('mongodb')->collection('scan_results')->where('opco_id', $user_opco)->where('risk', (int)Config::get('enums.severity_status.High'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $pending_rem_m = DB::connection('mongodb')->collection('scan_results')->where('opco_id', $user_opco)->where('risk', (int)Config::get('enums.severity_status.Medium'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $pending_rem_l = DB::connection('mongodb')->collection('scan_results')->where('opco_id', $user_opco)->where('risk', (int)Config::get('enums.severity_status.Low'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

//        $info_catg = DB::connection('mongodb')->collection('scan_results')
//            ->where('opco_id', $user_opco)
//            ->whereNotNull('hod_signoff_date')
//            ->whereNotNull('vul_category')
//            ->where('rem_pmo_closure_status', 0)
//            ->where('false_positive', 0)
//            ->where('risk', (int)Config::get('enums.severity_status.Informational'))
//            ->select('name', 'host' , 'vul_category')
//            ->groupby('name', 'desc')
//            ->limit(10)->get();
//
//        $low_catg = DB::connection('mongodb')->collection('scan_results')
//            ->where('opco_id', $user_opco)
//            ->whereNotNull('hod_signoff_date')
//            ->whereNotNull('vul_category')
//            ->where('rem_pmo_closure_status', 0)
//            ->where('false_positive', 0)
//            ->where('risk', (int)Config::get('enums.severity_status.Low'))
//            ->select('name', 'host', 'vul_category')
//            ->groupby('name', 'desc')
//            ->limit(10)->get();
//
//        $med_catg = DB::connection('mongodb')->collection('scan_results')
//            ->where('opco_id', $user_opco)
//            ->whereNotNull('hod_signoff_date')
//            ->whereNotNull('vul_category')
//            ->where('rem_pmo_closure_status', 0)
//            ->where('false_positive', 0)
//            ->where('risk', (int)Config::get('enums.severity_status.Medium'))
//            ->select('name', 'host', 'vul_category')
//            ->groupby('name', 'desc')
//            ->limit(10)->get();
//
//        $high_catg = DB::connection('mongodb')->collection('scan_results')
//            ->where('opco_id', $user_opco)
//            ->whereNotNull('hod_signoff_date')
//            ->whereNotNull('vul_category')
//            ->where('rem_pmo_closure_status', 0)
//            ->where('false_positive', 0)
//            ->where('risk', (int)Config::get('enums.severity_status.High'))
//            ->select('name', 'host', 'vul_category')
//            ->groupby('name', 'desc')
//            ->limit(10)->get();
//
//        $critical_catg = DB::connection('mongodb')->collection('scan_results')
//            ->where('opco_id', $user_opco)
//            ->whereNotNull('hod_signoff_date')
//            ->whereNotNull('vul_category')
//            ->where('rem_pmo_closure_status', 0)
//            ->where('false_positive', 0)
//            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
//            ->select('name', 'host', 'vul_category')
//            ->groupby('name', 'desc')
//            ->limit(10)->get();
//
//        $streamscount = Stream::get();
//        $st_complete = Stream::where('status', '11')->get();
//        $opco_id = $request->opco_id;

        return view('dashboards.ciso_dashboard', compact('usercount', 'ticketscount','streamscount', 'st_complete',
            'med_risk', 'high_risk', 'info_risk', 'low_risk','critical_risk', 'pending_rem_c', 'pending_rem_m', 'pending_rem_l', 'pending_rem_h',
            'med_catg', 'high_catg', 'info_catg', 'low_catg','critical_catg',
            'close', 'open', 'opco_risk_array', 'opco_array', 'vul_categories',
            'hosts_per_cat_array', 'cat_array', 'unique_vuln_names', 'num_of_hosts_by_vuln_name', 'opco_id' ));
    }

    // Returns findings by severity level
    public function riskdetails($id) {
        $opcos = array_flip(Config::get('enums.opco_switch'));
        $enums = array_flip(Config::get('enums.severity_status'));
        if($id == 4)
            $low_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('opco_id', Auth::user()->opco_id)
                ->get();
        elseif ($id == 3)
            $med_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('opco_id', Auth::user()->opco_id)
                ->get();
        elseif ($id == 2)
            $high_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('opco_id', Auth::user()->opco_id)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->get();
        elseif ($id == 1)
            $critical_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('opco_id', Auth::user()->opco_id)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->get();

        return view('dashboards.cisoshowdetails', compact('med_risk_dt', 'high_risk_dt', 'low_risk_dt','critical_risk_dt', 'enums','id','opcos'));
    }

    // Return all opco findings by severity level
    public function opco_findings()
    {
        $opco_array = DB::connection('mysql')->table('opco')->where('status', Config::get('enums.opco_status.Primary'))->get()->toArray();
        $opco_array = json_decode(json_encode($opco_array), true);
        $opco_risk_array = [];

        foreach($opco_array as $opco){
            $tmp_risk_array = [];
            $critical = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', $opco->opco_id)
                ->count();

            $high = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', $opco->opco_id)
                ->count();

            $medium = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->where('opco_id', $opco->opco_id)
                ->count();

            $low = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->where('opco_id', $opco->opco_id)
                ->count();

            array_push($tmp_risk_array, $critical);
            array_push($tmp_risk_array, $high);
            array_push($tmp_risk_array, $medium);
            array_push($tmp_risk_array, $low);
            array_push($opco_risk_array, $tmp_risk_array);
        }

        $db_opco_findings = DB::connection('mongodb')->collection('db_opco_findings')->first();

        DB::connection('mongodb')->collection('db_opco_findings')
            ->where('_id', $db_opco_findings['_id'])
            ->update([
                'opco_risk_array' => $opco_risk_array,
                'opco_array' => $opco_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
            ]);
    }

    // Returns total count of findings by severity level
    public function risk_levels()
    {
        $info_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))->count();

        $low_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Low'))->groupby('name', 'host')->count();

        $med_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))->groupby('name', 'host')->count();

        $high_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))->count();

        $critical_risk = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))->count();

        $db_risk_levels = DB::connection('mongodb')->collection('db_risk_levels')->first();

        DB::connection('mongodb')->collection('db_risk_levels')
            ->where('_id', $db_risk_levels['_id'])
            ->update([
                'info_risk' => $info_risk,
                'low_risk' => $low_risk,
                'med_risk' => $med_risk,
                'high_risk' => $high_risk,
                'critical_risk' => $critical_risk,
                'updated_at' => time(),
            ]);
    }

    // Returns the total of open and closed external findings of all opco
    public function open_close()
    {
        $close = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_pmo_closure_status', 1)->where('false_positive', 0)->whereNotNull('hod_signoff_date')->count();
        $open = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->whereNotNull('hod_signoff_date')->count();

        $db_open_close_findings = DB::connection('mongodb')->collection('db_open_close_findings')->first();

        DB::connection('mongodb')->collection('db_open_close_findings')
            ->where('_id', $db_open_close_findings['_id'])
            ->update([
                'close' => $close,
                'open' => $open,
                'updated_at' => time(),
            ]);
    }

    // Returns the number of host by category of external findings of all opco
    public function num_of_hosts_by_cat()
    {
        $cat_array = DB::connection('mysql')->table('vul_categories')->get()->toArray();
        $cat_array = json_decode(json_encode($cat_array), true);
        $hosts_per_cat_array = [];

        foreach($cat_array as $key=>$cat){
            $count = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', $cat['id'])
                ->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->count();

            array_push($hosts_per_cat_array, $count);
        }
        $db_num_of_hosts_by_cat = DB::connection('mongodb')->collection('db_num_of_hosts_by_cat')->first();

        DB::connection('mongodb')->collection('db_num_of_hosts_by_cat')
            ->where('_id', $db_num_of_hosts_by_cat['_id'])
            ->update([
                'hosts_per_cat_array' => $hosts_per_cat_array,
                'cat_array' => $cat_array,
                'updated_at' => time(),
            ]);
    }

    // Returns the number of hosts by vulnerability name of external findings of all opco
    public function num_of_hosts_by_vuln_name()
    {
        $unique_vuln_names = DB::connection('mongodb')->collection('scan_results')->groupBy('name')->get();
        $num_of_hosts_by_vuln_name = [];

        foreach($unique_vuln_names as $name) {
            $res = DB::connection('mongodb')->collection('scan_results')->distinct()->select('host')->where('name', $name['name'])->get();
            array_push($num_of_hosts_by_vuln_name, $res->count());
        }

        $db_num_of_hosts_by_vuln_name = DB::connection('mongodb')->collection('db_num_of_hosts_by_vuln_name')->first();
        $unique_vuln_names = json_decode(json_encode($unique_vuln_names), true);

        DB::connection('mongodb')->collection('db_num_of_hosts_by_vuln_name')
            ->where('_id', $db_num_of_hosts_by_vuln_name['_id'])
            ->update([
                'num_of_hosts_by_vuln_name' => $num_of_hosts_by_vuln_name,
                'unique_vuln_names' => $unique_vuln_names,
                'updated_at' => time(),
            ]);
    }

    // Returns selected finding details
    public function show($_id)
    {
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('dashboards.cisoshowcsv', compact('issue','enums', 'all_comments'));
    }

    // Returns selected finding details
    public function showxml($_id)
    {
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('dashboards.cisoshowxml', compact('issue','enums', 'all_comments'));
    }

    // Returns selected appscan finding details
    public function showappscan($_id)
    {
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('dashboards.cisoshowappscan', compact('issue','enums', 'all_comments'));
    }
}

