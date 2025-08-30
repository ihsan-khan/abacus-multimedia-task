<?php

namespace App\Http\Controllers\API;

use App\Models\UserSession;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class UserActivityController extends Controller
{
    public function getLoginDuration()
    {
        $sessions = UserSession::where('user_id', auth()->id())
            ->orderBy('login_at', 'desc')
            ->first();

        if (!$sessions) {
            return response()->json(['message' => 'No active session found'], 404);
        }

        $loginAt = Carbon::parse($sessions->login_at);
        $endTime = $sessions->logout_at ? Carbon::parse($sessions->logout_at) : now();

        $durationSeconds = $loginAt ? $endTime->diffInSeconds($loginAt) : 0;

        return response()->json([
            'status' => true,
            'data' => [
                'login_at' => $sessions->login_at,
                'logout_at' => $sessions->logout_at,
                'duration_in_minutes' => round($durationSeconds / 60, 2),
                'is_active' => is_null($sessions->logout_at)
            ]
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

    public function getOnlineDuration()
    {
        $user = auth()->user();

        // Get latest session
        $session = UserSession::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$session) {
            return response()->json([
                'status' => true,
                'online_duration' => 0,
                'is_active' => false,
                'message' => 'No active session found'
            ]);
        }

        // If user logged out, use logout time
        if ($session->logout_at) {
            $endTime = Carbon::parse($session->logout_at);
            $isActive = false;
        } else {
            // If user is still logged in but idle check
            $isActive = $user->isOnline(5); // 5 min threshold
            $endTime = $isActive ? now() : Carbon::parse($user->last_activity_at);
        }

        $loginAt = Carbon::parse($session->login_at);
        $durationSeconds = $endTime->diffInSeconds($loginAt);

        return response()->json([
            'status' => true,
            'online_duration' => [
                'seconds' => $durationSeconds,
                'minutes' => round($durationSeconds / 60, 2),
                'hours'   => round($durationSeconds / 3600, 2),
            ],
            'is_active' => $isActive,
            'last_activity_at' => $user->last_activity_at,
        ]);
    }
}
