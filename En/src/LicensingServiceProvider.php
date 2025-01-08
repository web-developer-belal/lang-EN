<?php
namespace zPlus\Licensing;

use Illuminate\Support\ServiceProvider;

class LicensingServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the LicenseManager singleton
        $this->app->singleton(LicenseManager::class, function () {
            return new LicenseManager();
        });
    }

    public function boot()
    {
        // Hook into the request lifecycle
        app(LicenseManager::class)->verifyLicense();
    }
}

