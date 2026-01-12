<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingTransaction extends Model
{
    use HasFactory;

    protected $table = 'masuk';
    protected $primaryKey = 'idmasuk';

    protected $fillable = [
        'idbarang',
        'tanggal',
        'keterangan',
        'qty',
        'namabarang_m',
        'penginput',
        'kodebarang_m',
        'tipe',
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
