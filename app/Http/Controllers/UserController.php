<?php
namespace App\Http\Controllers;

use App\User;
use App\Role;
use App\Opco;
use App\Permission;
use App\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Intervention\Image\File;
use DB;
use Config;
use Hash;
use langleyfoxall\passwordrules\PasswordRules;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UserController extends Controller
{
    use Authorizable;

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

        $department = DB::table('departments')->pluck('department', 'id');
        $result = User::where('status', '!=', 2)->get();
        return view('user.index', compact('result','department'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name', 'id');
        $opco = Opco::pluck('opco', 'id');
        $department = DB::table('departments')->pluck('department', 'id');

        return view('user.new', compact('roles','opco', 'department'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'bail|required|min:2',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
           'department' => 'required',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'opco_id' => 'required',
            'roles' => 'required|min:1'
        ]);
        
        // hash password
        $request->merge(['password' => bcrypt($request->get('password'))]);
        // Create the user
        if ( $user = User::create($request->except('roles', 'permissions')) ) {
            $this->syncPermissions($request, $user);
            flash('User has been created.');
        } else {
            flash()->error('Unable to create user.');
        }
        return redirect()->route('users.index');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $user = User::find($id);
        $roles = Role::pluck('name', 'id');
        $opco = Opco::pluck('opco', 'id');
        $permissions = Permission::all('name', 'id');
        $department = DB::table('departments')->pluck('department', 'id');
        
        if(!Auth::user()->roles->contains('1') && Auth::user()->id != $id){
               $user = User::find(Auth::user()->id);
          return view('user.edit', compact('user', 'roles', 'opco','department', 'permissions'));
        } else if(Auth::user()->roles->contains('1')){
            if($user->roles->contains('1')){
                $user = User::find(Auth::user()->id);
                 return view('user.edit', compact('user', 'roles', 'opco','department', 'permissions'));
            } else {
            return view('user.edit', compact('user', 'roles', 'opco','department', 'permissions'));
            }
        }

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

        $this->validate($request, [
            'name' => 'bail|required|min:2',
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'roles' => 'required|min:1',
          // 'department' => 'required',
            'opco_id' => 'required'
        ]);
        // Get the user
        $user = User::findOrFail($id);
        $nm = $user->name;
        $nm = preg_replace("/[^a-zA-Z]/", "", $nm);

        if ($request->hasFile('avatar'))
        {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $avatar->getClientOriginalExtension();
            // Delete current image before uploading new image
            if ($user->avatar !== 'avatar.jpg') {
                $file = public_path('/profile/' . $user->avatar);

                if (File::exists($file)) {
                    unlink($file);
                }

            }
            Image::make($avatar)->resize(300,300)->save(public_path('/profile/' . $filename));
            $user->avatar = $filename;
            $user->save();
        }


              // Update user
        $user->fill($request->except('roles', 'permissions','opco', 'password'));

        // check for password change
        if($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }

        // Password validation
        if(isset($request->password)){
            $this->validate($request, [
                'password' => PasswordRules::optionallyChangePassword($nm),
                'password_confirmation' => 'required'
            ]);

            // Check for previous password history
            $checkhash = DB::table('password_history')->select('password')->where('user_id', $request->user)->get();
            // Break if old password found 
            foreach($checkhash as $hash) {
                if (Hash::check($request->password, $hash->password)){
                    return back()->withErrors(['password' => ['The password entered must not be an old password.']]);
                }
            }

        if($request->department == null){
        DB::table('users')->where('id', $id)->update([
                'password' => bcrypt($request->password),
                'department' => null
            ]);

        } else {
            DB::table('users')->where('id', $id)->update([
                'password' => bcrypt($request->password),
                'department' => $request->department
            ]);
            }

            
             // Password history count
            $count = DB::table('password_history')->where('user_id', $request->user)->count();
            // If password history records more than 5, replace with latest old pw
            if($count >= 5) {
               $getid = DB::table('password_history')->select('id')->where('user_id', $request->user)->oldest('created_at')->first();
                DB::table('password_history')->where('id', $getid->id)->update([ 
                    'user_id' => $request->user,
                    'password' => bcrypt($request->password),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } else 
            {    DB::table('password_history')->insert([ 
                    'user_id' => $request->user,
                    'password' => bcrypt($request->password),
                ]);
            }

            DB::commit();
        }

        // Handle the user roles
        $this->syncPermissions($request, $user);
        $user->save();
        flash()->success('User has been updated.');
        return redirect()->route('users.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function destroy($id)
    {
        if (Auth::user()->id == $id) {
            flash()->warning('Deletion of currently logged in user is not allowed :(')->important();
            return redirect()->back();
        }

        DB::table('users')->where('id', $id)->update([
            'status' => Config::get('enums.user_status.Deleted'),
        ]);

        flash()->success('User has been deleted');
        return redirect()->back();
    }
    /**
     * Sync roles and permissions
     *
     * @param Request $request
     * @param $user
     * @return string
     */
    private function syncPermissions(Request $request, $user)
    {
        // Get the submitted roles
        $roles = $request->get('roles', []);
        $permissions = $request->get('permissions', []);
        // Get the roles
        $roles = Role::find($roles);
        // check for current role changes
        if( ! $user->hasAllRoles( $roles ) ) {
            // reset all direct permissions for user
            $user->permissions()->sync([]);
        } else {
            // handle permissions
            $user->syncPermissions($permissions);
        }
        $user->syncRoles($roles);
        return $user;
    }

    public function deactivate($id)
    {
        if (Auth::user()->id == $id) {
            flash()->warning('Deactivating of currently logged in user is not allowed :(')->important();
            return redirect()->back();
        }

        DB::table('users')->where('id', $id)->update([
            'status' => Config::get('enums.user_status.Inactive'),
        ]);
        flash()->success('User has been deactivated');
        return redirect()->route('users.index');
    }

    public function activate($id)
    {
        if (Auth::user()->id == $id) {
            flash()->warning('Activating of currently logged in user is not allowed :(')->important();
            return redirect()->back();
        }

        DB::table('users')->where('id', $id)->update([
            'status' => Config::get('enums.user_status.Active'),
        ]);
        flash()->success('User has been activated');
        return redirect()->route('users.index');
    }
}
