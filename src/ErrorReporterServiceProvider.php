<?php

namespace GerardSzymanski\ErrorReporter;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use GerardSzymanski\ErrorReporter\Listeners\SendErrorMail;

class ErrorReporterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/error-reporter.php', 'error-reporter');
    }

    public function boot(): void
    {
        // Nasłuchuj wszystkich logów i reaguj na poziomy z konfiguracji
        Event::listen(MessageLogged::class, [SendErrorMail::class, 'handle']);

        // Publikacje
        $this->publishes([
            __DIR__ . '/../config/error-reporter.php' => config_path('error-reporter.php'),
        ], 'error-reporter-config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'error-reporter');
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/error-reporter'),
        ], 'error-reporter-views');
    }
}