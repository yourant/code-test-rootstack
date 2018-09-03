<?php

namespace App\Mailers;

use Illuminate\Support\Collection;
use Mail;

class AlertMailer
{
    static function notifyUnclassifiedCheckpointCodesToAdministrators(Collection $checkpointCodes = null)
    {
        // Preparing the mail
        Mail::send('emails.alerts.unclassified_checkpoint_codes', compact('checkpointCodes'), function ($message) {
            $message
                ->to(['jyacoy@mailamericas.com', 'aabraham@mailamericas.com'])
                ->cc('jcieri@mailamericas.com')
                ->subject('There are unclassified checkpoint codes. Action needed!');
        });
    }

    static function notifyAlertMinimumMovementsProvidersToAdministrators(Collection $totals = null)
    {
        // Preparing the mail
        Mail::send('emails.alerts.notify_movements', compact('totals'), function ($message) {
            $message
                ->to('hleon@theegg.io')
                ->subject('Movements reported by providers the last 24 hours. Action needed!');
        });
    }
}