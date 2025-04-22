<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventImageCaching
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if ($response->headers->has('Content-Type')) {
            if (strpos($response->headers->get('Content-Type'), 'image/') === 0) {
                $response->headers->set('Cache-Control', 'public, max-age=31536000');
                $response->headers->set('Pragma', 'public');
            }
        }
        
        return $response;
    }
} 