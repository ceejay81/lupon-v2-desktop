<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Juana Bula',
            'email' => 'admin@bula.gov.ph',
            'password' => bcrypt('password'),
        ]);

        $this->call([
            SettingSeeder::class,
            LuponMemberSeeder::class,
        ]);
    }
}
