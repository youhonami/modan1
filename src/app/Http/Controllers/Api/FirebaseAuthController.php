<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class FirebaseAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'firebase_uid' => 'required|string|unique:users,firebase_uid',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'firebase_uid' => $request->firebase_uid,
            'password' => bcrypt(uniqid()), // 仮パスワード（Firebaseで管理するので使わない）
        ]);

        return response()->json(['message' => 'User created'], 201);
    }
}
