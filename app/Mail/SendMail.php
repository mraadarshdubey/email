<?php

namespace App\Mail;

use App\Http\Controllers\UnsubscribeController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /** Recipient address — used to build the per-recipient unsubscribe link. */
    public ?string $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData, ?string $recipient = null)
    {
        $this->mailData = $mailData;
        $this->recipient = $recipient;
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
     * List-Unsubscribe headers (RFC 2369 / RFC 8058).
     *
     * Gmail and Outlook require these for bulk senders: they render a native
     * "Unsubscribe" control and expect a one-click POST to honour it.
     */
    public function headers(): Headers
    {
        if (! $this->recipient) {
            return new Headers();
        }

        $url = UnsubscribeController::urlFor($this->recipient);

        return new Headers(text: [
            'List-Unsubscribe' => '<' . $url . '>',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ]);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.template',
            with: [
                'unsubscribeUrl' => $this->recipient
                    ? UnsubscribeController::urlFor($this->recipient)
                    : null,
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
