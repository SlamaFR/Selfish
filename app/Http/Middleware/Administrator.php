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
                "title" => __('general.error'),
                "message" => __('toast.error.permission')
            ], 401);
        }
        return $next($request);
    }
}
