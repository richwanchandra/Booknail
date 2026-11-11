<?php

namespace App\Providers;

use App\Services\WaGateway\FonnteDriver;
use App\Services\WaGateway\MockDriver;
use App\Services\WaGateway\WaGatewayInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            WaGatewayInterface::class,
            function () {
                $driver = config('services.fonnte.driver', 'mock');
                if ($driver === 'fonnte') {
                    return new FonnteDriver;
                }

                return new MockDriver;
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Blade::prepareStringsForCompilationUsing(function ($string) {
            return preg_replace_callback('/<x-lucide-([a-z0-9-]+)\b(.*?)(\/>|>(.*?)<\/x-lucide-\1>)/s', function ($matches) {
                $icon = $matches[1];
                $attrs = $matches[2];

                return "<x-icon name=\"{$icon}\"{$attrs} />";
            }, $string);
        });
    }
}
