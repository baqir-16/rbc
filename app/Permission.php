<?php

namespace App;

class Permission extends \Spatie\Permission\Models\Permission
{

    public static function defaultPermissions()
    {
//    run this commands when adding new permissions manually in db: php artisan cache:forget spatie.permission.cache then php artisan cache:clear
        return [
            'view_users',
            'add_users',
            'edit_users',
            'delete_users',

            'view_roles',
            'add_roles',
            'edit_roles',
            'delete_roles',

            'view_posts',
            'add_posts',
            'edit_posts',
            'delete_posts',

            'admin_view',
            'pmo_view',
            'tester_view',
            'analyst_view',
            'qa_view',
            'hod_view',
            'remofficer_view',
            'rempmo_view',
            'view_history',

            'asset_admin',
            'asset_owner',
            'asset_manager',
            'gciso_view',
            'ciso_view'

        ];
    }
}
