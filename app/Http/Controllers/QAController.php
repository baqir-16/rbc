<?php

namespace App\Http\Controllers;

use App\Stream;
use Illuminate\Http\Request;
use DB;
use Config;
use Auth;

// Main controller for QA functions
class QAController extends Controller
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

    // Return all ticket assigned to the QA
    public function index()
    {
        // display tickets information where status is at QA and assigned to the user
        $qa_forward_status = [];
        $where = ['status' => Config::get('enums.stream_status.QA'), 'qa_id' => Auth::user()->id];
        $streams = Stream::with('comments', 'opco', 'modules', 'user', 'tickets')->where($where)->paginate(10);
        foreach($streams as $stream){
            $count = DB::connection('mongodb')->collection('scan_results')
                ->where('is_verified', '=', 0)
                ->where('stream_id', '=', $stream->id)
                ->where('risk', '!=', 1)
                ->count();
            array_push($qa_forward_status, $count);
        }
        return view('QA.index')
            ->with('streams', $streams)
            ->with('qa_forward_status', $qa_forward_status);
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
        // store QA's update into the database
        DB::beginTransaction();

        try {

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'is_verified' => (int)$request->is_verified,
                'revalidate' => (int)$request->revalidate,
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
            }else{
                return redirect()->action('ReviewController@index', ['id' => $request->stream_id]);
            }

            DB::commit();
            flash()->success('Issue was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT updated successfully');
        }
        return redirect()->action('ReviewController@index', ['id' => $request->stream_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // show tickets information
        $streams = Stream::with('comments', 'opco', 'modules', 'user', 'tickets')->paginate(10);;
        return view('QA.edit')->with('streams', $streams);
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
    public function update(Request $request, Stream $stream)
    {
        // forward ticket to HoD
    DB::beginTransaction();

    try {
        DB::table('streams')
            ->where('id', $request->id)
            ->update(['status' => Config::get('enums.stream_status.HOD'),
                'hod_assigned_date' => date('Y-m-d H:i:s', time())]);

        DB::commit();
        flash()->success('Stream was successfully forwarded to HoD!');
    } catch (Exception $e) {
        DB::rollback();
        $request->session()->flash('alert-danger', 'Stream update was NOT successful!');
    }

    return redirect()->route('QA.index');
    }

    public function backward(Request $request, Stream $stream)
    {
        // revert ticket back to analyst
        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['status' => Config::get('enums.stream_status.Analyst')]);

            DB::commit();
            flash()->success('Stream was successfully sent back to Analyst!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Stream update was NOT successful!');
        }

        return redirect()->route('QA.index');
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

    public function modify(Request $request)
    {
        // store QA's update into the database
        DB::beginTransaction();

        try {
            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }else{
                return redirect()->action('ReviewController@index', ['id' => $request->stream_id]);
            }

            DB::commit();
            flash()->success('Issue was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT updated successfully');
        }
        return redirect()->action('ReviewController@index', ['id' => $request->stream_id]);
    }

    /*
    Save changes of scan results depending on the type of the file uploaded 
    */
    public function modifyappscan(Request $request)
    {
        DB::beginTransaction();

        try {
            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }else{
                return redirect()->action('ReviewController@index', ['id' => $request->stream_id]);
            }

            DB::commit();
            flash()->success('Issue was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT updated successfully');
        }
        return redirect()->action('ReviewController@index', ['id' => $request->stream_id]);
    }

    public function modifyburp(Request $request)
    {
        DB::beginTransaction();

        try {
            if(isset($request->comment)) {
                DB::table('comments')->insert([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'issue_id' => $request->_id,
                        'comments' => $request->comment,
                        'status' => 1,
                    ]
                );
            }else{
                return redirect()->action('ReviewController@index', ['id' => $request->stream_id]);
            }

            DB::commit();
            flash()->success('Issue was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT updated successfully');
        }
        return redirect()->action('ReviewController@index', ['id' => $request->stream_id]);
    }

    public function modifyNexpsoe(Request $request)
    {

        DB::beginTransaction();

        try {

            DB::connection('mongodb')->collection('scan_results')->where('_id', $request->_id)->update([
                'is_verified' => $request->is_verified,
                'revalidate' => $request->revalidate,
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
            }else{
                return redirect()->action('ReviewController@index', ['id' => $request->stream_id]);
            }

            DB::commit();
            flash()->success('Issue was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Issue was NOT updated successfully');
        }
        return redirect()->action('ReviewController@index', ['id' => $request->stream_id]);
    }

}
