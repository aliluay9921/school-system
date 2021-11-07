<?php

namespace App\Http\Middleware;

use App\Traits\SendResponse;
use Closure;
use Illuminate\Http\Request;

class ManagerMiddleware
{
    use SendResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->user_type == 1 || auth()->user()->user_type == 0) {
            return $next($request);
        } else {
            return $this->send_response(401, 'غير مصرح لك بالدخول', [], []);
        }
    }
}