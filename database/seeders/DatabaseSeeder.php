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
        // User::factory(10)->create();

        // Admin User
        User::factory()->create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@smktarunabangsa.sch.id',
            'password' => bcrypt('admin123'),
        ]);

        $this->call([
            StudentSeeder::class,
        ]);
    }
}
