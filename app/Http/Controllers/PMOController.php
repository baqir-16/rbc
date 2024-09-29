<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Stream;
use DB;
use Config;
use Auth;

// Main controller for the Cyber PMO functions
class PMOController extends Controller
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

    // Returns all tickets under the cyber pmo
    public function index(Request $request)
    {
        // show list of tickets
        $username_arr = [];
        $forward_status = [];
        $streams = Stream::where('ticket_id', $request->id)
            ->where('status', '!=', Config::get('enums.stream_status.Disabled'))
            ->with('comments', 'opco', 'modules', 'user', 'tickets')->paginate(10);
        $stream_status_enums = array_flip(Config::get('enums.stream_status'));


        foreach ($streams as $key => $stream) {
            $tmp_arr = [];
            $tmp_status = true;

            $result = User::select('username')->where('id', $stream->tester_id)->get()->toArray();
            if (!empty($result)) {
                array_push($tmp_arr, $result[0]['username']);
            } else {
                array_push($tmp_arr, "Unassigned");
                $tmp_status = false;
            }

            $result = User::select('username')->where('id', $stream->analyst_id)->get()->toArray();
            if (!empty($result)) {
                array_push($tmp_arr, $result[0]['username']);
            } else {
                array_push($tmp_arr, "Unassigned");
                $tmp_status = false;
            }

            $result = User::select('username')->where('id', $stream->qa_id)->get()->toArray();
            if (!empty($result)) {
                array_push($tmp_arr, $result[0]['username']);
            } else {
                array_push($tmp_arr, "Unassigned");
                $tmp_status = false;
            }

            $result = User::select('username')->where('id', $stream->hod_id)->get()->toArray();
            if (!empty($result)) {
                array_push($tmp_arr, $result[0]['username']);
            } else {
                array_push($tmp_arr, "Unassigned");
                $tmp_status = false;
            }
            array_push($username_arr, $tmp_arr);
            array_push($forward_status, $tmp_status == true ? 1 : 0 );
        }
        return view('PMO.index')
            ->with('streams', $streams)
            ->with('stream_status_enums', $stream_status_enums)
            ->with('username_arr', $username_arr)
            ->with('forward_status', $forward_status);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // get needed information and display for PMO to create new tickets
        $user = User::all()->pluck('name', 'id');
        $tester = User::role('Tester')->pluck('name', 'id');
        $analyst= User::role('Analyst')->pluck('name', 'id');
        $qa = User::role('QA')->pluck('name', 'id');
        $streams = Stream::pluck('id');
        return view('PMO.assign_tasks')
            ->with('user', $user)
            ->with('streams', $streams)
            ->with('tester', $tester)
            ->with('analyst', $analyst)
            ->with('qa', $qa);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // insert newly created tickets into database
        $this->validate($request, array(
            'tester_id' => 'required',
            'analyst_id' => 'required',
            'qa_id' => 'required',
            'hod_id' => 'required'
        ));

        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('id', $request->id)
                ->update([
                    'tester_id' => $request->tester_id,
                    'analyst_id' => $request->analyst_id,
                    'qa_id' => $request->qa_id,
                    'hod_id' => $request->hod_id,
                ]);

            DB::commit();
            flash()->success('Task was successfully updated!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Task update was NOT successful!');
        }
        return redirect()->action('PMOController@index', ['id' => $request->ticket_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function show(Stream $stream)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Stream $stream)
    {
        // show needed information and display to edit a ticket
         $streams = Stream::where('id', $request->id)->pluck('department');
        $user = User::all()->where('department', $streams)->pluck('name', 'id');
        $tester = User::role('Tester')->where('department', $streams)->pluck('name', 'id');
        $analyst= User::role('Analyst')->where('department', $streams)->pluck('name', 'id');
        $qa = User::role('QA')->where('department', $streams)->pluck('name', 'id');
        $hod = User::role('HOD')->where('department', $streams)->pluck('name', 'id');
        $ticket_id = Stream::where('id', $request->id)->where('department', $streams)->pluck('ticket_id')->first();
        $stream_id = Stream::where('id', $request->id)->where('department', $streams)->pluck('id')->first();

        return view('PMO.assign_tasks')
            ->with('user', $user)
            ->with('ticket_id', $ticket_id)
            ->with('id', $stream_id)
            ->with('tester', $tester)
            ->with('analyst', $analyst)
            ->with('qa', $qa)
            ->with('hod', $hod)
            ->with('id', $request->id);
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
        // insert newly updated ticket into database
        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('id', $request->id)
                ->update(['status' => Config::get('enums.stream_status.Tester'),
                    'tester_assigned_date' => date('Y-m-d H:i:s', time())]);

            DB::commit();
            flash()->success('Task was successfully forwarded to Tester');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'This operation was NOT successful!');
        }

        return redirect()->back();
    }

    public function deleteTicket(Request $request, Stream $stream)
    {
        // delete ticket from database
        DB::beginTransaction();

        try {
            DB::table('streams')
                ->where('ticket_id', $request->id)
                ->update([
                    'status' => Config::get('enums.stream_status.Disabled'),
                    'updated_at' => date('Y-m-d H:i:s', time())
                ]);

            DB::table('tickets')
                ->where('id', $request->id)
                ->update([
                    'status' => Config::get('enums.ticket_status.Disabled'),
                    'updated_at' => date('Y-m-d H:i:s', time())
                ]);

            $stream = Stream::where('id', $request->id)->get();
            $where = ['id' => $stream[0]->tester_comments_id, 'user_id' => Auth::user()->id, 'stream_id' => $stream[0]->id];
            DB::connection('mysql')->table('comments')->where($where)->update([
                'status'    => 0,
                'updated_at'    => date('Y-m-d H:i:s', time()),
            ]);
            DB::connection('mongodb')->collection('scan_results')->where('stream_id', (Int)$request->id)->delete();

            DB::commit();
            flash()->success('Ticket was successfully deleted');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Ticket was NOT successfully deleted');
        }

        return redirect()->back();
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
