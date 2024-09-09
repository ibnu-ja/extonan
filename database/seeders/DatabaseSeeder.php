<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RolesPermissionsSeeder::class);

        User::factory()->create([
            'name' => 'editor',
            'password' => bcrypt('password'),
            'email' => 'editor@test.test',
        ])->assignRole('editor');

        User::factory()->create([
            'name' => 'editor2',
            'password' => bcrypt('password'),
            'email' => 'editor2@test.test',
        ])->assignRole('editor');

        User::factory()->create([
            'name' => 'author',
            'password' => bcrypt('password'),
            'email' => 'author@test.test',
        ])->assignRole('author');

        User::factory()->create([
            'name' => 'author2',
            'password' => bcrypt('password'),
            'email' => 'author2@test.test',
        ])->assignRole('author');

        User::factory()->create([
            'name' => 'author',
            'password' => bcrypt('password'),
            'email' => 'contributor@test.test',
        ])->assignRole('contributor');
    }
}
