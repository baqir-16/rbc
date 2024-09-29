<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Input;
use App;
use Response;
use File;
use ScanResults;
use DB;
use SSH;

// Controller for QA functionalities
class ReviewController extends Controller
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

    //Return all tickets assigned to the QA
    public function index(Request $request)
    {
        $result = DB::connection('mongodb')->collection('scan_results')->where('stream_id', (int)$request->id)->where('risk', '!=', 1)->get()->toArray();
        $enums = array_flip(Config::get('enums.severity_status'));

        return view('Review.index', compact('result','enums'));
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

    //Return selected finding details
    public function show($_id)
    {
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Review.edit', compact('issue','enums', 'all_comments'));
    }

    //Return selected finding details
    public function showxml($_id)
    {
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Review.reviewxml', compact('issue','enums', 'all_comments'));
    }

    //Return selected finding details of appscan
    public function showappscan($_id)
    {
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Review.reviewappscan', compact('issue','enums', 'all_comments'));
    }

    //Return selected finding details of nexpose
    public function shownexpose($_id)
    {
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Review.reviewnexpose', compact('issue','enums', 'all_comments'));
    }

    //Return selected finding details burpsuite
    public function showburp($_id)
    {
        $issue = DB::connection('mongodb')->collection('scan_results')->where('_id', $_id)->get()->first();
        $enums = array_flip(Config::get('enums.severity_status'));
        $all_comments = DB::table('comments')->where('issue_id', $_id)
            ->join('users', 'users.id', 'comments.user_id')
            ->select('comments.id', 'comments', 'username', 'issue_id',  'comments.created_at', 'comments.user_id')
            ->get();
        return view('Review.reviewburp', compact('issue','enums', 'all_comments'));
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
        return view('Review.edit');
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
    
    // Mark findings as reverified
    public function reverified(Request $request)
    {
        $result = DB::connection('mongodb')->collection('scan_results')->where(['stream_id' => (int)$request->id, 'revalidate' => 2 ])->paginate(500);
        $enums = array_flip(Config::get('enums.severity_status'));

        return view('Review.reverified', compact('result','enums'));
    }
}
