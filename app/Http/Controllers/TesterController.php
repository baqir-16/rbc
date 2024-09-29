<?php

namespace App\Http\Controllers;

use App\Stream;
use App\Ticket;
use App\ScanResults;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Artisan;
use Input;
use App;
use Response;
use File;
use Excel;
use DB;
use SnappyPDF;
use Config;
use MongoDB\BSON\UTCDateTime;
use DateTime;


// Main controller for Tester functions
class TesterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // Returns all tickets assigned to the Tester
    public function index()
    {
        $where = ['status' => Config::get('enums.stream_status.Tester'), 'tester_id' => Auth::user()->id];
        $streams = Stream::with('comments', 'opco', 'modules', 'user', 'tickets')->where($where)->paginate(10);
        $status_arr = [];
        $no_file_status_arr = [];

        foreach($streams as $stream){
            $result = DB::connection('mongodb')->collection('scan_results')->where('stream_id', $stream->id)->get()->first();

            if(isset($result['stream_id']))
                array_push($status_arr, '1');
            else
                array_push($status_arr, '0');
        }

        foreach($streams as $stream){
            $result = Stream::where('id', $stream->id)
                ->where('tester_no_file_upload', Config::get('enums.active_status.Active'))->first();

            if(isset($result))
                array_push($no_file_status_arr, '1');
            else
                array_push($no_file_status_arr, '0');
        }

        return view('Tester.index')
            ->with('streams', $streams)
            ->with('no_file_status_arr', $no_file_status_arr)
            ->with('status_arr', $status_arr);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // Return view of the ticket selected
    public function create(Request $request)
    {
        $stream = Stream::where('id', $request->id)->first();
        return view('Tester.edit')->with('stream', $stream);
    }

    // TO remove all whitespaces in text, front and back
    public function removeAllWhitespaces($str)
    {
        $str = trim($str);
        $str = preg_replace("/\s+/", "", $str);
        return $str;
    }

    // To remove whitespaces in text
    public function removeNameWhitespaces($str)
    {
        $str = trim($str);
        $str = preg_replace("/\s+/", " ", $str);
        return $str;
    }

    // Add https in front of URL if there is none
    public function addhttp($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }
        return $url;
    }

    // REmove https in front of URL
    public function removehttp($url)
    {
        $url = preg_replace('#^https?://#', '', $url);
        return $url;
    }

    // Remove right slash in URL
    public function removeURLSlashRightSide($url)
    {
        $url = rtrim($url,'/');
        return $url;
    }

    // Mark ticket with no finding upload from the Tester, by enabling this the Tester can forward 0 finding ticket to the Analyst
    public function noNewFinding($streamID)
    {
        DB::table('streams')
            ->where('id', $streamID)
            ->update(['no_findings' => Config::get('enums.active_status.Active')]);
    }

    public function getURLScheme($url)
    {
//        $url = parse_url($url);
//        $scheme = parse_url($url, PHP_URL_SCHEME);
//
//        if(count($scheme) == 0)
//            $scheme = 'https://';
//        else
//            $scheme = $scheme . '://';

        return 'https://';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // Store findings uploaded by the Tester
    public function store(Request $request)
    {   
        $newFinding = false;
        $stream = Stream::where('id', $request->id)->first();
        $ticket = Ticket::where('id', $stream->ticket_id)->first();
        if (Input::hasFile('file') && !isset($request->nofile)) {
            foreach(Input::file('file') as $file) {
                $path = $file->getRealPath();
                $string = file_get_contents($path);
                $xml = new \SimpleXMLElement($string);

                if (!empty($xml) && $xml->count()) {

                    foreach ($xml->Report->ReportHost as $ReportItem) {

                        foreach ($ReportItem->ReportItem as $ReportItem1) {


                            switch ($ReportItem1->risk_factor) {
                                case "None":
                                    $ReportItem1->risk_factor = Config::get('enums.severity_status.Informational');
                                    break;
                                case "Low":
                                    $ReportItem1->risk_factor = Config::get('enums.severity_status.Low');
                                    break;
                                case "Medium":
                                    $ReportItem1->risk_factor = Config::get('enums.severity_status.Medium');
                                    break;
                                case "High":
                                    $ReportItem1->risk_factor = Config::get('enums.severity_status.High');
                                    break;
                                case "Critical":
                                    $ReportItem1->risk_factor = Config::get('enums.severity_status.Critical');
                                    break;
                            }

                            $duplicate_check = DB::connection('mongodb')->collection('scan_results')
                                ->where('name', '=', $ReportItem1->plugin_name . '  ')
                                ->where('host', '=', $ReportItem['name'] . ' ')
                                ->where('port', '=', $ReportItem1->required_port . '  ')
                                ->where('rem_pmo_closure_status', '=', 0)
                                ->count();

                            if ($duplicate_check == 0) {

                                DB::beginTransaction();

                                try {
                                    DB::connection('mongodb')->collection('scan_results')->insert([
                                        'stream_id' => (int)$request->id,
                                        'opco_id' => (int)$ticket->opco_id,
                                        'department' => (int)$ticket->department,
                                        'module_id' => (int)$stream->module_id,
                                        'analyst_id' => 0,
                                        'qa_id' => 0,
                                        'rem_officer_id' => 0,
                                        'rem_pmo_id' => 0,
                                        'name' => $ReportItem1->plugin_name . '  ',
                                        'host' => $ReportItem['name'] . ' ',
                                        'port' => $ReportItem1->required_port . '  ',
                                        'cvss' => $ReportItem1->cvss_base_score . '  ',
                                        'risk' => (int)$ReportItem1->risk_factor,
                                        'description' => $ReportItem1->description . '  ',
                                        'solution' => $ReportItem1->solution . '  ',
                                        'plugin_output' => $ReportItem1->plugin_output . '  ',
                                        'false_positive' => 0,
                                        'img_filename' => null,
                                        'is_validated' => 0,
                                        'validation_date' => 0,
                                        'is_verified' => 0,
                                        'verification_date' => 0,
                                        'revalidate' => 0,
                                        'revalidation_date' => 0,
                                        'reverify' => 0,
                                        'reverification_date' => 0,
                                        'rem_officer_rem_status' => 0,
                                        'rem_officer_rem_date' => 0,
                                        'rem_officer_img_filename' => null,
                                        'rem_pmo_closure_status' => 0,
                                        'rem_pmo_closure_date' => 0,
                                        'category' => 2,
                                        'assign_user' => null,
                                        'reported_date' => new UTCDateTime(strtotime(date("m/d/Y")) * 1000),
                                        'created_at' => new UTCDateTime(strtotime(date("Y/m/d")) * 1000),
                                        'updated_at' => new UTCDateTime(strtotime(date("Y/m/d")) * 1000),
                                    ]);
                                    DB::commit();
                                    $newFinding = true;
                                } catch (Exception $e) {
                                    DB::rollback();
                                }
                            } else if ($duplicate_check > 0) {
                                $res = DB::connection('mongodb')->collection('scan_results')
                                    ->where('name', '=', $ReportItem1->plugin_name . '  ')
                                    ->where('host', '=', $ReportItem['name'] . ' ')
                                    ->where('port', '=', $ReportItem1->required_port . '  ')
                                    ->where('rem_pmo_closure_status', '=', 0)
                                    ->orderBy('_id', 'desc')
                                    ->first();

                                if ($res['stream_id'] != $request->id) {
                                    DB::beginTransaction();

                                    try {
                                        DB::connection('mongodb')->collection('scan_results')
                                            ->where('_id', $res['_id'])
                                            ->update([
                                                'recurrence' => new UTCDateTime(strtotime(date("Y/m/d")) * 1000),
                                                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d")) * 1000)
                                            ]);

                                        DB::commit();
                                    } catch (Exception $e) {
                                        DB::rollback();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        DB::beginTransaction();

        try {
            if(isset($request->comment)) {
                $comment_id = DB::table('comments')->insertGetId([
                    'user_id' => Auth::user()->id,
                    'stream_id' => $request->id,
                    'comments' => $request->comment,
                    'status' => 1,
                ]);

                DB::table('streams')
                    ->where('id', $request->id)
                    ->update(['tester_comments_id' => $comment_id]);
            }

            $datetime = strtotime($request->datetime);
            DB::table('streams')
                ->where('id', $request->id)
                ->update([
                    'tester_scheduled_date' => date('Y-m-d H:i:s', $datetime)
                ]);

            if(isset($request->nofile)){
                DB::table('streams')
                    ->where('id', $request->id)
                    ->update([
                        'tester_no_file_upload' => Config::get('enums.active_status.Active')
                    ]);
            }else{
                DB::table('streams')
                    ->where('id', $request->id)
                    ->update([
                        'tester_no_file_upload' => Config::get('enums.active_status.Inactive')
                    ]);
            }

            if(!$newFinding)
                $this->noNewFinding($request->id);

            DB::commit();
            $request->session()->flash('alert-success', 'Successful!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'NOT successful!');
        }
        return redirect()->action('TesterController@index');
    }

    public function storeXML(Request $request)
    {
        $newFinding = false;
        $stream = Stream::where('id', $request->id)->first();
        $ticket = Ticket::where('id', $stream->ticket_id)->first();

        if (Input::hasFile('file') && !isset($request->nofile)) {

            foreach(Input::file('file') as $file) {
                $path = $file->getRealPath();
                $string = file_get_contents($path);
                $xml = new \SimpleXMLElement($string);
                $count = 0;

                if (!empty($xml) && $xml->count()) {
                    foreach ($xml->Scan->ReportItems->ReportItem as $ReportItem) {

                        switch ($ReportItem->Severity) {
                            case "informational":
                                $ReportItem->Severity = Config::get('enums.severity_status.Informational');
                                break;
                            case "low":
                                $ReportItem->Severity = Config::get('enums.severity_status.Low');
                                break;
                            case "medium":
                                $ReportItem->Severity = Config::get('enums.severity_status.Medium');
                                break;
                            case "high":
                                $ReportItem->Severity = Config::get('enums.severity_status.High');
                                break;
                            case "critical":
                                $ReportItem->Severity = Config::get('enums.severity_status.Critical');
                                break;
                        }

                        if($count == 0) {
                            $ReportItem->Name = $this->removeNameWhitespaces($ReportItem->Name);
                            $url_scheme = $this->getURLScheme($xml->Scan->StartURL);
                            $xml->Scan->StartURL = $this->removehttp($xml->Scan->StartURL);
                            $xml->Scan->StartURL = $this->removeAllWhitespaces($xml->Scan->StartURL);
                            $xml->Scan->StartURL = $this->removeURLSlashRightSide($xml->Scan->StartURL);
                            $xml->Scan->StartURL = urlencode($xml->Scan->StartURL);
                            $count++;
                        }

                        $duplicate_check = DB::connection('mongodb')->collection('scan_results')
                            ->where('name', '=', $ReportItem->Name . '  ')
                            ->where('host', '=', $xml->Scan->StartURL . ' ')
                            ->where('Affects', '=', $ReportItem->Affects . '  ')
                            ->where('rem_pmo_closure_status', '=', 0)
                            ->count();

                        if ($duplicate_check == 0) {
                            $url = $xml->Scan->StartURL;
                            $laststring = substr(strrchr($url,'/'), 1);
                            $alpha = preg_replace('/[^A-Za-z0-9\-]/', '', $laststring);
                            $strlength = strlen($laststring) - strlen($alpha);

                            if($strlength != 0)
                                $xml->Scan->StartURL = substr($url, 0, - $strlength);

                            DB::beginTransaction();

                            try {
                                DB::connection('mongodb')->collection('scan_results')->insert([
                                    'stream_id' => (int)$request->id,
                                    'opco_id' => (int)$ticket->opco_id,
                                    'department' => (int)$ticket->department,
                                    'module_id' => (int)$stream->module_id,
                                    'analyst_id' => 0,
                                    'qa_id' => 0,
                                    'rem_officer_id' => 0,
                                    'rem_pmo_id' => 0,
                                    'name' => $ReportItem->Name . '  ',
                                    'ModuleName' => $ReportItem->ModuleName . '  ',
                                    'url_scheme' => $url_scheme,
                                    'host' => $xml->Scan->StartURL . ' ',
                                    'Details' => $ReportItem->Details . '  ',
                                    'Affects' => $ReportItem->Affects . '  ',
                                    'Parameter' => $ReportItem->Parameter . '  ',
                                    'AOP_SourceFile' => $ReportItem->AOP_SourceFile . '  ',
                                    'AOP_Additional' => $ReportItem->AOP_Additional . '  ',
                                    'IsFalsePositive' => $ReportItem->IsFalsePositive . '  ',
                                    'risk' => (int)$ReportItem->Severity,
                                    'Type' => $ReportItem->Type . '  ',
                                    'Impact' => $ReportItem->Impact . '  ',
                                    'Description' => $ReportItem->Description . '  ',
                                    'Recommendation' => $ReportItem->Recommendation . '  ',
                                    'Request' => $ReportItem->TechnicalDetails->Request . '  ',
                                    'Response' => $ReportItem->TechnicalDetails->Response . '  ',
                                    'CWE' => $ReportItem->CWEList->CWE . '  ',
                                    'Descriptor' => $ReportItem->CVSS->Descriptor . '  ',
                                    'Score' => $ReportItem->CVSS->Score . '  ',
                                    'AV' => $ReportItem->CVSS->AV . '  ',
                                    'AC' => $ReportItem->CVSS->AC . '  ',
                                    'AU' => $ReportItem->CVSS->AU . '  ',
                                    'C' => $ReportItem->CVSS->C . '  ',
                                    'I' => $ReportItem->CVSS->I . '  ',
                                    'A' => $ReportItem->CVSS->A . '  ',
                                    'E' => $ReportItem->CVSS->E . '  ',
                                    'RL' => $ReportItem->CVSS->RL . '  ',
                                    'RC' => $ReportItem->CVSS->RC . '  ',
                                    'Descriptor_CVSS3' => $ReportItem->CVSS3->Descriptor . '  ',
                                    'Score_CVSS3' => $ReportItem->CVSS3->Score . '  ',
                                    'TempScore' => $ReportItem->CVSS3->TempScore . '  ',
                                    'EnvScore' => $ReportItem->CVSS3->EnvScore . '  ',
                                    'AV_CVSS3' => $ReportItem->CVSS3->AV . '  ',
                                    'AC_CVSS3' => $ReportItem->CVSS3->AC . '  ',
                                    'PR' => $ReportItem->CVSS3->PR . '  ',
                                    'UI' => $ReportItem->CVSS3->UI . '  ',
                                    'S' => $ReportItem->CVSS3->S . '  ',
                                    'C_CVSS3' => $ReportItem->CVSS3->C . '  ',
                                    'I_CVSS3' => $ReportItem->CVSS3->I . '  ',
                                    'A_CVSS3' => $ReportItem->CVSS3->A . '  ',
                                    'E_CVSS3' => $ReportItem->CVSS3->E . '  ',
                                    'RL_CVSS3' => $ReportItem->CVSS3->RL . '  ',
                                    'RC_CVSS3' => $ReportItem->CVSS3->RC . '  ',
                                    'false_positive' => 0,
                                    'img_filename' => null,
                                    'is_validated' => 0,
                                    'validation_date' => 0,
                                    'is_verified' => 0,
                                    'verification_date' => 0,
                                    'revalidate' => 0,
                                    'revalidation_date' => 0,
                                    'reverify' => 0,
                                    'reverification_date' => 0,
                                    'rem_officer_rem_status' => 0,
                                    'rem_officer_rem_date' => 0,
                                    'rem_officer_img_filename' => null,
                                    'rem_pmo_closure_status' => 0,
                                    'rem_pmo_closure_date' => 0,
                                    'category' => 2,
                                    'reported_date' => new UTCDateTime(strtotime(date("m/d/Y"))*1000),
                                    'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                    'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                ]);
                                DB::commit();
                                $newFinding = true;
                            } catch (Exception $e) {
                                DB::rollback();
                            }
                        }else if($duplicate_check > 0){
                            $res = DB::connection('mongodb')->collection('scan_results')
                                ->where('name', '=', $ReportItem->Name . '  ')
                                ->where('host', '=', $xml->Scan->StartURL . ' ')
                                ->where('Affects', '=', $ReportItem->Affects . '  ')
                                ->where('rem_pmo_closure_status', '=', 0)
                                ->orderBy('_id', 'desc')
                                ->first();

                            if($res['stream_id'] != $request->id){
                                DB::beginTransaction();

                                try {
                                    DB::connection('mongodb')->collection('scan_results')
                                        ->where('_id', $res['_id'])
                                        ->update([
                                            'recurrence' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                            'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000)
                                        ]);

                                    DB::commit();
                                } catch (Exception $e) {
                                    DB::rollback();
                                }
                            }
                        }
                    }
                }
            }
        }

        DB::beginTransaction();

        try {
            if(isset($request->comment)) {
                $comment_id = DB::table('comments')->insertGetId([
                    'user_id' => Auth::user()->id,
                    'stream_id' => $request->id,
                    'comments' => $request->comment,
                    'status' => 1,
                ]);

                DB::table('streams')
                    ->where('id', $request->id)
                    ->update(['tester_comments_id' => $comment_id]);
            }

            $datetime = strtotime($request->datetime);
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['tester_scheduled_date' => date('Y-m-d H:i:s', $datetime)]);

            if(isset($request->nofile)){
                DB::table('streams')
                    ->where('id', $request->id)
                    ->update([
                        'tester_no_file_upload' => Config::get('enums.active_status.Active')
                    ]);
            }else{
                DB::table('streams')
                    ->where('id', $request->id)
                    ->update([
                        'tester_no_file_upload' => Config::get('enums.active_status.Inactive')
                    ]);
            }

            if(!$newFinding)
                $this->noNewFinding($request->id);

            DB::commit();
            $request->session()->flash('alert-success', 'Successful!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'NOT successful!');
        }
        return redirect()->action('TesterController@index');
    }

    public function storeNexpose(Request $request)
    {
        $newFinding = false;
        $stream = Stream::where('id', $request->id)->first();
        $ticket = Ticket::where('id', $stream->ticket_id)->first();

        if (Input::hasFile('file') && !isset($request->nofile)) {
            $path = Input::file('file')->getRealPath();

            $data = Excel::load($path, function ($reader) {
            })->get();

            if(!empty($data) && $data->count()) {
                if (isset($data[0]['original_severity']) OR isset($data[0]['title']) OR isset($data[0]['ip_address'])) {

                    foreach ($data as $key => $value) {
                        switch ($value->original_severity) {
                            case "None":
                                $value->original_severity = Config::get('enums.severity_status.Informational');
                                break;
                            case "Low":
                                $value->original_severity = Config::get('enums.severity_status.Low');
                                break;
                            case "Medium":
                                $value->original_severity = Config::get('enums.severity_status.Medium');
                                break;
                            case "High":
                                $value->original_severity = Config::get('enums.severity_status.High');
                                break;
                            case "Critical":
                                $value->original_severity = Config::get('enums.severity_status.Critical');
                                break;
                        }

                        if ($value->original_severity == null) {
                            if ($value->original_cvss_score == 0.0) {
                                $value->original_severity = 1;
                            }

                            if ($value->original_cvss_score > 0.0 and $value->original_cvss_score < 4.0) {
                                $value->original_severity = 2;
                            }

                            if ($value->original_cvss_score > 3.9 and $value->original_cvss_score < 7.0) {
                                $value->original_severity = 3;
                            }

                            if ($value->original_cvss_score > 6.9 and $value->original_cvss_score < 9.0) {
                                $value->original_severity = 4;
                            }

                            if ($value->original_cvss_score > 8.9) {
                                $value->original_severity = 5;
                            }
                        }

                        $duplicate_check = DB::connection('mongodb')->collection('scan_results')
                            ->where('name', '=', $value->title)
                            ->where('host', '=', $value->ip_address)
                            ->where('port', '=', (int)$value->port)
                            ->where('rem_pmo_closure_status', '=', 0)
                            ->count();

                        if ($duplicate_check == 0) {
                            DB::beginTransaction();

                            try {
                                DB::connection('mongodb')->collection('scan_results')->insert([
                                    'stream_id' => (int)$request->id,
                                    'module_id' => (int)$stream->module_id,
                                    'opco_id' => (int)$ticket->opco_id,
                                    'department' => (int)$ticket->department,
                                    'analyst_id' => 0,
                                    'qa_id' => 0,
                                    'rem_pmo_id' => 0,
                                    'module' => 'nexpose',
                                    'host' => $value->ip_address,
                                    'host_name' => $value->host_name,
                                    'OS' => $value->os,
                                    'OS_version' => $value->os_version,
                                    'name' => $value->title,
                                    'description' => $value->description,
                                    'fix' => $value->fix,
                                    'summary' => $value->summary,
                                    'original_CV' => $value->original_cvss_score,
                                    'Vulnerability' => $value->vulnerability,
                                    'risk' => (int)$value->original_severity,
                                    'port' => (int)$value->port,
                                    'false_positive' => 0,
                                    'img_filename' => null,
                                    'is_validated' => 0,
                                    'validation_date' => 0,
                                    'is_verified' => 0,
                                    'verification_date' => 0,
                                    'revalidate' => 0,
                                    'revalidation_date' => 0,
                                    'reverify' => 0,
                                    'reverification_date' => 0,
                                    'rem_officer_rem_status' => 0,
                                    'rem_officer_rem_date' => 0,
                                    'rem_officer_img_filename' => null,
                                    'rem_pmo_closure_status' => 0,
                                    'rem_pmo_closure_date' => 0,
                                    'category' => 2,
                                    'reported_date' => new UTCDateTime(strtotime(date("m/d/Y")) * 1000),
                                    'created_at' => new UTCDateTime(strtotime(date("Y/m/d")) * 1000),
                                    'updated_at' => new UTCDateTime(strtotime(date("Y/m/d")) * 1000),

                                ]);

                                DB::commit();
                                $newFinding = true;
                            }catch (Exception $e){
                                DB::rollback();
                            }
                        }else if($duplicate_check > 0) {
                            $res = DB::connection('mongodb')->collection('scan_results')
                                ->where('name', '=', $value->title)
                                ->where('host', '=', $value->ip_address)
                                ->where('port', '=', (int)$value->port)
                                ->where('rem_pmo_closure_status', '=', 0)
                                ->orderBy('_id', 'desc')
                                ->first();

                            if ($res['stream_id'] != $request->id) {
                                DB::beginTransaction();

                                try {
                                    DB::connection('mongodb')->collection('scan_results')
                                        ->where('_id', $res['_id'])
                                        ->update([
                                            'recurrence' => new UTCDateTime(strtotime(date("Y/m/d")) * 1000),
                                            'updated_at' => new UTCDateTime(strtotime(date("Y/m/d")) * 1000)
                                        ]);

                                    DB::commit();
                                }catch (Exception $e){
                                    DB::rollback();
                                }
                            }
                        }
                    }
                }
            }
        }

                    DB::beginTransaction();

                    try {
                        if (isset($request->comment)) {
                            $comment_id = DB::table('comments')->insertGetId([
                                'user_id' => Auth::user()->id,
                                'stream_id' => $request->id,
                                'comments' => $request->comment,
                                'status' => 1,
                            ]);

                            DB::table('streams')
                                ->where('id', $request->id)
                                ->update(['tester_comments_id' => $comment_id]);
                        }

                        $datetime = strtotime($request->datetime);
                        DB::table('streams')
                            ->where('id', $request->id)
                            ->update([
                                'tester_scheduled_date' => date('Y-m-d H:i:s', $datetime)
                            ]);

                        if (isset($request->nofile)) {
                            DB::table('streams')
                                ->where('id', $request->id)
                                ->update([
                                    'tester_no_file_upload' => Config::get('enums.active_status.Active')
                                ]);
                        } else {
                            DB::table('streams')
                                ->where('id', $request->id)
                                ->update([
                                    'tester_no_file_upload' => Config::get('enums.active_status.Inactive')
                                ]);
                        }

                        if (!$newFinding)
                            $this->noNewFinding($request->id);

                        DB::commit();
                        $request->session()->flash('alert-success', 'Successfully Stored!');
                    } catch (Exception $e) {
                        DB::rollback();
                        $request->session()->flash('alert-danger', 'Operation is NOT successful!');
                    }
                    return redirect()->action('TesterController@index');
                }


    public function storeAppscan(Request $request)
    {
        $newFinding = false;
        $stream = Stream::where('id', $request->id)->first();
        $ticket = Ticket::where('id', $stream->ticket_id)->first();

        if (Input::hasFile('file') && !isset($request->nofile)) {

            foreach(Input::file('file') as $file) {
                $path = $file->getRealPath();
                $string = file_get_contents($path);
                $xml = new \SimpleXMLElement($string);

                if (!empty($xml) && $xml->count()) {
                    foreach ($xml->{'url-group'}->item as $Item) {

                        $duplicate_check = DB::connection('mongodb')->collection('scan_results')
                            ->where('name', '=', $Item->{'issue-type'} . '  ')
                            ->where('host', '=', $xml->{'scan-configuration'}->{'scanned-hosts'}->host . ' ')
                            ->where('port', '=', $xml->{'scan-configuration'}->{'scanned-hosts'}->port . ' ')
                            ->where('rem_pmo_closure_status', '=', 0)
                            ->count();

                        if ($duplicate_check == 0) {

                            DB::beginTransaction();

                            try {
                                DB::connection('mongodb')->collection('scan_results')->insert([
                                    'stream_id' => (int)$request->id,
                                    'opco_id' => (int)$ticket->opco_id,
                                    'department' => (int)$ticket->department,
                                    'module_id' => (int)$stream->module_id,
                                    'analyst_id' => 0,
                                    'qa_id' => 0,
                                    'rem_officer_id' => 0,
                                    'rem_pmo_id' => 0,
                                    'name' => $Item->{'issue-type'} . '  ',
                                    'host' => $xml->{'scan-configuration'}->{'scanned-hosts'}->item->host . ' ',
                                    'port' => $xml->{'scan-configuration'}->{'scanned-hosts'}->item->port . ' ',
                                    'risk' => (int)$Item->{'max-severity'}+1,
//                                    'description' => $ReportItem->Description . '  ',
//                                    'remediation' => $ReportItem->Recommendation . '  ',
                                    'false_positive' => 0,
                                    'img_filename' => null,
                                    'is_validated' => 0,
                                    'validation_date' => 0,
                                    'is_verified' => 0,
                                    'verification_date' => 0,
                                    'revalidate' => 0,
                                    'revalidation_date' => 0,
                                    'reverify' => 0,
                                    'reverification_date' => 0,
                                    'rem_officer_rem_status' => 0,
                                    'rem_officer_rem_date' => 0,
                                    'rem_officer_img_filename' => null,
                                    'rem_pmo_closure_status' => 0,
                                    'rem_pmo_closure_date' => 0,
                                    'category' => 2,
                                    'reported_date' => new UTCDateTime(strtotime(date("m/d/Y"))*1000),
                                    'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                    'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                ]);
                                DB::commit();
                                $newFinding = true;
                            } catch (Exception $e) {
                                DB::rollback();
                            }
                        }else if($duplicate_check > 0){
                            $res = DB::connection('mongodb')->collection('scan_results')
                                ->where('name', '=', $Item->{'issue-type'} . '  ')
                                ->where('host', '=', $xml->{'scan-configuration'}->{'scanned-hosts'}->host . ' ')
                                ->where('port', '=', $xml->{'scan-configuration'}->{'scanned-hosts'}->port . ' ')
                                ->where('rem_pmo_closure_status', '=', 0)
                                ->orderBy('_id', 'desc')
                                ->first();

                            if($res['stream_id'] != $request->id){
                                DB::beginTransaction();

                                try {
                                    DB::connection('mongodb')->collection('scan_results')
                                        ->where('_id', $res['_id'])
                                        ->update([
                                            'recurrence' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                            'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000)
                                        ]);

                                    DB::commit();
                                } catch (Exception $e) {
                                    DB::rollback();
                                }
                            }
                        }
                    }
                }
            }
        }

        DB::beginTransaction();

        try {
            if(isset($request->comment)) {
                $comment_id = DB::table('comments')->insertGetId([
                    'user_id' => Auth::user()->id,
                    'stream_id' => $request->id,
                    'comments' => $request->comment,
                    'status' => 1,
                ]);

                DB::table('streams')
                    ->where('id', $request->id)
                    ->update(['tester_comments_id' => $comment_id]);
            }

            $datetime = strtotime($request->datetime);
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['tester_scheduled_date' => date('Y-m-d H:i:s', $datetime)]);

            if(isset($request->nofile)){
                DB::table('streams')
                    ->where('id', $request->id)
                    ->update([
                        'tester_no_file_upload' => Config::get('enums.active_status.Active')
                    ]);
            }else{
                DB::table('streams')
                    ->where('id', $request->id)
                    ->update([
                        'tester_no_file_upload' => Config::get('enums.active_status.Inactive')
                    ]);
            }

            if(!$newFinding)
                $this->noNewFinding($request->id);

            DB::commit();
            $request->session()->flash('alert-success', 'Successful!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'NOT successful!');
        }
        return redirect()->action('TesterController@index');
    }

    public function storeBurpsuite(Request $request)
    {
        $newFinding = false;
        $stream = Stream::where('id', $request->id)->first();
        $ticket = Ticket::where('id', $stream->ticket_id)->first();

        if (Input::hasFile('file') && !isset($request->nofile)) {
            foreach(Input::file('file') as $file) {
                $path = $file->getRealPath();
                $string = file_get_contents($path);
                $xml = new \SimpleXMLElement($string);

                if (!empty($xml) && $xml->count()) {
                    foreach ($xml->issue as $Item) {

                        switch ($Item->severity) {
                            case "Information":
                                $Item->severity = Config::get('enums.severity_status.Informational');
                                break;
                            case "Low":
                                $Item->severity = Config::get('enums.severity_status.Low');
                                break;
                            case "Medium":
                                $Item->severity = Config::get('enums.severity_status.Medium');
                                break;
                            case "High":
                                $Item->severity = Config::get('enums.severity_status.High');
                                break;
                            case "Critical":
                                $Item->severity = Config::get('enums.severity_status.Critical');
                                break;
                        }

                        $duplicate_check = DB::connection('mongodb')->collection('scan_results')
                            ->where('name', '=', $Item->name . '  ')
                            ->where('host', '=', $Item->host . '  ')
    //                        ->where('port', '=', $xml->{'scan-configuration'}->{'scanned-hosts'}->item->port . ' ')
                            ->where('rem_pmo_closure_status', '=', 0)
                            ->count();

                        if ($duplicate_check == 0) {

                            DB::beginTransaction();

                            try {
                                DB::connection('mongodb')->collection('scan_results')->insert([
                                    'stream_id' => (int)$request->id,
                                    'opco_id' => (int)$ticket->opco_id,
                                    'department' => (int)$ticket->department,
                                    'module_id' => (int)$stream->module_id,
                                    'scan_id' => (int)$ticket->sc_model,
                                    'analyst_id' => 0,
                                    'qa_id' => 0,
                                    'rem_officer_id' => 0,
                                    'rem_pmo_id' => 0,
                                    'name' => $Item->name . '  ',
                                    'background' => $Item->issueBackground . '  ',
                                    'host' => $Item->host . '  ',
    //                                'port' => $xml->{'scan-configuration'}->{'scanned-hosts'}->item->port . ' ',
                                    'risk' => (int)$Item->severity,
                                    'remediation' => $Item->remediationDetail . '  ',
                                    'false_positive' => 0,
                                    'img_filename' => null,
                                    'is_validated' => 0,
                                    'validation_date' => 0,
                                    'is_verified' => 0,
                                    'verification_date' => 0,
                                    'revalidate' => 0,
                                    'revalidation_date' => 0,
                                    'reverify' => 0,
                                    'reverification_date' => 0,
                                    'rem_officer_rem_status' => 0,
                                    'rem_officer_rem_date' => 0,
                                    'rem_officer_img_filename' => null,
                                    'rem_pmo_closure_status' => 0,
                                    'rem_pmo_closure_date' => 0,
                                    'category' => 2,
                                    'assign_user' => null,
                                    'reported_date' => new UTCDateTime(strtotime(date("m/d/Y"))*1000),
                                    'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                    'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                ]);
                                DB::commit();
                                $newFinding = true;
                            } catch (Exception $e) {
                                DB::rollback();
                            }
                        }else if($duplicate_check > 0){
                            $res = DB::connection('mongodb')->collection('scan_results')
                                ->where('name', '=', $Item->name . '  ')
                                ->where('host', '=', $Item->host . '  ')
    //                            ->where('port', '=', $xml->{'scan-configuration'}->{'scanned-hosts'}->item->port . ' ')
                                ->where('rem_pmo_closure_status', '=', 0)
                                ->orderBy('_id', 'desc')
                                ->first();

                            if($res['stream_id'] != $request->id){
                                DB::beginTransaction();

                                try {
                                    DB::connection('mongodb')->collection('scan_results')
                                        ->where('_id', $res['_id'])
                                        ->update([
                                            'recurrence' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                            'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000)
                                        ]);

                                    DB::commit();
                                } catch (Exception $e) {
                                    DB::rollback();
                                }
                            }
                        }
                    }
                }
            }
        }

        DB::beginTransaction();

        try {
            if(isset($request->comment)) {
                $comment_id = DB::table('comments')->insertGetId([
                    'user_id' => Auth::user()->id,
                    'stream_id' => $request->id,
                    'comments' => $request->comment,
                    'status' => 1,
                ]);

                DB::table('streams')
                    ->where('id', $request->id)
                    ->update(['tester_comments_id' => $comment_id]);
            }

            $datetime = strtotime($request->datetime);
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['tester_scheduled_date' => date('Y-m-d H:i:s', $datetime)]);

            if(isset($request->nofile)){
                DB::table('streams')
                    ->where('id', $request->id)
                    ->update([
                        'tester_no_file_upload' => Config::get('enums.active_status.Active')
                    ]);
            }else{
                DB::table('streams')
                    ->where('id', $request->id)
                    ->update([
                        'tester_no_file_upload' => Config::get('enums.active_status.Inactive')
                    ]);
            }

            if(!$newFinding)
                $this->noNewFinding($request->id);

            DB::commit();
            $request->session()->flash('alert-success', 'Successful!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'NOT successful!');
        }
        return redirect()->action('TesterController@index');
    }

    public function storeAsset(Request $request)
    {
        $stream = Stream::where('id', $request->id)->first();
        $ticket = Ticket::where('id', $stream->ticket_id)->first();

        if (Input::hasFile('file')) {
            $path = Input::file('file')->getRealPath();

            $data = Excel::load($path, function ($reader) {
            })->get();

            if (!empty($data) && $data->count()) {
                foreach ($data as $key => $value) {

                    $insert[] = [
                        'stream_id' => (int)$request->id,
                        'module_id' => (int)$stream->module_id,
                        'opco_id' => (int)$ticket->opco_id,
                        'analyst_id' => 0,
                        'qa_id' => 0,
                        'rem_officer_id' => 0,
                        'rem_pmo_id' => 0,
                        // application-asset and non-application-asset
                        'asset_tag_number' => $value->asset_tag_number,
                        'pysical_location' => $value->pysical_location,
                        'floor' => $value->floor,
                        'asset_type' => $value->asset_type,
                        'manufacturer' => $value->manufacturer,
                        'serial_number' => $value->serial_number,
                        'make_and_model' => $value->make_and_model,
                        'application_id' => $value->application_id,
                        'application_name' => $value->application_name,
                        'url' => $value->url,
                        'application_type' => $value->application_type,
                        'device_name' => $value->device_name,
//                        'hostname/node_name' => $value->hostname/node_name,
                        'internal_ip_address' => $value->internal_ip_address,
                        'public_ip_address' => $value->public_ip_address,
                        'web_server_ip_address' => $value->web_server_ip_address,
                        'database_ip_address' => $value->database_ip_address,
                        'domain_name' => $value->domain_name,
                        'device_status' => $value->device_status,
                        'operating_system' => $value->operating_system,
                        'software_version' => $value->software_version,
                        'kernel_version' => $value->kernel_version,
                        'device_type' => $value->device_type,
//                        'cluster/standalone' => $value->cluster/standalone,
//                        'physical/vm' => $value->physical/vm,
//                        'asset_age(years)' => $value->asset_age(years),
//                        'Prod/Non-Prod/DR' => $value->Prod/Non-Prod/DR,
                        'database_component' => $value->database_component,
                        'latest_patch_level' => $value->latest_patch_level,
                        'monitoring_server_ip_address' => $value->monitoring_server_ip_address,
                        'monitoring_tool' => $value->monitoring_tool,
                        'hosted_applications' => $value->hosted_applications,
//                        'app/db_name' => $value->app/db_name,
                        'business_criticality' => $value->business_criticality,
                        'business_environment' => $value->business_environment,
                        'business_description' => $value->business_description,
                        'mbss_type' => $value->mbss_type,
                        'mbss_compliance' => $value->mbss_compliance,
                        'mbss_compliance_percentage' => $value->mbss_compliance_percentage,
                        'mbss_owner' => $value->mbss_owner,
                        'asset_owner' => $value->asset_owner,
                        'group_owner' => $value->group_owner,
//                        'sub-group_owner' => $value->sub-group_owner,
                        'administrator' => $value->administrator,
                        'alternate_administrator' => $value->alternate_administrator,
                        'hod' => $value->hod,
                        'vendor' => $value->vendor,
                        'operating_system_responsible' => $value->operating_system_responsible,
                        'database_responsible' => $value->database_responsible,
                        'web_application_responsible' => $value->web_application_responsible,
                        'web_server_responsible' => $value->web_server_responsible,
                        'web_service_responsible' => $value->web_service_responsible,
                        'remarks' => $value->remarks,
                        'false_positive' => 0,
                        'img_filename' => null,
                        'is_validated' => 0,
                        'validation_date' => 0,
                        'is_verified' => 0,
                        'verification_date' => 0,
                        'revalidate' => 0,
                        'revalidation_date' => 0,
                        'reverify' => 0,
                        'reverification_date' => 0,
                        'rem_officer_rem_status' => 0,
                        'rem_officer_rem_date' => 0,
                        'rem_officer_img_filename' => null,
                        'rem_pmo_closure_status' => 0,
                        'rem_pmo_closure_date' => 0,
                        'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                        'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                    ];
                }

                if (!empty($insert)) {
                    DB::beginTransaction();

                    try {
                        DB::connection('mongodb')->collection('scan_results')->insert($insert);

                        if(isset($request->comment)) {
                            $comment_id = DB::table('comments')->insertGetId([
                                'user_id' => Auth::user()->id,
                                'stream_id' => $request->id,
                                'comments' => $request->comment,
                                'status' => 1,
                            ]);

                            $datetime = strtotime($request->datetime);
                            $updateDetails=array(
                                'tester_comments_id' => $comment_id,
                                'tester_scheduled_date' => date('Y-m-d H:i:s', $datetime),
                            );
                            DB::table('streams')
                                ->where('id', $request->id)
                                ->update($updateDetails);
                        }
                        DB::commit();
                        $request->session()->flash('alert-success', 'Upload was successful!');
                    } catch (Exception $e) {
                        DB::rollback();
                        $request->session()->flash('alert-danger', 'Upload was NOT successful!');
                    }
                }
                return redirect()->action('TesterController@index');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */

    // Show all findings of a ticket excluding "Informational" findings
    public function show(Request $request, $id)
    {
        $result = DB::connection('mongodb')->collection('scan_results')->where('stream_id', (int)$id)->where('risk', '!=', 1)->get();
        $enums = array_flip(Config::get('enums.severity_status'));

        return view('Tester.show', compact('result','enums', 'id', 'stream'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function edit(Stream $stream)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */

    // Forward ticket to Analyst
    public function update(Request $request, Stream $stream)
    {
        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['status' => Config::get('enums.stream_status.Analyst'),
                    'analyst_assigned_date' => date('Y-m-d H:i:s', time())]);

            DB::commit();
            flash()->success('Stream is successfully forwarded to Analyst!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Stream update was not successful!');
        }
        return redirect()->route('Tester.index');
    }

    // Backward ticket to the Cyber PMO
    public function backward(Request $request, Stream $stream)
    {
        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['status' => Config::get('enums.stream_status.PMO')]);

            DB::commit();
            flash()->success('Stream was successfully sent back to PMO! ');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Stream update was not successful!');
        }
        return redirect()->route('Tester.index');
    }

    // Remove all uplaoded findings of selected ticket
    public function deleteUploaded(Request $request, Stream $stream)
    {
        $stream = Stream::where('id', $request->id)->get();

        DB::beginTransaction();

        try {
            $where = ['id' => $stream[0]->tester_comments_id, 'user_id' => Auth::user()->id, 'stream_id' => $stream[0]->id];
            DB::connection('mysql')->table('comments')->where($where)->update([
                'status'    => 0,
                'updated_at'    => date('Y-m-d H:i:s', time()),
            ]);

            DB::connection('mongodb')->collection('scan_results')->where('stream_id', (Int)$request->id)->delete();

            DB::commit();
            flash()->success('Uploaded data deleted successfully');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Uploaded data are NOT deleted successfully');
        }
        return redirect()->route('Tester.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stream $stream)
    {

    }

    public function viewUploadOld()
    {
        return view('upload_old');
    }

    // To upload old findings, used for data migration from existing data source
    public function storeUploadOld(Request $request)
    {
        if (Input::hasFile('file')) {
            foreach(Input::file('file') as $file) {
                $path = $file->getRealPath();

                $data = Excel::load($path, function ($reader) {
                })->get();

                if (!empty($data) && $data->count()) {
                    foreach ($data as $key => $value) {
                        if ($value->scope != "Internal")
                            continue;

                        if($value->vuln_name == NULL || $value->vuln_name == "")
                            continue;

                        foreach ($value as $key => $v) {
                            if ($v == "NULL")
                                $value[$key] = NULL;
                        }

                        switch ($value->status) {
                            case "OPEN":
                                $value->status = Config::get('enums.mdb_stream_status.Open');
                                break;
                            case "CLOSED":
                                $value->status = Config::get('enums.mdb_stream_status.Close');
                                break;
                            case "REVALIDATION":
                                $value->status = Config::get('enums.mdb_stream_status.Revalidation');
                                break;
                            case "EXCEPTION":
                                $value->status = Config::get('enums.mdb_stream_status.Exception');
                                break;
                        }

                        switch ($value->risk_rating) {
                            case "Informational":
                                $value->risk_rating = Config::get('enums.severity_status.Informational');
                                break;
                            case "Low":
                                $value->risk_rating = Config::get('enums.severity_status.Low');
                                break;
                            case "Medium":
                                $value->risk_rating = Config::get('enums.severity_status.Medium');
                                break;
                            case "High":
                                $value->risk_rating = Config::get('enums.severity_status.High');
                                break;
                            case "Critical":
                                $value->risk_rating = Config::get('enums.severity_status.Critical');
                                break;
                        }

                        switch ($value->opco) {
                            case "HQ":
                                $value->opco = Config::get('enums.opco_switch.HQ');
                                break;
                            case "CoE":
                                $value->opco = Config::get('enums.opco_switch.CoE');
                                break;
                            case "ADS":
                                $value->opco = Config::get('enums.opco_switch.ADS');
                                break;
                            case "Axiata":
                                $value->opco = Config::get('enums.opco_switch.Axiata');
                                break;
                            case "E.Co":
                                $value->opco = Config::get('enums.opco_switch.ECo');
                                break;
                            case "XL":
                                $value->opco = Config::get('enums.opco_switch.XL');
                                break;
                            case "NCELL":
                                $value->opco = Config::get('enums.opco_switch.NCELL');
                                break;
                            case "SMART":
                                $value->opco = Config::get('enums.opco_switch.SMART');
                                break;
                            case "Dialog":
                                $value->opco = Config::get('enums.opco_switch.Dialog');
                                break;
                            case "Robi":
                                $value->opco = Config::get('enums.opco_switch.Robi');
                                break;
                            case "Celcom":
                                $value->opco = Config::get('enums.opco_switch.Celcom');
                                break;
                        }

                        switch ($value->vuln_cat) {
                            case "Broken Access Control":
                                $value->vuln_cat = Config::get('enums.vuln_category.Broken Access Control');
                                break;
                            case "Broken Authentication":
                                $value->vuln_cat = Config::get('enums.vuln_category.Broken Authentication');
                                break;
                            case "Injection":
                                $value->vuln_cat = Config::get('enums.vuln_category.Injection');
                                break;
                            case "Patch Management":
                                $value->vuln_cat = Config::get('enums.vuln_category.Patch Management');
                                break;
                            case "Security Misconfiguration":
                                $value->vuln_cat = Config::get('enums.vuln_category.Security Misconfiguration');
                                break;
                            case "Sensitive Data Exposure":
                                $value->vuln_cat = Config::get('enums.vuln_category.Sensitive Data Exposure');
                                break;
                            case "System Development and Maintenance":
                                $value->vuln_cat = Config::get('enums.vuln_category.System Development and Maintenance');
                                break;
                            case "Unprotected Services":
                                $value->vuln_cat = Config::get('enums.vuln_category.Unprotected Services');
                                break;
                            case "Weak Cryptography":
                                $value->vuln_cat = Config::get('enums.vuln_category.Weak Cryptography');
                                break;

                        }

                        $value->closed_date = str_replace('/', '-', $value->closed_date);
                        if($value->closed_date != NULL)
                            $value->closed_date = new UTCDateTime(DateTime::createFromFormat("m-d-Y", $value->closed_date)->getTimestamp()*1000);
                        else
                            $value->closed_date = NULL;

                        if(trim($value->category) == "Web Application") {
                            if($value->url == NULL)
                                $this->insert_rem_tracker_nessus($request, $value);
                            else
                                $this->insert_rem_tracker_acunetix($request, $value);
                        }else if(trim($value->category) == "External"){
                            if($value->ip == NULL)
                                $this->insert_rem_tracker_acunetix($request, $value);
                            else
                                $this->insert_rem_tracker_nessus($request, $value);
                        }else if(trim($value->category) == "Mobile Application"){
                            if($value->ip == NULL)
                                $this->insert_rem_tracker_acunetix($request, $value);
                            else
                                $this->insert_rem_tracker_nessus($request, $value);
                        }else if(trim($value->category) == "In-Depth Penetration Testing"){
                            if($value->ip == NULL)
                                $this->insert_rem_tracker_acunetix($request, $value);
                            else
                                $this->insert_rem_tracker_nessus($request, $value);
                        }else if(trim($value->category) == "Internal"){
                            if($value->ip == NULL)
                                $this->insert_rem_tracker_acunetix($request, $value);
                            else
                                $this->insert_rem_tracker_nessus($request, $value);
                        }
                    }
                }
            }
        }
        Artisan::call('schedule:run');
        return view('upload_old');
    }

    public function insert_rem_tracker_nessus($request, $value)
    {
        $value->vuln_name = $this->removeNameWhitespaces($value->vuln_name);
        $value->host = $this->removeAllWhitespaces($value->host);

        $duplicate_check = DB::connection('mongodb')->collection('scan_results')
            ->where('host',$value->ip)
            ->where('port',(int)$value->port)
            ->where('name',$value->vuln_name)
            ->where('rem_pmo_closure_status',0)
            ->where('false_positive', 0)
            ->count();

        if ($duplicate_check == 0) {
            DB::beginTransaction();

            try {
                DB::connection('mongodb')->collection('scan_results')->insert([
                    'stream_id' => 0,
                    'module_id' => 1,
                    'opco_id' => (int)$value->opco,
                    'department' => (int)$ticket->department,
                    'analyst_id' => 0,
                    'qa_id' => 0,
                    'rem_officer_id' => 0,
                    'rem_pmo_id' => 0,
                    'reported_date' => new UTCDateTime(DateTime::createFromFormat("m/d/Y", $value->reported_date)->getTimestamp()*1000),
                    'plugin_id' => $value->plugin_id,
                    'cve' => $value->cve,
                    'cvss' => $value->cvss,
                    'risk' => (int)$value->risk_rating,
                    'host' => $value->ip,
                    'protocol' => $value->protocol,
                    'port' => (int)$value->port,
                    'name' => $value->vuln_name,
                    'synopsis' => $value->synopsis,
                    'description' => $value->desc,
                    'Recommendation' => $value->remediation,
                    'solution' => $value->remediation,
                    'see_also' => $value->see_also,
                    'plugin_output' => $value->plugin_output,
                    'false_positive' => 0,
                    'img_filename' => null,
                    'is_validated' => 0,
                    'validation_date' => 0,
                    'is_verified' => 0,
                    'verification_date' => 0,
                    'revalidate' => 0,
                    'revalidation_date' => 0,
                    'reverify' => 0,
                    'reverification_date' => 0,
                    'hod_signoff_date' => new UTCDateTime(DateTime::createFromFormat("m/d/Y", $value->reported_date)->getTimestamp()*1000),
                    'rem_officer_rem_status' => (int)$value->status,
                    'rem_officer_rem_date' => $value->closed_date,
                    'rem_officer_img_filename' => null,
                    'rem_pmo_closure_status' => (int)$value->status,
                    'rem_pmo_closure_date' => $value->closed_date,
                    'recurrence' => $value->recurrence != NULL ? new UTCDateTime(DateTime::createFromFormat("m/d/Y", $value->recurrence)->getTimestamp()*1000) : NULL,
                    'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                    'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                    'vul_category' => (int)$value->vuln_cat,
                ]);

                DB::commit();
                $request->session()->flash('alert-success', 'Successful!');
            } catch (Exception $e) {
                DB::rollback();
                $request->session()->flash('alert-danger', 'NOT successful!');
            }
        } else if ($duplicate_check > 0) {
            $res = DB::connection('mongodb')->collection('scan_results')
                ->where('host', '=', $value->ip)
                ->where('port', '=', (int)$value->port)
                ->where('name', '=', $value->vuln_name)
                ->where('rem_pmo_closure_status', '=', 0)
                ->where('false_positive', 0)
                ->orderBy('_id', 'desc')
                ->first();

            if ($res['stream_id'] != $request->id) {
                DB::beginTransaction();

                try {
                    DB::connection('mongodb')->collection('scan_results')
                        ->where('_id', $res['_id'])
                        ->update([
                            'recurrence' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                            'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000)
                        ]);

                    DB::commit();
                    $request->session()->flash('alert-success', 'Successful!');
                } catch (Exception $e) {
                    DB::rollback();
                    $request->session()->flash('alert-danger', 'NOT successful!');
                }
            }
        }
    }

    public function insert_rem_tracker_acunetix($request, $value)
    {
        $value->vuln_name = $this->removeNameWhitespaces($value->vuln_name);
        $url_scheme = $this->getURLScheme($value->url);
        $value->url = $this->removehttp($value->url);
        $value->url = $this->removeAllWhitespaces($value->url);
        $value->url = $this->removeURLSlashRightSide($value->url);
        $value->url = urlencode($value->url);

        $duplicate_check = DB::connection('mongodb')->collection('scan_results')
            ->where('host', '=', $value->url)
            ->where('port', '=', (int)$value->port)
            ->where('name', '=', $value->vuln_name)
            ->where('rem_pmo_closure_status', '=', 0)
            ->where( 'false_positive', 0)
            ->count();

        if ($duplicate_check == 0) {
            DB::beginTransaction();

            try {
                DB::connection('mongodb')->collection('scan_results')->insert([
                    'stream_id' => 0,
                    'module_id' => 2,
                    'opco_id' => (int)$value->opco,
                    'department' => (int)$ticket->department,
                    'analyst_id' => 0,
                    'qa_id' => 0,
                    'rem_officer_id' => 0,
                    'rem_pmo_id' => 0,
                    'reported_date' => new UTCDateTime(DateTime::createFromFormat("m/d/Y", $value->reported_date)->getTimestamp()*1000),
                    'name' => $value->vuln_name,
                    'solution' => $value->remediation,
                    'ModuleName' => '',
                    'url_scheme' => $url_scheme,
                    'host' => $value->url,
                    'port' => (int)$value->port,
                    'Details' => '',
                    'Affects' => '',
                    'Parameter' => '',
                    'AOP_SourceFile' => '',
                    'AOP_Additional' => '',
                    'IsFalsePositive' => '',
                    'risk' => (int)$value->risk_rating,
                    'Type' => '',
                    'Impact' => '',
                    'Description' => $value->desc,
                    'Recommendation' => $value->remediation,
                    'Request' => '',
                    'Response' => '',
//                                        'CWE' => '',
//                                        'Descriptor' => '',
//                                        'Score' => '',
//                                        'AV' => '',
//                                        'AC' => '',
//                                        'AU' => '',
//                                        'C' => '',
//                                        'I' => '',
//                                        'A' => '',
//                                        'E' => '',
//                                        'RL' => '',
//                                        'RC' => '',
//                                        'Descriptor_CVSS3' => '',
//                                        'Score_CVSS3' => '',
//                                        'TempScore' => '',
//                                        'EnvScore' => '',
//                                        'AV_CVSS3' => '',
//                                        'AC_CVSS3' => '',
//                                        'PR' => '',
//                                        'UI' => '',
//                                        'S' => '',
//                                        'C_CVSS3' => '',
//                                        'I_CVSS3' => '',
//                                        'A_CVSS3' => '',
//                                        'E_CVSS3' => '',
//                                        'RL_CVSS3' => '',
//                                        'RC_CVSS3' => '',
                    'false_positive' => 0,
                    'img_filename' => null,
                    'is_validated' => 0,
                    'validation_date' => 0,
                    'is_verified' => 0,
                    'verification_date' => 0,
                    'revalidate' => 0,
                    'revalidation_date' => 0,
                    'reverify' => 0,
                    'reverification_date' => 0,
                    'hod_signoff_date' => new UTCDateTime(DateTime::createFromFormat("m/d/Y", $value->reported_date)->getTimestamp()*1000),
                    'rem_officer_rem_status' => (int)$value->status,
                    'rem_officer_rem_date' => $value->closed_date,
                    'rem_officer_img_filename' => null,
                    'rem_pmo_closure_status' => (int)$value->status,
                    'rem_pmo_closure_date' => $value->closed_date,
                    'recurrence' => $value->recurrence != NULL ? new UTCDateTime(DateTime::createFromFormat("m/d/Y", $value->recurrence)->getTimestamp()*1000) : NULL,
                    'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                    'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                    'vul_category' => (int)$value->vuln_cat,
                ]);

                DB::commit();
                $request->session()->flash('alert-success', 'Successful!');
            } catch (Exception $e) {
                DB::rollback();
                $request->session()->flash('alert-danger', 'NOT successful!');
            }
        } else if ($duplicate_check > 0) {
            $res = DB::connection('mongodb')->collection('scan_results')
                ->where('host', '=', $value->url)
                ->where('port', '=', (int)$value->port)
                ->where('name', '=', $value->vuln_name)
                ->where('rem_pmo_closure_status', '=', 0)
                ->where( 'false_positive', 0)
                ->orderBy('_id', 'desc')
                ->first();

            if ($res['stream_id'] != $request->id) {
                DB::beginTransaction();

                try {
                    DB::connection('mongodb')->collection('scan_results')
                        ->where('_id', $res['_id'])
                        ->update([
                            'recurrence' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                            'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000)
                        ]);

                    DB::commit();
                    $request->session()->flash('alert-success', 'Successful!');
                } catch (Exception $e) {
                    DB::rollback();
                    $request->session()->flash('alert-danger', 'NOT successful!');
                }
            }
        }
    }

    // Export all findings in Excel format
    public function export(Request $request)
    {
        $totalres = DB::connection('mongodb')->collection('scan_results')->where('risk','!=',0)->whereNotNull('reported_date')->get()->toArray();

        $totalres = array_map(function($totalres) {
            $enums1 = array_flip(Config::get('enums.mdb_stream_status'));
            $stats = $enums1[$totalres['rem_officer_rem_status']];
            $enums3 = array_flip(Config::get('enums.severity_status'));
            $r = $enums3[$totalres['risk']];
            $enums2 = array_flip(Config::get('enums.vuln_category'));
            $opco = array_flip(Config::get('enums.opco_switch'));
//            $vul = $enums2[$totalres['vul_category']];
            $reportDate = $totalres['reported_date'] -> toDateTime()->format('Y-m-d H:i:s');
            return array(
                'db id' => (string)$totalres['_id'],
                'vulnerability name' => $totalres['name'],
                'Status' => $stats,
                'Risk' => $r,
                'Host' => $totalres['host'],
                'Port' => $totalres['port'],
                'Reported on' => $reportDate,
                'Category' => isset($totalres['vul_category']) ? $enums2[$totalres['vul_category']] : null,
                'Target fix date' => isset($totalres['target_fix_date']) ? $totalres['target_fix_date'] : null,
                'Revalidation date' => isset($totalres['rem_officer_revalidation_date']) ? $totalres['rem_officer_revalidation_date'] -> toDateTime()->format('Y-m-d H:i:s') : null,
                'Description' => isset($totalres['Description']) ? $totalres['Description'] : null,
                'Recommendation' => isset($totalres['Recommendation']) ? $totalres['Recommendation'] : null,
                'Recurrence' => isset($totalres['recurrence']) ? $totalres['recurrence'] -> toDateTime()->format('Y-m-d H:i:s') : null,
                'OpCo' => $opco[$totalres['opco_id']],
            );
        }, $totalres);

        $totalres = json_decode(json_encode($totalres), true);

        $date = new DateTime();
        $date = $date->format('dmY');
        $filename = ' Category ' . $date;

        Excel::create($filename, function($excel) use($totalres) {
            $excel->sheet('Sheet1', function($sheet) use($totalres) {
                $sheet->fromArray($totalres);
            });
        })->download('xlsx');

        return view('upload_old');
    }

    // To upload and insert category into existing findings
    public function storeCategory(Request $request)
    {
//        dd(DB::connection('mongodb')->collection('scan_results')->where('_id','5bbc65130947da2138006f16')->get()->toArray());

        if (Input::hasFile('file')) {
            foreach(Input::file('file') as $file) {
                $path = $file->getRealPath();

                $data = Excel::load($path, function ($reader) {
                })->get();

                if (!empty($data) && $data->count()) {
                    foreach ($data as $key => $value) {
                        DB::beginTransaction();
                            try {
                                DB::connection('mongodb')->collection('scan_results')->where('_id',$value->db_id)->update([
                                    'category' => 1,
                                ]);

                                DB::commit();
                                $request->session()->flash('alert-success', 'Successful!');
                            } catch (Exception $e) {
                                DB::rollback();
                                $request->session()->flash('alert-danger', 'NOT successful!');
                            }
                        }
                    return view('upload_old');
                    }
                else{
                    DB::beginTransaction();
                    try {
                        DB::connection('mongodb')->collection('scan_results')->whereNotNull('reported_date')->update([
                            'category' => 2,
                        ]);

                        DB::commit();
                        $request->session()->flash('alert-success', 'Successful!');
                    } catch (Exception $e) {
                        DB::rollback();
                        $request->session()->flash('alert-danger', 'NOT successful!');
                    }
                    return view('upload_old');
                }
                }
            }

    }
}
