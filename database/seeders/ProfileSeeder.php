<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('profiles')->insert([
            'name' => 'default_user'
        ]);

        DB::table('profiles')->insert([
            'name' => 'professionals_user'
        ]);

        DB::table('profiles')->insert([
            'name' => 'admin_user'
        ]);
    }
}
