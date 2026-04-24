<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminKelolaUserController extends Controller
{
    public function index()
    {
        return view('admin.kelola-user.index');
    }

    public function data(Request $request)
    {
        try {
            $query = User::where('role', 'user');

            if ($request->filled('search')) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->status === 'aktif') {
                $query->where('is_active', 1);
            } elseif ($request->status === 'nonaktif') {
                $query->where('is_active', 0);
            }

            return response()->json([
                'success' => true,
                'data' => $query->latest()->get()
            ]);
        } catch (\Exception $e) {

            Log::error('Gagal mengambil data user', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data user'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'no_hp' => 'nullable',
                'password' => 'required|min:6'
            ], [
                'name.required' => 'Nama wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 6 karakter'
            ]);

            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => 'user',
                'is_active' => 1,
            ]);

            Log::info('User berhasil ditambahkan', [
                'email' => $validated['email']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan'
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            Log::error('Gagal menambahkan user', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan user'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'no_hp' => 'nullable',
                'password' => 'nullable|min:6'
            ], [
                'name.required' => 'Nama wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'password.min' => 'Password minimal 6 karakter'
            ]);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'] ?? null,
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($validated['password'])
                ]);
            }

            Log::info('User berhasil diupdate', [
                'id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate'
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            Log::error('Gagal update user', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat update user'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $user = User::findOrFail($id);
            $user->delete();

            Log::info('User berhasil dihapus', [
                'id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {

            Log::error('Gagal hapus user', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus user'
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {

            $user = User::findOrFail($id);

            $user->update([
                'is_active' => !$user->is_active
            ]);

            Log::info('Status user diubah', [
                'id' => $id,
                'status' => $user->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah'
            ]);
        } catch (\Exception $e) {

            Log::error('Gagal toggle status user', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status user'
            ], 500);
        }
    }
}
