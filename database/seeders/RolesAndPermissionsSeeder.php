<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */
        $permissions = [
            ['name' => 'can_view', 'group' => 'General'],
            ['name' => 'can_create', 'group' => 'General'],
            ['name' => 'can_edit', 'group' => 'General'],
            ['name' => 'can_delete', 'group' => 'General'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'group' => $permission['group'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */
        $roles = [
            'Super Admin',
            'Admin',
            'Logistics',
            'Branch',
            'Operator',
            'User',
        ];

        foreach ($roles as $roleName) {
            DB::table('roles')->updateOrInsert(
                ['name' => $roleName],
                [
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pages
        |--------------------------------------------------------------------------
        */
        $pages = [
            'dashboard',
            'users',
            'roles',
            'pages',
            'privileges',
            'role-privileges',
            'branches',
            'services',
            'create-waybill',
            'logistics-tracking',
            'return-waybills',
            'backjob-waybills',
            'returned-backjob-waybills',
            'albums',
            'proof-of-payment',
            'reports',
        ];

        foreach ($pages as $page) {
            DB::table('pages')->updateOrInsert(
                ['description' => $page],
                [
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Role Page Permissions
        |--------------------------------------------------------------------------
        */
        $pagePermissions = [
            'Super Admin' => [
                '*' => [
                    'can_view',
                    'can_create',
                    'can_edit',
                    'can_delete',
                ],
            ],

            'Admin' => [
                'dashboard' => ['can_view'],

                'users' => ['can_view', 'can_create', 'can_edit', 'can_delete'],
                'roles' => ['can_view', 'can_create', 'can_edit', 'can_delete'],
                'pages' => ['can_view', 'can_create', 'can_edit', 'can_delete'],
                'privileges' => ['can_view', 'can_create', 'can_edit', 'can_delete'],
                'role-privileges' => ['can_view', 'can_create', 'can_edit', 'can_delete'],

                'branches' => ['can_view', 'can_create', 'can_edit', 'can_delete'],
                'services' => ['can_view', 'can_create', 'can_edit', 'can_delete'],

                'create-waybill' => ['can_view', 'can_create'],
                'logistics-tracking' => ['can_view', 'can_edit'],
                'return-waybills' => ['can_view', 'can_create', 'can_edit'],
                'backjob-waybills' => ['can_view', 'can_create', 'can_edit'],
                'returned-backjob-waybills' => ['can_view', 'can_create', 'can_edit'],

                'albums' => ['can_view', 'can_create', 'can_edit', 'can_delete'],
                'proof-of-payment' => ['can_view', 'can_create', 'can_edit'],
                'reports' => ['can_view'],
            ],

            'Logistics' => [
                'dashboard' => ['can_view'],
                'logistics-tracking' => ['can_view', 'can_edit'],
                'return-waybills' => ['can_view'],
                'backjob-waybills' => ['can_view'],
                'returned-backjob-waybills' => ['can_view'],
            ],

            'Branch' => [
                'dashboard' => ['can_view'],
                'create-waybill' => ['can_view', 'can_create'],
                'return-waybills' => ['can_view', 'can_create'],
                'proof-of-payment' => ['can_view', 'can_create'],
            ],

            'Operator' => [
                'dashboard' => ['can_view'],
                'create-waybill' => ['can_view', 'can_create', 'can_edit'],
                'backjob-waybills' => ['can_view', 'can_create', 'can_edit'],
                'returned-backjob-waybills' => ['can_view', 'can_create', 'can_edit'],
                'albums' => ['can_view', 'can_create', 'can_edit'],
            ],

            'User' => [
                'dashboard' => ['can_view'],
            ],
        ];

        foreach ($pagePermissions as $roleName => $pageRules) {
            $roleId = DB::table('roles')
                ->where('name', $roleName)
                ->value('id');

            if (!$roleId) {
                continue;
            }

            if (isset($pageRules['*'])) {
                foreach ($pages as $pageDescription) {
                    $pageId = DB::table('pages')
                        ->where('description', $pageDescription)
                        ->value('id');

                    foreach ($pageRules['*'] as $permissionName) {
                        $permissionId = DB::table('permissions')
                            ->where('name', $permissionName)
                            ->value('id');

                        if ($pageId && $permissionId) {
                            DB::table('role_page_permission')->insertOrIgnore([
                                'role_id' => $roleId,
                                'page_id' => $pageId,
                                'permission_id' => $permissionId,
                            ]);
                        }
                    }
                }

                continue;
            }

            foreach ($pageRules as $pageDescription => $permissionNames) {
                $pageId = DB::table('pages')
                    ->where('description', $pageDescription)
                    ->value('id');

                if (!$pageId) {
                    continue;
                }

                foreach ($permissionNames as $permissionName) {
                    $permissionId = DB::table('permissions')
                        ->where('name', $permissionName)
                        ->value('id');

                    if ($permissionId) {
                        DB::table('role_page_permission')->insertOrIgnore([
                            'role_id' => $roleId,
                            'page_id' => $pageId,
                            'permission_id' => $permissionId,
                        ]);
                    }
                }
            }
        }
    }
}