<?php
namespace App\Mailers;

use App\Models\Download;
use App\Services\Cloud\DownloadLinkManager;
use Mail;

class ReportMailer
{
    /**
     * @var DownloadLinkManager
     */
    protected $downloadLinkManager;

    public function __construct(DownloadLinkManager $downloadLinkManager)
    {
        $this->downloadLinkManager = $downloadLinkManager;
    }

    public function sendToUser($user, Download $download)
    {
        $link = $this->downloadLinkManager->url($download);

        Mail::queue('emails.reports', compact('user', 'link'), function ($message) use ($user) {
            $message
                ->to($user->email)
                ->subject('Your report is ready for download!');

            if ($user->alternative_email) {
                $message
                    ->cc($user->alternative_email);
            }
        }, 'tracking-mailers');
    }
}