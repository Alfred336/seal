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

class NewPlanInquiry extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PlanInquiry $inquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New '.ucfirst($this->inquiry->plan).' Plan Inquiry - SealTech',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.new-plan-inquiry');
    }

    public function attachments(): array
    {
        return [];
    }
}
