<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::where('email', 'kboahene760@gmail.com')->first();

        // Create SuperAdmin Role
        $super_admin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'sanctum',
        ]);

        if ($user) {
            $user->assignRole($super_admin);

            // Create SuperAdmin Permission
            // Products
            $super_admin_permission = Permission::firstOrCreate(['name' => 'products.add', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'products.edit', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'products.show', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'products.delete', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'products.status_update', 'guard_name' => 'sanctum']);

            // category
            $super_admin_permission = Permission::firstOrCreate(['name' => 'category.add', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'category.edit', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'category.show', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'category.delete', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'category.status_update', 'guard_name' => 'sanctum']);
            
            //Products
            $super_admin_permission = Permission::firstOrCreate(['name' => 'products.add', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'products.edit', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'products.show', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'products.delete', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name' => 'products.status_update', 'guard_name' => 'sanctum']);

            //Customers
            $super_admin_permission = Permission::firstOrCreate(['name'=> 'customers.view', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name'=> 'customers.status.update', 'guard_name' => 'sanctum']);


            //User Management
            $super_admin_permission = Permission::firstOrCreate(['name'=> 'user.view', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name'=> 'user.create', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name'=> 'user.edit', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name'=> 'user.delete', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name'=> 'user.status.update', 'guard_name' => 'sanctum']);


            //Settings
            $super_admin_permission = Permission::firstOrCreate(['name'=> 'role.create', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name'=> 'role.view', 'guard_name' => 'sanctum']);
            $super_admin_permission = Permission::firstOrCreate(['name'=> 'role.delete', 'guard_name' => 'sanctum']);

            
            // get all permission of super admin role (just the name)
            $super_admin_permission = Permission::pluck('name')->toArray();

            $super_admin->syncPermissions($super_admin_permission);
        }

    }
}
