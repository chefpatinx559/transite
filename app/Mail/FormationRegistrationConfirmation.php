<?php

namespace App\Mail;

use App\Models\Formation;
use App\Models\FormationRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormationRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FormationRegistration $registration,
        public Formation             $formation,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->formation->is_free
            ? "Inscription confirmée — {$this->formation->title}"
            : "Paiement accepté — {$this->formation->title}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.formation-registration',
        );
    }
}
