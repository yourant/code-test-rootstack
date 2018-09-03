<?php

namespace App\Mail;

use App\Models\Download;
use App\Services\Cloud\DownloadLinkManager;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReportGenerated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Download
     */
    protected $download;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Download $download)
    {
        $this->user = $user;
        $this->download = $download;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $downloadLinkManager = app()->make(DownloadLinkManager::class);

        $link = $downloadLinkManager->url($this->download);
        $user = $this->user;
        $filename = $this->download->filename;

        $mailable = $this
            ->view('emails.reports')
            ->with(compact('user', 'link', 'filename'))
            ->to($user->email)
            ->subject('Your report is ready for download!')
            ->onQueue('tracking-mailers');

        if ($user->alternative_email) {
            $mailable->cc($user->alternative_email);
        }

        return $mailable;
    }
}
