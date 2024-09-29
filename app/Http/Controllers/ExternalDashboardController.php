<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stream;
use DB;
use App;
use Response;
use ScanResults;
use Config;
use Carbon\Carbon;

class ExternalDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // retrieve information from db
        $opco_array = DB::connection('mysql')->table('opco')->where('status', Config::get('enums.opco_status.Primary'))->get()->toArray();
        $updated_date = DB::connection('mongodb')->collection('external_db_num_of_hosts_by_cat')->select("updated_at")
            ->whereNotNull('updated_at')
            ->first();
        
        $timeTask = Carbon::parse(date('Y-m-d H:i:s', $updated_date['updated_at']->toDateTime()->format('U')));
        $Currentdate = Carbon::now();
        $totalTime = $Currentdate->diffInMinutes($timeTask);

        $aging_and_kpi = DB::connection('mongodb')->collection('external_db_aging_and_kpi')->get();
        $num_of_hosts_by_cat = DB::connection('mongodb')->collection('external_db_num_of_hosts_by_cat')->get();
        $num_of_hosts_by_vuln_name = DB::connection('mongodb')->collection('external_db_num_of_hosts_by_vuln_name')->get();
        $open = DB::connection('mongodb')->collection('external_db_open')->get();
        $open_close_findings = DB::connection('mongodb')->collection('external_db_open_close_findings')->get();
        $vuln_exposure_past_four_months = DB::connection('mongodb')->collection('external_db_vuln_exposure_past_four_months')->get();
        $opco_findings = DB::connection('mongodb')->collection('external_db_opco_findings')->get();
        $risk_levels = DB::connection('mongodb')->collection('external_db_open_close_findings')->get();

        $w1 = Carbon::now()->subWeek(8);
        $w2 = Carbon::now()->subWeeks(7);
        $w3 = Carbon::now()->subWeeks(6);
        $w4 = Carbon::now()->subWeeks(5);
        $w5 = Carbon::now()->subWeeks(4);
        $w6 = Carbon::now()->subWeeks(3);
        $w7 = Carbon::now()->subWeeks(2);
        $w8 = Carbon::now()->subWeek(1);
        $w9 = Carbon::now();

        $past_vuln_exposure_open = $vuln_exposure_past_four_months[0]['open_array'];
        $past_vuln_exposure_close = $vuln_exposure_past_four_months[0]['close_array'];

        return view('dashboards.external_dashboard', compact(
            'aging_and_kpi', 'num_of_hosts_by_cat',
            'num_of_hosts_by_vuln_name', 'open', 'open_close_findings', 'vuln_exposure_past_four_months',
            'opco_findings', 'risk_levels', 'opco_id', 'totalTime', 'opco_array',
            'w1', 'w2', 'w3', 'w4', 'w5', 'w6', 'w7', 'w8', 'w9', 'open', 'close', 'past_vuln_exposure_open', 'past_vuln_exposure_close'
        ));
    }

    public function externalOpcoDashboard(Request $request, $opcoid)
    {
        $opco_enums = array_flip(Config::get('enums.opco_switch'));
        $opco = $opco_enums[$opcoid];
        $opco_array = DB::connection('mysql')->table('opco')->where('status', Config::get('enums.opco_status.Primary'))->get()->toArray();
        $updated_date = $critical_open = DB::connection('mongodb')->collection('external_db_num_of_hosts_by_cat')->select("updated_at")
            ->whereNotNull('updated_at')
            ->first();

        $timeTask = Carbon::parse(date('Y-m-d H:i:s', $updated_date['updated_at']->toDateTime()->format('U')));
        $Currentdate = Carbon::now();
        $totalTime = $Currentdate->diffInMinutes($timeTask);

        $aging_and_kpi = DB::connection('mongodb')->collection('external_db_aging_and_kpi')->get();
        $num_of_hosts_by_cat = DB::connection('mongodb')->collection('external_db_num_of_hosts_by_cat')->get();
        $num_of_hosts_by_vuln_name = DB::connection('mongodb')->collection('external_db_num_of_hosts_by_vuln_name')->get();
        $open = DB::connection('mongodb')->collection('external_db_open')->get();
        $open_close_findings = DB::connection('mongodb')->collection('external_db_open_close_findings')->get();
        $vuln_exposure_past_four_months = DB::connection('mongodb')->collection('external_db_vuln_exposure_past_four_months')->get();
        $opco_findings = DB::connection('mongodb')->collection('external_db_opco_findings')->get();
        $risk_levels = DB::connection('mongodb')->collection('external_db_open_close_findings')->get();

        return view('dashboards.external_opco_dashboard', compact('aging_and_kpi', 'num_of_hosts_by_cat',
            'num_of_hosts_by_vuln_name', 'open', 'open_close_findings', 'vuln_exposure_past_four_months', 'opco_findings', 'risk_levels', 'opco', 'totalTime', 'opco_array'));
    }
}
