<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $fillable = [
        'stock_id',
        'event_type',
        'description',
        'old_values',
        'new_values',
        'user_id',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: StockHistory belongs to Stock
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'idbarang');
    }

    /**
     * Relationship: StockHistory belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper method untuk log event
     */
    public static function logEvent($stockId, $eventType, $description, $oldValues = null, $newValues = null, $userId = null)
    {
        return self::create([
            'stock_id' => $stockId,
            'event_type' => $eventType,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }
}
