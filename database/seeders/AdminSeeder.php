<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\ApiToken;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $admin = User::create([
            'email' => 'admin@gmail.com',
            'name' => 'superadmin',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'phone' => '+380' . $faker->numerify('#########'),
            'position_id' => 1
        ]);

        $token_name = 'token_'.$admin->id;

        $key = $admin->createToken($token_name)->plainTextToken;

        ApiToken::create([
            'user_id' => $admin->id,
            'key' => $key,
        ]);

    }
}
