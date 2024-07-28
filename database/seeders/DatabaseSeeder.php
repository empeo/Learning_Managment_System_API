<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            LevelSeeder::class,
        ]);
        // $adminRole = Role::create(['name' => 'admin']);
        // $userRole = Role::create(['name' => 'user']);

        // // Create permissions
        // $permissions = [
        //     'manage users',
        //     'manage courses',
        //     'manage orders',
        //     'view profile',
        //     'edit profile',
        // ];

        // foreach ($permissions as $permission) {
        //     Permission::create(['name' => $permission]);
        // }

        // // Assign permissions to roles
        // $adminRole->givePermissionTo(Permission::all());
        // $userRole->givePermissionTo(['view profile', 'edit profile']);
    }

}
