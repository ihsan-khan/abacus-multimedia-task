<?php

namespace App\Http\Controllers\API;

use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $activities = UserActivity::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $currentSession = UserActivity::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        $currentSessionData = null;
        if ($currentSession) {
            $loginAt = $currentSession->login_at;
            $lastActivityAt = $currentSession->last_activity_at;
            $loginDuration = null;
            if ($loginAt && $lastActivityAt) {
                $loginDuration = now()->diffInSeconds($loginAt);
            }
            $currentSessionData = [
                'login_duration_seconds' => $loginDuration,
                'login_at' => $loginAt,
                'last_activity_at' => $lastActivityAt
            ];
        }

        return response()->json([
            'activities' => $activities,
            'current_session' => $currentSessionData
        ]);
    }
}
