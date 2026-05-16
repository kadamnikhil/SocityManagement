<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{

    public function index()
    {
        $settings = Setting::all();
        return view('Admin.Settings.index');
    }

    public function data(Request $request)
    {
        $query = Setting::query();

        return DataTables::eloquent($query)
            ->editColumn('key', function ($setting) {
                return $setting->key;
            })
            ->editColumn('value', function ($setting) {
                return $setting->value;
            })
            ->editColumn('values', function ($setting) {
                return $setting->values;
            })
            ->addIndexColumn()
            ->rawColumns(['key', 'value', 'values'])
            ->setRowId('id')
            ->make(true);
    }

    public function list()
    {
        $setting = Setting::all();
        return view('Admin.Settings.index', compact('setting'));
    }

    public function destroy(Setting $setting)
    {
        // Implement the logic for deleting the service
    }

    // Updating settings based on the data received from the form.
    public function updateDataPage(Request $request)
    {
        // Loop through each key in the request. The $index variable represents the current iteration index.
        foreach ($request->key as $index => $key) {
            // Update a setting with the specified key.
            // If a setting with the given key exists, update its 'value'.
            // If not, create a new setting with the specified 'key' and 'value'.
            Setting::updateOrCreate(
                [
                    'id' => $request->setting_id[$index]
                ],
                [
                    'key' => $request->key[$index],
                    'value' => $request->value[$index]
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Settings Updated Successfully',
        ]);
    }

    public function getDataPage(Request $request)
    {
        $setting = Setting::all();
        return view('Admin.Settings.data_page_form', compact('setting'));
    }
}
