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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'マタタビ',
                'email' => 'www@www.com',
                'password' => Hash::make('wwwwwwwwww'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ミーコ',
                'email' => 'eee@eee.com',
                'password' => Hash::make('eeeeeeeeee'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
