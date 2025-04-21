<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    public function handle(Request $req, Closure $next): Response
    {
        $user = $req->user('sanctum');

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'result' => false,
                'message' => 'Access denied. Admins only.',
                "data" => []
            ], 403);
        } 
        
        return $next($req);
    }
}
