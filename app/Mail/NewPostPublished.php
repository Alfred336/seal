<?php

namespace App\Mail;

use App\Models\Post;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class NewPostPublished extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Pre-computed at dispatch time (when routes are available),
     * so the queue worker never needs to call URL::temporarySignedRoute().
     */
    public string $unsubscribeUrl;

    /**
     * Create a new message instance.
     *
     * @param  string  $recipientEmail  The subscriber's email — required to generate a per-subscriber signed URL.
     */
    public function __construct(
        public Post $post,
        public string $recipientEmail
    ) {
        // Compute the signed unsubscribe URL here (at job-dispatch time) while
        // all routes are available. The value is serialised into the queue
        // payload and reused during send, so the worker never needs routing.
        $this->unsubscribeUrl = Subscription::generateUnsubscribeUrl($this->recipientEmail);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Post: '.$this->post->title,
        );
    }

    /**
     * Get the message headers for RFC 2369 / RFC 8058 compliance.
     * Gmail and Outlook use these to show a native "Unsubscribe" button.
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe' => "<{$this->unsubscribeUrl}>",
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.new-post-published',
            with: [
                'unsubscribeUrl' => $this->unsubscribeUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

