<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    /**
     * Save (or refresh) a FCM device token for the authenticated user.
     * Called by the browser after the user grants notification permission.
     */
    public function store(Request $request)
    {
        $request->validate(['token' => 'required|string|max:512']);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        FcmToken::updateOrCreate(
            ['token' => $request->token],
            ['user_id' => $user->id, 'last_used_at' => now()]
        );

        return response()->json(['message' => 'Token saved.']);
    }

    /**
     * Remove a FCM token (e.g. on logout or permission revoke).
     */
    public function destroy(Request $request)
    {
        $request->validate(['token' => 'required|string|max:512']);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        FcmToken::where('user_id', $user->id)
            ->where('token', $request->token)
            ->delete();

        return response()->json(['message' => 'Token removed.']);
    }
}