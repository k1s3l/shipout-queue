<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SmsWebhook
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
        return $request->header('api_key') == env('NEXMO_KEY') ? $next($request) : abort(404);
    }
}
