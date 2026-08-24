<?php

namespace App\Http\Controllers;

use App\Models\ContactTag;
use App\Models\EmailContact;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Public newsletter signup used by the marketing site (sendpeak.in).
 *
 * Subscribers land in the same contact list as the rest of the app, tagged
 * "Newsletter" so they can be targeted as a segment.
 */
class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        // Honeypot: a hidden field real visitors never fill in.
        if ($request->filled('website')) {
            return response()->json(['ok' => true, 'message' => 'Subscribed.']);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc|max:255',
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'That email address does not look valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()->first('email'),
            ], 422);
        }

        // The marketing site has no logged-in user, so subscribers are attached
        // to the account owner (the first/admin user).
        $owner = User::orderBy('id')->first();

        if (! $owner) {
            Log::error('Newsletter signup failed: no user account exists to own the contact.');

            return response()->json([
                'ok' => false,
                'message' => 'Signup is temporarily unavailable. Please try again later.',
            ], 503);
        }

        try {
            $email = strtolower(trim($request->input('email')));

            $contact = EmailContact::firstOrCreate(
                ['user_id' => $owner->id, 'email' => $email],
                ['status' => 'active', 'notes' => 'Subscribed via sendpeak.in newsletter']
            );

            $tag = ContactTag::firstOrCreate(
                ['user_id' => $owner->id, 'name' => 'Newsletter'],
                ['color' => '#8b5cf6', 'description' => 'Subscribed from the marketing site']
            );

            // syncWithoutDetaching keeps any tags the contact already has.
            $contact->tags()->syncWithoutDetaching([$tag->id]);

            return response()->json([
                'ok' => true,
                'message' => $contact->wasRecentlyCreated
                    ? "You're on the list — welcome aboard!"
                    : "You're already subscribed — thanks!",
            ]);
        } catch (\Throwable $e) {
            Log::error('Newsletter signup failed', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }
}
