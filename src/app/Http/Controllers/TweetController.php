<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;
use App\Models\User;

class TweetController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firebase_uid' => 'required|exists:users,firebase_uid',
            'body' => 'required|string|max:120',
        ]);

        $user = User::where('firebase_uid', $validated['firebase_uid'])->firstOrFail();

        $tweet = Tweet::create([
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        return response()->json($tweet, 201);
    }

    public function index()
    {
        $tweets = Tweet::with('user', 'likes')->latest()->get();

        return response()->json($tweets);
    }
}
