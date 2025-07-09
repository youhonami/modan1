<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;
use App\Models\User;
use App\Models\Like;

class TweetController extends Controller
{
    /**
     * ツイートを保存
     */
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

        // user情報を含めて返す
        return response()->json([
            'id' => $tweet->id,
            'body' => $tweet->body,
            'user' => [
                'name' => $user->name,
                'firebase_uid' => $user->firebase_uid,
            ],
        ], 201);
    }

    /**
     * 全ツイート一覧を取得
     */
    public function index()
    {
        $tweets = Tweet::with(['user', 'likes'])->latest()->get();

        return response()->json($tweets->map(function ($tweet) {
            return [
                'id' => $tweet->id,
                'body' => $tweet->body,
                'user' => [
                    'name' => $tweet->user->name,
                    'firebase_uid' => $tweet->user->firebase_uid,
                ],
                'likes' => $tweet->likes,
            ];
        }));
    }

    /**
     * ツイートを削除
     */
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'firebase_uid' => 'required|exists:users,firebase_uid',
        ]);

        $user = User::where('firebase_uid', $request->firebase_uid)->firstOrFail();
        $tweet = Tweet::where('id', $id)->where('user_id', $user->id)->first();

        if (!$tweet) {
            return response()->json(['message' => '削除対象が見つかりません'], 404);
        }

        $tweet->delete();

        return response()->json(['message' => '削除成功'], 200);
    }

    public function like(Request $request, $id)
    {
        $request->validate([
            'firebase_uid' => 'required|exists:users,firebase_uid',
        ]);

        $user = User::where('firebase_uid', $request->firebase_uid)->firstOrFail();

        $tweet = Tweet::findOrFail($id);
        $tweet->likes()->firstOrCreate(['user_id' => $user->id]);

        return response()->json(['message' => 'いいねしました']);
    }

    public function unlike(Request $request, $id)
    {
        $request->validate([
            'firebase_uid' => 'required|exists:users,firebase_uid',
        ]);

        $user = User::where('firebase_uid', $request->firebase_uid)->firstOrFail();

        $tweet = Tweet::findOrFail($id);
        $tweet->likes()->where('user_id', $user->id)->delete();

        return response()->json(['message' => 'いいねを解除しました']);
    }
}
