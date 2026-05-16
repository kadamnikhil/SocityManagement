<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use View;


class UserController extends Controller
{
    public function index(){
        $systemRoles = getSystemRoles();
        $usersQuery = User::whereHas("roles", function($q) use($systemRoles){
            $q->whereIn("name", $systemRoles)->where('name','!=','SuperAdmin');
        });

        $summary = [
            'total' => (clone $usersQuery)->count(),
            'active' => (clone $usersQuery)->where('status', 'ACTIVE')->count(),
            'inactive' => (clone $usersQuery)->where('status', 'INACTIVE')->count(),
        ];

        return view('Admin.Users.index', compact('summary'));
    }

    public function data(Request $request){
        $systemRoles = getSystemRoles();
        $query = User::with('roles')->whereHas("roles", function($q) use($systemRoles){ $q->whereIn("name", $systemRoles)->where('name','!=','SuperAdmin'); });

        return DataTables::eloquent($query)
            ->addColumn('customer', function ($user) {
                $fullName = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
                $fullName = $fullName !== '' ? $fullName : 'Unnamed User';
                $initials = strtoupper(substr((string) ($user->first_name ?? 'U'), 0, 1).substr((string) ($user->last_name ?? ''), 0, 1));
                $initials = trim($initials) !== '' ? $initials : 'U';

                return '
                    <div class="user-row-profile">
                        <div class="user-avatar">'.e($initials).'</div>
                        <div class="user-row-name">
                            <strong>'.e($fullName).'</strong>
                            <span>#'.e($user->id).'</span>
                        </div>
                    </div>
                ';
            })
            ->addColumn('contact', function ($user) {
                return '
                    <div class="user-contact-stack">
                        <span><i class="ti ti-mail"></i>'.e($user->email ?: 'No email').'</span>
                        <span><i class="ti ti-phone"></i>'.e($user->mobile ?: 'Not added').'</span>
                    </div>
                ';
            })
            ->addColumn('roles_badges', function ($user) {
                $roleNames = $user->getRoleNames();
                return $roleNames->isNotEmpty()
                    ? $roleNames->map(fn ($role) => '<span class="user-role-pill">'.e($role).'</span>')->implode('')
                    : '<span class="user-role-pill">No role</span>';
            })
            ->addColumn('status_control', function ($user) {
                $statusClass = $user->status === 'ACTIVE' ? 'is-active' : 'is-inactive';
                $statusText = ucwords(strtolower((string) $user->status));
                $checked = $user->status === 'ACTIVE' ? 'checked' : '';

                return '
                    <div class="user-status-wrap">
                        <span class="user-status-badge '.$statusClass.'">'.$statusText.'</span>
                        <input class="form-check-input user-status-switch" type="checkbox" '.$checked.' data-routekey="'.e($user->route_key).'"/>
                    </div>
                ';
            })
            ->addColumn('profile_action', function ($user) {
                return '
                    <a href="'.route('admin.users.show', ['user' => $user->route_key]).'" class="btn btn-primary btn-sm">
                        <i class="ti ti-eye me-1"></i> View
                    </a>
                ';
            })
            ->editColumn('first_name', function ($user) {
                return $user->first_name;
            }) 
            ->editColumn('last_name', function ($user) {
                return $user->last_name;
            }) 
            ->editColumn('email', function ($user) {
                return $user->email;
            }) 
            ->editColumn('mobile', function ($user) {
                return $user->mobile;
            }) 
            ->editColumn('status', function ($user) {
                if($user->status == 'ACTIVE'){
                    return '<div class="form-check form-switch"><input class="form-check-input user-status-switch" type="checkbox" checked data-routekey="'.$user->route_key.'"/></div>';
                }else{
                    return '<div class="form-check form-switch"><input class="form-check-input user-status-switch" type="checkbox" data-routekey="'.$user->route_key.'"/></div>';
                }
            })
            ->addColumn('action', function ($user) {
                $show = '<a href="'.route('admin.users.show',['user' => $user->route_key]).'" class="badge bg-info fs-1 modal-one-btn" data-entity="users" data-title="User" data-route-key="'.$user->route_key.'"><i class="fa fa-eye"></i></a>';
                return $show;
            })   
           ->addIndexColumn()
           ->rawColumns(['customer','contact','roles_badges','status_control','profile_action','first_name','last_name','email','mobile','status','action'])->setRowId('id')->make(true);
    }

