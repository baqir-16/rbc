<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Stream;
use App\Pdfreport;
use DB;
use Config;
use View;
use App;
use SSH;
use File;
use Response;
use PDF;
use phpseclib\Net\SFTP;

class CloseController extends Controller
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

    // Return list of closed tickets
    public function index()
    {
        $streams = Stream::where('status', '10')->with('comments', 'opco', 'modules', 'user', 'tickets')->get();
        return view('close.index')->with('streams', $streams);
    }

    public function searchData(Request $request) {

        $term = $request->get('term');
        $data = DB::connection('mongodb')->table('scan_results')->where("host","LIKE","%$term%")->select('host as value')->get();
        return response()->json($data);

    }

    // Return findings of selected opco and host
    public function scanresults(Request $request) {

        $opco = $request->opco_id;
        $host = $request->host;

        $results = DB::connection('mongodb')->collection('scan_results')
            ->where('false_positive', 0)
            ->where('rem_pmo_closure_status', 0)
            ->whereNotNull('hod_signoff_date')
            ->where('opco_id', (int)$opco)
            ->where('host', $host)
            ->get();
        $enums = array_flip(Config::get('enums.severity_status'));

//        dd($results);

        return view('close.showresults', compact('results', 'enums'));

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // Returns generated report of closed findings
    public function show(Request $request, $id)
    {
        $streams = Stream::where('status', '10')
            ->with('comments', 'opco', 'modules', 'user', 'tickets')
            ->get();

        $mresults = DB::connection('mongodb')->collection('scan_results')
            ->where('stream_id', (int)$id)
            ->where('false_positive', 0)
            ->where('rem_pmo_closure_status', 0)
            ->where('risk', '!=', 1)
            ->groupby('name','risk', 'description', 'solution', 'synopsis')
            ->options(['allowDiskUse' => true])
            ->orderBy('risk', 'desc')->get();

        $mdetails = DB::connection('mongodb')->collection('scan_results')
            ->where('stream_id', (int)$id)
            ->where('false_positive', 0)
            ->where('rem_pmo_closure_status', 0)
            ->where('risk', '!=', 1)
            ->select('url_scheme', 'module_id', 'name','risk', 'description', 'solution', 'synopsis','host', 'cve', 'cvss', 'protocol', 'port', 'img_filename', 'Affects', 'Impact', 'Description', 'Recommendation', 'ModuleName', 'Details', 'Request', 'Response', 'Affected URL', 'ip_address', 'OS', 'OS_version',
                'fix', 'summary')
            ->options(['allowDiskUse' => true])
            ->orderBy('risk', 'desc')->get();

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

        if($sresults[0]->report_filename == NULL) {
            $snappy = App::make('snappy.pdf');
            $html = View::make('close.generate_pdf', compact('report_status','mresults','mdetails','sresults','tickets','modules','comments','opco','user','tester','analyst','qa','hod','enums', 'pdfinfo'))->render();
            $filename = 'report_'.time()."_".rand(100,999);
            $generatedPdfFilePath = base_path().DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$filename;
            $snappy->setTimeout(600);
            $snappy->setOption('footer-center', '[page]');
            $snappy->generateFromHtml($html, $generatedPdfFilePath);

            DB::beginTransaction();

            try {
                DB::table('streams')->where('id', $id)->update([
                    'report_filename' => $filename,
                    'updated_at' => date('Y-m-d H:i:s', time()),
                ]);
                DB::commit();
//                $request->session()->flash('alert-success', 'Report generated successfully');
            } catch (Exception $e) {
                DB::rollback();
//                $request->session()->flash('alert-danger', 'Report failed to generate');
            }

            $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

            if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                throw new Exception('SFTP Login failed');
            }

            $sftp->mkdir('/var/www/html/pdf_reports');

            SSH::into('MDB_SERVER')->put($generatedPdfFilePath, '/var/www/html/pdf_reports/'.$filename);
            File::delete($generatedPdfFilePath);
        } else {
            $filename = $sresults[0]->report_filename;
        }
        $display_report = true;

        return view('close.index', compact('mresults','mdetails','sresults','streams','tickets','modules','comments','opco','user','display_report','filename', 'pdfinfo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // Close selected ticket
    public function update(Request $request, Stream $stream)
    {
        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['status' => Config::get('enums.stream_status.Closed')]);

            DB::commit();
            flash()->success('Stream was successfully SignedOff!!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Stream update was NOT successful!');
        }

        return redirect()->route('close.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function progress_streams () {

        $streams = Stream::wherein('streams.status', [1, 2, 3, 4, 5])->with('comments', 'opco', 'modules', 'user', 'tickets')
            ->count();


        $pmo = User::join('streams', 'users.id', 'streams.pmo_id')->join('modules', 'modules.id', 'streams.module_id')->join('tickets', 'tickets.id', 'streams.ticket_id')->where('streams.status', 1)->get();
        $tester = User::join('streams', 'users.id', 'streams.tester_id')->join('modules', 'modules.id', 'streams.module_id')->join('tickets', 'tickets.id', 'streams.ticket_id')->where('streams.status', 2)->get();
        $analyst = User::join('streams', 'users.id', 'streams.analyst_id')->join('modules', 'modules.id', 'streams.module_id')->join('tickets', 'tickets.id', 'streams.ticket_id')->where('streams.status', 3)->get();
        $qa = User::join('streams', 'users.id', 'streams.qa_id')->join('modules', 'modules.id', 'streams.module_id')->join('tickets', 'tickets.id', 'streams.ticket_id')->where('streams.status', 4)->get();
        $hod = User::join('streams', 'users.id', 'streams.hod_id')->join('modules', 'modules.id', 'streams.module_id')->join('tickets', 'tickets.id', 'streams.ticket_id')->where('streams.status', 5)->get();

        return view('close.progress_streams', compact('pmo', 'tester', 'analyst', 'qa', 'hod', 'streams'));
    }



    public function closed_streams () {

        $streams = Stream::where('status', '11')->with('comments', 'opco', 'modules', 'user', 'tickets')->paginate(10);
        return view('close.closed_streams')->with('streams', $streams);
    }

    public function pdf_files () {

        $streams = Stream::where('status', '11')->with('comments', 'opco', 'modules', 'user', 'tickets')->paginate(10);
        return view('close.pdf_files')->with('streams', $streams);
    }

}