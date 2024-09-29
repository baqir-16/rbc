<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Input;
use File;
use DB;
use SSH;
use Auth;
use Config;
use phpseclib\Net\SFTP;
use Hash;
use langleyfoxall\passwordrules\PasswordRules;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

// Main controller for user profile functions
class ProfileController extends Controller
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

    // Returns profile information of the logged in user
    public function index()
    {
        // show user's own profile
        $profile = DB::table('users')->where('id', Auth::user()->id)->get();

        return view('profile.index', compact('profile', 'opco'));
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

    // Update logged in user profile
    public function store(Request $request)
    {
        // update user's profile image and insert image filename into db
        DB::beginTransaction();

        try {
            if(isset($request->img_filename)) {
                $filename = 'img_' . time() . '_' . rand(100, 999);
                SSH::into('MDB_SERVER')->put($request->img_filename->getRealPath(), '/var/www/html/profile_img/'.$filename);
            }

            DB::table('users')->where('id', Auth::user()->id)->update([
                'avatar' => $filename,
            ]);

            DB::commit();
            flash()->success('Profile Picture was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Profile Picture was NOT updated successfully');
        }
        return redirect()->action('ProfileController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */

    // Return and display user profile for edit view
    public function edit($id)
    {
        // show and display user's information to edit
        $user = User::find($id);
        if (Auth::user()->id != $id){
            $user = User::find(Auth::user()->id);
          return view('profile.edit', compact('user'));
          
        } else {
        
        return view('profile.edit', compact('user'));

        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */

    // Store updated user profile data
    public function update(Request $request, $id)
    {
        // store user's updated information into db
        //  // Get the user
        $user = User::findOrFail($id);
        $nm = $user->name;
        $nm = preg_replace("/[^a-zA-Z]/", "", $nm);
         // Password validation
        if(isset($request->password)){
            $this->validate($request, [
                'password' => PasswordRules::optionallyChangePassword($nm),
                'password_confirmation' => 'required'
            ]);

            // Check for previous password history
            $checkhash = DB::table('password_history')->select('password')->where('user_id', $id)->get();
            // Break if old password found 
            foreach($checkhash as $hash) {
                if (Hash::check($request->password, $hash->password)){
                    return back()->withErrors(['password' => ['The password entered must not be an old password.']]);
                }
            }

            DB::table('users')->where('id', Auth::user()->id)->update([
                'password' => bcrypt($request->password),
            ]);

            // Password history count
            $count = DB::table('password_history')->where('user_id', $id)->count();
            // If password history records more than 5, replace with latest old pw
            if($count >= 5) {
               $getid = DB::table('password_history')->select('id')->where('user_id', $id)->oldest('created_at')->first();
                DB::table('password_history')->where('id', $getid->id)->update([ 
                    'user_id' => $id,
                    'password' => bcrypt($request->password),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } else 
            {    DB::table('password_history')->insert([ 
                    'user_id' => $id,
                    'password' => bcrypt($request->password),
                ]);
            }

            DB::commit();
        }
        // add image filename into database if exists
        if(isset($request->img_filename)) {
            $sftp = new SFTP($_ENV['MONGO_DB_HOST']);

            if (!$sftp->login($_ENV['MONGO_DB_SERVER_USERNAME'], $_ENV['MONGO_DB_SERVER_PASSWORD'])) {
                throw new Exception('SFTP Login failed');
            }

            $sftp->mkdir('/var/www/html/profile_img');

            $filename = 'img_' . time() . '_' . rand(100, 999);
            SSH::into('MDB_SERVER')->put($request->img_filename->getRealPath(), '/var/www/html/profile_img/' . $filename);

            DB::table('users')->where('id', Auth::user()->id)->update([
                'avatar' => $filename,
            ]);

            DB::commit();
        }

        $this->validate($request, [
            'name' => 'bail|required|min:2',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
        // update name if isset
        if(isset($request->name)){
            DB::table('users')->where('id', Auth::user()->id)->update([
                'name'=> $request->name,
            ]);
        }
        // update email if isset
        if(isset($request->email)){
            DB::table('users')->where('id', Auth::user()->id)->update([
                'email'=> $request->email,
            ]);
        }
        flash()->success('Profile has been updated.');
        return redirect()->route('profiles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
