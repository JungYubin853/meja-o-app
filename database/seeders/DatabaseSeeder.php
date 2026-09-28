<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Sync outlets from the API if the table is empty
        if (Outlet::count() === 0) {
            $this->command->info('No outlets found. Running API sync...');
            Artisan::call('app:sync-outlets');
            $this->command->info(Artisan::output());
        }

        // 2. Create Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('sadmin123'),
                'role' => 'super_admin',
                'outlet_id' => null,
            ]
        );

        $outlets = Outlet::all();

        // 3. Create Admin and Staff for each Outlet
        foreach ($outlets as $outlet) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $outlet->name));
            
            // To ensure uniqueness if multiple outlets produce the same slug (e.g. unnamed branches)
            $slug .= $outlet->id;

            // Admin for this outlet
            User::updateOrCreate(
                ['email' => "admin_{$slug}@gmail.com"],
                [
                    'name' => "Admin {$outlet->name}",
                    'password' => Hash::make('admin123'),
                    'role' => 'admin',
                    'outlet_id' => $outlet->id,
                ]
            );

            // Staff for this outlet
            User::updateOrCreate(
                ['email' => "staff_{$slug}@gmail.com"],
                [
                    'name' => "Staff {$outlet->name}",
                    'password' => Hash::make('staff123'),
                    'role' => 'staff',
                    'outlet_id' => $outlet->id,
                ]
            );
        }
        
        // $this->call([
        //     TableSeeder::class,
        // ]);
    }
}
