<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'log';
    protected $primaryKey = 'idlog';
    
    public $timestamps = false;

    protected $fillable = [
        'date',
        'usr',
        'method',
        'endpoint',
        'status_code',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
}
