<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $last_activity = $user->last_activity_at < now()->subMinutes(1);
            if ($user->current_session_start && $last_activity) {
                $sessionDuration = now()->diffInSeconds($user->current_session_start);
                $user->total_online_seconds += $sessionDuration;

                // Reset session
                $user->current_session_start = null;
                $user->inactivity_threshold = null;
                $user->save();
            }

            // Check if session should be ended due to inactivity
            if ($user->inactivity_threshold && now()->greaterThan($user->inactivity_threshold)) {
                $this->endInactiveSession($user);
            }

            // Update last seen timestamp
            $user->last_seen_at = now();

            // Set new inactivity threshold (1 minute from now)
            $user->inactivity_threshold = now()->addMinutes(1);

            // Start session if not already started
            if (!$user->current_session_start) {
                $user->current_session_start = now();
            }

            $user->save();
        }

        return $next($request);
    }

    protected function endInactiveSession($user)
    {
        if ($user->current_session_start) {
            // Calculate session duration and add to total
            $sessionDuration = now()->diffInSeconds($user->current_session_start);
            $user->total_online_seconds += $sessionDuration;

            // Reset session
            $user->current_session_start = null;
            $user->inactivity_threshold = null;
        }
    }
}
