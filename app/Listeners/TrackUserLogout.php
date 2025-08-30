<?php

namespace App\Listeners;

use App\Models\UserActivity;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackUserLogout
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
    public function handle(object $event): void
    {
        $activity = UserActivity::where('user_id', $event->user->id)
            ->whereNull('logout_time')
            ->latest('login_time')
            ->first();

        if ($activity) {
            $activity->logout_time = now();
            $activity->save();
        }
    }
}
