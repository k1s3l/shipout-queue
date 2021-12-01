<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Webhook
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
        return $request->header('token') != env('WEBHOOK_TOKEN') ? $next($request) : route('login');
    }
}
