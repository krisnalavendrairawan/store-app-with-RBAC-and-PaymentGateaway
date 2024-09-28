<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);


        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'delete role']);

        Permission::create(['name' => 'create product']);
        Permission::create(['name' => 'update product']);
        Permission::create(['name' => 'delete product']);

        Permission::create(['name' => 'create category']);
        Permission::create(['name' => 'update category']);
        Permission::create(['name' => 'delete category']);

        Permission::create(['name' => 'create transaction']);

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        $roleAdmin = Role::findByName('admin');
        $roleAdmin->givePermissionTo(Permission::all());
        $roleManager = Role::findByName('manager');
        $roleManager->givePermissionTo(
            [
                'create product',
                'update product',
                'delete product',
                'create category',
                'update category',
                'delete category',
                'create transaction',
            ]
        );
    }
}
