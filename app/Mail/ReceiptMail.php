<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Penerimaan Pembayaran Transaksi Tanggal ' . $this->mailData['gen_date'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'content_email.receipt',
            with: [
                'debtor_name' => $this->mailData['debtor_name'],
                'debtor_acct' => $this->mailData['debtor_acct'],
                'gen_date' => $this->mailData['gen_date']
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->mailData['filestatus'] == null) {
            return [
                Attachment::fromPath(env('ROOT_RECEIPT_FILE_PATH') . $this->mailData['company'] . '/' . $this->mailData['filenames'])
                    ->as($this->mailData['filenames'])
                    ->withMime('application/pdf'),
            ];
        } else {
            return [
                Attachment::fromPath(env('ROOT_SIGNED_FILE_PATH') . $this->mailData['company'] . '/' . $this->mailData['filenames'])
                    ->as($this->mailData['filenames'])
                    ->withMime('application/pdf'),
            ];
        }
    }
}
