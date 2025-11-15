<?php

namespace GerardSzymanski\ErrorReporter\Listeners;

use GerardSzymanski\ErrorReporter\Mail\ErrorReport;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendErrorMail
{
    /**
     * Główna obsługa zdarzenia logu.
     */
    public function handle(MessageLogged $event): void
    {
        $levels = config('error-reporter.levels', ['error','critical','alert','emergency']);

        if (! in_array(strtolower($event->level), $levels, true)) {
            return;
        }

        $to = config('error-reporter.to');
        $bcc = config('error-reporter.bcc');

        if (!$to) {
            return; // brak adresu — brak akcji
        }

        // Zbuduj dane raportu
        $exception = $event->context['exception'] ?? null;
        $snapshot = \GerardSzymanski\ErrorReporter\Support\RequestSnapshot::make();

        $payload = [
            'level' => $event->level,
            'message' => (string) $event->message,
            'context' => $this->sanitizeContext($event->context ?? []),
            'exception' => $exception,
            'snapshot' => $snapshot,
            'occurred_at' => now(),
            'app_env' => app()->environment(),
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
        ];

        // Anty‑spam/throttling per unikalna treść
        $ttl = max(0, (int) config('error-reporter.throttle_seconds', 60));

        $hash = sha1(json_encode([
            $payload['level'],
            $payload['message'],
            optional($exception)->getFile(),
            optional($exception)->getLine(),
            optional($exception)->getCode(),
        ]));
        $cacheKey = "error-reporter:sent:".$hash;

        if ($ttl > 0 && Cache::has($cacheKey)) {
            return; // już wysłane w oknie TTL
        }

        $mailable = new ErrorReport($payload);
        $from = config('error-reporter.from');

        if ($from) {
            $mailable->from($from);
        }

        if (config('error-reporter.queue')) {
            Mail::to($to)->cc($bcc)->queue($mailable);
        } else {
            Mail::to($to)->cc($bcc)->send($mailable);
        }

        if ($ttl > 0) {
            Cache::put($cacheKey, true, now()->addSeconds($ttl));
        }
    }

    /**
     * Usuń potencjalnie wrażliwe dane (np. hasła) z kontekstu.
     */
    protected function sanitizeContext(array $context): array
    {
        $blocked = ['password', 'password_confirmation', 'current_password', 'token', 'secret', 'Authorization'];

        array_walk_recursive($context, function (&$value, $key) use ($blocked) {
            if (in_array((string) $key, $blocked, true)) {
                $value = '***';
            }
        });

        return $context;
    }
}