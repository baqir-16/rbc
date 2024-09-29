<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;
use DB;
use Config;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
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
//     standard CRUD is performed in this controller for departments
        $results = DB::table('departments')->get();

        return view('department.index', compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // show information needed to create information
        $department = DB::table('departments')->pluck('department', 'id');

        return view('department.new', compact('department'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // store created department information into database
        DB::beginTransaction();

        try {

            $this->validate($request, [
                'department' => 'required'
            ]);

            DB::table('departments')->insert([
                    'department' => $request->department
                ]
            );

            DB::commit();
            flash()->success('Department was successfully created!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Department creation was NOT successful!');
        }
        return redirect()->route('departments.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // retrieve information from department table to show
        $results = DB::table('departments')->get();

        return view('department.index', compact('results'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $department =  Department::findOrFail($id);

        return view('department.edit', compact('department'));
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

        // update edited department information
        DB::beginTransaction();

        try {

            $this->validate($request, [
                'department' => 'required'
            ]);

            DB::table('departments')->where('id', $request->id)->update([
                    'department' => $request->department
                ]
            );

            DB::commit();
            flash()->success('Department was successfully updated!');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Update was NOT successful!');
        }
        return redirect()->route('departments.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {

    //   DB::table('departments')->where('id', $id)->delete();

    //     flash()->success('Department has been deleted.');

    //     return redirect()->back();
    // }
}
