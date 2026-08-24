<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        // Registration is disabled app-wide, so Google sign-in only ever
        // authenticates an existing account — it never creates a new one.
        if (! $user) {
            return redirect()->route('login')->withErrors([
                'email' => 'No account found for this Google email. Ask an admin to create your account first.',
            ]);
        }

        if (! $user->google_id) {
            $user->google_id = $googleUser->getId();
            $user->avatar = $googleUser->getAvatar();
            $user->save();
        }

        Auth::login($user, true);

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