    public function list(){
		$users = User::all();
        return response()->json([   
            'status' => 'success',
            'list' => $users
        ],200);   
	}

    // public function create(){
    //     $systemRoles = getSystemRoles();
    //     $roles = Role::whereNotIn('name', $systemRoles)->get();
    //     return view('Admin.Users.form',compact('roles'));
    // }

    // public function store(Request $request){
    //     $request->validate($this->rules, $this->customMessages);

    //     $user = new User;
    //     $user->fill($request->all());
    //     $user->password = bcrypt($request->password);
    //     $user->save();

    //     $permissions = [];
    //     $user->assignRole($request->roles);
	// 	foreach($request->roles as $role_name){
	// 		$role = Role::where('name',$role_name)->first();
	// 		array_push($permissions,$role->permissions()->get());
	// 	}
	// 	$user->syncPermissions($permissions);

    //     return response()->json([   
    //         'status' => 'success',
    //         'message' => 'User Updated Successfully',
    //         'user' => $user
    //     ],201);            
    // }

    public function show(User $user){
        $user->load('roles')->loadCount(['societyWings', 'societyFlats']);
        $children = $user->getchildrens();
        $permissions = $user->getAllPermissions()->pluck('name')->sort()->values();

        return view('Admin.Users.show', compact('user', 'children', 'permissions'));
    }

    // public function edit(User $user){
    //     $systemRoles = getSystemRoles();
    //     $roles = Role::whereNotIn('name', $systemRoles)->get();
    //     return View('Admin.Users.form',compact('user','roles'));
    // }

    // public function update(Request $request,$user){
    //     $this->rules['email'] = 'required|email|unique:users,email,'.$user->id;
    //     $this->rules['mobile'] = 'required|digits:10|unique:users,mobile,'.$user->id;
    //     $this->rules['password'] = 'nullable|min:6';
    //     $this->rules['password_confirmation'] = 'nullable|same:password';
    //     $request->validate($this->rules, $this->customMessages);
        
    //     $user->fill($request->all());
    //     $user->password = bcrypt($request->password);
    //     $user->save();

    //     $permissions = [];
    //     $user->syncRoles([]);
    //     $user->assignRole($request->roles);
	// 	foreach($request->roles as $role_name){
	// 		$role = Role::where('name',$role_name)->first();
	// 		array_push($permissions,$role->permissions()->get());
	// 	}
	// 	$user->syncPermissions($permissions);

    //     return response()->json([   
    //         'status' => 'success',
    //         'message' => 'User Updated Successfully',
    //         'user' => $user
    //     ],201);    
    // }

    public function destroy(User $user){
        
    }

    public function changeStatus(Request $request){
        $user = User::findByKey($request->route_key);
        $user->status = $request->status;
        $user->save();

        return response()->json([   
            'status' => 'success',
            'message' => $user->first_name.' has marked '.$user->status.' successfully',
            'user' => $user
        ],201);    
    }

    private $rules = [
        'first_name' => 'required|regex:/^[\pL\s\-]+$/u',
        'last_name' => 'required|regex:/^[\pL\s\-]+$/u',
        'email' => 'required|email|unique:users,email',
        'mobile' => 'required|digits:10|unique:users,mobile',
        'password' => 'required|min:6',
        'password_confirmation' => 'required|same:password',
        'roles' => 'required|min:1',
        'status' => 'required',
    ];
  
    private $customMessages = [
        'first_name.required' => 'First Name is required',
        'first_name.regex' => 'First Name should contain only alphabets',
        'last_name.required' => 'Last Name is required',
        'last_name.regex' => 'Last Name should contain only alphabets',
        'email.required' => 'Email is required',
        'email.email' => 'Email should be a valid email',
        'email.unique' => 'Email already exists',
        'mobile.required' => 'Mobile is required',
        'mobile.digits' => 'Mobile should be 10 digits',
        'mobile.unique' => 'Mobile already exists',
        'password.required' => 'Password is required',
        'password.min' => 'Password should be minimum 6 characters',
        'confirm_password.required' => 'Confirm Password is required',
        'confirm_password.same' => 'Confirm Password should be same as Password',
        'status.required' => 'Status is required',
    ];
}
