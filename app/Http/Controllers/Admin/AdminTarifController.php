<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarif;
use Illuminate\Support\Facades\Validator;

class AdminTarifController extends Controller
{
    public function index()
    {
        return view('admin.tarif.index');
    }

    public function data(Request $request)
    {
        $query = Tarif::query();

        if ($request->search) {
            $query->where('nama', 'like', '%' . strtolower($request->search) . '%');
        }

        return response()->json([
            'data' => $query->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:tarifs,nama',
            'tarif_per_jam' => 'required|numeric|min:0',
            'tarif_maksimal' => 'required|numeric|min:0',
        ], [
            'nama.unique' => 'Jenis kendaraan ini sudah ada dalam daftar tarif.',
            'nama.required' => 'Nama tarif/jenis kendaraan wajib diisi.'
        ]);

        $data = $request->all();
        $data['nama'] = strtolower($request->nama);

        Tarif::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tarif berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $id)
    {
        $tarif = Tarif::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100|unique:tarifs,nama,' . $id,
            'tarif_per_jam' => 'required|numeric|min:0',
            'tarif_maksimal' => 'required|numeric|min:0',
        ], [
            'nama.unique' => 'Nama tarif ini sudah digunakan.',
        ]);

        $data = $request->all();
        $data['nama'] = strtolower($request->nama);

        $tarif->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Tarif berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        $tarif = Tarif::findOrFail($id);
        $tarif->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarif berhasil dihapus'
        ]);
    }
}