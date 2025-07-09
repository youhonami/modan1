<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'ノラネコ',
                'email' => 'qqq@qqq.com',
                'password' => Hash::make('qqqqqqqqqq'),
                'firebase_uid' => 'poq2QfC3kFb0bMqypzeKC7JZugZ2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'マタタビ',
                'email' => 'www@www.com',
                'password' => Hash::make('wwwwwwwwww'),
                'firebase_uid' => 'HK62S2WkpZYpGiftaetopjXuSKE2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ミーコ',
                'email' => 'eee@eee.com',
                'password' => Hash::make('eeeeeeeeee'),
                'firebase_uid' => 'cmQwzEWwELgimLoWKQb5lETfK4R2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
