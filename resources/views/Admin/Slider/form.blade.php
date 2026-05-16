@extends('layouts.admin')
@section('title')
    Slider
@endsection
@section('content')
    <form method="POST"
        action="{{ Route::is('admin.sliders.create') ? route('admin.sliders.store') : route('admin.sliders.update', ['slider' => $slider->route_key]) }}"
        method="POST" enctype="multipart/form-data" autocomplete="off" id="slider-form">
        @csrf
        {{ Route::is('admin.sliders.create') ? '' : method_field('PUT') }}
        <div class="row">
            <div class="col-lg-12 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-header">
                        <h5> {{ Route::is('admin.sliders.create') ? 'Create' : 'Edit' }} Slider </h5>
                    </div>
                    <div class="card-body border-top">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <label class="control-label col-form-label">Index <sup class="tcul-star-restrict">*</sup></label>
                                <input type="text" class="form-control" placeholder="Index" name="index"
                                    value="{{ isset($slider) ? $slider->index : '' }}" />
                                <div id="index-error" style="color:red"></div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <label class="control-label col-form-label">Title</label>
                                <input type="text" class="form-control" placeholder="Title" name="title"
                                    value="{{ isset($slider) ? $slider->title : '' }}" />
                                <div id="title-error" style="color:red"></div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-12 col-md-6">
                                <label class="control-label col-form-label">Image <sup class="tcul-star-restrict">*</sup></label>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label for="basicInput">Photo</label>
                                        <input type="file" name="photo" class="form-control-file" id="slider-photo"
                                            placeholder="Please Select Photo"></input>
                                        <div id="photo-error" style="color:red"></div>
                                    </fieldset>
                                    @if (isset($slider))
                                        <img src="{{ asset(Storage::url($slider->photo)) }}" border="10" width="100"
                                            height="100" class="img-rounded img-thumbnail">
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <label class="control-label col-form-label">URL</label>
                                <input type="text" class="form-control" placeholder="URL" name="url"
                                    value="{{ isset($slider) ? $slider->url : '' }}" />
                                <div id="url-error" style="color:red"></div>
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
                        <a href="{{ route('admin.sliders.index') }}" type="button" class="btn btn-secondary">
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
        $('#slider-form').submit(function(e) {
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
                processData: false,
                success: function(data) {
                    if (data.status == 'success') {
                        toastr.success(data.message, '', {
                            showMethod: "slideDown",
                            hideMethod: "slideUp",
                            timeOut: 1500,
                            closeButton: true,
                        });
                        setTimeout(function() {
                            window.location.href = "{!! route('admin.sliders.index') !!}";
                        }, 100);
                    } else {
                        toastr.error('There is some error!!', '', {
                            showMethod: "slideDown",
                            hideMethod: "slideUp",
                            timeOut: 1500,
                            closeButton: true,
                        });
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    toastr.error('There are some errors in Form. Please check your inputs', '', {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 1500,
                        closeButton: true,
                    });
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('#' + key + '-error').html(value);
                    });
                    $('html, body').animate({
                        scrollTop: $('#' + Object.keys(xhr.responseJSON.errors)[0] + '-error')
                            .offset().top - 200
                    }, 500);
                }
            });
        });
    </script>
@endsection
