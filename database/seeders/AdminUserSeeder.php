<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('admin_users')->truncate();

        \DB::table('admin_users')->insert([
            'id' => 1,
            'name' => 'Hifast',
            'email' => 'demo@beikeshop.com',
            'password' => '$2y$10$1LZdM8bNIGiZFsAaGSfBnO8Yqj1q0am19951kfALJrqFQz4b9Y2Oa',
            'active' => 1,
            'locale' => 'en',
            'created_at' => null,
            'updated_at' => '2026-02-16 20:37:03',
        ]);
    }
}
