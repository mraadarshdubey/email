<?php
/**
 * One-time remote setup runner for shared hosting without SSH access.
 *
 * Place this file in public_html/ alongside index.php, visit it once in
 * your browser with the correct token, then DELETE this file immediately.
 * It runs `migrate --force` (and optionally `storage:link` / `db:seed`)
 * against your production database.
 *
 * Usage: https://yourdomain.com/remote-setup.php?token=YOUR_TOKEN&action=migrate
 * Actions: migrate | seed-admin | storage-link
 */

// CHANGE THIS to the token you generated — do not deploy with the default.
$SETUP_TOKEN = 'REPLACE_WITH_YOUR_TOKEN';

if (! hash_equals($SETUP_TOKEN, $_GET['token'] ?? '')) {
    http_response_code(403);
    exit('Forbidden.');
}

// Path to the app code directory (sibling to public_html — adjust if different).
$appPath = __DIR__ . '/../kaxon_app';

require $appPath . '/vendor/autoload.php';
$app = require_once $appPath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

header('Content-Type: text/plain');

$action = $_GET['action'] ?? 'migrate';

switch ($action) {
    case 'migrate':
        $kernel->call('migrate', ['--force' => true]);
        break;
    case 'seed-admin':
        $kernel->call('db:seed', ['--class' => 'AdminSeeder', '--force' => true]);
        break;
    case 'storage-link':
        $kernel->call('storage:link');
        break;
    default:
        exit("Unknown action: {$action}\n");
}

echo $kernel->output();
echo "\nDone. Remember to delete this file (remote-setup.php) now.\n";
