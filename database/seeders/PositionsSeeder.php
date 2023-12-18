<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Position;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        Position::create(['title' => 'Admin']);

        for($i = 0; $i < 5; $i++)
        {
            $position = $faker->jobTitle;

            Position::create(['title' => $position]);
        }

    }
}
