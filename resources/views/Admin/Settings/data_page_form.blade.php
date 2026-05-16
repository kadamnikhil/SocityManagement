@if (isset($setting))
    <form action="{{ route('admin.settings.update.data.page') }}" method="POST" enctype="multipart/form-data"
        autocomplete="off" id="dataPage-form">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <h4 class="card-title"></h4>
                    @foreach ($setting as $setting)
                        <input type="hidden" name="setting_id[]" value="{{ $setting->id }}">
                        <div class="col-sm-12 col-md-3">
                            <label class="control-label col-form-label">Key<sup></sup></label>
                            <input type="text" class="form-control" placeholder="Enter Key" id="key"
                                name="key[]" value="{{ isset($setting) ? $setting->key : '' }}" readonly />
                            <div id="key-error" style="color:red"></div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <label class="control-label col-form-label">Value<sup></sup></label>
                            <input type="text" class="form-control" placeholder="Enter Counter First" id="value"
                                name="value[]" value="{{ isset($setting) ? $setting->value : '' }}" />
                            <div id="value-error" style="color:red"></div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="control-label col-form-label">Values<sup></sup></label>
                            <input type="text" class="form-control" placeholder="Enter Values" id="values"
                                name="values[]" value="{{ isset($setting) ? $setting->values : '' }}" />
                            <div id="values-error" style="color:red"></div>
                        </div>
                    @endforeach
                    <div class="col-md-12 text-center mt-4" style="text-align: end;">
                        <button type="submit" class="btn btn-primary mr-2"><i class="ft-check-square mr-1"></i>
                            Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endif
