<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
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
            subject: $this->mailData['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'content_email.invoice',
            with: [
                'name' => $this->mailData['name'],
                'lot_descs' => $this->mailData['lot_descs'],
                'descs' => $this->mailData['descs'],
                'amount' => $this->mailData['amount'],
                'jatuh_tempo' => $this->mailData['jatuh_tempo'],
                'tahun' => $this->mailData['tahun'],
                'va_mandiri' => $this->mailData['va_mandiri'],
                'va_bca' => $this->mailData['va_bca'],
                'va_permata' => $this->mailData['va_permata'],
                'va_tokped' => $this->mailData['va_tokped']
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
        if ($this->mailData['process_id'] != '0') {
            return [
                Attachment::fromPath(env('ROOT_INVOICE_FILE_PATH') . 'invoice/HISTORY_INVOICE/' . $this->mailData['filenames'])
                    ->as($this->mailData['filenames'])
                    ->withMime('application/pdf'),
            ];
        } else {
            return [
                Attachment::fromPath(env('ROOT_INVOICE_FILE_PATH') . 'invoice/' . $this->mailData['filenames'])
                    ->as($this->mailData['filenames'])
                    ->withMime('application/pdf'),
            ];
        }
    }
}
