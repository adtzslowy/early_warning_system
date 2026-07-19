<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[
            \Spatie\Permission\PermissionRegistrar::class
        ]->forgetCachedPermissions();

        $permissions = [
            "view dashboard",
            "view monitoring",
            "view prediction",
            "view devices",
            "view sensors",
            "create devices",
            "edit devices",
            "delete devices",
            "view alert",
            "create alert",
            "edit alert",
            "delete alert",
            "view users",
            "create users",
            "edit users",
            "delete users",
            "view notifications"
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(["name" => $permission]);
        }

        $admin = Role::firstOrCreate(["name" => "admin"]);
        $operator = Role::firstOrCreate(["name" => "operator"]);

        $admin->givePermissionTo(Permission::all());

        $operator->givePermissionTo([
            "view dashboard",
            "view monitoring",
            "view prediction",
            "view devices",
            "view sensors",
            "edit devices",
        ]);

        User::firstOrCreate(
            ["email" => "aditzkun0987@gmail.com"],
            [
                "name" => "Aditya Prasetyo",
                "password" => "d476ead1",
            ],
        )->syncRoles("admin");

        User::firstOrCreate(
            ["email" => "merli.andika@gmail.com"],
            [
                "name" => "Merli Andika",
                "password" => "merliandika",
            ],
        )->syncRoles("operator");
    }
}
