<?php

namespace App\Http\Middleware;

use App\Models\Country;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CountryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $country_code = $request->route('country_code');

        $country = Country::where('country_code', $country_code)->first();

        if (!$country) {
            abort(404);
        }

        session(['selected_country' => $country->id]);
        session(['selected_country_code' => $country_code]); 

        return $next($request);
    }
}
