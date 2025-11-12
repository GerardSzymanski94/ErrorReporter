<?php

namespace GerardSzymanski\ErrorReporter\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class ErrorReport extends Mailable
{
    use Queueable, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->makeSubject()
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'error-reporter::mail.error-report',
            with: ['p' => $this->payload]
        );
    }

    private function makeSubject(): string
    {
        $app = $this->payload['app_name'] ?? config('app.name');
        $lvl = strtoupper((string) ($this->payload['level'] ?? 'ERROR'));
        $msg = (string) ($this->payload['message'] ?? '');
        $env = (string) ($this->payload['app_env'] ?? '');
        return sprintf('[%s][%s] %s — %s', $app, $env, $lvl, mb_strimwidth($msg, 0, 80, '…'));
    }
}
