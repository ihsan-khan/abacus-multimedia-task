<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UserActivity;
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
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $activity = UserActivity::where('user_id', $user->id)
                ->whereNull('logout_time')
                ->latest('login_time')
                ->first();
            if ($activity) {
                $activity->last_activity = now();
                $activity->save();
            }
        }

        return $next($request);
    }
}
