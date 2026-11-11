<?php

namespace App\Http\Middleware;

use App\Support\AppSectionsLock;
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

        if (AppSectionsLock::isDisabled() && ! AppSectionsLock::currentUserCanControl()) {
            $path = trim($request->path(), '/');
            $allowed = $path === 'dashboard'
                || $path === 'deconnexion'
                || $request->routeIs('dashboard')
                || $request->routeIs('logout');

            if (! $allowed) {
                if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'ok' => false,
                        'message' => AppSectionsLock::message(),
                    ], 403);
                }

                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
