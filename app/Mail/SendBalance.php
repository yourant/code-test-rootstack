<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendBalance extends Mailable
{
    use Queueable, SerializesModels;

    protected $balance;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($balance)
    {
        $this->balance = $balance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('emails.alerts.balance')
            ->with(['balance' => $this->balance])
            ->subject('Balance at Anti-Captcha')
            ->onQueue('tracking-mailers');
    }

}
