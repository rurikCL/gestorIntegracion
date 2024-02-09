<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Alexis Rebolledo',
            'email' => 'alexis.rebolledo@pompeyo.cl',
            'password' => bcrypt('benitore'),
            'brand_id' => 1,
        ]);

        User::factory()->create([
            'name' => 'Usuario Pruebas',
            'email' => 'api.pruebas@pompeyo.cl',
            'password' => bcrypt('password'),
            'brand_id' => 1,
        ]);*/

        User::factory()->create([
            'name' => 'Cristian Tapia',
            'email' => 'ctapia@lemon.cl',
            'password' => bcrypt('password'),
            'brand_id' => 12,
        ]);
    }
}
