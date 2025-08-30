<?php

namespace App\Listeners;

use App\Models\UserActivity;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackUserLogin
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
        UserActivity::create([
            'user_id' => $event->user->id,
            'login_time' => now(),
            'last_activity' => now(),
        ]);
    }
}
