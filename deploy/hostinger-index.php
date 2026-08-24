<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Hostinger shared-hosting entry point
|--------------------------------------------------------------------------
|
| This is public/index.php with the app-code paths adjusted: on shared
| hosting the document root is fixed to public_html, so the rest of the
| Laravel app lives one level up in a sibling folder ("kaxon_app") that is
| NOT web-accessible. Only this file's directory (public_html) is served.
|
*/

$appPath = __DIR__ . '/../kaxon_app';

if (file_exists($maintenance = $appPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appPath . '/vendor/autoload.php';

$app = require_once $appPath . '/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
