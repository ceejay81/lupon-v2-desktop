<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LuponMember;

class LuponMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['name' => 'Hon. Jose Rizal', 'position' => 'Punong Barangay', 'member_type' => 'punong_barangay'],
            ['name' => 'Maria Clara', 'position' => 'Lupon Secretary', 'member_type' => 'lupon_secretary'],
            ['name' => 'Andres Bonifacio', 'position' => 'Member', 'member_type' => 'regular'],
            ['name' => 'Emilio Jacinto', 'position' => 'Member', 'member_type' => 'regular'],
            ['name' => 'Gabriela Silang', 'position' => 'Member', 'member_type' => 'regular'],
            ['name' => 'Apolinario Mabini', 'position' => 'Member', 'member_type' => 'regular'],
            ['name' => 'Melchora Aquino', 'position' => 'Member', 'member_type' => 'regular'],
        ];

        foreach ($members as $member) {
            LuponMember::create(array_merge($member, [
                'appointment_date' => now()->subMonths(6),
                'is_active' => true,
            ]));
        }
    }
}
