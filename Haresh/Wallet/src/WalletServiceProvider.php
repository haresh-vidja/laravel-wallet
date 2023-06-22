<?php
namespace Haresh\Wallet;

use Illuminate\Support\ServiceProvider;

/**
 * Class WalletServiceProvider
 * 
 * Registers wallet-related services and publishes package resources.
 */
class WalletServiceProvider extends ServiceProvider
{
    /**
     * Bootstraps the package services and loads migrations.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/wallet.php' => config_path('wallet.php'),
        ], 'wallet-config');
    }

    /**
     * Registers wallet services within the Laravel container.
     */
    public function register()
    {
        $this->app->singleton('wallet', function () {
            return new WalletManager();
        });
    }
}
