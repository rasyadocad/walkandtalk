<?php

namespace App\Mail;

use App\Models\laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LaporanDitugaskanSupervisor extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;

    /**
     * Create a new message instance.
     */
    public function __construct(laporan $laporan)
    {
        $this->laporan = $laporan;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Notifikasi Penugasan Laporan Safety Walk and Talk')
                    ->markdown('emails.laporan-ditugaskan');
    }
}
