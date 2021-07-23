<?php

namespace App\Providers;

use App\Services\PaystackClient;
use Illuminate\Support\ServiceProvider;

class PaystackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PaystackClient::class, function () {
            $client = new PaystackClient();

            $client->baseUrl(config('services.paystack.base_url'))
                ->withHeaders(['Accept' => 'application/json'])
                ->withToken(config('services.paystack.secret_key'));

            return $client;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
