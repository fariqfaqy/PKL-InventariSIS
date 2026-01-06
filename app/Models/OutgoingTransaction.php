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
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    /**
     * Relationship: Belongs to Stock
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'idbarang', 'idbarang');
    }
}
