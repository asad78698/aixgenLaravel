<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRequestMethod
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('CheckRequestMethod middleware triggered.');


        //middleware to check if the request is POST and logic start from here
        
        if ($request->isMethod('GET')) {
            if ($request->route()->getName() == 'createsale') {
                return redirect()->route('home');
            } else {
                return redirect()->route('showcharts')->with('error', 'Please submit the form using POST method.');
            }
        }

        //middleware to check if the request is POST and logic end here

        return $next($request);
    }
}
