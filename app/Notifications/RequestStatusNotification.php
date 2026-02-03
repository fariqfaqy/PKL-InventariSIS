<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RequestBarang;

class RequestStatusNotification extends Notification
{
    use Queueable;

    public $request;
    public $action;
    public $adminNote;

    /**
     * Create a new notification instance.
     */
    public function __construct(RequestBarang $request, $action, $adminNote = null)
    {
        $this->request = $request;
        $this->action = $action; // 'approved' or 'rejected'
        $this->adminNote = $adminNote;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = $this->action === 'approved' 
            ? 'Request barang Anda telah DISETUJUI oleh admin'
            : 'Request barang Anda telah DITOLAK oleh admin';

        return [
            'request_id' => $this->request->id_request,
            'action' => $this->action,
            'message' => $message,
            'barang' => $this->request->stock->namabarang,
            'qty' => $this->request->qty,
            'admin_note' => $this->adminNote,
            'url' => route('user.pemakaian.index'),
        ];
    }
}

