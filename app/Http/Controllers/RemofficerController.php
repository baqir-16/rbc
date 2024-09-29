<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Input;
use App;
use Response;
use File;
use ScanResults;
use DB;
use SSH;
use Auth;
use DateTime;
use phpseclib\Net\SFTP;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;
use Excel;

// Main controller for the remediation officer functions
class RemofficerController extends Controller
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

    // Return findings to be remediated by the remediation officer
    public function index()
    {   
        $departments = DB::table('departments')->pluck('department', 'id');
        $hod_signoff_dt = NULL;
        $hod_signoff_date = NULL;
        $days_open = NULL;
        $enums = array_flip(Config::get('enums.severity_status'));
        $enums1 = array_flip(Config::get('enums.mdb_stream_status'));

        //if is admin show findings from all departments
        if (Auth::user()->roles->contains('1')) {

            $result = DB::connection('mongodb')->collection('scan_results')->where([
            'opco_id' => (int)Auth::user()->opco_id
        ])->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('risk', '!=', 1)->where('department','>=', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->get()->toArray();

            $result1 = DB::connection('mongodb')->collection('scan_results')->where([
            'opco_id' => (int)Auth::user()->opco_id
        ])->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 3)->where('risk', '!=', 1)->where('department','>=', 0)->where('rem_pmo_closure_status', 3)->where('false_positive', 0)->get()->toArray();

        } else {
        // show findings from user's own departments
        $result = DB::connection('mongodb')->collection('scan_results')->where([
            'opco_id' => (int)Auth::user()->opco_id
        ])->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('risk', '!=', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->get()->toArray();

        $result1 = DB::connection('mongodb')->collection('scan_results')->where([
            'opco_id' => (int)Auth::user()->opco_id
        ])->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 3)->where('risk', '!=', 1)->where('rem_pmo_closure_status', 3)->where('false_positive', 0)->where('department', Auth::user()->department)->get()->toArray();
         }
        // check each results and see how many days it's opened 
        foreach($result as $key => $res) {
            $now = Carbon::now();
            $reported_date = Carbon::parse(date('Y-m-d', $res['reported_date']->toDateTime()->format('U')));
            $days_open = $reported_date->diffInDays($now);
            $res['reported_date'] = $days_open;
            $result[$key]['days_open'] = $days_open;
        }
        // check each results and see how many days it's opened
        foreach($result1 as $key => $res) {
            $now = Carbon::now();
            $reported_date = Carbon::parse(date('Y-m-d', $res['reported_date']->toDateTime()->format('U')));
            $days_open = $reported_date->diffInDays($now);
            $res['reported_date'] = $days_open;
            $result1[$key]['days_open'] = $days_open;
        }

        // display information to blade
        $vul_categories = DB::table('vul_categories')->pluck('name', 'id')->toArray();
        return view('Remofficer.index', compact('departments'))
            ->with('result', $result)
            ->with('result1', $result1)
            ->with('enums', $enums)
            ->with('enums1', $enums1)
            ->with('hod_signoff_date', $hod_signoff_date)
            ->with('vul_categories', $vul_categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // Store updated finding details by the remediation officer
    public function store(Request $request)
    {
        // store new information provided from rem officer
        $this->validate($request, [
            'vul_category_id' => 'required',
            'datetime' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $upload_filename_array = [];
            // if ticket status is open store image
            if($request->status == Config::get('enums.mdb_stream_status.Open')) {
                if (isset($request->img_filename)) {
                    foreach ($request->img_filename as $file) {
                        $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                        if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                            throw new Exception('SFTP Login failed');
                        }

                        $sftp->mkdir('/var/www/html/img');

                        $filename = 'img_' . time() . '_' . rand(100, 999);
                        array_push($upload_filename_array, $filename);
                        SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/' . $filename);
                    }
                }
            }else if($request->status == Config::get('enums.mdb_stream_status.Exception')){
                //if ticket status is exception store pdf
                if (isset($request->pdf_filename)) {
                    foreach ($request->pdf_filename as $file) {
                        $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                        if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                            throw new Exception('SFTP Login failed');
                        }

                        $sftp->mkdir('/var/www/html/pdf_exception_reports');

                        $filename = 'pdf_' . time() . '_' . rand(100, 999);
                        array_push($upload_filename_array, $filename);
                        SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/pdf_exception_reports/' . $filename);
                    }
                }
            }
            // update information in db
            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'rem_officer_img_filename' => $upload_filename_array,
                'risk' => (int)$request->risk,
                'rem_officer_rem_status' => (int)$request->status,
                'rem_pmo_closure_status' => (int)$request->status,
                'target_fix_date' => $request->datetime,
                'vul_category' => (int)$request->vul_category_id,
                'comment' => $request->comment,
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
        return redirect()->action('RemofficerController@index', ['id' => $request->stream_id]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // Display selected finding details
    public function show($_id)
    {
        // retrieve information and display for rem officer to edit
        $departments = DB::table('departments')->pluck('department', 'id');
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();

        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');
        return view('Remofficer.edit', compact('issue','enums','vul_categories', 'all_comments', 'departments'));
    }
    /*
    Retrieve and show scan results depending on file type
    */

    public function showxml($_id)
    {
        $departments = DB::table('departments')->pluck('department', 'id');
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();

        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');
        return view('Remofficer.remshowxml', compact('issue','enums','vul_categories', 'all_comments', 'deparemnts'));
    }

    public function shownexpose($_id)
    {
        $departments = DB::table('departments')->pluck('department', 'id');
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();

        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');
        return view('Remofficer.shownexpose', compact('issue','enums','vul_categories', 'all_comments', 'departments'));
    }

    public function showappscan($_id)
    {
        $departments = DB::table('departments')->pluck('department', 'id');
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();

        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');
        return view('Remofficer.showappscan', compact('issue','enums','vul_categories', 'all_comments', 'departments'));
    }

    public function showburp($_id)
    {
        $departments = DB::table('departments')->pluck('department', 'id');
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();

        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');
        return view('Remofficer.showburp', compact('issue','enums','vul_categories', 'all_comments', 'departments'));
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
    public function update(Request $request, $id)
    {
        //
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

    // Forward selected finding to remediation PMO
    public function forward(Request $request){
        // forward tickets
        $id = $request->id;
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['rem_officer_rem_date' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),'rem_officer_rem_status'=>1,'rem_pmo_closure_status'=>0]);

        return redirect()->action('RemofficerController@index', ['id' => $request->stream_id]);
    }

    // Forward all selected finding to remediation PMO
    public function forwardAll(Request $request)
    {
        $ids = explode(',', $request['allvals']);
        foreach ($ids as $id) {
            DB::connection('mongodb')->collection('scan_results')->where('_id', $id)->update(['rem_officer_rem_date' =>new UTCDateTime(strtotime(date("Y/m/d"))*1000),'rem_officer_rem_status' => 1,'rem_pmo_closure_status'=>0]);
        }
        return response(['msg' => 'Successful'], 200)->header('Content-Type', 'application/json');
    }

    public function modify(Request $request)
    {
        // rem officer edit tickets and store into db
        $this->validate($request, [
            'vul_category_id' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if($request->status == Config::get('enums.mdb_stream_status.Open')) {
                if (isset($request->img_filename)) {
                    foreach ($request->img_filename as $file) {
                        $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                        if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                            throw new Exception('SFTP Login failed');
                        }

                        $sftp->mkdir('/var/www/html/img');

                        $filename = 'img_' . time() . '_' . rand(100, 999);
                        array_push($upload_filename_array, $filename);
                        SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/' . $filename);
                    }
                }
            }else if($request->status == Config::get('enums.mdb_stream_status.Exception')){
                if (isset($request->pdf_filename)) {
                    foreach ($request->pdf_filename as $file) {
                        $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                        if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                            throw new Exception('SFTP Login failed');
                        }

                        $sftp->mkdir('/var/www/html/pdf_exception_reports');

                        $filename = 'pdf_' . time() . '_' . rand(100, 999);
                        array_push($upload_filename_array, $filename);
                        SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/pdf_exception_reports/' . $filename);
                    }
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'rem_officer_img_filename' => $upload_filename_array,
                'risk' => (int)$request->risk,
                'rem_officer_rem_status' => (int)$request->status,
                'rem_pmo_closure_status' => (int)$request->status,
                'target_fix_date' => $request->datetime,
                'vul_category' => (int)$request->vul_category_id,
                'comment' => $request->comment,
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
        return redirect()->action('RemofficerController@index', ['id' => $request->stream_id]);
    }

    public function modifynexpose(Request $request)
    {
        $this->validate($request, [
            'vul_category_id' => 'required',
            'datetime' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if($request->status == Config::get('enums.mdb_stream_status.Open')) {
                if (isset($request->img_filename)) {
                    foreach ($request->img_filename as $file) {
                        $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                        if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                            throw new Exception('SFTP Login failed');
                        }

                        $sftp->mkdir('/var/www/html/img');

                        $filename = 'img_' . time() . '_' . rand(100, 999);
                        array_push($upload_filename_array, $filename);
                        SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/' . $filename);
                    }
                }
            }else if($request->status == Config::get('enums.mdb_stream_status.Exception')){
                if (isset($request->pdf_filename)) {
                    foreach ($request->pdf_filename as $file) {
                        $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                        if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                            throw new Exception('SFTP Login failed');
                        }

                        $sftp->mkdir('/var/www/html/pdf_exception_reports');

                        $filename = 'pdf_' . time() . '_' . rand(100, 999);
                        array_push($upload_filename_array, $filename);
                        SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/pdf_exception_reports/' . $filename);
                    }
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'rem_officer_img_filename' => $upload_filename_array,
                'risk' => (int)$request->risk,
                'rem_officer_rem_status' => (int)$request->status,
                'rem_pmo_closure_status' => (int)$request->status,
                'target_fix_date' => $request->datetime,
                'vul_category' => (int)$request->vul_category_id,
                'comment' => $request->comment,
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
        return redirect()->action('RemofficerController@index', ['id' => $request->stream_id]);
    }

    public function modifyappscan(Request $request)
    {
        $this->validate($request, [
            'vul_category_id' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if($request->status == Config::get('enums.mdb_stream_status.Open')) {
                if (isset($request->img_filename)) {
                    foreach ($request->img_filename as $file) {
                        $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                        if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                            throw new Exception('SFTP Login failed');
                        }

                        $sftp->mkdir('/var/www/html/img');

                        $filename = 'img_' . time() . '_' . rand(100, 999);
                        array_push($upload_filename_array, $filename);
                        SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/' . $filename);
                    }
                }
            }else if($request->status == Config::get('enums.mdb_stream_status.Exception')){
                if (isset($request->pdf_filename)) {
                    foreach ($request->pdf_filename as $file) {
                        $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                        if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                            throw new Exception('SFTP Login failed');
                        }

                        $sftp->mkdir('/var/www/html/pdf_exception_reports');

                        $filename = 'pdf_' . time() . '_' . rand(100, 999);
                        array_push($upload_filename_array, $filename);
                        SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/pdf_exception_reports/' . $filename);
                    }
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'rem_officer_img_filename' => $upload_filename_array,
                'risk' => (int)$request->risk,
                'rem_officer_rem_status' => (int)$request->status,
                'rem_pmo_closure_status' => (int)$request->status,
                'target_fix_date' => $request->datetime,
                'vul_category' => (int)$request->vul_category_id,
                'comment' => $request->comment,
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
        return redirect()->action('RemofficerController@index', ['id' => $request->stream_id]);
    }

    public function modifyburp(Request $request)
    {
        $this->validate($request, [
            'vul_category_id' => 'required',
            'datetime' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if($request->status == Config::get('enums.mdb_stream_status.Open')) {
                if (isset($request->img_filename)) {
                    foreach ($request->img_filename as $file) {
                        $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                        if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                            throw new Exception('SFTP Login failed');
                        }

                        $sftp->mkdir('/var/www/html/img');

                        $filename = 'img_' . time() . '_' . rand(100, 999);
                        array_push($upload_filename_array, $filename);
                        SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/' . $filename);
                    }
                }
            }else if($request->status == Config::get('enums.mdb_stream_status.Exception')){
                if (isset($request->pdf_filename)) {
                    foreach ($request->pdf_filename as $file) {
                        $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                        if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                            throw new Exception('SFTP Login failed');
                        }

                        $sftp->mkdir('/var/www/html/pdf_exception_reports');

                        $filename = 'pdf_' . time() . '_' . rand(100, 999);
                        array_push($upload_filename_array, $filename);
                        SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/pdf_exception_reports/' . $filename);
                    }
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'rem_officer_img_filename' => $upload_filename_array,
                'risk' => (int)$request->risk,
                'rem_officer_rem_status' => (int)$request->status,
                'rem_pmo_closure_status' => (int)$request->status,
                'target_fix_date' => $request->datetime,
                'vul_category' => (int)$request->vul_category_id,
                'comment' => $request->comment,
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
        return redirect()->action('RemofficerController@index', ['id' => $request->stream_id]);
    }

    public function exportOpco(Request $request)
    {
        
        $res = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)->where(['opco_id' => (int)Auth::user()->opco_id])->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->get()->toArray();
        $res1 = DB::connection('mongodb')->collection('scan_results')->where('risk', '!=', 1)->where(['opco_id' => (int)Auth::user()->opco_id])->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 3)->where('rem_pmo_closure_status', 3)->where('false_positive', 0)->where('department', Auth::user()->department)->get()->toArray();

        foreach($res as $key => $reset) {
            $now = Carbon::now();
            $reported_date = Carbon::parse(date('Y-m-d', $reset['reported_date']->toDateTime()->format('U')));
            $days_open = $reported_date->diffInDays($now);
            $reset['reported_date'] = $days_open;
            $res[$key]['days_open'] = $days_open;
        }

        foreach($res1 as $key => $reset1) {
            $now = Carbon::now();
            $reported_date = Carbon::parse(date('Y-m-d', $reset1['reported_date']->toDateTime()->format('U')));
            $days_open = $reported_date->diffInDays($now);
            $reset1['reported_date'] = $days_open;
            $res1[$key]['days_open'] = $days_open;
        }

        $totalres = array_merge($res, $res1);
        
        $totalres = array_map(function($totalres) {
            $departments = DB::table('departments')->pluck('department', 'id');
            $enums1 = array_flip(Config::get('enums.mdb_stream_status'));
            $stats = $enums1[$totalres['rem_officer_rem_status']];
            $enums3 = array_flip(Config::get('enums.severity_status'));
            $r = $enums3[$totalres['risk']];
            $enums2 = array_flip(Config::get('enums.vuln_category'));
            $opco = array_flip(Config::get('enums.opco_switch'));
//            $vul = $enums2[$totalres['vul_category']];
            $reportDate = $totalres['hod_signoff_date'] -> toDateTime()->format('Y-m-d H:i:s');
            return array(
                'db id' => (string)$totalres['_id'],
                'vulnerability name' => $totalres['name'],
                'Department' => $departments[$totalres['department']], 
                'Status' => $stats,
                'Risk' => $r,
                'Host' => $totalres['host'],
                'Port' => $totalres['port'],
                'Days Open' => $totalres['days_open'],
                'Reported on' => $reportDate,
                'Category' => isset($totalres['vul_category']) ? $enums2[$totalres['vul_category']] : null,
                'Critical Asset' => isset($totalres['critical_asset']) ? $totalres['critical_asset'] : null,
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
        $filename = ' Remediation Officer ' . $date;

        Excel::create($filename, function($excel) use($totalres) {
            $excel->sheet('Sheet1', function($sheet) use($totalres) {
                $sheet->fromArray($totalres);
            });
        })->download('xlsx');

        return redirect()->action('RemofficerController@index');
    }

    // Update status of remediation of selected finding
    public function updateRemediated(Request $request)
    {
        if (Input::hasFile('file') ) {
            foreach(Input::file('file') as $file) {
                $path = $file->getRealPath();

                $data = Excel::load($path, function ($reader) {
                })->get();
    
                if (!empty($data) && $data->count()) {
                    foreach ($data as $key => $value) {
                        if($value->vulnerability_name == NULL || $value->vulnerability_name == "")
                            break;

                        $departments = DB::table('departments')->pluck('department', 'id');
                        foreach($departments as $key => $dep){
                            if($dep == $value->department)
                            $value->department = $key;
                        }             

                        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');

                        foreach($vul_categories as $key => $cat){
                            if($cat == $value->category)
                                $value->category = $key;
                        }

                        if($value->category == 0){
                            $value->category = null;
                        }
                
                        switch ($value->status) {
                            case "Open":
                                $value->status = Config::get('enums.mdb_stream_status.Open');
                                break;
                            case "Close":
                                $value->status = Config::get('enums.mdb_stream_status.Close');
                                break;
                            case "Revalidation":
                                $value->status = Config::get('enums.mdb_stream_status.Revalidation');
                                break;
                            case "Exception":
                                $value->status = Config::get('enums.mdb_stream_status.Exception');
                                break;
                        }


//                        $enums1 = array_flip(Config::get('enums.mdb_stream_status'));
//                        $stats = $enums1[$value->status];

//                        dd((int)$value->status);

//                        $duplicate_check = DB::connection('mongodb')->collection('scan_results')
//                            ->where('_id', '=', $value->db_id)
//                            ->where('rem_officer_rem_status', '!=', (int)$value->status)
////                            ->where('port', '=', $value->port)
////                            ->where('name', '=', $value->name)
////                            ->where('rem_pmo_closure_status', '=', 0)
//                            ->count();

//                        dd($duplicate_check);

//                        if ($duplicate_check > 0) {
                            DB::beginTransaction();

                            if($value->category == null){

                            try {
                              DB::connection('mongodb')->collection('scan_results')
                                ->where('_id', $value->db_id)
                                ->where('rem_officer_rem_status', '=', (int)$value->status)

                            ->where('rem_officer_rem_status', '!=', 3)
                                ->update([
                                    'rem_officer_rem_date' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                    'rem_officer_rem_status' => (int)$value->status,
                                    'rem_pmo_closure_status' => 0,
                                     'target_fix_date' => date($value->target_fix_date),
                                    'vul_category' => null,
                                    // 'department' =>(int)$value->department
                                 
                                ]);

                                DB::commit();
                                $request->session()->flash('alert-success', 'Successful!');
//                                $newFinding = true;
                            } catch (Exception $e) {
                                DB::rollback();
                                $request->session()->flash('alert-danger', 'NOT successful!');
                            }
                        } else {
                            try {
                              DB::connection('mongodb')->collection('scan_results')
                                ->where('_id', $value->db_id)
                                ->where('rem_officer_rem_status', '=', (int)$value->status)

                            ->where('rem_officer_rem_status', '!=', 3)
                                ->update([
                                    'rem_officer_rem_date' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                                    'rem_officer_rem_status' => (int)$value->status,
                                    'rem_pmo_closure_status' => 0,
                                     'target_fix_date' => date($value->target_fix_date),
                                    'vul_category' => (int)$value->category,
                                    'department' =>(int)$value->department
                                 
                                ]);

                                DB::commit();
                                $request->session()->flash('alert-success', 'Successful!');
//                                $newFinding = true;
                            } catch (Exception $e) {
                                DB::rollback();
                                $request->session()->flash('alert-danger', 'NOT successful!');
                            }
                        }
//                        }
                    }
                }
            }
        }
        return redirect()->action('RemofficerController@index');
    }

    public function deleteImage(Request $request, $_id, $img, $type){
        $res = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_img_filename',$img)->update(['rem_officer_img_filename' => NULL]);

        if($res == 1){
            SSH::into('MDB_SERVER')->delete('/var/www/html/img/' . $img);
            $request->session()->flash('alert-success', 'Image delete was successful!');
        }else{
            $request->session()->flash('alert-danger', 'Image delete was NOT successful!');
        }
        return redirect($type.'/'.$_id);
    }
}