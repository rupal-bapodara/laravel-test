<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;

class WelcomeEmail extends Mailable
{
    public function __construct(public User $user) {}

    public function build(): self
    {
        return $this->subject(sprintf('Welcome to %s', config('app.name')))
            ->view('emails.welcome');
    }
}
