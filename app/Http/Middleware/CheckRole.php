<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Pemakaian di route: ->middleware('role:admin') atau 'role:admin,petugas'
     * Dijalankan SETELAH middleware auth, sehingga $request->user() sudah pasti ada.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = $request->user()?->loadMissing('role')->role?->name;

        if (! in_array($role, $roles, true)) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk: ' . implode(', ', $roles) . '.');
        }

        return $next($request);
    }
}
