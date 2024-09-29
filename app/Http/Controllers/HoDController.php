<?php

namespace App\Http\Controllers;

use App\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Pdfreport;
use App\User;
use DB;
use App;
use Response;
use ScanResults;
use DateTime;
use MongoDB\BSON\UTCDateTime;

class HoDController extends Controller
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

    // Returns all tickets assigned to HOD
    public function index()
    {
        // retrieve information from db where ticket status is HoD and display
        $where = ['status' => Config::get('enums.stream_status.HOD'), 'hod_id' => Auth::user()->id];
        $streams = Stream::with('comments', 'opco', 'modules', 'user', 'tickets')->where($where)->paginate(10);
        return view('HoD.index')->with('streams', $streams);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */

    // Returns findings in selected ticket
    public function show($id)
    {
        // retrieve information from mongodb of a particular ticket in detail 
        $mresults = DB::connection('mongodb')->collection('scan_results')
            ->where('stream_id', (int)$id)
            ->where('false_positive', 0)
            ->where('risk', '!=', 1)
            ->groupby('name','risk')
            ->options(['allowDiskUse' => true])
            ->orderBy('risk', 'desc')->get();

        $mdetails = DB::connection('mongodb')->collection('scan_results')
            ->where('stream_id', (int)$id)
            ->where('false_positive', 0)
            ->where('risk', '!=', 1)
            ->select('url_scheme', 'module_id', 'name','risk', 'description', 'solution', 'synopsis','host', 'cve', 'cvss', 'protocol', 'port', 'img_filename', 'Affects', 'Impact', 'Description', 'Recommendation', 'ModuleName', 'Details', 'Request', 'Response', 'Affected URL', 'ip_address', 'OS', 'OS_version',
                'fix', 'summary')
            ->options(['allowDiskUse' => true])
            ->orderBy('risk', 'desc')->get();

//        $report_status = DB::connection('mongodb')->collection('scan_results')
//            ->where('stream_id', (int)$id)
//            ->wherein('risk', [3,4,5])
//            ->count();

        $critical_count = DB::connection('mongodb')->collection('scan_results')
            ->where('stream_id', (int)$id)
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->where('false_positive', 0)
            ->get()->toArray();
        $high_count = DB::connection('mongodb')->collection('scan_results')
            ->where('stream_id', (int)$id)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->get()->toArray();

        $medium_count = DB::connection('mongodb')->collection('scan_results')
            ->where('stream_id', (int)$id)
            ->where('false_positive', 0)
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))
            ->get()->toArray();

        $report_status = array_merge($critical_count, $high_count, $medium_count);

        $enums = array_flip(Config::get('enums.severity_status'));

        $sresults = Stream::with('comments', 'opco', 'modules', 'user', 'tickets')->where('id', $id)->get();
        $tester = User::where('id', $sresults[0]->tester_id)->first();
        $analyst = User::where('id', $sresults[0]->analyst_id)->first();
        $qa = User::where('id', $sresults[0]->qa_id)->first();
        $hod = User::where('id', $sresults[0]->hod_id)->first();

        $pdfinfo = Pdfreport::latest()->with('user')->get();


        return view('HoD.signoff', compact('report_status','mresults','sresults','mdetails','tickets','modules','comments','opco','user','enums','tester','analyst','hod','qa', 'pdfinfo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function edit(Stream $stream)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */

    // Backward ticket to QA
    public function backward(Request $request, Stream $stream)
    {
        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['status' => Config::get('enums.stream_status.QA')]);

            DB::commit();
            flash()->success('Task was successfully sent back to QA
            !');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Stream update was NOT successful!');
        }

        return redirect()->route('HoD.index');
    }

    // Update ticket sign off status
    public function update(Request $request, Stream $stream)
    {
        // sign off ticket
        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('id', $request->id)
                ->update([
                    'status' => Config::get('enums.stream_status.Signed_Off'),
                    'hod_signoff_date' => date("Y/m/d"),
                ]);

            DB::connection('mongodb')->collection('scan_results')
                ->where('stream_id', (int)$request->id)
                ->update([
                    'hod_signoff_date' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                    'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                ]);

            DB::commit();
            flash()->success('Task was successfully Signed Off');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Stream update was NOT successful');
        }

        return redirect()->route('HoD.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stream $stream)
    {
        //
    }
}
