<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;
use App\Stream;
use App\Comment;
use App\Opco;
use App\User;
use App\Module;
use App\Authorizable;
use Illuminate\Support\Facades\Auth;
use DB;
use Config;
use Symfony\Component\VarDumper\Tests\Fixture\DumbFoo;

// Controller for Ticket Management by Cyber PMO
class TicketController extends Controller
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
    // Return all tickets created
    public function index()
    {
        $department = DB::table('departments')->pluck('department', 'id');
        $ticket_edit_status = [];
        $tickets = Ticket::with('comments', 'opco', 'user', 'modules', 'department')
            ->where('status', '!=', Config::get('enums.stream_status.Disabled'))
            ->paginate(10);

        foreach ($tickets as $ticket) {
            $stream_count = DB::table('streams')
                ->where('ticket_id', $ticket->id)
                ->where('status', '!=', Config::get('enums.stream_status.Disabled'))
                ->where('status', '!=', Config::get('enums.stream_status.PMO'))
                ->count();
            if($stream_count > 0)
                array_push($ticket_edit_status, false);
            else
                array_push($ticket_edit_status, true);
        }
        return view('Ticket.index', compact('department'))
            ->with('tickets', $tickets)
            ->with('ticket_edit_status', $ticket_edit_status);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
        // New ticket
    public function create()
    {
        $user = User::all();
        $department = DB::table('departments')->pluck('department', 'id');
        $comments = DB::table('comments')->pluck('comments', 'id');
        $modules = DB::table('modules')->pluck('name', 'id');
        $opco = DB::table('opco')->pluck('opco', 'id');
        $streams = DB::table('streams')->pluck('id');
        return view('Ticket.new')->with('user', $user)
            ->with('opco', $opco)
            ->with('comments', $comments)
            ->with('modules', $modules)
            ->with('streams', $streams)
            ->with('department', $department);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // Store newly created ticket
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'department' => 'required',
            'ref' => 'required|unique:tickets',
            'opco_id' => 'required',
            'module_id' => 'required',
        ]);

        if(!isset($request->comments))
            $request->comments = "";

        DB::beginTransaction();

        try {
            $request['user_id'] = Auth::user()->id;
            $request['status'] = 1;

            if(isset($request->comments)) {
                $comment_id = DB::table('comments')->insertGetId([
                        'user_id' => Auth::user()->id,
                        'stream_id' => 0,
                        'comments' => $request->comments,
                        'status' => 1,
                        'department' => $request->department,
                    ]
                );
            }

            $request['pmo_comments_id'] = $comment_id;

            $ticket = new Ticket();
            $ticket->fill($request->all());
            $ticket_id = DB::table('tickets')->insertGetId($ticket->toArray());

            foreach($request->module_id as $mod_id){
                DB::table('streams')->insert([
                        'ticket_id' => $ticket_id,
                        'module_id' => $mod_id,
                        'pmo_id' => Auth::user()->id,
                        'pmo_comments_id' => $comment_id,
                        'status' => 1,
                        'department' => $request->department
                    ]
                );
            }

            DB::commit();
            flash()->success('Ticket creation was successful!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Ticket creation was NOT successful!');
        }
//        Ticket::create($request->all());

        return redirect()->route('Ticket.index');
//            ->with('success','Ticket created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    //Display ticket edit view and populate existing data
    public function edit(Ticket $ticket, $ticket_id)
    {
        $selected_modules = [];
        $department = DB::table('departments')->pluck('department', 'id');
        $ticket = DB::table('tickets')->where('id', $ticket_id)->get();
        $streams = DB::table('streams')->where('ticket_id', $ticket_id)
            ->where('status', '!=', Config::get('enums.stream_status.Disabled'))->get();
        $modules = DB::table('modules')->pluck('name', 'id');

        foreach ($streams as $stream){
            array_push($selected_modules, $stream->module_id);
        }

        $comment = DB::table('comments')->where('id', $ticket[0]->pmo_comments_id)->get();
        $opco = DB::table('opco')->pluck('opco', 'id');

        return view('Ticket.edit')
            ->with('ticket', $ticket)
            ->with('opco', $opco)
            ->with('modules', $modules)
            ->with('selected_modules', $selected_modules)
            ->with('streams', $streams)
            ->with('comment', $comment)
            ->with('department', $department);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    //Update details of selected ticket
    public function update(Request $request, $id)
    {
        $ticket = DB::table('tickets')->where('id', $id)->get();

        $this->validate($request, [
            'title' => 'required',
            'ref' => 'required|unique:tickets,ref,'.$ticket[0]->id,
            'department' => 'required',
            'opco_id' => 'required',
            'module_id' => 'required',
        ]);

        if(!isset($request->comments))
            $request->comments = "";

        DB::beginTransaction();

        try {
            if(isset($request->comments)) {
                DB::table('comments')->where('id', $ticket[0]->pmo_comments_id)->update([
                        'comments' => $request->comments,
                        'updated_at' => date('Y-m-d H:i:s', time()),
                    ]
                );
            }

            $ticket_obj = new Ticket();
            $ticket_obj->fill($request->all());
            $ticket_obj->updated_at = time();
            DB::table('tickets')->where('id', $ticket[0]->id)->update($ticket_obj->toArray());

            DB::table('streams')->where('ticket_id', $ticket[0]->id)->update([
                    'status' => Config::get('enums.stream_status.Disabled'),
                    'updated_at' => date('Y-m-d H:i:s', time()),
                ]
            );

            foreach($request->module_id as $mod_id){
                DB::table('streams')->insert([
                        'ticket_id' => $id,
                        'module_id' => $mod_id,
                        'status' => Config::get('enums.stream_status.PMO'),
                        'updated_at' => date('Y-m-d H:i:s', time()),
                        'department' => $request->department
                    ]
                );
            }

            DB::commit();
            flash()->success('Ticket update was successful');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Ticket update was NOT successful');
        }
        return redirect()->route('Ticket.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
