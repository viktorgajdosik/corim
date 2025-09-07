<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param string      $event     'banned' | 'deactivated' | 'unbanned' | 'activated' | 'granted_admin' | 'revoked_admin'
     * @param string      $changedBy Display name of the admin (or 'Admin')
     * @param string|null $reason    Optional reason (used for banned/deactivated if provided)
     */
    public function __construct(
        public string $event,
        public string $changedBy = 'Admin',
        public ?string $reason = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        [$subject, $line1] = match ($this->event) {
            'banned'         => ['Your account has been banned',       'Your account has been banned.'],
            'deactivated'    => ['Your account has been deactivated',  'Your account has been deactivated.'],
            'unbanned'       => ['Your ban has been lifted',           'Your account ban has been lifted.'],
            'activated'      => ['Your account has been reactivated',  'Your account has been reactivated.'],
            'granted_admin'  => ['Admin access granted',               'You have been granted admin access.'],
            'revoked_admin'  => ['Admin access revoked',               'Your admin access has been revoked.'],
            default          => ['Account update',                      'Your account status changed.'],
        };

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($line1)
            ->line('Changed by: '.$this->changedBy);

        // Include reason for ban (and also for deactivated if you ever pass it)
        if ($this->reason && in_array($this->event, ['banned', 'deactivated'], true)) {
            $mail->line('Reason: '.$this->reason);
        }

        return $mail->action('Go to CORIM', url('/'))
                    ->line('If you have questions, reply to this email.');
    }
}
