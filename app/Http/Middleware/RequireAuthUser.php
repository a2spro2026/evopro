<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAuthUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('auth_user')) {
            return redirect('/')
                ->withErrors(['login' => 'Veuillez vous connecter avec un utilisateur enregistré.']);
        }

        return $next($request);
    }
}
