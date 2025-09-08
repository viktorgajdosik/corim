<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InstitutionRequestDecided extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  'approved'|'declined'  $decision
     */
    public function __construct(
        public string $decision,
        public string $name,
        public string $domain,
        public ?string $website = null,
        public string $decidedBy = 'Admin'
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $approved = $this->decision === 'approved';

        $subject = $approved
            ? 'Your institution request was approved'
            : 'Your institution request was declined';

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello,')
            ->line("Institution: {$this->name}")
            ->line("Email domain: @{$this->domain}")
            ->line('Decision: ' . ucfirst($this->decision))
            ->line('Decided by: ' . $this->decidedBy);

        if ($this->website) {
            $mail->line('Website: ' . $this->website);
        }

        if ($approved) {
            $mail->action('Register on CORIM', url('/register'))
                 ->line('You can now register users using emails ending in @' . $this->domain . '.');
        } else {
            $mail->line('If you believe this was in error, please reply to this email.');
        }

        return $mail;
    }
}
