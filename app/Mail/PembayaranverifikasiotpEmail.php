<?php

namespace App\Mail;

use App\Models\Transaksimaster;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PembayaranverifikasiotpEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verifikasi OTP Pembayaran',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $no_transaksi = $this->data['no_transaksi'];
        $nominal_bayar = $this->data['nominal_bayar'];

        $transaksi = Transaksimaster::where('no_transaksi', $no_transaksi)->first();

        $total = collect($nominal_bayar)->sum(function ($value) {
            return (int) str_replace('.', '', $value);
        });

        return new Content(
            view: 'contents.dashboard.email.pembayaranverifikasiotp',
            with: [
                'transaksi' => $transaksi,
                'no_transaksi' => $no_transaksi,
                'kode' => $this->data['kode'],
                'nominal_bayar' => $nominal_bayar,
                'total_dibayar' => $total
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
