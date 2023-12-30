<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'can-view-dashboard',
            'can-view-profile',
            'can-update-profile',
            'can-view-setting',
            'can-update-setting',
            'can-view-blog',
            'can-create-blog',
            'can-update-blog',
            'can-delete-blog',
            'can-view-user',
            'can-create-user',
            'can-update-user',
            'can-delete-user',
            'can-view-role',
            'can-create-role',
            'can-update-role',
            'can-delete-role',
            'can-view-permission',
            'can-create-permission',
            'can-update-permission',
            'can-delete-permission',
            'can-view-category',
            'can-create-category',
            'can-update-category',
            'can-delete-category',
            'can-view-tag',
            'can-create-tag',
            'can-update-tag',
            'can-delete-tag',
            'can-view-comment',
            'can-create-comment',
            'can-update-comment',
            'can-delete-comment',
        ]);

        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo([
            'can-view-profile',
            'can-update-profile',
            'can-view-blog',
            'can-view-category',
            'can-view-tag',
            'can-view-comment',
            'can-create-comment',
            'can-update-comment',
            'can-delete-comment',
        ]);

        $writter = Role::create(['name' => 'writter']);
        $writter->givePermissionTo([
            'can-view-profile',
            'can-update-profile',
            'can-view-blog',
            'can-create-blog',
            'can-update-blog',
            'can-delete-blog',
            'can-view-category',
            'can-create-category',
            'can-update-category',
            'can-delete-category',
            'can-view-tag',
            'can-create-tag',
            'can-update-tag',
            'can-delete-tag',
            'can-view-comment',
            'can-create-comment',
            'can-update-comment',
            'can-delete-comment',
            'can-view-dashboard',
        ]);
    }
}
