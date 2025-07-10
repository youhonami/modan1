<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\User;
use App\Models\Tweet;

class CommentController extends Controller
{
    public function store(Request $request, $tweetId)
    {
        $validated = $request->validate([
            'firebase_uid' => 'required|exists:users,firebase_uid',
            'body' => 'required|string|max:255',
        ]);

        $user = User::where('firebase_uid', $validated['firebase_uid'])->firstOrFail();
        $tweet = Tweet::findOrFail($tweetId);

        $comment = Comment::create([
            'tweet_id' => $tweet->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        return response()->json([
            'id' => $comment->id,
            'body' => $comment->body,
            'user' => [
                'name' => $user->name,
            ],
        ], 201);
    }

    public function index($tweetId)
    {
        $comments = Comment::with('user')
            ->where('tweet_id', $tweetId)
            ->latest()
            ->get();

        return response()->json($comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'body' => $comment->body,
                'user' => [
                    'name' => $comment->user->name,
                ],
            ];
        }));
    }
}
