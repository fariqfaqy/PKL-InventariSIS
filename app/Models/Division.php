<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_divisi',
        'kode_divisi',
        'deskripsi',
        'kepala_divisi',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all users in this division.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get active users count.
     */
    public function getActiveUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Scope to get only active divisions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
