<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => "Admin1",
            'email' => "akmaldavinci06@gmail.com",
            'password' => "Password123",
            'user_type' => User::USER_ADMIN,
            'profile_picture' => "asdasd.png",
            'is_supervisor' => 1,
            'branch_id' => 1,
        ]);
        for ($i=0; $i < 10; $i++) { 
            User::create([
                'name' => "user".strval($i),
                'email' => "user".strval($i)."@mienta.id",
                'password' => "Password123",
                'user_type' => User::USER_EMPLOYEE,
                'profile_picture' => "asdasd.png",
                'is_supervisor' => 0,
                'branch_id' => 1,
            ]);
        }
    }
}
