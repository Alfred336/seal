<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SampleDataSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'chapa@sealtech.co.tz'],
            [
                'name' => 'chapa',
                'password' => 'mafanikio',
                'role' => 'admin',
            ]
        );
    }
}