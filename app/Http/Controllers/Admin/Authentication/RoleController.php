<?php

namespace App\Http\Controllers\Admin\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Session;
use DB;



class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id','DESC')->paginate(50);
        return view('admin.roles.index',compact('roles'))->with('i', ($request->input('page', 1) - 1) * 50);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get();
        return view('admin.roles.create',compact('permission'));
    }

    // only insert permission information to permission table
    public function createNewPermission(Request $request)
    {
        $new_permission = strtolower( $request->input('permission_name'));
        $all_permission = Permission::get();
        $isExist = false;
        foreach ($all_permission as $permission){
              $lowerCaseValue = strtolower($permission->name);
              if($lowerCaseValue == $new_permission)
                {
                    $isExist = true;
                    break;
                }
        }
         // login user activities record
        (new AuthenticationDataService())->InsertLoginUserActivity(29,1, Auth::user()->id,Auth::user()->emp_auto_id,null);
        
        if($isExist == false){
            Permission::create(['name' => $new_permission]);
            Session::flash('success', 'Successfully Added');
            return redirect()->back();
        }else {
            Session::flash('error', 'This Permission Name is Already Exist');
            return redirect()->back();
        }

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
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

         // login user activities record
        (new AuthenticationDataService())->InsertLoginUserActivity(20,1, Auth::user()->id,Auth::user()->emp_auto_id,null);

        return redirect()->route('roles.index')->with('success','Role created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        return view('roles.show',compact('role','rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        return view('admin.roles.edit',compact('role','permission','rolePermissions'));
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
            'name' => 'required',
            'permission' => 'required',

        ]);
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));
 
         // login user activities record
        (new AuthenticationDataService())->InsertLoginUserActivity(22,2, Auth::user()->id, Auth::user()->emp_auto_id,null);

        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');

    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
    {

       // DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')

                        ->with('success','Role Updated successfully');

    }





}
