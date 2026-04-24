<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkirTransaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminKelolaPetugasController extends Controller
{

    public function index(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'petugas']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = ($request->status === 'aktif') ? 1 : 0;
            $query->where('is_active', $status);
        }

        $petugas = $query->latest()->paginate(10)->withQueryString();

        return view('admin.kelola-petugas.index', compact('petugas'));
    }

    public function create()
    {
        return view('admin.kelola-petugas.create');
    }


    public function store(Request $request)
    {

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'no_hp'    => 'nullable|numeric|digits_between:10,15',
            'password' => 'required|min:6',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar di sistem.',
            'no_hp.numeric'      => 'Nomor HP harus berupa angka.',
            'no_hp.digits_between' => 'Nomor HP harus antara 10 sampai 15 digit.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal harus 6 karakter.',
        ]);

        try {
         
            User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'no_hp'     => $request->no_hp,
                'password'  => Hash::make($request->password),
                'role'      => 'petugas',
                'is_active' => 1, // Default aktif saat dibuat
            ]);

            return redirect()->route('admin.petugas.index')
                ->with('success', 'Petugas baru berhasil ditambahkan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }


    public function show(Request $request, $id)
    {
        $petugas = User::whereIn('role', ['admin', 'petugas'])->findOrFail($id);
        return view('admin.kelola-petugas.show', compact('petugas'));
    }


public function transaksiData(Request $request, $id)
{
    $start = $request->start ? \Carbon\Carbon::parse($request->start)->startOfDay() : null;
    $end = $request->end ? \Carbon\Carbon::parse($request->end)->endOfDay() : null;

    $query = ParkirTransaksi::with('kendaraan')->where('petugas_id', $id);
    
    if ($start && $end) {
        $query->whereBetween('waktu_masuk', [$start, $end]);
    }

    $data = $query->latest('waktu_masuk')->paginate(10);

    $baseSelesai = ParkirTransaksi::where('petugas_id', $id)->where('status', 'selesai');

    $summaryBulan = [
        'hari_ini'       => (clone $baseSelesai)->whereDate('waktu_keluar', now())->sum('total_bayar'),
        'count_hari_ini' => (clone $baseSelesai)->whereDate('waktu_keluar', now())->count(),
        'minggu_ini'       => (clone $baseSelesai)->whereBetween('waktu_keluar', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_bayar'),
        'count_minggu_ini' => (clone $baseSelesai)->whereBetween('waktu_keluar', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        'bulan_ini'       => (clone $baseSelesai)->whereMonth('waktu_keluar', now()->month)->whereYear('waktu_keluar', now()->year)->sum('total_bayar'),
        'count_bulan_ini' => (clone $baseSelesai)->whereMonth('waktu_keluar', now()->month)->whereYear('waktu_keluar', now()->year)->count(),
    ];

    $filterQuery = ParkirTransaksi::where('petugas_id', $id)->where('status', 'selesai');
    if ($start && $end) {
        $filterQuery->whereBetween('waktu_keluar', [$start, $end]);
    }

    $summaryFilter = [
        'total_filter' => $filterQuery->sum('total_bayar'),
        'count_filter' => $filterQuery->count()
    ];

    return response()->json([
        'data' => $data->items(),
        'meta' => [
            'current_page' => $data->currentPage(),
            'last_page'    => $data->lastPage()
        ],
        'summary' => $summaryBulan,
        'summary_filter' => $summaryFilter
    ]);
}
 
    public function edit($id)
    {
        $petugas = User::whereIn('role', ['admin', 'petugas'])->findOrFail($id);
        return view('admin.kelola-petugas.edit', compact('petugas'));
    }

    public function update(Request $request, $id)
    {
        $petugas = User::whereIn('role', ['admin', 'petugas'])->findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($id)],
            'no_hp'    => 'nullable|numeric|digits_between:10,15',
            'password' => 'nullable|min:6',
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.unique'   => 'Email ini sudah digunakan oleh pengguna lain.',
            'password.min'   => 'Password minimal 6 karakter jika ingin diubah.',
        ]);

        try {
            $data = $request->only(['name', 'email', 'no_hp']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $petugas->update($data);

            return redirect()->route('admin.petugas.index')
                ->with('success', 'Informasi petugas berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data petugas.');
        }
    }

    public function destroy($id)
    {
        try {
            $petugas = User::whereIn('role', ['admin', 'petugas'])->findOrFail($id);

            if ($petugas->id === auth()->id()) {
                return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
            }

            if ($petugas->parkirTransaksis()->exists()) {
                return back()->with('error', 'Petugas tidak bisa dihapus karena sudah memiliki transaksi yang terrekam.');
            }

            Log::info('Menghapus petugas', [
                'deleted_by' => auth()->id(),
                'petugas_id' => $petugas->id
            ]);

            $petugas->delete();

            return redirect()->route('admin.petugas.index')
                ->with('success', 'Petugas berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {

            Log::error('DB Error saat hapus petugas', [
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal menghapus karena data masih terhubung dengan sistem.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return back()->with('error', 'Data petugas tidak ditemukan.');
        } catch (Exception $e) {

            Log::error('General error hapus petugas', [
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }


    public function toggleStatus($id)
    {
        try {
            $petugas = User::whereIn('role', ['admin', 'petugas'])->findOrFail($id);
            $petugas->update([
                'is_active' => !$petugas->is_active
            ]);

            $status = $petugas->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return back()->with('success', "Akun petugas berhasil $status.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengubah status akun.');
        }
    }
}
