<?php

namespace App\Providers;

use App\Classes\Channels\SmsRuApi;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository as Config;

class SmsRuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SmsRuApi::class, function ($app) {
            return $this->createSmsRuClient($app['config']);
        });
    }

    public function provides()
    {
        return [
            SmsRuApi::class,
        ];
    }

    /**
     * @param Config $config
     * @throw RuntimeException
     * @return SmsRuApi
     */
    protected function createSmsRuClient(Config $config)
    {
        if (! $config->has('sms_ru')) {
            $this->raiseRuntimeException('Config sms_ru should be not empty');
        }

        if (! $config->has('sms_ru.api_id')) {
            $this->raiseRuntimeException('API key is required');
        }

        return new SmsRuApi($config->get('sms_ru'));
    }

    protected function raiseRuntimeException($message)
    {
        throw new \RuntimeException($message);
    }
}
