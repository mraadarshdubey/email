<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public newsletter signup for the marketing site (sendpeak.in). Throttled
// because it is unauthenticated and reachable cross-origin.
Route::middleware('throttle:10,1')
    ->post('/newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class, 'subscribe']);

// Amazon SES bounce/complaint feedback, delivered by SNS. Called by AWS only.
Route::post('/ses/webhook', [\App\Http\Controllers\SesWebhookController::class, 'handle'])
    ->name('ses.webhook');
