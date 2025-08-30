<?php

namespace App\Http\Controllers\API;

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
}
