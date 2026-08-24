<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\InstantCampaignController;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IndividualEmailController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactTagController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\EmailActivityController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\AutomationRuleController;
use App\Http\Controllers\AutomationSequenceController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\EmailFollowupController;
use App\Http\Controllers\LeadFormController;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\LeadFormPublicController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Middleware\TickAutomations;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('home');
// });

Auth::routes(['register' => false, 'verify' => false, 'reset' => false]);

Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

// Public email tracking endpoints (hit directly by recipients' mail clients/browsers, no auth)
// Generous limit — legitimate clients can re-fetch the pixel / re-click multiple times.
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/t/o/{token}', [TrackingController::class, 'open'])->name('tracking.open');
    Route::get('/t/c/{token}', [TrackingController::class, 'click'])->name('tracking.click');
});

// Public lead-capture form pages (no auth — anyone with the link can view/submit)
Route::middleware('throttle:30,1')->get('/f/{slug}', [LeadFormPublicController::class, 'show'])->name('lead-forms.public.show');
Route::middleware('throttle:10,1')->post('/f/{slug}', [LeadFormPublicController::class, 'submit'])->name('lead-forms.public.submit');

// One-click unsubscribe (RFC 8058). No auth: the signature makes the link
// tamper-proof, so recipients can opt out straight from an email.
Route::middleware('signed')->group(function () {
    Route::get('/unsubscribe', [UnsubscribeController::class, 'show'])->name('unsubscribe.show');
    Route::post('/unsubscribe', [UnsubscribeController::class, 'perform'])->name('unsubscribe.perform');
});

// All application routes require authentication
Route::middleware(['auth', TickAutomations::class])->group(function () {
    // Dashboard & Home Routes
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/instant/campaign', [InstantCampaignController::class, 'create'])->name('instant.campaign.create');
    Route::post('/instant/campaign', [InstantCampaignController::class, 'import'])->name('instant.campaign.import');
    Route::get('/saved/templates', [HomeController::class, 'savedTemplates'])->name('saved.templates');

    // AI email drafting
    Route::middleware('throttle:20,1')->post('/ai/compose-email', [AiController::class, 'composeEmail'])->name('ai.compose-email');

    // Email Account Management Routes
    Route::resource('email-accounts', EmailAccountController::class)->except(['show']);
    Route::post('/email-accounts/{emailAccount}/set-default', [EmailAccountController::class, 'setDefault'])->name('email-accounts.set-default');
    Route::post('/email-accounts/{emailAccount}/toggle-active', [EmailAccountController::class, 'toggleActive'])->name('email-accounts.toggle-active');
    Route::post('/email-accounts/{emailAccount}/test', [EmailAccountController::class, 'test'])->name('email-accounts.test');

    // Email Template Management Routes
    Route::resource('email-templates', EmailTemplateController::class);
    Route::post('/email-templates/{emailTemplate}/toggle-active', [EmailTemplateController::class, 'toggleActive'])->name('email-templates.toggle-active');
    Route::post('/email-templates/{emailTemplate}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('email-templates.duplicate');
    Route::get('/api/email-templates/{emailTemplate}', [EmailTemplateController::class, 'getTemplate'])->name('email-templates.get');

    // Profile Management Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings/password', [ProfileController::class, 'showPasswordForm'])->name('settings.password');
    Route::put('/settings/password', [ProfileController::class, 'updatePassword'])->name('settings.password.update');

    // Individual Email Routes
    Route::get('/individual-emails', [IndividualEmailController::class, 'create'])->name('individual-emails.create');
    Route::post('/individual-emails/send', [IndividualEmailController::class, 'send'])->name('individual-emails.send');
    Route::post('/individual-emails/validate', [IndividualEmailController::class, 'validateEmails'])->name('individual-emails.validate');

    // Email Activity (open/click tracking)
    Route::get('/broadcasts', [BroadcastController::class, 'index'])->name('broadcasts.index');
    Route::get('/email-activity', [EmailActivityController::class, 'index'])->name('email-activity.index');

    // Contact Management Routes
    Route::get('/contacts/import/form', [ContactController::class, 'importForm'])->name('contacts.import.form');
    Route::post('/contacts/import', [ContactController::class, 'import'])->name('contacts.import');
    Route::post('/contacts/bulk-action', [ContactController::class, 'bulkAction'])->name('contacts.bulk-action');
    Route::resource('contacts', ContactController::class);

    // Contact Tag Management Routes
    Route::resource('tags', ContactTagController::class)->except(['show']);

    // Automation Rules (if-this-then-that)
    Route::post('/automation-rules/{automation_rule}/toggle-active', [AutomationRuleController::class, 'toggleActive'])->name('automation-rules.toggle-active');
    Route::resource('automation-rules', AutomationRuleController::class)->except(['show']);

    // Automation Sequences (multi-step drip campaigns)
    Route::post('/automation-sequences/{automation_sequence}/toggle-active', [AutomationSequenceController::class, 'toggleActive'])->name('automation-sequences.toggle-active');
    Route::post('/automation-sequences/{automation_sequence}/run-now', [AutomationSequenceController::class, 'runNow'])->name('automation-sequences.run-now');
    Route::resource('automation-sequences', AutomationSequenceController::class);

    // RSS-to-Email Feeds
    Route::post('/rss-feeds/{rss_feed}/toggle-active', [RssFeedController::class, 'toggleActive'])->name('rss-feeds.toggle-active');
    Route::post('/rss-feeds/{rss_feed}/check-now', [RssFeedController::class, 'checkNow'])->name('rss-feeds.check-now');
    Route::resource('rss-feeds', RssFeedController::class)->except(['show']);

    // Follow-ups (auto-resend to non-openers/non-clickers of a past broadcast)
    Route::post('/email-followups/{email_followup}/toggle-active', [EmailFollowupController::class, 'toggleActive'])->name('email-followups.toggle-active');
    Route::post('/email-followups/{email_followup}/run-now', [EmailFollowupController::class, 'runNow'])->name('email-followups.run-now');
    Route::resource('email-followups', EmailFollowupController::class)->except(['show']);

    // Lead-capture Forms (management side; public pages are outside this group)
    Route::post('/lead-forms/{lead_form}/toggle-active', [LeadFormController::class, 'toggleActive'])->name('lead-forms.toggle-active');
    Route::resource('lead-forms', LeadFormController::class)->except(['show']);
});
