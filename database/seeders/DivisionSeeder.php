<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            [
                'nama_divisi' => 'Divisi Teknologi Informasi',
                'kode_divisi' => 'IT-001',
                'deskripsi' => 'Divisi yang menangani sistem informasi, infrastruktur IT, dan keamanan siber',
                'kepala_divisi' => 'Ir. Ahmad Wijaya, M.T.',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Divisi Keuangan',
                'kode_divisi' => 'FIN-001',
                'deskripsi' => 'Divisi yang mengelola keuangan, akuntansi, dan pelaporan keuangan perusahaan',
                'kepala_divisi' => 'Dra. Siti Nurhaliza, M.M.',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Divisi Operasional',
                'kode_divisi' => 'OPS-001',
                'deskripsi' => 'Divisi yang menangani operasional harian dan pemeliharaan infrastruktur',
                'kepala_divisi' => 'Budi Santoso, S.T.',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Divisi SDM & Umum',
                'kode_divisi' => 'HRD-001',
                'deskripsi' => 'Divisi yang mengelola sumber daya manusia, rekrutmen, dan administrasi umum',
                'kepala_divisi' => 'Ani Rahmawati, S.Psi., M.M.',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Divisi Perencanaan & Pengembangan',
                'kode_divisi' => 'PPP-001',
                'deskripsi' => 'Divisi yang fokus pada perencanaan strategis dan pengembangan bisnis',
                'kepala_divisi' => 'Dr. Joko Widodo, M.Sc.',
                'is_active' => true,
            ],
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }
    }
}
