<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $clientName,
        public string $originalSubject,
        public string $replyText
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Réponse à votre message : '.$this->originalSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact.reply',
        );
    }
}
