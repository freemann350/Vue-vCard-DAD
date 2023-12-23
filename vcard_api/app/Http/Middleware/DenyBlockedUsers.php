<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Vcard;

class DenyBlockedUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = $request->input('username'); 

        $vCard = Vcard::withTrashed()->where('phone_number', $username)->first();

        if ($vCard && ($vCard->blocked == 1 || $vCard->deleted_at != NULL)) 
        {
            return response()->json(['error' => 'Your account is blocked.'], 403);
        }

        return $next($request);
    }
}
