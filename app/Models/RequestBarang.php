<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestBarang extends Model
{
    use HasFactory;

    protected $table = 'request_barang';
    protected $primaryKey = 'id_request';

    protected $fillable = [
        'user_id',
        'idbarang',
        'qty',
        'tipe_request',
        'tanggal_mulai_sewa',
        'tanggal_akhir_sewa',
        'keperluan',
        'status',
        'catatan_user',
        'catatan_admin',
        'diproses_oleh',
        'tanggal_request',
        'tanggal_diproses',
    ];

    protected $casts = [
        'tanggal_mulai_sewa' => 'date',
        'tanggal_akhir_sewa' => 'date',
        'tanggal_request' => 'datetime',
        'tanggal_diproses' => 'datetime',
    ];

    /**
     * Relationship: Belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Belongs to Stock
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'idbarang', 'idbarang');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'processing' => 'purple',
            'rejected' => 'red',
            'completed' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'processing' => 'Diproses',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
            default => $this->status
        };
    }

    /**
     * Get tipe request label
     */
    public function getTipeRequestLabelAttribute()
    {
        return match($this->tipe_request) {
            'pinjam_sewa' => 'Pinjam Barang Sewa',
            'pakai_habis_pakai' => 'Pakai Barang Habis Pakai',
            default => $this->tipe_request
        };
    }
}
