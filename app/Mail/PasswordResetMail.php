<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $token,
        public readonly int $expiresInMinutes,
    ) {}

    public function build(): self
    {
        return $this->subject('Redefinição de senha')
            ->markdown('emails.password-reset', [
                'url' => route('password.reset', ['token' => $this->token]),
                'expiresInMinutes' => $this->expiresInMinutes,
            ]);
    }
}
