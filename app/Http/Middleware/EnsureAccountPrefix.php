<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountPrefix
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        $expectedPrefix = $user->routePrefix();
        $currentPrefix = $request->route('account');

        URL::defaults([
            'account' => $expectedPrefix,
        ]);

        if ($currentPrefix !== $expectedPrefix) {
            $segments = $request->segments();

            array_shift($segments);

            $newPath = $expectedPrefix;

            if (count($segments)) {
                $newPath .= '/' . implode('/', $segments);
            }

            $query = $request->getQueryString();

            return redirect('/' . $newPath . ($query ? '?' . $query : ''));
        }

        return $next($request);
    }
}