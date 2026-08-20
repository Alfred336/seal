<?php

namespace App\Mail;

use App\Models\PlanInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlanInquiryConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PlanInquiry $inquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your SealTech plan inquiry',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.plan-inquiry-confirmation');
    }

    public function attachments(): array
    {
        return [];
    }
}
