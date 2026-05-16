<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permissiongroup;
use App\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    private $permissions = [
        'Crud' => [
            'controller' => 'Admin\CrudController',
            'permissions' => [
                'crud-view' => [
                    'index',
                    'data',
                ],
                'crud-store' => [
                    'create',
                    'store',
                ],
                'crud-update' => [
                    'edit',
                    'update',
                ],
                'crud-generate' => [
                    'generate',
                    'undo',
                ],
            ]
        ],
        'Dashboard' => [
            'controller' => 'Admin\DashboardController',
            'permissions' => [
                'dashboard-view' => [
                    'index',
                    'flatProfile',
                ],
            ]
        ],
        'Account' => [
            'controller' => 'Admin\ProfileController',
            'permissions' => [
                'account-profile' => [
                    'show',
                ],
            ]
        ],
        'SocietySetup' => [
            'controller' => 'Admin\SocietySetupController',
            'permissions' => [
                'society-setup-view' => [
                    'wings',
                    'flats',
                ],
                'society-setup-manage' => [
                    'storeWings',
                    'updateFlatDetail',
                    'generateFlats',
                ],
            ]
        ],
        'Expense' => [
            'controller' => 'Admin\ExpenseController',
            'permissions' => [
                'expense-view' => [
                    'index',
                    'show',
                ],
                'expense-manage' => [
                    'store',
                    'storeItem',
                    'destroyItem',
                    'destroy',
                ],
            ]
        ],
        'ExtraActivity' => [
            'controller' => 'Admin\ExtraActivityController',
            'permissions' => [
                'extra-activity-view' => [
                    'index',
                    'show',
                    'downloadListPdf'
                ],
                'extra-activity-manage' => [
                    'store',
                    'updateStatus',
                    'destroy',
                ],
            ]
        ],
        'Maintenance' => [
            'controller' => 'Admin\MaintenanceController',
            'permissions' => [
                'maintenance-view' => [
                    'index',
                    'show',
                    'downloadListPdf',
                ],
                'maintenance-manage' => [
                    'store',
                    'updateStatus',
                    'destroy',
                ],
            ]
        ],
        'Role' => [
            'controller' => 'Admin\RoleController',
            'permissions' => [
                'role-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'role-store' => [
                    'create',
                    'store',
                ],
                'role-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
                'role-permission' => [
                    'permissionsShow',
                    'permissionsUpdate',
                ],
            ]
        ],
        'User' => [
            'controller' => 'Admin\UserController',
            'permissions' => [
                'user-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'user-store' => [
                    // 'create',
                    // 'store',
                ],
                'user-update' => [
                    // 'edit',
                    // 'update',
                    'changeStatus',
                ],
            ]
        ],
        'Employee' => [
            'controller' => 'Admin\EmployeeController',
            'permissions' => [
                'employee-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'employee-store' => [
                    'create',
                    'store',
                ],
                'employee-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
            ]
        ],
        'Enquiry' => [
            'controller' => 'Admin\EnquiryController',
            'permissions' => [
                'enquiry-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'enquiry-store' => [

                ],
                'enquiry-update' => [

                ],
            ]
        ],
        'Slider' => [
            'controller' => 'Admin\SliderController',
            'permissions' => [
                'slider-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'slider-store' => [
                    'create',
                    'store',
                ],
                'slider-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
            ]
        ],
        'Category' => [
            'controller' => 'Admin\CategoryController',
            'permissions' => [
                'category-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'category-store' => [
                    'create',
                    'store',
                ],
                'category-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                    'changeHomeFeaturedStatus',
                ],
            ]
        ],
        'Product' => [
            'controller' => 'Admin\ProductController',
            'permissions' => [
                'product-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'product-store' => [
                    'create',
                    'store',
                ],
                'product-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                    'changeHomeFeaturedStatus',
                ],
            ]
        ],
        'Service' => [
            'controller' => 'Admin\ServiceController',
            'permissions' => [
                'service-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'service-store' => [
                    'create',
                    'store',
                ],
                'service-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                    'changeHomeFeaturedStatus',
                ],
            ]
        ],
        'Setting' => [
            'controller' => 'Admin\SettingController',
            'permissions' => [
                'setting-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                    'getDataPage',
                    'updateDataPage',
                ],
                'setting-store' => [
                    'create',
                    'store',
                ],
                'setting-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
            ]
        ],
        'Gallery' => [
            'controller' => 'Admin\GalleryController',
            'permissions' => [
                'gallery-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                    'getDataPage',
                    'updateDataPage',
                ],
                'gallery-store' => [
                    'create',
                    'store',
                ],
                'gallery-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
            ]
        ],
        'Order' => [
            'controller' => 'Admin\OrderController',
            'permissions' => [
                'order-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                    'getDataPage',
                    'updateDataPage',
                ],
                'order-store' => [
                    'create',
                    'store',
                ],
                'order-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
            ]
        ],

        'Activity' => [
            'controller' => 'Admin\ActivityController',
            'permissions' => [
                'activity-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                    'getDataPage',
                    'updateDataPage',
                ],
                'activity-store' => [
                    'create',
                    'store',
                ],
                'activity-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
            ]
        ],
        //End of Permission Arr
    ];


    public $roles = [
        'SuperAdmin' => [
            //Crud
            'crud-view',
            'crud-store',
            'crud-update',
            'crud-generate',

            #Dashboard
            'dashboard-view',

            'account-profile',

            #Society setup
            // 'society-setup-view',
            // 'society-setup-manage',

            // #Expenses
            // 'expense-view',
            // 'expense-manage',

            // #Maintenance
            // 'maintenance-view',
            // 'maintenance-manage',

            // #Extra Activity
            // 'extra-activity-view',
            // 'extra-activity-manage',

            #Roles
            'role-view',
            'role-store',
            'role-update',
            'role-permission',

            #User
            'user-view',
            'user-store',
            'user-update',

            #Employee
            'employee-view',
            'employee-store',
            'employee-update',

            #Enquiry
            'enquiry-view',
            'enquiry-store',
            'enquiry-update',

            #Slider
            'slider-view',
            'slider-store',
            'slider-update',

            #Category
            'category-view',
            'category-store',
            'category-update',

            #Product
            'product-view',
            'product-store',
            'product-update',

            #Service
            'service-view',
            'service-store',
            'service-update',

            #Setting
            'setting-view',
            'setting-store',
            'setting-update',

            #Gallery
            'gallery-view',
            'gallery-store',
            'gallery-update',

            #Order
            'order-view',
            'order-store',
            'order-update',

            // #Activity
            // 'activity-view',
            // 'activity-store',
            // 'activity-update',
            
            //End of Role Permission
        ],
        'User' => [

        ],
        
        'MEMBER' => [
            'dashboard-view',
            'account-profile',
            'society-setup-view',
            'society-setup-manage',
            'expense-view',
            'expense-manage',
            'maintenance-view',
            'maintenance-manage',
            'extra-activity-view',
            'extra-activity-manage',
        ],

        /** Society self-registration: same panel entry as member; expand permissions in admin if needed. */
        'ADMIN' => [
            'dashboard-view',
            'account-profile',
            'society-setup-view',
            'society-setup-manage',
            'expense-view',
            'expense-manage',
            'maintenance-view',
            'maintenance-manage',
            'extra-activity-view',
            'extra-activity-manage',
        ],
    ];

    private $users = [
        [
            'first_name'  => 'Super',
            'last_name'  => 'Admin',
            'roles'  => [
                'SuperAdmin',
            ],
            'mobile'  => '9999999999',
            'email'  => 'super@technicul.com',
            'password' => 'super@123@technicul'
        ],
    ];

    public function run(){
        #Groups & Permission
        $this->deletePermissions();
        $this->createPermissions();

        #Create Roles
        $this->createRoles();

        #Create Users
        $this->createUsers();
    }

    private function deletePermissions(){
        $permissions = Permission::all();
        foreach($permissions as $permission){
            $delete_permission = true;
            foreach($this->permissions as $group => $data){
                foreach($data['permissions'] as $p_name => $methods){
                    if($p_name == $permission->name){
                        $delete_permission = false;
                    }
                }
            }
            if($delete_permission){
                $permission->delete();
            }
        }

        $permission_groups = Permissiongroup::all();
        foreach($permission_groups as $permission_group){
            $delete_permissiongroup = true;
            foreach($this->permissions as $group => $data){
                if($group == $permission_group->name){
                    $delete_permissiongroup = false;
                }
            }
            if($delete_permissiongroup){
                $permission_group->delete();
            }
        }
    }

    private function createPermissions(){
        foreach($this->permissions as $group => $data){
            $permissiongroup = Permissiongroup::where('name',$group)->first();
            if(!$permissiongroup){
                $permissiongroup = new Permissiongroup;
                $permissiongroup->name = $group;
                $permissiongroup->controller = $data['controller'];
                $permissiongroup->save();
            }else{
                $permissiongroup->controller = $data['controller'];
                $permissiongroup->save();
            }

            foreach($data['permissions'] as $permissions_name => $methods){
                $permission = Permission::where('permissiongroup_id',$permissiongroup->id)->where('name',$permissions_name)->first();
                if(!$permission){
                    $permission = new Permission;
                    $permission->permissiongroup_id = $permissiongroup->id;
                    $permission->name = $permissions_name;
                    $permission->methods = $methods;
                    $permission->guard_name = config('auth.defaults.guard');
                    $permission->save();
                }else{
                    $permission->methods = $methods;
                    $permission->save();
                }
            }
        }
    }

    private function createRoles(){
        foreach($this->roles as $role_name => $permissions){
            $role = Role::where('name',$role_name)->first();
            if(!$role){
                $role = new Role;
                $role->name = $role_name;
                $role->guard_name = config('auth.defaults.guard');
                $role->save();
            }

            $permission_ids = Permission::whereIn('name',$permissions)->pluck('id');
            $role->syncPermissions($permission_ids);
        }
    }

    private function createUsers(){
        foreach($this->users as $data){
            $user = User::where('email',$data['email'])->first();
            if(!$user){
                $user = new User;
                $user->email = $data['email'];
                $user->mobile = $data['mobile'];
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->password = \Hash::make($data['password']);
                $user->save();
            }else{
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->password = \Hash::make($data['password']);
                $user->save();
            }

            #Assign Role & Sync Permission to User
            $user->assignRole($data['roles']);

            #Sync All Roles Permissions to User
            $all_permissions = collect();
            foreach($data['roles'] as $role_name){
                $role = Role::where('name',$role_name)->first();
                $permissions = $role->permissions()->get();
                foreach($permissions as $permission){
                    $all_permissions->push($permission);
                }
            }
            $user->syncPermissions($all_permissions);
        }
    }

}
