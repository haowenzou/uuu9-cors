<?php

namespace U9\Cors\Middleware;

use Illuminate\Support\ServiceProvider;

class CorsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
    }

    public function register()
    {
        $request = app('request');
        if ($request->isMethod('OPTIONS'))
        {
            app()->options(
                $request->path(),
                function() use ($request)
                {
                    $cors = app()->make('U9\Cors\Middleware\Cors');
                    return $cors->setOptionsHeaders($request, response('', 204));
                }
            );
        }
    }
}
