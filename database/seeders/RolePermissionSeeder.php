<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::where('email', "kboahene760@gmail.com")->first();

        //Create SuperAdmin Role
        $super_admin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'sanctum',
        ]);

        $user->assignRole($super_admin);

        //Create SuperAdmin Permission
        // Products
        $super_admin_permission = Permission::firstOrCreate(['name' => 'products.add','guard_name' => 'sanctum',]);
        $super_admin_permission = Permission::firstOrCreate(['name' => 'products.edit','guard_name' => 'sanctum',]);
        $super_admin_permission = Permission::firstOrCreate(['name' => 'products.show','guard_name' => 'sanctum',]);
        $super_admin_permission = Permission::firstOrCreate(['name' => 'products.delete','guard_name' => 'sanctum',]);
        $super_admin_permission = Permission::firstOrCreate(['name' => 'products.status_update','guard_name' => 'sanctum']);

        //category
        $super_admin_permission = Permission::firstOrCreate(['name' => 'category.add','guard_name' => 'sanctum']);
        $super_admin_permission = Permission::firstOrCreate(['name' => 'category.edit','guard_name' => 'sanctum']);
        $super_admin_permission = Permission::firstOrCreate(['name' => 'category.show','guard_name' => 'sanctum']);
        $super_admin_permission = Permission::firstOrCreate(['name' => 'category.delete','guard_name' => 'sanctum']);
        $super_admin_permission = Permission::firstOrCreate(['name' => 'category.status_update','guard_name' => 'sanctum']);

        //get all permission of super admin role (just the name)
        $super_admin_permission = Permission::pluck('name')->toArray();

        $super_admin->syncPermissions($super_admin_permission);
    }
}
