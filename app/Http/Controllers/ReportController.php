<?php

namespace App\Http\Controllers;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Input;
use App;
use Response;
use File;
use Excel;
use ScanResults;
use DB;
use SnappyPDF;
use App\Stream;
use SSH;

// Controller for Analyst functionalities
class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //Return all tickets assigned to the Analyst
    public function index(Request $request)
    {
        $id = $request->id;
        $stream = Stream::where('id', $id)->first();
        $result = DB::connection('mongodb')->collection('scan_results')->where('stream_id', (int)$request->id)->where('risk', '!=', 1)->get()->toArray();
        $enums = array_flip(Config::get('enums.severity_status'));

        return view('Report.index', compact('result','enums', 'id', 'stream'));
    }

    //Return selected finding details
    public function show(Request $request, $_id)
    {
        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Report.edit', compact('issue', 'all_comments','vul_categories'));
    }

    //Return selected finding details
    public function showxml(Request $request, $_id)
    {
        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Report.editxml', compact('issue', 'all_comments','vul_categories'));
    }

    //Return selected finding details of nexpose
    public function shownexpose(Request $request, $_id)
    {
        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');
        $enums = array_flip(Config::get('enums.severity_status'));
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Report.editnexpose', compact('issue', 'enums', 'all_comments','vul_categories'));
    }

    //Return selected finding details of appscan
    public function shownappscan(Request $request, $_id)
    {
        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');
        $enums = array_flip(Config::get('enums.severity_status'));
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Report.editappscan', compact('issue', 'enums', 'all_comments','vul_categories'));
    }

    //Return selected finding details burpsuite
    public function shownburp(Request $request, $_id)
    {
        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');
        $enums = array_flip(Config::get('enums.severity_status'));
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Report.editburp', compact('issue', 'enums','all_comments','vul_categories'));
    }

    public function update(Request $request)
    {
        return redirect()->route('Report.edit');
    }

    // Analyst upload file of findings
    public function upload(Request $request)
    {
         $ticket = Ticket::where('id', $stream->ticket_id)->first();
        if (Input::hasFile('file')) {
            $path = Input::file('file')->getRealPath();

            $data = Excel::load($path, function ($reader) {
            })->get();

            if (!empty($data) && $data->count()) {
                foreach ($data as $key => $value) {

                    switch ($value->risk) {
                        case "None":
                            $value->risk = Config::get('enums.severity_status.Informational');
                            break;
                        case "Low":
                            $value->risk = Config::get('enums.severity_status.Low');
                            break;
                        case "Medium":
                            $value->risk = Config::get('enums.severity_status.Medium');
                            break;
                        case "High":
                            $value->risk = Config::get('enums.severity_status.High');
                            break;
                        case "Critical":
                            $value->risk = Config::get('enums.severity_status.Critical');
                            break;
                    }

                    $insert[] = [
                        'department' => $ticket->department,
                        'stream_id' => 0,
                        'analyst_id' => 0,
                        'qa_id' => 0,
                        'remediation_officer_id' => 0,
                        'plugin_id' => $value->plugin_id,
                        'cve' => $value->cve,
                        'cvss' => $value->cvss,
                        'risk' => $value->risk,
                        'host' => $value->host,
                        'protocol' => $value->protocol,
                        'port' => $value->port,
                        'name' => $value->name,
                        'synopsis' => $value->synopsis,
                        'description' => $value->description,
                        'solution' => $value->solution,
                        'see_also' => $value->see_also,
                        'plugin_output' => $value->plugin_output,
                        'false_positive' => 0,
                        'is_validated' => 0,
                        'is_verified' => 0,
                        'revalidate' => 0,
                        'reverify' => 0,
                        'closure_date' => 0,
                        'validation_date' => 0,
                        'verification_date' => 0,
                        'revalidation_date' => 0,
                        'reverification_date' => 0,
                        'remediation_date' => 0,
                        'created_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                        'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
                    ];
                }

                if (!empty($insert)) {
                    $status = DB::connection('mongodb')->collection('scan_results')->insert($insert);

                    if($status)
                        $request->session()->flash('alert-success', 'Upload was successful!');
                    else
                        $request->session()->flash('alert-danger', 'Upload was NOT successful!');

                    return redirect()->action('ReportController@index');
                }
            }
        }
    }

    // Validate all findings
    public function vulnvalidateAll(Request $request){
        $ids = explode(',',$request['allvals']);
        foreach ($ids as $id) {
            DB::connection('mongodb')->collection('scan_results')->where('_id', $id)->update(['validation_date' => time(), 'is_validated' => 1, 'analyst_id' => Auth::User()->id, 'revalidate' => 0]);
        }
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Validate selected finding
    public function vulnvalidate(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['validation_date'=>time(),'is_validated'=>1,'analyst_id'=>Auth::User()->id,'revalidate'=>0]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Unvalidate selected finding
    public function vulnunvalidate(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['validation_date'=>null,'is_validated'=>0,'analyst_id'=>0,'revalidate'=>0]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');

    }

    // Make all selected findings false positive
    public function fpAll(Request $request){
        $ids = explode(',',$request['allvals']);
        foreach ($ids as $id){
            DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['false_positive'=>1,'validation_date'=>time(),'is_validated'=>1,'analyst_id'=>Auth::User()->id,'revalidate'=>0]);
        }
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make selected finding false positive
    public function fp(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['false_positive'=>1,'validation_date'=>time(),'is_validated'=>1,'analyst_id'=>Auth::User()->id,'revalidate'=>0]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make selected finding not false positive
    public function unfp(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['false_positive'=>0]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make all findings verified
    public function vulnverifyAll(Request $request){
        $ids = explode(',',$request['allvals']);
        foreach ($ids as $id){
            DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['verification_date'=>time(),'is_verified'=>1,'qa_id'=>Auth::User()->id,'reverify'=>0]);
        }
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make selected finding verified
    public function vulnverify(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['verification_date'=>time(),'is_verified'=>1,'qa_id'=>Auth::User()->id,'reverify'=>0]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make selected finding unverified
    public function vulnunverify(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['verification_date'=>null,'is_verified'=>0,'qa_id'=>0]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }
//check
    public function vulnrevalidateAll(Request $request){
        $ids = explode(',',$request['allvals']);
        foreach ($ids as $id){
            DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['revalidation_date'=>time(),'is_verified'=>0,'is_validated'=>0,'revalidate'=>1]);
        }
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make all findings revalidated
    public function vulnrevalidate(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['revalidation_date'=>time(),'is_verified'=>0,'is_validated'=>0,'revalidate'=>1]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make selected finding revalidated
    public function vulnunrevalidate(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['is_validated'=>1,'revalidate'=>0]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make selected finding unrevalidated
    public function vulnisunrevalidate(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['is_validated'=>1,'revalidate'=>2]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make all findings unrevalidated
    public function vulnisunrevalidateAll(Request $request){
        $ids = explode(',',$request['allvals']);
        foreach ($ids as $id){
            DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['is_validated'=>1,'revalidate'=>2]);
        }
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make all findings reverified
    public function vulnreverifyAll(Request $request){
        $ids = explode(',',$request['allvals']);
        foreach ($ids as $id){
            DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['reverification_date'=>time(),'is_verified'=>1,'reverify'=>1,'is_validated'=>1,'revalidate'=>0]);
        }
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make selected finding reverified
    public function vulnreverify(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['reverification_date'=>time(), 'is_verified'=>1,'reverify'=>1,'is_validated'=>1,'revalidate'=>0]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make selected finding unreverified
    public function vulnunreverify(Request $request){
        $id = $request['id'];
        DB::connection('mongodb')->collection('scan_results')->where('_id',$id)->update(['is_verified'=>1,'reverify'=>0,'is_validated'=>0,'revalidate'=>1]);
        return response(['msg' => 'Successful'], 200)->header('Content-Type','application/json');
    }

    // Make selected finding revalidated
    public function revalidated(Request $request)
    {
        $result = DB::connection('mongodb')->collection('scan_results')->where(['stream_id' => (int)$request->id, 'revalidate' => 1 ])->paginate(500);
        $enums = array_flip(Config::get('enums.severity_status'));

        return view('Report.revalidated', compact('result','enums'));
    }

    // Remove finding PoC
    public function deleteImage(Request $request, $_id, $img){
        $res = DB::connection('mongodb')->collection('scan_results')->where('img_filename',$img)->update(['img_filename' => NULL]);

        if($res == 1){
            SSH::into('MDB_SERVER')->delete('/var/www/html/img/' . $img);
            $request->session()->flash('alert-success', 'Image delete was successful!');
        }else{
            $request->session()->flash('alert-danger', 'Image delete was NOT successful!');
        }
        return redirect('Report/'.$_id);
    }

    // Remove finding comment
    public function deletecomment(Request $request) {

        DB::table('comments')->where('id', '=', $request->id)->delete();


        flash('Comment has been deleted.');

        return redirect()->back();

    }
}
