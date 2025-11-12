<?php


namespace Vendor\ErrorReporter\Support;


class RequestSnapshot
{
    public function __construct(
        public ?string $method,
        public ?string $url,
        public array $headers,
        public array $input,
        public ?string $ip,
        public ?int $userId,
        public ?string $userEmail,
        public array $server
    ) {}

    public static function make(): self
    {
        try {
            $req = request();
            $user = auth()->user();
            $headers = [];

            foreach (['User-Agent','Accept','Referer','X-Request-Id'] as $h) {
                if ($req->headers->has($h)) {
                    $headers[$h] = $req->headers->get($h);
                }
            }

            $input = $req->all();
            foreach (['password','password_confirmation','current_password','token','secret','Authorization'] as $key) {
                if (array_key_exists($key, $input)) {
                    $input[$key] = '***';
                }
            }

            return new self(
                method_exists($req, 'method') ? $req->method() : null,
                method_exists($req, 'fullUrl') ? $req->fullUrl() : null,
                $headers,
                $input,
                method_exists($req, 'ip') ? $req->ip() : null,
                $user?->id,
                $user?->email,
                [
                    'php_sapi' => PHP_SAPI,
                    'cli' => app()->runningInConsole(),
                ],
            );
        } catch (\Throwable $e) {
            return new self(null, null, [], [], null, null, null, []);
        }
    }
}