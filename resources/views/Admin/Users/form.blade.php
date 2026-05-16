@extends('layouts.admin')
@section('title') Users @endsection
@section('content')
<form method="POST" action="{{ Route::is('admin.users.create') ? route('admin.users.store') : route('admin.users.update', ['user' => $user->route_key]) }}" method="POST" enctype="multipart/form-data" autocomplete="off" id="user-form">
    @csrf
    {{ Route::is('admin.users.create') ? '' : method_field('PUT') }}
    <div class="row">
        <div class="col-lg-12 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-header">
                    <h5> {{ Route::is('admin.users.create') ? 'Create' : 'Edit' }} User </h5>
                </div>
                <div class="card-body border-top">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label class="control-label col-form-label">First Name</label>
                            <input type="text" class="form-control" placeholder="First Name" name="first_name" value="{{ isset($user) ? $user->first_name : '' }}"/>
                            <div id="first_name-error" style="color:red"></div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="control-label col-form-label">Last Name</label>
                            <input type="text" class="form-control" placeholder="Last Name" name="last_name" value="{{ isset($user) ? $user->last_name : '' }}"/>
                            <div id="last_name-error" style="color:red"></div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="control-label col-form-label">E-Mail</label>
                            <input type="text" class="form-control" placeholder="E-Mail" name="email" value="{{ isset($user) ? $user->email : '' }}"/>
                            <div id="email-error" style="color:red"></div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="control-label col-form-label">Mobile</label>
                            <input type="text" class="form-control" placeholder="Mobile" name="mobile" value="{{ isset($user) ? $user->mobile : '' }}"/>
                            <div id="mobile-error" style="color:red"></div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="control-label col-form-label">Password</label>
                            <input type="text" class="form-control" placeholder="Password" name="password" value=""/>
                            <div id="password-error" style="color:red"></div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="control-label col-form-label">Confirm Password</label>
                            <input type="text" class="form-control" placeholder="Confirm Password" name="password_confirmation" value=""/>
                            <div id="password_confirmation-error" style="color:red"></div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="control-label col-form-label">Role(s)</label>
                            <select class="form-control select2" name="roles[]" multiple="multiple">
                                @foreach($roles as $role)
                                    @if(isset($user) && $user->hasRole($role->name))
                                        <option value="{{ $role->name }}" selected>{{ $role->name }}</option>
                                    @else
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div id="roles-error" style="color:red"></div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="control-label col-form-label">Status</label>
                            <select class="form-control" name="status">
                                <option value="">Select</option>

                                @if(isset($user) && $user->status == 'ACTIVE')
                                    <option value="ACTIVE" selected>Active</option>
                                @else
                                    <option value="ACTIVE">Active</option>
                                @endif

                                @if(isset($user) && $user->status == 'INACTIVE')
                                    <option value="INACTIVE" selected>Inactive</option>
                                @else
                                    <option value="INACTIVE">Inactive</option>
                                @endif
                            </select>
                            <div id="status-error" style="color:red"></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        Save
                        &nbsp;
                        <i class="ti ti-device-floppy"></i> 
                    </button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="{{ route('admin.users.index') }}" type="button" class="btn btn-secondary">
                        Cancel
                        &nbsp; 
                        <i class="ti ti-arrow-back-up-double"></i> 
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $('#user-form').submit(function(e) {
        e.preventDefault();
        $('div[id$="-error"]').empty();
        var form = $(this);
        var url = form.attr('action');
        $.ajax({
            type: "POST",
            url: url,
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            success: function(data){
                if(data.status == 'success'){
                    toastr.success(data.message,'',{   
                        showMethod: "slideDown", 
                        hideMethod: "slideUp", 
                        timeOut: 1500, 
                        closeButton: true,
                    });
                    setTimeout(function(){
                        window.location.href = "{!! route('admin.users.index') !!}";
                    }, 100);
                }else{
                    toastr.error('There is some error!!','',{   
                        showMethod: "slideDown", 
                        hideMethod: "slideUp", 
                        timeOut: 1500, 
                        closeButton: true,
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                toastr.error(xhr.responseJSON.message,'',{   
                    showMethod: "slideDown", 
                    hideMethod: "slideUp", 
                    timeOut: 1500, 
                    closeButton: true,
                });
                $.each(xhr.responseJSON.errors, function(key,value) {
                    $('#'+key+'-error').html(value);
                });
                $('html, body').animate({
                    scrollTop: $('#'+Object.keys(xhr.responseJSON.errors)[0]+'-error').offset().top - 200
                }, 500);
            }
        });
    });
</script>
@endsection