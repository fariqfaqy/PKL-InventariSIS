<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with('division')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $divisions = Division::active()->orderBy('nama_divisi', 'asc')->get();
        return view('admin.users.create', compact('divisions'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'division_id' => 'nullable|exists:divisions,id',
            'nip' => 'nullable|string|max:50|unique:users,nip',
            'jabatan' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'tanggal_masuk' => 'nullable|date',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user', // Auto set role sebagai user (admin hanya 1)
            'division_id' => $validated['division_id'] ?? null,
            'nip' => $validated['nip'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'no_telp' => $validated['no_telp'] ?? null,
            'tanggal_masuk' => $validated['tanggal_masuk'] ?? null,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pegawai berhasil ditambahkan!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('division');
        
        // Get user activities - gabungkan dari 2 sumber:
        // 1. Aset Sewa yang sedang digunakan (dari Stock)
        // 2. Transaksi keluar yang sudah selesai (dari OutgoingTransaction)
        
        $activities = collect();
        
        // 1. Aset Sewa yang sedang digunakan (dari Stock)
        $activeAsetSewa = \App\Models\Stock::with('user')
            ->where('kategori', 'aset_sewa')
            ->where('status_kondisi', 'digunakan')
            ->where('user_id', $user->id)
            ->get()
            ->map(function($stock) {
                return [
                    'id' => 'AS-' . $stock->idbarang,
                    'type' => 'sewa',
                    'label' => 'Menyewa Aset',
                    'color' => 'purple',
                    'barang' => $stock->namabarang,
                    'kode' => $stock->kodebarang,
                    'qty' => 1,
                    'tanggal' => $stock->tanggal_mulai_pakai ? \Carbon\Carbon::parse($stock->tanggal_mulai_pakai) : now(),
                    'status' => 'sedang_dipakai',
                    'tanggal_pinjam' => $stock->tanggal_mulai_pakai,
                    'tanggal_kembali' => $stock->tanggal_akhir_pakai,
                    'tanggal_selesai' => null,
                    'kategori' => 'aset_sewa',
                    'tipe_request' => 'aset_sewa',
                ];
            });
        
        // 2. Transaksi keluar (OutgoingTransaction) - untuk history yang sudah selesai dan material umum
        $outgoingActivities = \App\Models\OutgoingTransaction::with(['stock'])
            ->where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function($transaction) {
                // Determine activity type based on kategori and tipe_request
                $activityType = 'unknown';
                $activityLabel = 'Aktivitas';
                $activityColor = 'gray';
                
                // Cek kategori barang
                if ($transaction->kategori === 'aset_sewa') {
                    $activityType = 'sewa';
                    $activityLabel = 'Menyewa Aset';
                    $activityColor = 'purple';
                } elseif ($transaction->kategori === 'material_umum') {
                    // Cek tipe_request
                    if ($transaction->tipe_request === 'peminjaman' || $transaction->tipe_request === 'pinjam_material') {
                        $activityType = 'pinjam';
                        $activityLabel = 'Meminjam Barang';
                        $activityColor = 'cyan';
                    } elseif ($transaction->tipe_request === 'permintaan' || $transaction->tipe_request === 'pakai_habis_pakai') {
                        $activityType = 'pakai';
                        $activityLabel = 'Memakai Barang';
                        $activityColor = 'green';
                    } else {
                        // Fallback: cek dari stock sub_kategori
                        $subKategori = $transaction->stock->sub_kategori ?? null;
                        if ($subKategori === 'pinjam_material') {
                            $activityType = 'pinjam';
                            $activityLabel = 'Meminjam Barang';
                            $activityColor = 'cyan';
                        } elseif ($subKategori === 'pakai_habis_pakai') {
                            $activityType = 'pakai';
                            $activityLabel = 'Memakai Barang';
                            $activityColor = 'green';
                        }
                    }
                } elseif ($transaction->kategori === 'aset_tetap') {
                    $activityType = 'pinjam';
                    $activityLabel = 'Meminjam Aset';
                    $activityColor = 'blue';
                }
                
                return [
                    'id' => $transaction->idkeluar,
                    'type' => $activityType,
                    'label' => $activityLabel,
                    'color' => $activityColor,
                    'barang' => $transaction->namabarang_k,
                    'kode' => $transaction->kodebarang_k,
                    'qty' => $transaction->qty,
                    'tanggal' => $transaction->tanggal,
                    'status' => $transaction->status,
                    'tanggal_pinjam' => $transaction->tanggal_pinjam,
                    'tanggal_kembali' => $transaction->tanggal_kembali,
                    'tanggal_selesai' => $transaction->tanggal_selesai,
                    'kategori' => $transaction->kategori,
                    'tipe_request' => $transaction->tipe_request,
                ];
            });
        
        // Gabungkan activities dari Stock dan OutgoingTransaction
        $activities = $activeAsetSewa->merge($outgoingActivities)->sortByDesc('tanggal')->values();
        
        // Group activities by type for statistics
        $stats = [
            'sewa' => $activities->where('type', 'sewa')->count(),
            'pinjam' => $activities->where('type', 'pinjam')->count(),
            'pakai' => $activities->where('type', 'pakai')->count(),
            'total' => $activities->count(),
            'active' => $activities->where('status', 'sedang_dipakai')->count(),
            'completed' => $activities->where('status', 'selesai')->count(),
        ];
        
        return view('admin.users.show', compact('user', 'activities', 'stats'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $divisions = Division::active()->orderBy('nama_divisi', 'asc')->get();
        return view('admin.users.edit', compact('user', 'divisions'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,user',
            'password' => 'nullable|string|min:8|confirmed',
            'division_id' => 'nullable|exists:divisions,id',
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'jabatan' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'tanggal_masuk' => 'nullable|date',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->division_id = $validated['division_id'] ?? null;
        $user->nip = $validated['nip'] ?? null;
        $user->jabatan = $validated['jabatan'] ?? null;
        $user->no_telp = $validated['no_telp'] ?? null;
        $user->tanggal_masuk = $validated['tanggal_masuk'] ?? null;

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pegawai berhasil diperbarui!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pegawai berhasil dihapus!');
    }

    /**
     * Export users data to PDF.
     */
    public function exportPdf()
    {
        $users = User::with('division')
            ->orderBy('name', 'asc')
            ->get();
        
        $pdf = Pdf::loadView('admin.pdf.users', compact('users'))
            ->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Data_Pegawai_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
