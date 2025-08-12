<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransformJsonFields
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('sinais_vitais_json')) {
            $request->merge([
                'sinais_vitais' => json_decode($request->sinais_vitais_json, true)
            ]);
        }

        return $next($request);
    }
}
