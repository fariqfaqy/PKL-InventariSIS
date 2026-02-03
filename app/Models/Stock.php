<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';
    protected $primaryKey = 'idbarang';

    protected $fillable = [
        'namabarang',
        'deskripsi',
        'stock',
        'image',
        'penginput',
        'kodebarang',
        'rack',
        'kategori',
        'sub_kategori',
        'jenis',
        'merek',
        'tipe',
        'durasi_sewa',
        'status_kondisi',
        'keterangan_kondisi',
        'tanggal_update_kondisi',
        'user_id',
        'tanggal_mulai_pakai',
        'tanggal_akhir_pakai',
    ];

    protected $casts = [
        'tanggal_update_kondisi' => 'date',
        'tanggal_mulai_pakai' => 'date',
        'tanggal_akhir_pakai' => 'date',
        'stock' => 'integer',
        'durasi_sewa' => 'integer',
    ];

    /**
     * Relationship: Stock has many incoming transactions
     */
    public function incomingTransactions()
    {
        return $this->hasMany(IncomingTransaction::class, 'idbarang', 'idbarang');
    }

    /**
     * Relationship: Stock has many outgoing transactions
     */
    public function outgoingTransactions()
    {
        return $this->hasMany(OutgoingTransaction::class, 'idbarang', 'idbarang');
    }

    /**
     * Relationship: Stock has many rack assignments
     */
    public function rackAssignments()
    {
        return $this->hasMany(RackAssignment::class, 'idbarang', 'idbarang');
    }

    /**
     * Relationship: Stock belongs to User (untuk Aset Sewa)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get stock status based on quantity
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock >= 10) {
            return 'aman';
        } elseif ($this->stock >= 5) {
            return 'menengah';
        } else {
            return 'kritis';
        }
    }

    /**
     * Get stock status color
     */
    public function getStockStatusColorAttribute()
    {
        switch ($this->stock_status) {
            case 'aman':
                return 'success';
            case 'menengah':
                return 'warning';
            case 'kritis':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Get status kondisi label (real-time based on rental status)
     */
    public function getStatusKondisiLabelAttribute()
    {
        // Untuk Aset Sewa: cek status kondisi di database dulu
        if ($this->kategori === 'aset_sewa') {
            // Prioritas 1: Jika status_kondisi sudah 'selesai', barang tidak bisa dipakai lagi
            if ($this->status_kondisi === 'selesai') {
                return 'Selesai';
            }
            
            // Prioritas 2: Cek apakah ada rental aktif
            $activeRental = $this->outgoingTransactions()
                ->whereNull('id_request')
                ->where('status', 'sedang_dipakai')
                ->first();
            
            if ($activeRental) {
                return 'Sedang Digunakan';
            }
            
            // Prioritas 3: Cek status_kondisi lainnya
            return match($this->status_kondisi) {
                'diperbaiki' => 'Diperbaiki',
                'rusak' => 'Rusak',
                'digunakan' => 'Digunakan',
                default => 'Tersedia'
            };
        }
        
        // Default untuk kategori lain
        return match($this->status_kondisi) {
            'digunakan' => 'Digunakan',
            'diperbaiki' => 'Diperbaiki',
            'rusak' => 'Rusak',
            'selesai' => 'Selesai',
            default => 'Digunakan'
        };
    }

    /**
     * Get status kondisi badge color (real-time based on rental status)
     */
    public function getStatusKondisiBadgeAttribute()
    {
        // Untuk Aset Sewa: cek status kondisi di database dulu
        if ($this->kategori === 'aset_sewa') {
            // Prioritas 1: Jika status_kondisi sudah 'selesai'
            if ($this->status_kondisi === 'selesai') {
                return 'bg-gray-100 text-gray-800'; // Selesai - abu-abu
            }
            
            // Prioritas 2: Cek apakah ada rental aktif
            $activeRental = $this->outgoingTransactions()
                ->whereNull('id_request')
                ->where('status', 'sedang_dipakai')
                ->first();
            
            if ($activeRental) {
                return 'bg-blue-100 text-blue-800'; // Sedang Digunakan
            }
            
            // Prioritas 3: Cek status_kondisi lainnya
            return match($this->status_kondisi) {
                'diperbaiki' => 'bg-yellow-100 text-yellow-800',
                'rusak' => 'bg-red-100 text-red-800',
                'digunakan' => 'bg-blue-100 text-blue-800',
                default => 'bg-green-100 text-green-800' // Tersedia
            };
        }
        
        // Default untuk kategori lain
        return match($this->status_kondisi) {
            'digunakan' => 'bg-blue-100 text-blue-800',
            'diperbaiki' => 'bg-yellow-100 text-yellow-800',
            'rusak' => 'bg-red-100 text-red-800',
            'selesai' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get durasi sewa (dalam bulan) - untuk aset_sewa
     * Calculated from tanggal_mulai_sewa dan tanggal_akhir_sewa
     */
    public function getDurasiSewaRealAttribute()
    {
        if (!$this->tanggal_mulai_sewa || !$this->tanggal_akhir_sewa) {
            return $this->durasi_sewa; // return durasi_sewa yang sudah diset
        }
        
        $mulai = \Carbon\Carbon::parse($this->tanggal_mulai_sewa);
        $akhir = \Carbon\Carbon::parse($this->tanggal_akhir_sewa);
        
        return $mulai->diffInMonths($akhir);
    }
}
