<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingTransaction extends Model
{
    use HasFactory;

    protected $table = 'keluar';
    protected $primaryKey = 'idkeluar';

    protected $fillable = [
        'idbarang',
        'tanggal',
        'penerima',
        'qty',
        'namabarang_k',
        'penginput',
        'kodebarang_k',
        'tipe',
        'kategori',
        'durasi_sewa',
        'status',
        'tanggal_selesai',
        'status_approval',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tipe_request',
        'catatan_admin',
        'diproses_oleh',
        'tanggal_diproses',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_diproses' => 'datetime',
    ];

    /**
     * Relationship: Belongs to Stock
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'idbarang', 'idbarang');
    }
}
