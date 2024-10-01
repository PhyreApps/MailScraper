<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BladeUI\Icons\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->callAfterResolving(Factory::class, function (Factory $factory) {
            $factory->add('myicons', [
                'path' =>realpath( __DIR__ . '/../../resources/myicons'),
                'prefix' => 'myicons'
            ]);
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
