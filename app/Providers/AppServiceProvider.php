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

            // Self-healing MySQL schema (AUTO_INCREMENT & column defaults) if missing in production
            try {
                if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
                    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
                    try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `bookings` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;'); } catch (\Throwable $e) {}
                    try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `booking_details` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;'); } catch (\Throwable $e) {}
                    try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `payments` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;'); } catch (\Throwable $e) {}
                    try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `bookings` MODIFY `delay_minutes` INT NOT NULL DEFAULT 0;'); } catch (\Throwable $e) {}
                    try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `bookings` MODIFY `is_delayed` TINYINT(1) NOT NULL DEFAULT 0;'); } catch (\Throwable $e) {}
                    try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE `bookings` MODIFY `artist_confirmation_status` VARCHAR(30) NOT NULL DEFAULT 'pending';"); } catch (\Throwable $e) {}
                    try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE `bookings` MODIFY `booking_source` VARCHAR(30) NOT NULL DEFAULT 'WEB_ONLINE';"); } catch (\Throwable $e) {}
                    try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE `bookings` MODIFY `branch` VARCHAR(50) NULL DEFAULT 'Citra Garden 6';"); } catch (\Throwable $e) {}
                    try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `users` MODIFY `loyalty_points` INT UNSIGNED NOT NULL DEFAULT 0;'); } catch (\Throwable $e) {}
                    try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;'); } catch (\Throwable $e) {}
                    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
                }
            } catch (\Throwable $e) {
                // Silently ignore if restricted
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
