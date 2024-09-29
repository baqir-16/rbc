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
use MongoDB\BSON\UTCDateTime;
use phpseclib\Net\SFTP;
use Illuminate\Support\Facades\Auth;

// Main controller for the remediation pmo functions
class RempmoController extends Controller
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

    // Return the list of all remediated findings from the remediation officer
    public function index()
    {

        // if is admin show findings from all departments
        if (Auth::user()->roles->contains('Admin')) {

        $total = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->count();
        $total2 =  DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 2)->where('rem_pmo_closure_status', 2)->where('false_positive', 0)->count();
        if($total == 0 or $total2 == 0){
            $result = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->paginate(500);
            $result1 = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 2)->where('rem_pmo_closure_status', 2)->where('false_positive', 0)->paginate(500);
        }
        else{
            $result = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->paginate($total);
            $result1 = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 2)->where('rem_pmo_closure_status', 2)->where('false_positive', 0)->paginate($total2);
        }
    } else {
        // if not admin show findings from user's departments
        $total = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->count();
        $total2 =  DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 2)->where('rem_pmo_closure_status', 2)->where('false_positive', 0)->where('department', Auth::user()->department)->count();
        if($total == 0 or $total2 == 0){
            $result = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->paginate(500);
            $result1 = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 2)->where('rem_pmo_closure_status', 2)->where('false_positive', 0)->where('department', Auth::user()->department)->paginate(500);
        }
        else{
            $result = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->where('department', Auth::user()->department)->paginate($total);
            $result1 = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 2)->where('rem_pmo_closure_status', 2)->where('false_positive', 0)->where('department', Auth::user()->department)->paginate($total2);
        }
    }
       
        $departments = DB::table('departments')->pluck('department', 'id');
        $opco_array = DB::connection('mysql')->table('opco')->get()->toArray();
        $enums = array_flip(Config::get('enums.severity_status'));
        $enums1 = array_flip(Config::get('enums.mdb_stream_status'));

        return view('Rempmo.index', compact('result','result1','enums','enums1', 'departments'))->with('opco_array', json_decode(json_encode($opco_array), true));
    }

    // Return the list of all closed findings
    public function closed_findings()
    {
        // if is admin show closed findings from all departments
        if (Auth::user()->roles->contains('Admin')) {
         $total = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 1)->where('false_positive', 0)->count();
        if($total == 0){
            $result = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 1)->where('false_positive', 0)->paginate(500);
        }
        else{
            $result = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 1)->where('false_positive', 0)->paginate($total);
        }
    } else {
        // if is not admin show closed findings from user's department
         $total = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 1)->where('false_positive', 0)->where('department', Auth::user()->department)->count();
        if($total == 0){
            $result = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 1)->where('false_positive', 0)->where('department', Auth::user()->department)->paginate(500);
        }
        else{
            $result = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 1)->where('false_positive', 0)->where('department', Auth::user()->department)->paginate($total);
        }
    }
        $departments = DB::table('departments')->pluck('department', 'id');
        $opco_array = DB::connection('mysql')->table('opco')->get()->toArray();
        $enums = array_flip(Config::get('enums.severity_status'));
        $enums1 = array_flip(Config::get('enums.mdb_stream_status'));
        $enums2 = array_flip(Config::get('enums.vuln_category'));

        return view('Rempmo.closed_index', compact('result','enums','enums1','enums2', 'departments'))->with('opco_array', json_decode(json_encode($opco_array), true));
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
    public function store(Request $request){
        // remediation pmo update edited information into db
        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login(credentials('mongo_db_server_username'), credentials('mongo_db_server_password'))) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/pdf_reports');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'rem_pmo_img_filename' => $upload_filename_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'comment' => $request->comment,
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
        return redirect()->action('RempmoController@index', ['id' => $request->stream_id]);
    }
    /*
    Update scan results into db depending on file types
    */
    public function modifynexpose(Request $request)
    {

        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login(credentials('mongo_db_server_username'), credentials('mongo_db_server_password'))) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/pdf_reports');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'rem_pmo_img_filename' => $upload_filename_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'comment' => $request->comment,
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
        return redirect()->action('RempmoController@index', ['id' => $request->stream_id]);
    }

    public function modify(Request $request)
    {

        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login(credentials('mongo_db_server_username'), credentials('mongo_db_server_password'))) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/pdf_reports');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'rem_pmo_img_filename' => $upload_filename_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'comment' => $request->comment,
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
        return redirect()->action('RempmoController@index', ['id' => $request->stream_id]);
    }

    public function modifyappscan(Request $request)
    {

        DB::beginTransaction();

        try {
            $upload_filename_array = [];

            if(isset($request->img_filename)) {
                foreach ($request->img_filename as $file) {
                    $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

                    if (!$sftp->login(credentials('mongo_db_server_username'), credentials('mongo_db_server_password'))) {
                        throw new Exception('SFTP Login failed');
                    }

                    $sftp->mkdir('/var/www/html/pdf_reports');

                    $filename = 'img_' . time() . '_' . rand(100, 999);
                    array_push($upload_filename_array, $filename);
                    SSH::into('MDB_SERVER')->put($file->getRealPath(), '/var/www/html/img/'.$filename);
                }
            }

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'rem_pmo_img_filename' => $upload_filename_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                'comment' => $request->comment,
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
        return redirect()->action('RempmoController@index', ['id' => $request->stream_id]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($_id)
    {
        // retrieve information for rempmo to edit tickets
        $departments = DB::table('departments')->pluck('department', 'id');
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Rempmo.edit', compact('issue','enums', 'all_comments', 'departments'));
    }

    /*
     retrieve scan results to display for rempmo to edit depending on file type
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
        return view('Rempmo.pmoshowxml', compact('issue','enums', 'all_comments', 'departments'));
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
        return view('Rempmo.pmoshownexpose', compact('issue','enums', 'all_comments', 'departments'));
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
        return view('Rempmo.pmoshowappscan', compact('issue','enums', 'all_comments', 'departments'));
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
        return view('Rempmo.pmoshowburp', compact('issue','enums', 'all_comments', 'departments'));
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

    public function closeone(Request $request){
        // close ticket
        $id = $request->id;
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['rem_pmo_closure_date'  => new UTCDateTime(strtotime(date("Y/m/d"))*1000),'rem_pmo_closure_status'=>1,'rem_officer_rem_status'=>1]);
        return redirect()->action('RempmoController@index', ['id' => $request->stream_id]);
    }

    public function openofficer(Request $request){
        $id = $request->id;
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['rem_pmo_closure_status'=>0, 'rem_officer_rem_status'=>0, 'rem_officer_revalidation_date'=>new UTCDateTime(strtotime(date("Y/m/d"))*1000)]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    public function closeAll(Request $request)
    {
        // close all tickets
        $ids = explode(',', $request['allvals']);
        foreach ($ids as $id) {
            DB::connection('mongodb')->collection('scan_results')->where('_id', $id)->update(['rem_pmo_closure_date' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),'rem_pmo_closure_status'=>1,'rem_officer_rem_status'=>1]);
        }
        return redirect()->action('RempmoController@index', ['ids' => $request->stream_id]);
    }

    public function openAll(Request $request)
    {
        // open all tickets
        $ids = explode(',', $request['allvals']);
        foreach ($ids as $id) {
            DB::connection('mongodb')->collection('scan_results')->where('_id', $id)->update(['rem_pmo_closure_status'=>0,'rem_officer_rem_status'=>0]);
        }
        return redirect()->action('RempmoController@index', ['ids' => $request->stream_id]);
    }
}
