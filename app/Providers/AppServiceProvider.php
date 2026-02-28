<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // PHP 8.4+ raises E_NOTICE when tempnam() creates a file; suppress so Laravel doesn't convert it to an exception
        $previous = set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) use (&$previous): bool {
            if ($errno === \E_NOTICE && str_contains($errstr, 'tempnam(): file created in the system')) {
                return true;
            }
            $result = false;
            if (is_callable($previous)) {
                try {
                    $result = $previous($errno, $errstr, $errfile, $errline);
                } catch (\Throwable) {
                    $result = false;
                }
            }
            return (bool) $result;
        });
    }
}
