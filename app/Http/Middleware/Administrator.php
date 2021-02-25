<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Administrator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guest() || !Auth::user()->admin) {
            if ($request->isMethod('GET')) {
                return redirect('/');
            }
            return response()->json([
                "message" => "You are not authorized to do this."
            ], 401);
        }
        return $next($request);
    }
}
