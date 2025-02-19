<?php

namespace App\Http\Controllers\Admin\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\CompanyDataService;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;


class UserController extends Controller
{


    function __construct(){
        $this->middleware('permission:role-list',['only'=>['index','create','edit','insert','update']]);
     }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $br_ids = (Auth::user()->branch_office_id) == 1 ? [1,2] : [Auth::user()->branch_office_id];
        $data = User::select('id','name','email','status','emp_auto_id')->whereIn('branch_office_id',$br_ids)->where('id', '!=', Auth::user()->id)->orderBy('id', 'DESC')->paginate(50);

        return view('admin.users.index', compact('data'))->with('i', ($request->input('page', 1) - 1) * 50);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        if(Auth::user()->id == 1)
        $company_branches = (new CompanyDataService())->getACompanyListOfBranchForDropdownlist([1,2]);
        else
        $company_branches = (new CompanyDataService())->getACompanyListOfBranchForDropdownlist([Auth::user()->branch_office_id]);
        return view('admin.users.create', compact('roles','company_branches'));
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'roles' => 'required'
        ]);
        $input = $request->all();
        $input['status'] = 1;
        $input['branch_office_id'] =(int) $request->branch_office;
       // dd($request->all(),$input);

        if($request['empAutoIDForThisUser']){

            $input['is_emp'] = 1;
            $input['emp_auto_id'] = intval($request['empAutoIDForThisUser']);

        }else {

            $input['is_emp'] = 0;
            $input['emp_auto_id'] = 144; // default emp id 100 , auto id 144
        }


        $anEmp = (new AuthenticationDataService())->checkThisEmployeeAlreadyHasUserAccountByEmpAutoId( $request['empAutoIDForThisUser']);

        if ($anEmp) {
            Session::flash('error', 'This Employee has an User Account');
            return Redirect()->back();
        } else {

            $input['password'] = Hash::make($input['password']);
            $user = User::create($input);
            $user->assignRole($request->input('roles'));
             // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(30,1, Auth::user()->id,$input['emp_auto_id'],null);

            return redirect()->route('users.index')->with('success', 'User created successfully');

        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id

     * @return \Illuminate\Http\Response
     */

    // public function show($id)
    // {
    //     $user = User::find($id);
    //     return view('users.show',compact('user'));
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        foreach ($userRole as $arole){
            $existing_role = $arole;
        }
        return view('admin.users.edit', compact('user', 'roles', 'userRole','existing_role'));
    }

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

     // user Information Update from admin user
    public function update(Request $request, $id)
    {

        if(is_null($id)){
            Session::flash('error', 'User Not Found ');
            return redirect()->back();
        }

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'required|unique:users,phone_number,' . $id,
            'roles' => 'required'
        ]);
        $user = User::find($id);

        if (!is_null($request->get('name')) &&  !is_null($request->get('email')) &&  !is_null($request->get('phone_number')))
        {
            $activeInactive = $request->has('lock_checkbox') == true ? 1:0;
            (new AuthenticationDataService())->updateUserProfileInformationByAdmin($id,$request->get('name'),$request->get('email'),$request->get('phone_number'), $activeInactive);
             // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(18,2, Auth::user()->id, $user->emp_auto_id,null);

        }
        // user role update

         $user->syncRoles($request->input('roles'));

        if (is_null($request->get('password')) ||  is_null($request->get('password_confirmation')) )
        {
             // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(21,2, Auth::user()->id, $user->emp_auto_id,null);
           // Session::flash('error', 'Password and Confirm Password are not Match');
            return redirect()->back();
        }

      return  $this->changePasswordSave($request);
    }

    private function changePasswordSave(Request $request)
    {


        $user = User::find($request->id);
        $new_password = Hash::make($request->get('password'));
        $conf_password = Hash::make($request->get('password_confirmation'));


        if (strcmp($new_password, $conf_password) == 0)
        {
            Session::flash('error', 'Password Did Not Match');
            return redirect()->back();
        }

        $user->forceFill([
            'password' => Hash::make(request()->input('password')),
        ])->save();
         // login user activities record
        (new AuthenticationDataService())->InsertLoginUserActivity(17,2, Auth::user()->id, $user->emp_auto_id,null);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

  // show login user profile
    public function profile()
    {
        $user = User::find(Auth::user()->id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('admin.users.user-profile', compact('user', 'roles', 'userRole'));
    }

    // Login User Profile information Udpate Request . Own profile update
    public function profileInformationUpdate(Request $request)
    {

            $this->validate($request, [
                'name' => 'required',
                'phone_number' => 'required',
                'current_password' => 'required|string',
                'password' => 'required|confirmed|min:8|string'
            ]);
            $auth = Auth::user();

        // The passwords matches
            if (!Hash::check($request->get('current_password'), $auth->password))
            {
                return back()->with('error', "Current Password is Invalid");
            }

        // Current password and new password same
            if (strcmp($request->get('current_password'), $request->password) == 0)
            {
                return redirect()->back()->with("error", "New Password cannot be same as your current password.");
            }

            $user =  User::find($auth->id);
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $uplodedPath = (new  UploadDownloadController())->uploadEmployeeProfilePhoto($file, $user->profile_image);
                $user->profile_image = $uplodedPath;
            }
            $user->password =  Hash::make($request->password);
            $update = $user->save();

             // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(17,2, Auth::user()->id, $user->emp_auto_id,null);

            if ($update) {
                Auth::logout();
                return redirect('/login');
            } else {
                Session::flash('error', 'Update Operation Failed, Please try Again');
                return redirect()->back();
            }

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
       // User::find($id)->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
