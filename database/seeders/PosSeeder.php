<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $roleOwner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $roleKasir = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);

        // Create Users
        $owner = User::firstOrCreate(
            ['email' => 'owner@pos.com'],
            [
                'name' => 'Owner',
                'password' => Hash::make('Owner123'),
                'email_verified_at' => now(),
            ]
        );
        $owner->assignRole($roleOwner);

        $admin = User::firstOrCreate(
            ['email' => 'admin@pos.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($roleAdmin);

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@pos.com'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('Kasir123'),
                'email_verified_at' => now(),
            ]
        );
        $kasir->assignRole($roleKasir);
    }
}
