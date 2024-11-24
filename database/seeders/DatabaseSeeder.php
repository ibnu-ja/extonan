<?php

namespace Database\Seeders;

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

        //$users = [
        //    [
        //        "name" => "iji",
        //        "email" => "ibnu.ja@aol.com",
        //        "created_at" => "2020-07-19 8:53:51",
        //    ],
        //    [
        //        "name" => "seiga bot",
        //        "email" => "ibnu.ja@outlook.com",
        //        "created_at" => "2020-07-19 8:53:51",
        //    ],
        //    [
        //        "name" => "ALICE",
        //        "email" => "dhanuenv97@gmail.com",
        //        "created_at" => "2020-08-10 12:02:03"
        //    ],
        //    [
        //        "name" => "Anonymus A",
        //        "email" => "adit.b.setiawan@gmail.com",
        //        "created_at" => "2020-09-12 15:49:33"
        //    ],
        //    [
        //        "name" => "Nayuta",
        //        "email" => "ezraaaa12@gmail.com",
        //        "created_at" => "2020-09-13 4:32:43"
        //    ],
        //    [
        //        "name" => "Aria~",
        //        "email" => "kevinruwanda@gmail.com",
        //        "created_at" => "2020-09-13 6:13:30"
        //    ],
        //    [
        //        "name" => "SnowFlake",
        //        "email" => "sandiwahyudi150@gmail.com",
        //        "created_at" => "2020-09-13 10:15:36"
        //    ],
        //    [
        //        "name" => "hearn",
        //        "email" => "redhawk390@gmail.com",
        //        "created_at" => "2020-09-13 11:56:43"
        //    ],
        //    [
        //        "name" => "anaksopan",
        //        "email" => "raihangilanga@gmail.com",
        //        "created_at" => "2020-09-16 15:56:23"
        //    ],
        //    [
        //        "name" => "nino",
        //        "email" => "nakanonino80@gmail.com",
        //        "created_at" => "2021-03-31 12:32:31"
        //    ],
        //    [
        //        "name" => "Jircniv",
        //        "email" => "soonhiru@gmail.com",
        //        "created_at" => "2021-09-06 19:04:17"
        //    ],
        //    [
        //        "name" => "aqiladziki",
        //        "email" => "aqiladziki04@gmail.com",
        //        "created_at" => "2021-10-05 11:11:52"
        //    ],
        //];
        //
        //foreach ($users as $user) {
        //    \App\Models\User::factory()->create($user)->assignRole('editor');
        //}


        //\App\Models\User::factory()->count(count($as))->sequence($as)->create()->assignRole('editor');
        //
        \App\Models\User::factory()->create([
            'name' => 'nagh',
            'password' => bcrypt('password'),
            'email' => 'muhamadfaisal1432@gmail.com',
        ])->assignRole('editor');

        \App\Models\User::factory()->create([
            'name' => 'author',
            'password' => bcrypt('password'),
            'email' => 'author@test.test',
        ])->assignRole('author');

        \App\Models\User::factory()->create([
            'name' => 'author2',
            'password' => bcrypt('password'),
            'email' => 'author2@test.test',
        ])->assignRole('author');

        \App\Models\User::factory()->create([
            'name' => 'contributor',
            'password' => bcrypt('password'),
            'email' => 'contributor@test.test',
        ])->assignRole('contributor');
    }
}
