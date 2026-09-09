<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */

    public function run(): void
    {
        $this->call([
            TableSeeder::class,
        ]);

        // Inside run method:
        User::updateOrCreate(
            ['email' => 'mko_admin@gmail.com'],
            [
                'name' => 'Yubin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mko_staff@gmail.com'],
            [
                'name' => 'Bob',
                'password' => Hash::make('123456789'),
                'role' => 'staff',
            ]
        );
    }
}
