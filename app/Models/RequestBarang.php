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
        'request_type',
        'tanggal_mulai_sewa',
        'tanggal_akhir_sewa',
        'keperluan',
        'status',
        'catatan_user',
        'catatan_admin',
        'diproses_oleh',
        'tanggal_request',
        'tanggal_diproses',
        'parent_request_id',
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
            'cancelled' => 'gray',
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
            'cancelled' => 'Dibatalkan',
            default => $this->status
        };
    }

    /**
     * Get parent request relationship
     */
    public function parentRequest()
    {
        return $this->belongsTo(RequestBarang::class, 'parent_request_id', 'id_request');
    }

    /**
     * Get child change requests (request perubahan dari request ini)
     */
    public function changeRequests()
    {
        return $this->hasMany(RequestBarang::class, 'parent_request_id', 'id_request');
    }

    /**
     * Get outgoing transaction relationship
     */
    public function outgoingTransaction()
    {
        return $this->hasOne(OutgoingTransaction::class, 'id_request', 'id_request');
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

    /**
     * Check if request can be approved
     */
    public function canBeApproved(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if request can be rejected
     */
    public function canBeRejected(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    /**
     * Check if request can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    /**
     * Check if request can be cancelled (for approved requests)
     */
    public function canBeCancelled(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request can be deleted (only pending)
     */
    public function canBeDeleted(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is a change request
     */
    public function isChangeRequest(): bool
    {
        return !is_null($this->parent_request_id);
    }

    /**
     * Check if request is a cancellation request (using explicit column)
     */
    public function isCancellationRequest(): bool
    {
        // Use explicit request_type column for reliable detection
        // Fallback to magic string if column not set (backward compatibility)
        if ($this->request_type) {
            return $this->request_type === 'cancellation';
        }
        
        // Legacy fallback
        return $this->isChangeRequest() && (
            stripos($this->keperluan, 'PEMBATALAN') !== false ||
            stripos($this->catatan_user ?? '', 'PEMBATALAN') !== false
        );
    }
}
