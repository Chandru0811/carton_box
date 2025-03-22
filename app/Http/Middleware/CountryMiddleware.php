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
    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve the country_code from the route parameters
        $country_code = $request->route('country_code');

        // Validate that the country_code is present
        if (!$country_code) {
            abort(404, 'Country code is missing.');
        }

        // Fetch the country from the database
        $country = Country::where('country_code', $country_code)->first();
        if (!$country) {
            abort(404, 'Country not found.');
        }

        // Store the selected country and country code in the session
        session([
            'selected_country' => $country->id,
            'selected_country_code' => $country_code,
        ]);

        // Proceed to the next middleware or route handler
        return $next($request);
    }
}
