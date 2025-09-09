<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add cache headers for home API endpoints
        if ($this->isHomeApiEndpoint($request)) {
            $this->addCacheHeaders($response, $request);
        }

        return $response;
    }

    /**
     * Check if the request is for a home API endpoint
     */
    private function isHomeApiEndpoint(Request $request): bool
    {
        $path = $request->path();
        
        return str_contains($path, 'api/v1.0/top-headings') || 
               str_contains($path, 'api/v1.0/preferred/articles');
    }

    /**
     * Add appropriate cache headers to the response
     */
    private function addCacheHeaders(Response $response, Request $request): void
    {
        $path = $request->path();
        
        if (str_contains($path, 'top-headings')) {
            // Cache top-headings for 15 minutes
            $response->headers->set('Cache-Control', 'public, max-age=900');
            $response->headers->set('X-Cache-Status', 'enabled');
            $response->headers->set('X-Cache-TTL', '900');
        } elseif (str_contains($path, 'preferred/articles')) {
            // Cache preferred articles for 10 minutes
            $response->headers->set('Cache-Control', 'public, max-age=600');
            $response->headers->set('X-Cache-Status', 'enabled');
            $response->headers->set('X-Cache-TTL', '600');
        }
        
        // Add ETag for better caching
        $etag = md5($response->getContent());
        $response->headers->set('ETag', $etag);
    }
}
