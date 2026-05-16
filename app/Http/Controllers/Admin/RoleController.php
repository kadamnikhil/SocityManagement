<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Permissiongroup;
use App\Models\User;
use View;

class RoleController extends Controller
{
    public function index(){
        return view('Admin.Roles.index');
    }

    public function data(Request $request){
        $systemRoles = getSystemRoles();
        $query = Role::whereNotIn('name', $systemRoles);
        // $query = Role::where('id','!=',0);

        return DataTables::eloquent($query)
            ->editColumn('name', function ($role) {
                return $role->name;
            }) 
            ->addColumn('permissions', function ($role) {
                $permissions  = '<a href="'.route('admin.roles.permissions.show',['role' => $role->route_key]).'" class="btn btn-sm btn-info">Permissions &nbsp; <i class="ti ti-fingerprint"></i></a>';
                return $permissions;
            })
            ->addColumn('action', function ($role) {
                $edit  = '<a href="'.route('admin.roles.edit',['role' => $role->route_key]).'" class="badge bg-warning fs-1"><i class="fa fa-edit"></i></a>';
                $show = '<a href="'.route('admin.roles.show',['role' => $role->route_key]).'" class="badge bg-info fs-1 modal-one-btn" data-entity="roles" data-title="Role" data-route-key="'.$role->route_key.'"><i class="fa fa-eye"></i></a>';
                return $edit.' '.$show;
            })   
           ->addIndexColumn()
           ->rawColumns(['name','permissions','action'])->setRowId('id')->make(true);
    }

    public function list(){
		$roles = Role::whereNotIn('name',['SuperAdmin','Admin'])->get();
        return response()->json([   
            'status' => 'success',
            'list' => $roles
        ],200);   
	}

    public function create(){
        return view('Admin.Roles.form');
    }

    public function store(Request $request){
        $request->validate($this->rules, $this->customMessages);

        $role = new Role;
        $role->fill($request->all());
        $role->save();

        return response()->json([   
            'status' => 'success',
            'message' => 'Role Updated Successfully',
            'role' => $role
        ],201);            
    }

    public function show(Role $role){
        return view('Admin.Roles.show',compact('role'));
    }

    public function edit($role){
        return View('Admin.Roles.form',compact('role'));
    }

    public function update(Request $request,$role){
        $this->rules['name'] = 'required|regex:/^[\pL\s\-]+$/u|unique:roles,name,'.$role->id;
        $request->validate($this->rules, $this->customMessages);
        
        $role->fill($request->all());
        $role->save();

        return response()->json([   
            'status' => 'success',
            'message' => 'Role Updated Successfully',
            'role' => $role
        ],201);    
    }

    public function destroy(Role $role){
        
    }

    public function permissionsShow(Role $role){
        if(\Auth::user()->hasrole('SuperAdmin')){
            $permissions_groups = Permissiongroup::whereNotIn('name',['PermissionGroup','Permission'])->get();
        }else{
            $permissions_groups = Permissiongroup::whereNotIn('name',['PermissionGroup','Permission','Crud','User'])->get();
        }
        
        $permissions = Permission::all();
        $role_permissions = $role->getAllPermissions();

        return view('Admin.Roles.permissions',compact('role','permissions','permissions_groups','role_permissions'));
    }

    public function permissionsUpdate(Request $request,Role $role){
        $role->syncPermissions($request->permissions);
        $users = User::role($role->name)->get();
        if(isset($users)){
            foreach($users as $user){
                $user->syncPermissions($request->permissions);
            }
        }
        return response()->json([   
            'status' => 'success',
            'message' => 'Permissions Updated Successfully',
        ],201);    
    }

    private $rules = [
        'name' => 'required|regex:/^[\pL\s\-]+$/u|unique:roles',
    ];
  
    private $customMessages = [
        'name.required' => 'Please enter role',
        'name.unique' => 'The role has already been taken',
        'name.regex:/^[\pL\s\-]+$/u' => 'The role should be in letter format',
    ];
}
