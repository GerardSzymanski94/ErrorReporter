@php($ex = $p['exception'] ?? null)
@php($snap = $p['snapshot'] ?? null)


<h2 style="margin:0 0 8px;font:16px/1.3 system-ui,Segoe UI,Roboto,Arial">Błąd: {{ strtoupper($p['level']) }}</h2>
<p style="margin:0 0 12px;color:#555">{{ $p['occurred_at'] }} — {{ $p['app_name'] }} ({{ $p['app_env'] }})</p>
<hr>


<h3 style="font:15px/1.3 system-ui;margin:16px 0 8px">Wiadomość</h3>
<pre style="white-space:pre-wrap;font:12px/1.4 ui-monospace,Consolas,Monaco">{{ $p['message'] }}</pre>


@if($ex instanceof \Throwable)
    <h3 style="font:15px/1.3 system-ui;margin:16px 0 8px">Exception</h3>
    <p style="margin:0 0 8px">{{ get_class($ex) }} @ {{ $ex->getFile() }}:{{ $ex->getLine() }}</p>
    <pre style="white-space:pre-wrap;font:12px/1.4 ui-monospace,Consolas,Monaco">{{ $ex->getTraceAsString() }}</pre>
@endif


@if(!empty($p['context']))
    <h3 style="font:15px/1.3 system-ui;margin:16px 0 8px">Kontekst</h3>
    <pre style="white-space:pre-wrap;font:12px/1.4 ui-monospace,Consolas,Monaco">{{ json_encode($p['context'], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
@endif


@if($snap && config('error-reporter.include_request'))
    <h3 style="font:15px/1.3 system-ui;margin:16px 0 8px">Żądanie</h3>
    <ul style="margin:0 0 8px;padding-left:18px;color:#333">
        <li><strong>URL</strong>: {{ $snap->url }}</li>
        <li><strong>Metoda</strong>: {{ $snap->method }}</li>
        <li><strong>IP</strong>: {{ $snap->ip }}</li>
        <li><strong>User ID</strong>: {{ $snap->userId }}</li>
        <li><strong>User Email</strong>: {{ $snap->userEmail }}</li>
    </ul>
    <details>
        <summary style="cursor:pointer">Parametry wejściowe</summary>
        <pre style="white-space:pre-wrap;font:12px/1.4 ui-monospace,Consolas,Monaco">{{ json_encode($snap->input, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
    </details>
@endif