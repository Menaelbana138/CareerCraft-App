<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ReminderNotification;
use Illuminate\Console\Command;

class SendReminderNotifications extends Command
{
    protected $signature   = 'notifications:send-reminders';
    protected $description = 'Send daily reminder notifications to active users who have incomplete career paths.';

    public function handle(): int
    {
        $users = User::query()
            ->whereNull('suspended_at')
            ->whereNotNull('email_verified_at')
            ->with('careerPaths')
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            // Only notify users who have at least one in-progress career path
            $inProgress = $user->careerPaths
                ->filter(fn ($p) => ($p->pivot->status ?? '') === 'in_progress')
                ->count();

            if ($inProgress > 0) {
                $user->notify(new ReminderNotification(
                    "You have {$inProgress} career path(s) in progress. Keep going!"
                ));
                $sent++;
            }
        }

        $this->info("Reminders sent to {$sent} user(s).");

        return self::SUCCESS;
    }
}
