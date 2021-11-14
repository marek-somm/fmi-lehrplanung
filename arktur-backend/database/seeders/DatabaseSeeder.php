<?php

namespace Database\Seeders;

use App\Models\Organization;
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
        $this->call([
            UserSeeder::class,
            OrganizationSeeder::class,
            AffiliationSeeder::class,
            EventSeeder::class
        ]);

        $user = User::find(1);
        $user->organizations()->attach(Organization::find(1));
    }
}
