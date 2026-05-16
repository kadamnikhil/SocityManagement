<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use View;

class EmployeeController extends Controller
{
    public function index(){
        return view('Admin.Employees.index');
    }

    public function data(Request $request){
        $query = Employee::where('id','!=',0);

        return DataTables::eloquent($query)
            ->editColumn('emp_id', function ($employee) {
                return $employee->emp_id;
            }) 
            ->editColumn('first_name', function ($employee) {
                return $employee->user->first_name;
            }) 
            ->editColumn('last_name', function ($employee) {
                return $employee->user->last_name;
            })
            ->editColumn('designation', function ($employee) {
                return $employee->designation;
            })  
            ->editColumn('email', function ($employee) {
                return $employee->user->email;
            }) 
            ->editColumn('mobile', function ($employee) {
                return $employee->user->mobile;
            }) 
            ->editColumn('status', function ($employee) {
                if($employee->user->status == 'ACTIVE'){
                    return '<div class="form-check form-switch"><input class="form-check-input employee-status-switch" type="checkbox" checked data-routekey="'.$employee->route_key.'"/></div>';
                }else{
                    return '<div class="form-check form-switch"><input class="form-check-input employee-status-switch" type="checkbox" data-routekey="'.$employee->route_key.'"/></div>';
                }
            })
            ->addColumn('action', function ($employee) {
                $edit  = '<a href="'.route('admin.employees.edit',['employee' => $employee->route_key]).'" class="badge bg-warning fs-1"><i class="fa fa-edit"></i></a>';
                $show = '<a href="'.route('admin.employees.show',['employee' => $employee->route_key]).'" class="badge bg-info fs-1 modal-one-btn" data-entity="employees" data-title="Employee" data-route-key="'.$employee->route_key.'"><i class="fa fa-eye"></i></a>';
                return $edit.' '.$show;
            })   
           ->addIndexColumn()
           ->rawColumns(['emp_id','first_name','last_name','designation','email','mobile','status','action'])->setRowId('id')->make(true);
    }

    public function list(){
		$employees = Employee::all();
        return response()->json([   
            'status' => 'success',
            'list' => $employees
        ],200);   
	}

    public function create(){
        $systemRoles = getSystemRoles();
        $roles = Role::whereNotIn('name', $systemRoles)->get();
        $emp_id = getCountingNumber('Employee','EMP','emp_id',false);
        return view('Admin.Employees.form',compact('roles','emp_id'));
    }

    public function store(Request $request){
        $request->validate($this->rules, $this->customMessages);

        $user = New User;
        $user->fill($request->all());
        $user->password = bcrypt($request->password);
        $user->save();

        $permissions = [];
        $user->assignRole($request->roles);
		foreach($request->roles as $role_name){
			$role = Role::where('name',$role_name)->first();
			array_push($permissions,$role->permissions()->get());
		}
		$user->syncPermissions($permissions);

        $employee = new Employee;
        $employee->fill($request->all());
        $employee->user_id = $user->id;
        $employee->save();

        return response()->json([   
            'status' => 'success',
            'message' => 'Employee Updated Successfully',
            'employee' => $employee
        ],201);            
    }

    public function show(Employee $employee){
        return view('Admin.Employees.show',compact('employee'));
    }

    public function edit(Employee $employee){
        $systemRoles = getSystemRoles();
        $roles = Role::whereNotIn('name', $systemRoles)->get();
        return View('Admin.Employees.form',compact('employee','roles'));
    }

    public function update(Request $request,$employee){
        $this->rules['email'] = 'required|email|unique:users,email,'.$employee->user->id;
        $this->rules['personal_email'] = 'nullable|email|unique:employees,personal_email,'.$employee->id;
        $this->rules['mobile'] = 'required|digits:10|unique:users,mobile,'.$employee->user->id;
        $this->rules['password'] = 'nullable|min:6';
        $this->rules['password_confirmation'] = 'nullable|same:password';
    
        $request->validate($this->rules, $this->customMessages);
        
        $user = User::find($employee->user->id);
        $user->fill($request->all());
        $user->password = bcrypt($request->password);
        $user->save();

        $permissions = [];
        $user->syncRoles([]);
        $user->assignRole($request->roles);
		foreach($request->roles as $role_name){
			$role = Role::where('name',$role_name)->first();
			array_push($permissions,$role->permissions()->get());
		}
		$user->syncPermissions($permissions);

        $employee->fill($request->all());
        $employee->save();

        return response()->json([   
            'status' => 'success',
            'message' => 'Employee Updated Successfully',
            'employee' => $employee
        ],201);    
    }

    public function destroy(Employee $employee){
        
    }

    public function changeStatus(Request $request){
        $employee = Employee::findByKey($request->route_key);
        $user = User::find($employee->user_id);        
        $user->status = $request->status;
        $user->save();

        return response()->json([   
            'status' => 'success',
            'message' => $user->first_name.' has marked '.$user->status.' successfully',
            'employee' => $employee
        ],201);    
    }

    private $rules = [
        'first_name' => 'required|regex:/^[\pL\s\-]+$/u',
        'last_name' => 'required|regex:/^[\pL\s\-]+$/u',
        'email' => 'required|email|unique:users,email',
        'personal_email' => 'nullable|email|unique:employees,personal_email',
        'mobile' => 'required|digits:10|unique:users,mobile',
        'password' => 'required|min:6',
        'password_confirmation' => 'required|same:password',
        'roles' => 'required|min:1',
        'designation' => 'required',
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
        'personal_email.email' => 'Personal Email should be a valid email',
        'personal_email.unique' => 'Personal Email already exists',
        'mobile.required' => 'Mobile is required',
        'mobile.digits' => 'Mobile should be 10 digits',
        'mobile.unique' => 'Mobile already exists',
        'password.required' => 'Password is required',
        'password.min' => 'Password should be minimum 6 characters',
        'confirm_password.required' => 'Confirm Password is required',
        'confirm_password.same' => 'Confirm Password should be same as Password',
        'roles.required' => 'Roles is required',
        'designation.required' => 'Designation is required',
        'status.required' => 'Status is required',
    ];
}
