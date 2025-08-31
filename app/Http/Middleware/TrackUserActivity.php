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
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // End session if last activity was more than 1 minute ago
            if ($user->current_session_start && isset($user->last_activity_at) && $user->last_activity_at < now()->subMinute()) {
                $this->endInactiveSession($user);
            }

            // End session if inactivity threshold passed
            if ($user->inactivity_threshold && now()->greaterThan($user->inactivity_threshold)) {
                $this->endInactiveSession($user);
            }

            // Update last seen timestamp
            $user->last_seen_at = now();

            // Set new inactivity threshold (1 minute from now)
            $user->inactivity_threshold = now()->addMinute();

            // Start session if not already started
            if (!$user->current_session_start) {
                $user->current_session_start = now();
            }

            // Update last activity timestamp
            $user->last_activity_at = now();

            $user->save();
        }

        return $next($request);
    }

    /**
     * End the user's inactive session and persist changes.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function endInactiveSession($user): void
    {
        if ($user->current_session_start) {
            // Calculate session duration and add to total
            $sessionDuration = now()->diffInSeconds($user->current_session_start);
            $user->total_online_seconds += $sessionDuration;

            // Reset session
            $user->current_session_start = null;
            $user->inactivity_threshold = null;
            $user->save();
        }
    }
}
