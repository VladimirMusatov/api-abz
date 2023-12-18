<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for($i = 0; $i < 45; $i++)
        {

            User::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'phone' => '+380' . $faker->numerify('#########'),
                'position_id' => rand(2, 6),
                'photo' => $faker->imageUrl(70, 70, 'people', true, true, 'jpg'),
                'created_at' => now(),
                'updated_at' => now(),   
            ]);

        }
    }
}
