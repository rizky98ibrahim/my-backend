<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make Permissions for user, admin and writter blog
        $permissions = [
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
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
