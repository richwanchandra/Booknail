<?php

namespace App\Providers;

use App\Services\WaGateway\FonnteDriver;
use App\Services\WaGateway\MockDriver;
use App\Services\WaGateway\WaGatewayInterface;
use Illuminate\Support\Facades\URL;
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
        // Force HTTPS in production (Railway reverse proxy)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            // Self-healing MySQL AUTO_INCREMENT if missing in production
            try {
                if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
                    $hasAi = \Illuminate\Support\Facades\DB::selectOne("
                        SELECT EXTRA FROM INFORMATION_SCHEMA.COLUMNS
                        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND COLUMN_NAME = 'id'
                    ");
                    if ($hasAi && !str_contains(strtolower($hasAi->EXTRA ?? ''), 'auto_increment')) {
                        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
                        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `bookings` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');
                        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `booking_details` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');
                        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `payments` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');
                        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
                    }
                }
            } catch (\Throwable $e) {
                // Silently ignore if already altered or restricted
            }
        }

        \Illuminate\Support\Facades\Blade::prepareStringsForCompilationUsing(function ($string) {
            return preg_replace_callback('/<x-lucide-([a-z0-9-]+)\b(.*?)(\/>|>(.*?)<\/x-lucide-\1>)/s', function ($matches) {
                $icon = $matches[1];
                $attrs = $matches[2];

                return "<x-icon name=\"{$icon}\"{$attrs} />";
            }, $string);
        });
    }
}
