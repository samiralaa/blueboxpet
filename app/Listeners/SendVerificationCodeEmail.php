<?php

namespace App\Listeners;

use App\Events\VerificationCodeGenerated;
use App\Mail\VerificationCodeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendVerificationCodeEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VerificationCodeGenerated $event)
    {
        $user = $event->user;
        Mail::to($user->email)->send(new VerificationCodeMail($user->verification_code));
    }
}
