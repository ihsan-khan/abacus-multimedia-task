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
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();

            $activity = UserActivity::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest('last_activity_at')
                ->first();

            if ($activity) {
                $activity->update(['last_activity_at' => now()]);
            } else {
                UserActivity::create([
                    'user_id' => $user->id,
                    'last_activity_at' => now(),
                    'logout_at' => null,
                ]);
            }
        }

        return $response;
    }
}
