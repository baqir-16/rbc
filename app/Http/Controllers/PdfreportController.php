<?php

namespace App\Http\Controllers;

use App\Authorizable;
use App\Pdfreport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfreportController extends Controller
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
    public function index()
    {
    
        $result = Pdfreport::latest()->with('user')->paginate();
        return view('pdfreport.index', compact('result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pdfreport.new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->user()->pdfreports()->create($request->all());

        flash('PDF has been saved');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pdfreport  $pdfreport
     * @return \Illuminate\Http\Response
     */
    public function show(Pdfreport $pdfreport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pdfreport  $pdfreport
     * @return \Illuminate\Http\Response
     */
    public function edit(Pdfreport $pdfreport)
    {
        $pdfreport = Pdfreport::findOrFail($pdfreport->id);

        return view('pdfreport.edit', compact('pdfreport'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pdfreport  $pdfreport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pdfreport $pdfreport)
    {

        $me = $request->user();

        if( $me->hasRole('Admin') ) {
            $pdfreport = Pdfreport::findOrFail($pdfreport->id);
        } else {
            $pdfreport = $me->pdfreports()->findOrFail($pdfreport->id);
        }

        $pdfreport->update($request->all());

        flash()->success('PDF has been updated.');

        return redirect()->route('pdfreports.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pdfreport  $pdfreport
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pdfreport $pdfreport)
    {
        $me = Auth::user();

        if( $me->hasRole('Admin') ) {
            $pdfreport = Pdfreport::findOrFail($pdfreport->id);
        } else {
            $pdfreport = $me->pdfreports()->findOrFail($pdfreport->id);
        }

         $pdfreport->delete();

        flash()->success('PDF has been deleted.');

        return redirect()->route('pdfreports.index');
    }
}
