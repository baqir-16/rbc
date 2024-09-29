<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use Config;
use App\Stream;
use MongoDB\BSON\UTCDateTime;

// Main controller for generating remediation reports
class RemReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Returns the total number of open and closed findings of all opco
    public function total_open_closed()
    {
        // retrieve information from mongodb to display on remediation reports 
        $total_open = DB::connection('mongodb')->collection('scan_results')->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))->whereNotNull ('hod_signoff_date')->count();
        $total_closed = DB::connection('mongodb')->collection('scan_results')->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Close'))->orWhere('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Exception'))->whereNotNull ('hod_signoff_date')->count();
        $total_all = DB::connection('mongodb')->collection('scan_results')->where('false_positive', 0)->whereNotNull ('hod_signoff_date')->count();

        $opco_array = DB::connection('mysql')->table('opco')->where('status', Config::get('enums.opco_status.Primary'))->get();

        $opco_only_array = [];
        foreach($opco_array as $opco){
            array_push($opco_only_array, $opco->opco);
        }

        $open_low = [];
        $open_med = [];
        $open_high = [];
        $open_crit = [];
        $open_high_crit = [];
        $closed_low = [];
        $closed_med = [];
        $closed_high = [];
        $closed_crit = [];

        // query for number of open tickets and classify risk level
        foreach ($opco_array as $opco) {
            $low_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))
                ->where('opco_id', (int)$opco->id)
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($open_low, $low_risk);

            $med_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))
                ->where('opco_id', (int)$opco->id)
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($open_med, $med_risk);

            $high_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))
                ->where('opco_id', (int)$opco->id)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($open_high, $high_risk);

            $crit_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))
                ->where('opco_id', (int)$opco->id)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($open_crit, $crit_risk);
        }

        $total_low_risk = 0;
        foreach ($open_low as $opco) {
            $total_low_risk = $total_low_risk + $opco;
        }
        array_push($open_low, $total_low_risk);

        $total_med_risk = 0;
        foreach ($open_med as $opco) {
            $total_med_risk = $total_med_risk + $opco;
        }
        array_push($open_med, $total_med_risk);

        $total_high_risk = 0;
        foreach ($open_high as $opco) {
            $total_high_risk = $total_high_risk + $opco;
        }
        array_push($open_high, $total_high_risk);

        $total_crit_risk = 0;
        foreach ($open_crit as $opco) {
            $total_crit_risk = $total_crit_risk + $opco;
        }
        array_push($open_crit, $total_crit_risk);

        // query for number of closed tickets and classify risk level
        foreach ($opco_array as $opco) {
            $low_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Close'))
                ->where('opco_id', (int)$opco->id)
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_low, $low_risk);

            $med_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Close'))
                ->where('opco_id', (int)$opco->id)
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_med, $med_risk);

            $high_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Close'))
                ->where('opco_id', (int)$opco->id)
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_high, $high_risk);

            $crit_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Close'))
                ->where('opco_id', (int)$opco->id)
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_crit, $crit_risk);
        }

        $total_low_risk = 0;
        foreach ($closed_low as $opco) {
            $total_low_risk = $total_low_risk + $opco;
        }
        array_push($closed_low, $total_low_risk);

        $total_med_risk = 0;
        foreach ($closed_med as $opco) {
            $total_med_risk = $total_med_risk + $opco;
        }
        array_push($closed_med, $total_med_risk);

        $total_high_risk = 0;
        foreach ($closed_high as $opco) {
            $total_high_risk = $total_high_risk + $opco;
        }
        array_push($closed_high, $total_high_risk);

        $total_crit_risk = 0;
        foreach ($closed_crit as $opco) {
            $total_crit_risk = $total_crit_risk + $opco;
        }
        array_push($closed_crit, $total_crit_risk);

        // crit & high where status is open
        foreach ($opco_array as $opco) {
            $high_crit_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->orWhere('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))
                ->where('opco_id', (int)$opco->id)
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($open_high_crit, $high_crit_risk);
        }

        $total_high_crit_risk = 0;
        foreach ($open_high_crit as $opco) {
            $total_high_crit_risk = $total_high_crit_risk + $opco;
        }
        array_push($open_high_crit, $total_high_crit_risk);

        return view('Remreports.total_open_closed')
            ->with('opco_array', $opco_array)
            ->with('opco_only_array', $opco_only_array)
            ->with('total_open', $total_open)
            ->with('total_closed', $total_closed)
            ->with('total_all', $total_all)
            ->with('open_low', $open_low)
            ->with('open_med', $open_med)
            ->with('open_high', $open_high)
            ->with('open_crit', $open_crit)
            ->with('open_high_crit', $open_high_crit)
            ->with('closed_low', $closed_low)
            ->with('closed_med', $closed_med)
            ->with('closed_high', $closed_high)
            ->with('closed_crit', $closed_crit);
    }

    // Returns the weekly stats view
    public function weekly_stats(Request $request)
    {
        return view('Remreports.weekly_stats');
    }

    // Returns the total number of open critical and high findings
    public function getTotalOfCritHigh(Request $request)
    {
        // retrieve tickets with critical and high level of risk and display 
        $newFromDate = new UTCDateTime(strtotime($request->from1)*1000);
        $newToDate = new UTCDateTime(strtotime($request->to1)*1000);

        $result = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))
            ->where('reported_date','>=',$newFromDate)
            ->where('reported_date','<=',$newToDate)
            ->whereNotNull ('hod_signoff_date')
            ->get();

        $crit_high_array = [];

        foreach($result as $res){
            if($res['risk'] == (int)Config::get('enums.severity_status.Critical') || $res['risk'] == (int)Config::get('enums.severity_status.High'))
                array_push($crit_high_array, $res['risk']);
        }

        $countTotalOfCritHigh = count($crit_high_array);

        return view('Remreports.weekly_stats')
            ->with('countTotalOfCritHigh', $countTotalOfCritHigh);
    }

    // Return the total findings grouping by closed findings by selected opco
    public function getTotalFindingsGroupByClosedAndOpCo(Request $request)
    {
        // get tickets that are closed and classify by opco
        $newFromDate = new UTCDateTime(strtotime($request->from2)*1000);
        $newToDate = new UTCDateTime(strtotime($request->to2)*1000);

        $opco_array = DB::connection('mysql')->table('opco')->where('status', Config::get('enums.opco_status.Primary'))->get();
        $closed_low = [];
        $closed_med = [];
        $closed_high = [];
        $closed_crit = [];

        foreach ($opco_array as $opco) {
            $low_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Close'))
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->where('opco_id', (int)$opco->id)
                ->where('rem_pmo_closure_date','>=',$newFromDate)
                ->where('rem_pmo_closure_date','<=',$newToDate)
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_low, $low_risk);

            $med_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Close'))
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->where('opco_id', (int)$opco->id)
                ->where('rem_pmo_closure_date','>=',$newFromDate)
                ->where('rem_pmo_closure_date','<=',$newToDate)
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_med, $med_risk);

            $high_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Close'))
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco->id)
                ->where('rem_pmo_closure_date','>=',$newFromDate)
                ->where('rem_pmo_closure_date','<=',$newToDate)
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_high, $high_risk);

            $crit_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Close'))
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco->id)
                ->where('rem_pmo_closure_date','>=',$newFromDate)
                ->where('rem_pmo_closure_date','<=',$newToDate)
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_crit, $crit_risk);
        }

        $total_low_risk = 0;
        foreach ($closed_low as $opco) {
            $total_low_risk = $total_low_risk + $opco;
        }
        array_push($closed_low, $total_low_risk);

        $total_med_risk = 0;
        foreach ($closed_med as $opco) {
            $total_med_risk = $total_med_risk + $opco;
        }
        array_push($closed_med, $total_med_risk);

        $total_high_risk = 0;
        foreach ($closed_high as $opco) {
            $total_high_risk = $total_high_risk + $opco;
        }
        array_push($closed_high, $total_high_risk);

        $total_crit_risk = 0;
        foreach ($closed_crit as $opco) {
            $total_crit_risk = $total_crit_risk + $opco;
        }
        array_push($closed_crit, $total_crit_risk);

        return view('Remreports.weekly_stats')
            ->with('opco_array', $opco_array)
            ->with('closed_low', $closed_low)
            ->with('closed_med', $closed_med)
            ->with('closed_high', $closed_high)
            ->with('closed_crit', $closed_crit);
    }

    // Return the total findings grouping by revalidated findings of all opco
    public function getTotalFindingsGroupByRevalAndOpCo(Request $request)
    {

        $newFromDate = new UTCDateTime(strtotime($request->from3)*1000);
        $newToDate = new UTCDateTime(strtotime($request->to3)*1000);

        $opco_array = DB::connection('mysql')->table('opco')->where('status', Config::get('enums.opco_status.Primary'))->get();
        $closed_low = [];
        $closed_med = [];
        $closed_high = [];
        $closed_crit = [];

        foreach ($opco_array as $opco) {
            $low_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('scope', '=', 'IDP')
                ->where('status', 'REVALIDATION')
                ->where('risk_rating', 'Low')
                ->where('opco', $opco)
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_low, $low_risk);

            $med_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('scope', '=', 'IDP')
                ->where('status', 'REVALIDATION')
                ->where('risk_rating', 'Medium')
                ->where('opco', $opco)
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_med, $med_risk);

            $high_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('scope', '=', 'IDP')
                ->where('status', 'REVALIDATION')
                ->where('risk_rating', 'High')
                ->where('opco', $opco)
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_high, $high_risk);

            $crit_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('scope', '=', 'IDP')
                ->where('status', 'REVALIDATION')
                ->where('risk_rating', 'Critical')
                ->where('opco', $opco)
                ->whereNotNull ('hod_signoff_date')
                ->count();

            array_push($closed_crit, $crit_risk);
        }

        return view('Remreports.weekly_stats')
            ->with('closed_low2', $closed_low)
            ->with('closed_med2', $closed_med)
            ->with('closed_high2', $closed_high)
            ->with('closed_crit2', $closed_crit);
    }

    // Return open findings by severity of all opco by selected date range
    public function getOpenbyOpCo(Request $request)
    {

        $newFromDate = new UTCDateTime(strtotime($request->from4)*1000);
        $newToDate = new UTCDateTime(strtotime($request->to4)*1000);

        $opco_array = DB::connection('mysql')->table('opco')->where('status', Config::get('enums.opco_status.Primary'))->get();

        $opened_low = [];
        $opened_med = [];
        $opened_high = [];
        $opened_crit = [];

        foreach ($opco_array as $opco) {
            $low_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))
                ->where('risk', (int)Config::get('enums.severity_status.Low'))
                ->where('opco_id', (int)$opco->id)
                ->whereNotNull ('hod_signoff_date')
                ->where('hod_signoff_date','>=',$newFromDate)
                ->where('hod_signoff_date','<=',$newToDate)
                ->count();

            array_push($opened_low, $low_risk);

            $med_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))
                ->where('risk', (int)Config::get('enums.severity_status.Medium'))
                ->where('opco_id', (int)$opco->id)
                ->whereNotNull ('hod_signoff_date')
                ->where('hod_signoff_date','>=',$newFromDate)
                ->where('hod_signoff_date','<=',$newToDate)
                ->count();

            array_push($opened_med, $med_risk);

            $high_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))
                ->where('risk', (int)Config::get('enums.severity_status.High'))
                ->where('opco_id', (int)$opco->id)
                ->whereNotNull ('hod_signoff_date')
                ->where('hod_signoff_date','>=',$newFromDate)
                ->where('hod_signoff_date','<=',$newToDate)
                ->count();

            array_push($opened_high, $high_risk);

            $crit_risk = DB::connection('mongodb')->collection('scan_results')
                ->where('rem_pmo_closure_status', (int)Config::get('enums.mdb_stream_status.Open'))
                ->where('risk', (int)Config::get('enums.severity_status.Critical'))
                ->where('opco_id', (int)$opco->id)
                ->whereNotNull ('hod_signoff_date')
                ->where('hod_signoff_date','>=',$newFromDate)
                ->where('hod_signoff_date','<=',$newToDate)
                ->count();

            array_push($opened_crit, $crit_risk);
        }

        $total_low_risk = 0;
        foreach ($opened_low as $opco) {
            $total_low_risk = $total_low_risk + $opco;
        }
        array_push($opened_low, $total_low_risk);

        $total_med_risk = 0;
        foreach ($opened_med as $opco) {
            $total_med_risk = $total_med_risk + $opco;
        }
        array_push($opened_med, $total_med_risk);

        $total_high_risk = 0;
        foreach ($opened_high as $opco) {
            $total_high_risk = $total_high_risk + $opco;
        }
        array_push($opened_high, $total_high_risk);

        $total_crit_risk = 0;
        foreach ($opened_crit as $opco) {
            $total_crit_risk = $total_crit_risk + $opco;
        }
        array_push($opened_crit, $total_crit_risk);

        return view('Remreports.weekly_stats')
            ->with('opco_array', $opco_array)
            ->with('opened_low', $opened_low)
            ->with('opened_med', $opened_med)
            ->with('opened_high', $opened_high)
            ->with('opened_crit', $opened_crit);
    }

    public function leaderboard() {
        $users = DB::table('users')
            ->where('status', 1)->get()->toArray();

        $pmo_array = [];
        $pmo_close_array = [];
        $tester_array=[];
        $tester_close_array = [];
        $analyst_array=[];
        $analyst_close_array = [];
        $qa_array=[];
        $qa_close_array = [];
        $hod_array=[];
        $hod_close_array = [];

        foreach($users as $user){
            $pmo = DB::table('streams')
                ->where('pmo_id', $user->id)
                ->where('status', 1)->get()->toArray();

            if(count($pmo) > 0){
                $pmo['name'] = $user->name;
                array_push($pmo_array, $pmo);
            }
        }

        foreach($users as $user){
            $pmo_close = DB::table('streams')
                ->where('pmo_id', $user->id)
                ->where('status', [2,11])->get()->toArray();

            if(count($pmo_close) > 0){
                $pmo_close['name'] = $user->name;
                array_push($pmo_close_array, $pmo_close);
            }
        }

        foreach($users as $user){
            $tester = DB::table('streams')
                ->where('tester_id', $user->id)
                ->where('status', 2)->get()->toArray();

            if(count($tester) > 0){
                $tester['name'] = $user->name;
                array_push($tester_array, $tester);
            }
        }

        foreach($users as $user){
            $tester_close = DB::table('streams')
                ->where('tester_id', $user->id)
                ->where('status', [3,11])->get()->toArray();

            if(count($tester_close) > 0){
                $tester_close['name'] = $user->name;
                array_push($tester_close_array, $tester_close);
            }
        }

        foreach($users as $user){
            $analyst = DB::table('streams')
                ->where('analyst_id', $user->id)
                ->where('status', 3)->get()->toArray();

            if(count($analyst) > 0){
                $analyst['name'] = $user->name;
                array_push($analyst_array, $analyst);
            }
        }

        foreach($users as $user){
            $analyst_close = DB::table('streams')
                ->where('analyst_id', $user->id)
                ->where('status', [4,11])->get()->toArray();

            if(count($analyst_close) > 0){
                $analyst_close['name'] = $user->name;
                array_push($analyst_close_array, $analyst_close);
            }
        }

        foreach($users as $user){
            $qa = DB::table('streams')
                ->where('qa_id', $user->id)
                ->where('status', 4)->get()->toArray();

            if(count($qa) > 0){
                $qa['name'] = $user->name;
                array_push($qa_array, $qa);
            }
        }

        foreach($users as $user){
            $qa_close = DB::table('streams')
                ->where('qa_id', $user->id)
                ->where('status', [5,11])->get()->toArray();

            if(count($qa_close) > 0){
                $qa_close['name'] = $user->name;
                array_push($qa_close_array, $qa_close);
            }
        }

        foreach($users as $user){
            $hod = DB::table('streams')
                ->where('hod_id', $user->id)
                ->where('status', 5)->get()->toArray();

            if(count($hod) > 0){
                $hod['name'] = $user->name;
                array_push($hod_array, $hod);
            }
        }

        foreach($users as $user){
            $hod_close = DB::table('streams')
                ->where('hod_id', $user->id)
                ->where('status', [6,11])->get()->toArray();

            if(count($hod_close) > 0){
                $hod_close['name'] = $user->name;
                array_push($hod_close_array, $hod_close);
            }
        }

        $task = DB::table('streams')->where('status', 1)->get();
        $task2 = DB::table('streams')->where('status', 2)->get();
        $task3 = DB::table('streams')->where('status', 3)->get();
        $task4 = DB::table('streams')->where('status', 4)->get();
        $task5 = DB::table('streams')->where('status', 5)->get();
        $task6 = DB::table('streams')->whereBetween('status', [6,11])->get();
        $task7 = DB::connection('mongodb')->collection('scan_results')->distinct('stream_id')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('risk', '!=', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->get()->toArray();
        $task8 = DB::connection('mongodb')->collection('scan_results')->distinct('stream_id')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('risk', '!=', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->get()->toArray();
        $task9 = DB::connection('mongodb')->collection('scan_results')->distinct('stream_id')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 1)->where('risk', '!=', 1)->where('rem_pmo_closure_status', 1)->where('false_positive', 0)->get()->toArray();

        return view('Remreports.leaderboard', compact('pmo_array','pmo_close_array','tester_array','tester_close_array','analyst_array','analyst_close_array','qa_array','qa_close_array','hod_array','hod_close_array','task','task2','task3','task4','task5','task6','task7','task8','task9'));
    }
}
