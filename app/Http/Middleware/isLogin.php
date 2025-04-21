<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isLogin
{
    public function handle(Request $req, Closure $next): Response
    {
        $user = $req->user('sanctum');

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => 'Unauthorized. Please log in.',
                "data" => []
            ], 401);
        } 
        
        return $next($req);
    }
}
