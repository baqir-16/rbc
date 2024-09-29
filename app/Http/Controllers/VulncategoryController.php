<?php

namespace App\Http\Controllers;

use App\Vulncategory;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VulncategoryController extends Controller
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

    // Return list of vulnerability categories
    public function index()
    {
        $result = Vulncategory::with('user')->paginate();
        return view('vulncategory.index', compact('result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    // Display create new vulnerabilities category view
    public function create()
    {
        return view('vulncategory.new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // Store new vulnerability category
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:5',
        ]);

        $request->user()->vulncategories()->create($request->all());

        flash('Category has been saved');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Vulncategory  $vulncategory
     * @return \Illuminate\Http\Response
     */
    public function show(Vulncategory $vulncategory)
    {
    //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vulncategory  $vulncategory
     * @return \Illuminate\Http\Response
     */

    // Edit existing vulnerability category
    public function edit(Vulncategory $vulncategory)
    {
        $vulncategory = Vulncategory::findOrFail($vulncategory->id);

        return view('vulncategory.edit', compact('vulncategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vulncategory  $vulncategory
     * @return \Illuminate\Http\Response
     */
    // Update existing vulnerability category
    public function update(Request $request, Vulncategory $vulncategory)
    {
        $this->validate($request, [
            'name' => 'required|min:5',
        ]);

        $me = $request->user();

        if( $me->hasRole('Admin') ) {
            $vulncategory = Vulncategory::findOrFail($vulncategory->id);
        } else {
            $vulncategory = $me->vulncategories()->findOrFail($vulncategory->id);
        }

        $vulncategory->update($request->all());

        flash()->success('Category has been updated.');

        return redirect()->route('vulncategories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vulncategory  $vulncategory
     * @return \Illuminate\Http\Response
     */
    // Delete existing vulnerability category
    public function destroy(Vulncategory $vulncategory)
    {
        $me = Auth::user();

        if( $me->hasRole('Admin') ) {
            $vulncategory = Vulncategory::findOrFail($vulncategory->id);
        } else {
            $vulncategory = $me->vulncategories()->findOrFail($vulncategory->id);
        }

        $vulncategory->delete();

        flash()->success('Category has been deleted.');

        return redirect()->route('vulncategories.index');
    }
}
