<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class AdminSettingController extends Controller
{
    // halaman blade
    public function index()
    {
        return view('admin.settings.index');
    }

    // ambil data (untuk AJAX)
    public function data()
    {
        return response()->json([
            'success' => true,
            'data' => Setting::getAll()
        ]);
    }

    // simpan setting
    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required',
            'lokasi_parkir' => 'required',
            'alamat' => 'required',
            'kontak' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        try {
            Setting::set('app_name', $request->app_name);
            Setting::set('lokasi_parkir', $request->lokasi_parkir);
            Setting::set('alamat', $request->alamat);
            Setting::set('kontak', $request->kontak);
            Setting::set('latitude', $request->latitude);
            Setting::set('longitude', $request->longitude);

            return response()->json([
                'success' => true,
                'message' => 'Setting berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan setting'
            ], 500);
        }
    }
}
