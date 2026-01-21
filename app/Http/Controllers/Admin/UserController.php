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
            'role' => 'required|in:admin,user',
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
            'role' => $validated['role'],
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
        return view('admin.users.show', compact('user'));
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
