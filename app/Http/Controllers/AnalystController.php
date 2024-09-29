<?php

namespace App\Http\Controllers;

use App\Stream;
use App\Ticket;
use Illuminate\Http\Request;
use DB;
use Config;
use Auth;
use SSH;
use phpseclib\Net\SFTP;
use MongoDB\BSON\UTCDateTime;

class AnalystController extends Controller
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
    //To retrieve data and display in Analyst main page
    public function index()
    {
        
        $analyst_forward_status = [];
        $issue_count_array = [];
        $where = ['status' => Config::get('enums.stream_status.Analyst'), 'analyst_id' => Auth::user()->id];
        $streams = Stream::with('comments', 'opco', 'modules', 'user', 'tickets')->where($where)->paginate(10);

        foreach($streams as $stream){
            $count = DB::connection('mongodb')->collection('scan_results')
                ->where('is_validated', '=', 0)
                ->where('stream_id', '=', $stream->id)
                ->where('risk', '!=', 1)
                ->count();

            $issue_count = DB::connection('mongodb')->collection('scan_results')
                ->where('stream_id', '=', $stream->id)
                ->count();

            array_push($analyst_forward_status, $count);
            array_push($issue_count_array, $issue_count);
        }

        return view('Analyst.index')
            ->with('streams', $streams)
            ->with('issue_count_array', $issue_count_array)
            ->with('analyst_forward_status', $analyst_forward_status);
    }

    //Export findings in CSV format
    public function createcsv(Request $request)
    {
        return view('Report.create')
            ->with('stream_id', $request->id);
    }

    // Upload findings in CSV format
    public function issuecsv(Request $request)
    {
        $this->validate($request, [
            'risk' => 'required',
            'host' => 'required',
            'name' => 'required',
        ]);

        $stream = DB::table('streams')->where('id', $request->stream_id)->get();
        $ticket = DB::table('tickets')->where('id', $stream[0]->ticket_id)->get();


        DB::beginTransaction();

        try {
            $upload_filename_array = [];
//
            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/img');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->insert([
                'stream_id' => (int)$request->stream_id,
                'module_id' => $stream[0]->module_id,
                'opco_id' => $ticket[0]->opco_id,
                'department' => $ticket[0]->department,
                'risk' => (int)$request->risk,
                'category' => (int)$request->category,
                'host' => $request->host,
                'protocol' => $request->protocol,
                'plugin_output' => $request->plugin_output,
                'port' => $request->port,
                'name' => $request->name,
                'synopsis' => $request->synopsis,
                'description' => $request->description,
                'solution' => $request->solution,
                'false_positive' => 0,
                'is_validated' => 0,
                'is_verified' => 0,
                'revalidate' => 0,
                'reverify' => 0,
                'validation_date' => 0,
                'verification_date' => 0,
                'revalidation_date' => 0,
                'reverification_date' => 0,
                'remediation_date' => 0,
                'rem_officer_rem_status' => 0,
                'rem_officer_rem_date' => 0,
                'rem_pmo_closure_status' => 0,
                'rem_pmo_closure_date' => 0,
                'reported_date' => new UTCDateTime(strtotime(date("m/d/Y")) * 1000),
                'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'img_filename' => $upload_filename_array,
            ]);

            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }

            DB::commit();
            flash()->success('Issue was successfully created');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT created successfully');
        }
        return redirect()->action('ReportController@index', ['id' => $request->stream_id]);
    }

    public function createxml(Request $request)
    {
        return view('Report.createxml')
            ->with('stream_id', $request->id);;
    }

    // Upload findings in XML format
    public function issuexml(Request $request)
    {
        $this->validate($request, [
            'risk' => 'required',
            'host' => 'required',
            'name' => 'required',
        ]);

        $stream = DB::table('streams')->where('id', $request->stream_id)->get();
        $ticket = DB::table('tickets')->where('id', $stream[0]->ticket_id)->get();

        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/img');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->insert([
                'stream_id' => (int)$request->stream_id,
                'module_id' => $stream[0]->module_id,
                'opco_id' => $ticket[0]->opco_id,
                'department' => $ticket[0]->department,
                'name' => $request->name,
                'ModuleName' => $request->ModuleName,
                'host' => $request->host,
                'risk'=> (int)$request->risk,
                'category'=> (int)$request->category,
                'Affects'=> $request->Affects,
                'Details' => $request->Details,
                'Description' => $request->Description,
                'Recommendation' => $request->Recommendation,
                'Request' => $request->Request,
                'Response' => $request->Response,
                'Impact'=> $request-> Impact,
                'false_positive' => 0,
                'is_validated' => 0,
                'is_verified' => 0,
                'revalidate' => 0,
                'reverify' => 0,
                'validation_date' => 0,
                'verification_date' => 0,
                'revalidation_date' => 0,
                'reverification_date' => 0,
                'remediation_date' => 0,
                'rem_officer_rem_status' => 0,
                'rem_officer_rem_date' => 0,
                'rem_pmo_closure_status' => 0,
                'rem_pmo_closure_date' => 0,
                'reported_date' => new UTCDateTime(strtotime(date("m/d/Y")) * 1000),
                'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'img_filename' => $upload_filename_array,
            ]);

            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }

            DB::commit();
            flash()->success('Issue was successfully created');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT created successfully');
        }
        return redirect()->action('ReportController@index', ['id' => $request->stream_id]);
    }

    // Upload Nexpose findings
    public function issueNexpose(Request $request)
    {
        $this->validate($request, [
            'risk' => 'required',
            'host' => 'required',
            'name' => 'required',
        ]);

        $stream = DB::table('streams')->where('id', $request->stream_id)->get();
        $ticket = DB::table('tickets')->where('id', $stream[0]->ticket_id)->get();


        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/img');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->insert([
                'stream_id' => (int)$request->stream_id,
                'module_id' => $stream[0]->module_id,
                'opco_id' => $ticket[0]->opco_id,
                'department' => $ticket[0]->department,
                'name' => $request->name,
                'host' => $request->host,
                'port' => $request->port,
                'risk'=> (int)$request->risk,
                'category'=> (int)$request->category,
                'OS' => $request->OS,
                'OS_version' => $request->OS_version,
                'description' => $request->description,
                'fix' => $request->fix,
                'summary' => $request->summary,
                'false_positive' => 0,
                'is_validated' => 0,
                'is_verified' => 0,
                'revalidate' => 0,
                'reverify' => 0,
                'validation_date' => 0,
                'verification_date' => 0,
                'revalidation_date' => 0,
                'reverification_date' => 0,
                'remediation_date' => 0,
                'rem_officer_rem_status' => 0,
                'rem_officer_rem_date' => 0,
                'rem_pmo_closure_status' => 0,
                'rem_pmo_closure_date' => 0,
                'reported_date' => new UTCDateTime(strtotime(date("m/d/Y")) * 1000),
                'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'img_filename' => $upload_filename_array,
            ]);

            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }

            DB::commit();
            flash()->success('Issue was successfully created');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT created successfully');
        }
        return redirect()->action('ReportController@index', ['id' => $request->stream_id]);
    }

    // Upload AppScan findings    
    public function issueappscan(Request $request)
    {
        $this->validate($request, [
            'risk' => 'required',
            'host' => 'required',
            'name' => 'required',
        ]);

        $stream = DB::table('streams')->where('id', $request->stream_id)->get();
        $ticket = DB::table('tickets')->where('id', $stream[0]->ticket_id)->get();

        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/img');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->insert([
                'stream_id' => (int)$request->stream_id,
                'module_id' => $stream[0]->module_id,
                'opco_id' => $ticket[0]->opco_id,
                'department' => $ticket[0]->department,
                'name' => $request->name,
                'host' => $request->host,
                'port' => $request->port,
                'risk'=> (int)$request->risk,
                'category'=> (int)$request->category,
                'Description' => $request->Description,
                'Recommendation' => $request->Recommendation,
                'false_positive' => 0,
                'is_validated' => 0,
                'is_verified' => 0,
                'revalidate' => 0,
                'reverify' => 0,
                'validation_date' => 0,
                'verification_date' => 0,
                'revalidation_date' => 0,
                'reverification_date' => 0,
                'remediation_date' => 0,
                'rem_officer_rem_status' => 0,
                'rem_officer_rem_date' => 0,
                'rem_pmo_closure_status' => 0,
                'rem_pmo_closure_date' => 0,
                'reported_date' => new UTCDateTime(strtotime(date("m/d/Y")) * 1000),
                'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'img_filename' => $upload_filename_array,
            ]);

            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }

            DB::commit();
            flash()->success('Issue was successfully created');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT created successfully');
        }
        return redirect()->action('ReportController@index', ['id' => $request->stream_id]);
    }

    // Upload Burpsuite findings
    public function issueburp(Request $request)
    {
        $this->validate($request, [
            'risk' => 'required',
            'host' => 'required',
            'name' => 'required',
        ]);

        $stream = DB::table('streams')->where('id', $request->stream_id)->get();
        $ticket = DB::table('tickets')->where('id', $stream[0]->ticket_id)->get();

        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/img');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->insert([
                'stream_id' => (int)$request->stream_id,
                'module_id' => $stream[0]->module_id,
                'opco_id' => $ticket[0]->opco_id,
                'department' => $ticket[0]->department,
                'name' => $request->name,
                'host' => $request->host,
                'risk'=> (int)$request->risk,
                'category'=> (int)$request->category,
                'background' => $request->background,
                'remediation' => $request->remediation,
                'false_positive' => 0,
                'is_validated' => 0,
                'is_verified' => 0,
                'revalidate' => 0,
                'reverify' => 0,
                'validation_date' => 0,
                'verification_date' => 0,
                'revalidation_date' => 0,
                'reverification_date' => 0,
                'remediation_date' => 0,
                'rem_officer_rem_status' => 0,
                'rem_officer_rem_date' => 0,
                'rem_pmo_closure_status' => 0,
                'rem_pmo_closure_date' => 0,
                'reported_date' => new UTCDateTime(strtotime(date("m/d/Y")) * 1000),
                'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'img_filename' => $upload_filename_array,
            ]);

            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }

            DB::commit();
            flash()->success('Issue was successfully created');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT created successfully');
        }
        return redirect()->action('ReportController@index', ['id' => $request->stream_id]);
    }

    public function createappscan(Request $request)
    {
        return view('Report.createappscan')
            ->with('stream_id', $request->id);
    }

    public function createburp(Request $request)
    {
        return view('Report.createburp')
            ->with('stream_id', $request->id);
    }

    public function createnexpose(Request $request)
    {
        return view('Report.createnexpose')
            ->with('stream_id', $request->id);
    }

    // Store updated finding data by Analyst
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/img');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'risk' => (int)$request->risk,
                'category' => (int)$request->category,
                'vul_category' => (int)$request->vul_category_id,
                'port' => $request->port,
                'plugin_output' => $request->plugin_output,
                'description' => $request->description,
                'solution' => $request->solution,
                'false_positive' => (int)$request->false_positive,
                'is_validated' => (int)$request->is_validated,
                'img_filename' => $upload_filename_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
            ]);

            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }

            DB::commit();
            flash()->success('Issue was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT updated successfully');
        }
        return redirect()->action('ReportController@index', ['id' => $request->stream_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function show(Stream $stream)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function edit(Stream $stream)
    {
        dd("EDIT");
    }

    // Forward ticket to QA
    public function update(Request $request, Stream $stream)
    {
        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['status' => Config::get('enums.stream_status.QA'),
                    'qa_assigned_date' => date('Y-m-d H:i:s', time())]);

            DB::commit();
            flash()->success('Stream was successfully forwarded to QA!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Stream update was NOT successful!');
        }
        return redirect()->route('Analyst.index');
    }

    // Backward ticket to Tester
    public function backward(Request $request, Stream $stream)
    {
        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['status' => Config::get('enums.stream_status.Tester')]);

            DB::commit();
            flash()->success('Stream was successfully sent back to Tester!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Stream update was NOT successful!');
        }
        return redirect()->route('Analyst.index');
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

    // Update finding details
    public function modify(Request $request)
    {
        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/img');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'risk' => (int)$request->risk,
                'category' => (int)$request->category,
                'vul_category' => (int)$request->vul_category_id,
                'name' => $request->name,
                'ModuleName' => $request->ModuleName,
                'Details' => $request->Details,
                'Impact'=> $request->Impact,
                'Description'=> $request->Description,
                'Recommendation'=> $request->Recommendation,
                'false_positive' => (int)$request->false_positive,
                'is_validated' => (int)$request->is_validated,
                'img_filename' => $upload_filename_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
            ]);

            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }

            DB::commit();
            flash()->success('Issue was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT updated successfully');
        }
        return redirect()->action('ReportController@index', ['id' => $request->stream_id]);
    }

    // Update AppScan details
    public function modifyappscan(Request $request)
    {
        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/img');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'name' => $request->name,
                'host' => $request->host,
                'port' => $request->port,
                'risk' => (int)$request->risk,
                'category' => (int)$request->category,
                'vul_category' => (int)$request->vul_category_id,
                'Description' => $request->Description,
                'Recommendation'=> $request-> Recommendation,
                'false_positive' => (int)$request->false_positive,
                'is_validated' => (int)$request->is_validated,
                'img_filename' => $upload_filename_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
            ]);

            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }

            DB::commit();
            flash()->success('Issue was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT updated successfully');
        }
        return redirect()->action('ReportController@index', ['id' => $request->stream_id]);
    }

    // Update Burpsuite findings
    public function modifyburp(Request $request)
    {
        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/img');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'name' => $request->name,
                'risk' => (int)$request->risk,
                'category' => (int)$request->category,
                'vul_category' => (int)$request->vul_category_id,
                'background' => $request->background,
                'remediation' => $request->remediation,
                'false_positive' => (int)$request->false_positive,
                'is_validated' => (int)$request->is_validated,
                'img_filename' => $upload_filename_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
            ]);

            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }

            DB::commit();
            flash()->success('Issue was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT updated successfully');
        }
        return redirect()->action('ReportController@index', ['id' => $request->stream_id]);
    }

    // Update Nexpose findings
    public function modifynexpose(Request $request)
    {
        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/img');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'risk' => (int)$request->risk,
                'category' => (int)$request->category,
                'vul_category' => (int)$request->vul_category_id,
                'description' => $request->description,
                'fix' => $request->fix,
                'summary' => $request->summary,
                'false_positive' => (int)$request->false_positive,
                'is_validated' => (int)$request->is_validated,
                'img_filename' => $upload_filename_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
            ]);

            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }

            DB::commit();
            flash()->success('Issue was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT updated successfully');
        }
        return redirect()->action('ReportController@index', ['id' => $request->stream_id]);
    }
}
