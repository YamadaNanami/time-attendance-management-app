<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     *
     */
    public function handle(Request $request, Closure $next,$role)
    {
        $roleNum = match ($role) {
            'user' => 1,
            'admin' => 2,
            default => 1
        };

        if(Auth::check() && Auth::user()->role == $roleNum){
            return $next($request);
        }

        // ロールが不一致の場合の返却処理
        abort(403, 'このページにアクセスする権限がありません');
    }
}
