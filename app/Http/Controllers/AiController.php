<?php

namespace App\Http\Controllers;

use App\Services\AiEmailComposer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AiController extends Controller
{
    public function composeEmail(Request $request, AiEmailComposer $composer): JsonResponse
    {
        $validated = $request->validate([
            'brief' => 'required|string|max:2000',
        ]);

        try {
            $draft = $composer->draft($validated['brief']);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Could not generate a draft right now. Please try again.',
            ], 502);
        }

        return response()->json([
            'success' => true,
            'subject' => $draft['subject'],
            'body' => $draft['body'],
        ]);
    }
}
