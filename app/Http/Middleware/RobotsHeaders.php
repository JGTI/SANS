<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RobotsHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        foreach ((array) config('seo.noindex', []) as $pattern) {
            if ($request->is($pattern)) {
                $response->headers->set(
                    'X-Robots-Tag',
                    'noindex, nofollow, noarchive, nosnippet'
                );

                break;
            }
        }

        return $response;
    }
}
