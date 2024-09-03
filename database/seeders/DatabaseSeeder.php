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

        $editor = User::factory()->create([
            'name' => 'editor',
            'password' => bcrypt('password'),
            'email' => 'editor@test.test',
        ]);

        $editor->assignRole('editor');

        $author = User::factory()->create([
            'name' => 'author',
            'password' => bcrypt('password'),
            'email' => 'author@test.test',
        ]);

        $author->assignRole('author');

        $contributor = User::factory()->create([
            'name' => 'author',
            'password' => bcrypt('password'),
            'email' => 'contributor@test.test',
        ]);

        $contributor->assignRole('contributor');
    }
}
