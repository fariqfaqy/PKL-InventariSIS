<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DivisionController extends Controller
{
    /**
     * Display a listing of divisions.
     */
    public function index()
    {
        $divisions = Division::withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.divisions.index', compact('divisions'));
    }

    /**
     * Show the form for creating a new division.
     */
    public function create()
    {
        return view('admin.divisions.create');
    }

    /**
     * Store a newly created division in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_divisi' => 'required|string|max:255',
            'kode_divisi' => 'required|string|max:10|unique:divisions,kode_divisi',
            'deskripsi' => 'nullable|string',
            'kepala_divisi' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Division::create($validated);

        return redirect()->route('admin.divisions.index')
            ->with('success', 'Divisi berhasil ditambahkan!');
    }

    /**
     * Display the specified division.
     */
    public function show(Division $division)
    {
        $division->load('users');
        return view('admin.divisions.show', compact('division'));
    }

    /**
     * Show the form for editing the specified division.
     */
    public function edit(Division $division)
    {
        return view('admin.divisions.edit', compact('division'));
    }

    /**
     * Update the specified division in storage.
     */
    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'nama_divisi' => 'required|string|max:255',
            'kode_divisi' => 'required|string|max:10|unique:divisions,kode_divisi,' . $division->id,
            'deskripsi' => 'nullable|string',
            'kepala_divisi' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $division->update($validated);

        return redirect()->route('admin.divisions.index')
            ->with('success', 'Divisi berhasil diperbarui!');
    }

    /**
     * Remove the specified division from storage.
     */
    public function destroy(Division $division)
    {
        // Check if division has users
        if ($division->users()->count() > 0) {
            return back()->with('error', 'Divisi tidak dapat dihapus karena masih memiliki pegawai!');
        }

        $division->delete();

        return redirect()->route('admin.divisions.index')
            ->with('success', 'Divisi berhasil dihapus!');
    }

    /**
     * Export divisions data to PDF.
     */
    public function exportPdf()
    {
        $divisions = Division::withCount('users')
            ->orderBy('nama_divisi', 'asc')
            ->get();
        
        $pdf = Pdf::loadView('admin.pdf.divisions', compact('divisions'))
            ->setPaper('a4', 'portrait');
        
        $filename = 'Laporan_Data_Divisi_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
