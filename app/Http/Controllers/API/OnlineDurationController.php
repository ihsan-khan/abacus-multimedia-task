<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OnlineDurationController extends Controller
{
    public function getOnlineStats(Request $request)
    {
        $user = Auth::user();

        // Check if session expired due to inactivity
        if ($user->inactivity_threshold && now()->greaterThan($user->inactivity_threshold)) {
            $this->endInactiveSession($user);
        }

        // Calculate current session duration
        $currentSessionDuration = 0;
        $sessionExpiresIn = null;

        if ($user->current_session_start) {
            $currentSessionDuration = now()->diffInSeconds($user->current_session_start);
            $sessionExpiresIn = now()->diffInSeconds($user->inactivity_threshold);
        }

        // Calculate total online duration
        $totalOnlineDuration = $user->total_online_seconds + $currentSessionDuration;

        // Check if user is currently online
        $isOnline = $user->last_seen_at > now()->subMinutes(1);

        return response()->json([
            'current_session' => [
                'started_at' => $user->current_session_start,
                'duration_seconds' => $currentSessionDuration,
                'duration_formatted' => $this->secondsToTime($currentSessionDuration),
                'expires_in_seconds' => max(0, $sessionExpiresIn),
                'will_expire_at' => $user->inactivity_threshold
            ],
            'total_online' => [
                'seconds' => $totalOnlineDuration,
                'formatted' => $this->secondsToTime($totalOnlineDuration)
            ],
            'last_activity' => $user->last_seen_at,
            'is_online' => $isOnline,
            'session_active' => !is_null($user->current_session_start)
        ]);
    }

    public function endSession(Request $request)
    {
        $user = Auth::user();

        if ($user->current_session_start) {
            $sessionDuration = now()->diffInSeconds($user->current_session_start);
            $user->total_online_seconds += $sessionDuration;

            // Reset session
            $user->current_session_start = null;
            $user->inactivity_threshold = null;
            $user->save();

            return response()->json([
                'message' => 'Session ended successfully',
                'session_duration_seconds' => $sessionDuration,
                'session_duration_formatted' => $this->secondsToTime($sessionDuration)
            ]);
        }

        return response()->json([
            'message' => 'No active session to end',
            'session_active' => false
        ], 400);
    }

    public function extendSession(Request $request)
    {
        $user = Auth::user();

        if ($user->current_session_start) {
            // Extend inactivity threshold by 2 more minutes
            $user->inactivity_threshold = now()->addMinutes(2);
            $user->save();

            return response()->json([
                'message' => 'Session extended successfully',
                'new_expiry_time' => $user->inactivity_threshold,
                'expires_in_seconds' => now()->diffInSeconds($user->inactivity_threshold)
            ]);
        }

        return response()->json([
            'message' => 'No active session to extend',
            'session_active' => false
        ], 400);
    }

    protected function endInactiveSession($user)
    {
        if ($user->current_session_start) {
            $sessionDuration = now()->diffInSeconds($user->current_session_start);
            $user->total_online_seconds += $sessionDuration;

            $user->current_session_start = null;
            $user->inactivity_threshold = null;
            $user->save();
        }
    }

    private function secondsToTime($seconds)
    {
        if ($seconds <= 0) return '00:00:00';

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
