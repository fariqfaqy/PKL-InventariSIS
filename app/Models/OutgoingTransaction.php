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
        'id_request',
        'idbarang',
        'tanggal',
        'penerima',
        'user_id',
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
        'tanggal_mulai_sewa',
        'tanggal_akhir_sewa',
        'tanggal_mulai_pakai',
        'tanggal_akhir_pakai',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_diproses' => 'datetime',
        'tanggal_mulai_sewa' => 'date',
        'tanggal_akhir_sewa' => 'date',
        'tanggal_mulai_pakai' => 'date',
        'tanggal_akhir_pakai' => 'date',
    ];

    /**
     * Relationship: Belongs to Stock
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'idbarang', 'idbarang');
    }

    /**
     * Relationship: Belongs to User (user_id = user yang pakai/minta)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relationship: Belongs to RequestBarang
     * Link ke request yang di-approve admin
     */
    public function request()
    {
        return $this->belongsTo(RequestBarang::class, 'id_request', 'id_request');
    }
}
