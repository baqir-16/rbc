<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stream;
use DB;
use App;
use Response;
use ScanResults;
use Config;

class OpcoDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Dashboard for Opco
        $opco = DB::table('opco')->where('id', $request->opco_id)->first();
        $vul_categories = DB::connection('mysql')->table('vul_categories')->get();

        $db_opco_findings = DB::connection('mongodb')->collection('db_opco_findings')->first();
        foreach($db_opco_findings['opco_array'] as $key => $opco_arr){
            if($opco_arr['id'] == $opco->id) {
                $opco_array = $opco_arr;
                $opco_risk_array = $db_opco_findings['opco_risk_array'][$key];
            }
        }

        // Count closed scan results in mongodb
        $close = DB::connection('mongodb')->collection('scan_results')
            ->where('opco_id', (int)$opco->id)
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->where('risk', '!=', (int)Config::get('enums.severity_status.Informational'))
            ->whereNotNull('hod_signoff_date')
            ->count();
        // Count open scan results in mongodb
        $open = DB::connection('mongodb')->collection('scan_results')
            ->where('opco_id', (int)$opco->id)
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', '!=', (int)Config::get('enums.severity_status.Informational'))
            ->whereNotNull('hod_signoff_date')
            ->count();

        $cat_array = DB::connection('mysql')->table('vul_categories')->get()->toArray();
        $cat_array = json_decode(json_encode($cat_array), true);
        $hosts_per_cat_array = [];

        // fetch each vulnerability categories from mongodb
        foreach($cat_array as $key=>$cat){
            $temp_array = [];

            $critical = DB::connection('mongodb')->collection('scan_results')
                ->where('opco_id', (int)$opco->id)
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', '=', (int)Config::get('enums.severity_status.Critical'))
                ->count();

            array_push($temp_array, $critical);

            $high = DB::connection('mongodb')->collection('scan_results')
                ->where('opco_id', (int)$opco->id)
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', '=', (int)Config::get('enums.severity_status.High'))
                ->count();
            array_push($temp_array, $high);

            $medium = DB::connection('mongodb')->collection('scan_results')
                ->where('opco_id', (int)$opco->id)
                ->whereNotNull('vul_category')
                ->whereNotNull('hod_signoff_date')
                ->where('vul_category', (int)$key+1)
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('risk', '=', (int)Config::get('enums.severity_status.Medium'))
                ->count();
            array_push($temp_array, $medium);


            $low = DB::connection('mongodb')->collection('scan_results')
                ->where('opco_id', (int)$opco->id)
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

        foreach($hosts_per_cat_array as $key=>$value){
            if($value[0]+$value[1]+$value[2]+$value[3] == 0){
                unset($cat_array[$key]);
                unset($hosts_per_cat_array[$key]);
            }
        }

//        $unique_vuln_names = DB::connection('mongodb')->collection('scan_results')->distinct('name')->get();
//        $num_of_hosts_by_vuln_name = [];
//
//        foreach($unique_vuln_names as $name) {
//            $critical_n = DB::connection('mongodb')->collection('scan_results')
//                ->distinct()
//                ->select('host')
//                ->where('name', $name)
//                ->where('opco_id', (int)$opco->id)
//                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
//                ->whereNotNull('hod_signoff_date')
//                ->where('false_positive', 0)
//                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
//                ->count();
//
//            if($critical_n > 0) {
//                $temp_array = [];
//                array_push($temp_array, $name);
//                array_push($temp_array, Config::get('enums.severity_status.Critical'));
//                array_push($temp_array, $critical_n);
//                array_push($num_of_hosts_by_vuln_name, $temp_array);
//            }
//
//            $high_n = DB::connection('mongodb')->collection('scan_results')
//                ->distinct()
//                ->select('host')
//                ->where('name', $name)
//                ->where('opco_id', (int)$opco->id)
//                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
//                ->whereNotNull('hod_signoff_date')
//                ->where('false_positive', 0)
//                ->where('risk', (int)Config::get('enums.severity_status.High'))
//                ->count();
//
//            if($high_n > 0) {
//                $temp_array = [];
//                array_push($temp_array, $name);
//                array_push($temp_array, Config::get('enums.severity_status.High'));
//                array_push($temp_array, $high_n);
//                array_push($num_of_hosts_by_vuln_name, $temp_array);
//            }
//
//            $medium_n = DB::connection('mongodb')->collection('scan_results')
//                ->distinct()
//                ->select('host')
//                ->where('name', $name)
//                ->where('opco_id', (int)$opco->id)
//                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
//                ->whereNotNull('hod_signoff_date')
//                ->where('false_positive', 0)
//                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
//                ->count();
//
//            if($medium_n > 0) {
//                $temp_array = [];
//                array_push($temp_array, $name);
//                array_push($temp_array, Config::get('enums.severity_status.Medium'));
//                array_push($temp_array, $medium_n);
//                array_push($num_of_hosts_by_vuln_name, $temp_array);
//            }
//
//            $low_n = DB::connection('mongodb')->collection('scan_results')
//                ->distinct()
//                ->select('host')
//                ->where('name', $name)
//                ->where('opco_id', (int)$opco->id)
//                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
//                ->whereNotNull('hod_signoff_date')
//                ->where('false_positive', 0)
//                ->where('risk', (int)Config::get('enums.severity_status.Low'))
//                ->count();
//
//            if($low_n > 0) {
//                $temp_array = [];
//                array_push($temp_array, $name);
//                array_push($temp_array, Config::get('enums.severity_status.Low'));
//                array_push($temp_array, $low_n);
//                array_push($num_of_hosts_by_vuln_name, $temp_array);
//            }
//        }



        $info_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('opco_id', (int)$opco->id)
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->count();

        $low_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('opco_id', (int)$opco->id)
            ->where('risk', (int)Config::get('enums.severity_status.Low'))
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->count();

        $med_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('opco_id', (int)$opco->id)
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->count();

        $high_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('opco_id', (int)$opco->id)
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->count();

        $critical_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('opco_id', (int)$opco->id)
            ->whereNotNull('hod_signoff_date')
            ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->count();

        $pending_rem_c = DB::connection('mongodb')->collection('scan_results')->where('opco_id', (int)$opco->id)->where('risk', (int)Config::get('enums.severity_status.Critical'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $pending_rem_h = DB::connection('mongodb')->collection('scan_results')->where('opco_id', (int)$opco->id)->where('risk', (int)Config::get('enums.severity_status.High'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $pending_rem_m = DB::connection('mongodb')->collection('scan_results')->where('opco_id', (int)$opco->id)->where('risk', (int)Config::get('enums.severity_status.Medium'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();

        $pending_rem_l = DB::connection('mongodb')->collection('scan_results')->where('opco_id', (int)$opco->id)->where('risk', (int)Config::get('enums.severity_status.Low'))->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();



        $streamscount = Stream::get();
        $st_complete = Stream::where('status', '11')->get();
        $opco_id = $opco->opco;

        return view('dashboards.opco_dashboard', compact('usercount', 'ticketscount','streamscount', 'st_complete',
            'med_risk', 'high_risk', 'info_risk', 'low_risk','critical_risk', 'pending_rem_c','pending_rem_h','pending_rem_m','pending_rem_l',
            'med_catg', 'high_catg', 'info_catg', 'low_catg','critical_catg',
            'close', 'open', 'opco_risk_array', 'opco_array', 'vul_categories',
            'hosts_per_cat_array', 'cat_array', 'num_of_hosts_by_vuln_name', 'opco_id' ));
    }

    public function riskdetails($id, $opcoid) {
        // retrieve information of mongodb of each chosen vuln categories and show in details
        $opcos = array_flip(Config::get('enums.opco_switch'));
        $enums = array_flip(Config::get('enums.severity_status'));
        if($id == 4)
            $low_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('opco_id', (int)$opcoid)
                ->get();
        elseif ($id == 3)
            $med_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('opco_id', (int)$opcoid)
                ->get();
        elseif ($id == 2)
            $high_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('opco_id', (int)$opcoid)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->get();
        elseif ($id == 1)
            $critical_risk_dt = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('opco_id', (int)$opcoid)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->get();
        else
            $pending_rem_dt = DB::connection('mongodb')->collection('scan_results')
                ->whereNotNull('hod_signoff_date')
                ->where('rem_officer_rem_status', 1)
                ->where('rem_pmo_closure_status', 0)
                ->where('false_positive', 0)
                ->where('opco_id', (int)$opcoid)
                ->get();

        return view('home.showdetails', compact('med_risk_dt', 'high_risk_dt', 'low_risk_dt','critical_risk_dt', 'pending_rem_dt','enums','id','opcos'));
    }

}
