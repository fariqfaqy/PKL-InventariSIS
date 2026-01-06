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
}
