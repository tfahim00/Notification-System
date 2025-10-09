<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $messageContent;

    public function __construct(User $user, string $messageContent)
    {
        $this->user = $user;
        $this->messageContent = $messageContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notification Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}