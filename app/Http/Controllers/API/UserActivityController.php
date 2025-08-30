<?php

namespace App\Http\Controllers\API;

use App\Models\UserSession;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserActivityController extends Controller
{
    public function show()
    {
        $activity = UserActivity::where('user_id', Auth::id())
            ->latest('login_at')
            ->first();

        if (!$activity) {
            return response()->json(['message' => 'No activity found'], 404);
        }

        $loginDuration = $activity->login_duration;
        $onlineDuration = $activity->online_duration;

        return response()->json([
            'login_duration_seconds' => $loginDuration,
            'online_duration_seconds' => $onlineDuration,
            'activity' => $activity
        ]);
    }

    public function getLoginDurations()
    {
        $sessions = UserSession::where('user_id', auth()->id())
            ->orderBy('login_at', 'desc')
            ->get();

        $formatted = $sessions->map(function ($session) {
            // If logout_at is null → use current time
            $loginAt = \Carbon\Carbon::parse($session->login_at);
            $endTime = $session->logout_at ? \Carbon\Carbon::parse($session->logout_at) : now();

            $durationSeconds = $loginAt ? $endTime->diffInSeconds($loginAt) : 0;

            return [
                'login_at' => $session->login_at,
                'logout_at' => $session->logout_at, // null if still online
                'duration_in_minutes' => round($durationSeconds / 60, 2),
                'is_active' => is_null($session->logout_at) // true if still online
            ];
        });

        // Total duration (including active session)
        $totalDurationSeconds = $sessions->sum(function ($session) {
            $loginAt = \Carbon\Carbon::parse($session->login_at);
            $endTime = $session->logout_at ? \Carbon\Carbon::parse($session->logout_at) : now();
            return $loginAt ? $endTime->diffInSeconds($loginAt) : 0;
        });

        $totalDurationMinutes = round($totalDurationSeconds / 60, 2);
        $totalDurationHours   = round($totalDurationSeconds / 3600, 2);

        return response()->json([
            'status' => true,
            'data' => [
                'sessions' => $formatted,
                'total_duration' => [
                    'seconds' => $totalDurationSeconds,
                    'minutes' => $totalDurationMinutes,
                    'hours'   => $totalDurationHours,
                ]
            ]
        ]);
    }
}
