<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FutureCheckpoitMailer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @var Collection $invalidCheckpoints */
    protected $invalidCheckpoints;

    public function __construct(Collection $invalidCheckpoints)
    {
        $this->invalidCheckpoints = $invalidCheckpoints;
    }

    public function build()
    {
        $invalidCheckpoints = $this->invalidCheckpoints->load('package');

        $mailable = $this
            ->view('emails.alerts.notify_future_checkpoints')
            ->with(compact('invalidCheckpoints'))
            ->to(['jyacoy@mailamericas.com', 'jcdominguez@mailamericas.com', 'fmoirano@mailamericas.com', 'epancotto@mailamericas.com'])
            ->cc('plabin@mailamericas.com')
            ->bcc('jcieri@mailamericas.com')
            ->subject('Packages with future checkpoints!')
            ->onQueue('tracking-mailers');

        return $mailable;
    }
}
