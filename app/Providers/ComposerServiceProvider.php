<?php

namespace App\Providers;

use App\Http\ViewComposers\SortingFiltersComposer;
use Illuminate\Support\ServiceProvider;
use View;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
        View::composer('sortings.filters', SortingFiltersComposer::class);
        
    }

    /**
     * Register
     *
     * @return void
     */
    public function register()
    {
        //
    }
}