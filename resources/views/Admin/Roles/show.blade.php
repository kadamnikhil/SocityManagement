@php
    if(\Auth::user()->hasrole('SuperAdmin')){
        $permissions_groups = \App\Models\Permissiongroup::whereNotIn('name',['PermissionGroup','Permission'])->get();
    }else{
        $permissions_groups = \App\Models\Permissiongroup::whereNotIn('name',['PermissionGroup','Permission','Crud','User'])->get();
    }
    $permissions = \App\Models\Permission::all();
    $role_permissions = $role->getAllPermissions();
@endphp

<h5>{{$role->name}}</h5>
<br/>
<p>Permissions : </p>
<div class="table-responsive" style="overflow-x: auto;">
    <table class="table border table-bordered text-nowrap customize-table mb-0 align-middle">
        <thead class="text-dark fs-4">
            <tr class="">
                <th width="10%"><h6 class="fs-3 fw-semibold mb-0">Module</h6></th>
                <th width="3%"><h6 class="fs-3 fw-semibold mb-0">View</h6></th>
                <th width="3%"><h6 class="fs-3 fw-semibold mb-0">Create</h6></th>
                <th width="3%"><h6 class="fs-3 fw-semibold mb-0">Update</h6></th>
                <th><h6 class="fs-3 fw-semibold mb-0">Other</h6></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permissions_groups as $permissions_group)
            <tr>
                <td>
                    <h6 class="fs-3 fw-semibold mb-0">{{$permissions_group->name}}</h6>
                </td>
                @php 
                    $permission_group_name_string = strtolower(str_replace(' ','',$permissions_group->name)).'-'; 
                @endphp
                @foreach($permissions_group->permissions as $permission)
                    @php $checked = false; @endphp
                    @foreach($role_permissions as $role_permission)
                        @if($permission->id == $role_permission->id)
                            @php $checked = true; @endphp
                        @endif
                    @endforeach       
                    
                    @php 
                        $p = str_replace($permission_group_name_string,'',$permission->name); 
                        preg_replace('/[^a-zA-Z0-9]/', '', $p); 
                    @endphp
                    <td class="align-middle text-center">
                        @if($p != 'view' && $p != 'store' && $p != 'update')
                            <br/>
                            @if($checked)
                                <i class="ti ti-check text-success"></i>
                            @else
                                <i class="ti ti-x text-danger"></i>
                            @endif
                            <br/>
                            <span class="mb-5">{{ucwords($p)}}</span>
                        @else
                            @if($checked)
                                <i class="ti ti-check text-success"></i>
                            @else
                                <i class="ti ti-x text-danger"></i>
                            @endif
                        @endif
                    </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>