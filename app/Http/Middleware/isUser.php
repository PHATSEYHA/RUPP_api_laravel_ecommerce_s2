<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isUser
{
    public function handle(Request $req, Closure $next): Response
    {
        $user = $req->user('sanctum');

        if (!$user || $user->role !== 'user') {
            return response()->json([
                'result' => false,
                'message' => 'Access denied. Users only.',
                "data" => []
            ], 403);
        } 
        
        return $next($req);
    }
}
