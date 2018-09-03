<?php namespace App\Providers;

use App\Models\Agreement;
use App\Models\Client;
use App\Models\Marketplace;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use App\Observers\AgreementObserver;
use App\Observers\ClientObserver;
use App\Observers\MarketplaceObserver;
use App\Observers\ProviderObserver;
use App\Observers\ServiceObserver;
use App\Observers\UserObserver;
use App\Services\Validation;
use App\Services\WorkdaysCalculationService;
use Blade;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::resolver(function ($translator, $data, $rules, $messages) {
            return new Validation($translator, $data, $rules, $messages);
        });

        Blade::directive('cache', function ($expression) {
            return "<?php if (! App\\Services\\Cache\\ViewCaching::setUp{$expression}) { ?>";
        });
        Blade::directive('endcache', function () {
            return "<?php } echo App\\Services\\Cache\\ViewCaching::tearDown() ?>";
        });

        Agreement::observe(AgreementObserver::class);
        Client::observe(ClientObserver::class);
        Marketplace::observe(MarketplaceObserver::class);
        Provider::observe(ProviderObserver::class);
        Service::observe(ServiceObserver::class);
        User::observe(UserObserver::class);
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Illuminate\Contracts\Auth\Registrar',
            'App\Services\Registrar'
        );

        $this->app->singleton('workdays_calculation_service', function (Application $app) {
            $instance = $app->make(WorkdaysCalculationService::class);

            return $instance;
        });
    }

}
