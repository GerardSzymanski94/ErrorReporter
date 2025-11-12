<?php

namespace GerardSzymanski\ErrorReporter\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorReport extends Mailable
{
    use Queueable, SerializesModels;


    public array $payload;


    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->subject($this->buildSubject());
    }


    protected function buildSubject(): string
    {
        $app = $this->payload['app_name'] ?? config('app.name');
        $lvl = strtoupper((string) ($this->payload['level'] ?? 'ERROR'));
        $msg = (string) ($this->payload['message'] ?? '');
        $env = (string) ($this->payload['app_env'] ?? '');
        return sprintf('[%s][%s] %s — %s', $app, $env, $lvl, mb_strimwidth($msg, 0, 80, '…'));
    }


    public function build()
    {
        return $this->view('error-reporter::mail.error-report', [
            'p' => $this->payload,
        ]);
    }
}