<?php

namespace App\Http\Controllers\All;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Halaman profil
     */
    public function index()
    {
        return view('profile.index');
    }

    /**
     * Update profil
     */
    public function update(Request $request)
{
    $user = auth()->user();

    try {
        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'no_hp' => 'sometimes|required|string|max:20'
        ], [
            'name.required'  => 'Nama wajib diisi',
            'name.max'       => 'Nama maksimal 255 karakter',

            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
            'email.unique'   => 'Email sudah digunakan',

            'no_hp.required' => 'Nomor HP wajib diisi',
            'no_hp.max'      => 'Nomor HP maksimal 20 karakter',
        ]);

        // 🔥 hanya update yang berubah
        $user->fill($validated);

        if (!$user->isDirty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada perubahan data'
            ], 422);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {

        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    }
}

    /**
     * Ganti password
     */
    public function changePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|min:6|same:confirm_password',
                'confirm_password' => 'required'
            ], [
                'old_password.required' => 'Password lama wajib diisi',

                'new_password.required' => 'Password baru wajib diisi',
                'new_password.min'      => 'Password minimal 6 karakter',
                'new_password.same'     => 'Konfirmasi password tidak cocok',

                'confirm_password.required' => 'Konfirmasi password wajib diisi',
            ]);

            $user = auth()->user();

            // cek password lama
            if (!Hash::check($validated['old_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'old_password' => ['Password lama tidak sesuai']
                    ]
                ], 422);
            }

            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }
    }
}