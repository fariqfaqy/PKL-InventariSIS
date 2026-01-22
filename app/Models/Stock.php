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
        'status_kondisi',
        'keterangan_kondisi',
        'tanggal_update_kondisi',
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
     * Get status kondisi label
     */
    public function getStatusKondisiLabelAttribute()
    {
        return match($this->status_kondisi) {
            'digunakan' => 'Digunakan',
            'diperbaiki' => 'Diperbaiki',
            'rusak' => 'Rusak',
            default => 'Digunakan'
        };
    }

    /**
     * Get status kondisi badge color
     */
    public function getStatusKondisiBadgeAttribute()
    {
        return match($this->status_kondisi) {
            'digunakan' => 'bg-blue-100 text-blue-800',
            'diperbaiki' => 'bg-yellow-100 text-yellow-800',
            'rusak' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get durasi pakai (dalam hari)
     */
    public function getDurasiPakaiAttribute()
    {
        if (!$this->tanggal_mulai_pakai || !$this->tanggal_akhir_pakai) {
            return null;
        }
        
        $mulai = \Carbon\Carbon::parse($this->tanggal_mulai_pakai);
        $akhir = \Carbon\Carbon::parse($this->tanggal_akhir_pakai);
        
        return $mulai->diffInDays($akhir) + 1; // +1 untuk include hari pertama
    }
}
