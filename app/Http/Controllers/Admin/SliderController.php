<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Slider;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        return view('Admin.Slider.index');
    }

    public function data(Request $request)
    {
        $query = Slider::where('id', '!=', 0)->orderByDesc('id');

        return DataTables::eloquent($query)
            ->editColumn('index', function ($slider) {
                return $slider->index;
            })
            ->editColumn('title', function ($slider) {
                return $slider->title;
            })
            ->editColumn('photo', function ($slider) {
                return $slider->photo;
            })
            ->editColumn('url', function ($slider) {
                return $slider->url;
            })
            ->editColumn('status', function ($slider) {
                if ($slider->status == 'ACTIVE') {
                    return '<div class="form-check form-switch"><input class="form-check-input slider-status-switch" type="checkbox" checked data-routekey="' . $slider->route_key . '"/></div>';
                } else {
                    return '<div class="form-check form-switch"><input class="form-check-input slider-status-switch" type="checkbox" data-routekey="' . $slider->route_key . '"/></div>';
                }
            })
            ->addColumn('action', function ($slider) {
                $edit  = '<a href="'.route('admin.sliders.edit',['slider' => $slider->route_key]).'" class="badge bg-warning fs-1"><i class="fa fa-edit"></i></a>';
                return $edit;
            })
            ->addIndexColumn()
            ->rawColumns(['index','photo','action','status'])
            ->setRowId('id')
            ->make(true);
    }

    public function list()
    {
        $sliders = Slider::all();
        return view('Admin.Slider.index', compact('sliders'));
    }


    public function create()
    {
        return view('Admin.Slider.form');
    }

    public function store(Request $request)
    {
        $request->validate($this->rules, $this->customMessages);

        $slider = new Slider;
        $slider->fill($request->all());
        if ($request->hasFile('photo')) {
            $slider->photo = Storage::disk('public')->put('photos', $request->file('photo'));
        }
        $slider->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Slider Created Successfully',
            'slider' => $slider
        ], 201);
    }

    public function edit(Slider $slider)
    {
        return view('Admin.Slider.form', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $this->rules['index'] = 'required|unique:sliders,index,' . $slider->id;
        $this->rules['photo'] = 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';

        $request->validate($this->rules, $this->customMessages);
        $slider->fill($request->all());

        // Handle the main photo if it is present in the request
        if ($request->hasFile('photo')) {
            if ($slider->photo) {
                Storage::disk('public')->delete($slider->photo);
            }
            $slider->photo = Storage::disk('public')->put('photos', $request->file('photo'));
        }
        $slider->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Slider Updated Successfully',
            'slider' => $slider
        ], 201);
    }

    public function destroy(Slider $slider)
    {
        // Implement the logic for deleting the slider
    }


    public function changeStatus(Request $request)
    {
        $slider = Slider::findByKey($request->route_key);
        $slider->status = $request->status;
        $slider->save();

        return response()->json([
            'status' => 'success',
            'message' => $slider->name . ' has been marked ' . $slider->status . ' successfully',
            'slider' => $slider
        ], 201);
    }

    private $rules = [
        'index' => 'required|unique:sliders,index',
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'title' => 'nullable|string',
        'url' => 'nullable|url',
    ];

    private $customMessages = [
        'index.required' => 'Index is required',
        'photo.required' => 'Main photo is required',
        'photo.image' => 'Main photo must be an image',
        'photo.mimes' => 'Main photo must be a file of type: jpeg, png, jpg, gif, svg',
        'photo.max' => 'Main photo size should not exceed 2048 KB',
        'title.string' => 'Title must be a string',
        'url.url' => 'URL must be a valid URL',
    ];

}
