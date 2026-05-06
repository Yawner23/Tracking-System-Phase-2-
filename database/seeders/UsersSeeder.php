<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('branches')->updateOrInsert(
            ['address' => 'Main Branch'],
            [
                'contact_number' => '09123456789',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $mainBranchId = DB::table('branches')
            ->where('address', 'Main Branch')
            ->value('id');

        $users = [
            [
                'email' => 'superadmin@example.com',
                'name' => 'Super Admin',
                'branch_id' => null,
                'contact_number' => '09000000000',
                'role' => 'Super Admin',
            ],
            [
                'email' => 'admin@example.com',
                'name' => 'Admin User',
                'branch_id' => null,
                'contact_number' => '09000000001',
                'role' => 'Admin',
            ],
            [
                'email' => 'branch@example.com',
                'name' => 'Main Branch User',
                'branch_id' => $mainBranchId,
                'contact_number' => '09123456789',
                'role' => 'Branch',
            ],
            [
                'email' => 'logistics@example.com',
                'name' => 'Logistics User',
                'branch_id' => null,
                'contact_number' => '09000000002',
                'role' => 'Logistics',
            ],
            [
                'email' => 'operator@example.com',
                'name' => 'Operator User',
                'branch_id' => null,
                'contact_number' => '09000000003',
                'role' => 'Operator',
            ],
            [
                'email' => 'user@example.com',
                'name' => 'Regular User',
                'branch_id' => null,
                'contact_number' => '09000000004',
                'role' => 'User',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                [
                    'branch_id' => $user['branch_id'],
                    'name' => $user['name'],
                    'contact_number' => $user['contact_number'],
                    'password' => Hash::make('password'),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $userId = DB::table('users')
                ->where('email', $user['email'])
                ->value('id');

            $roleId = DB::table('roles')
                ->where('name', $user['role'])
                ->value('id');

            if ($userId && $roleId) {
                DB::table('role_user')->insertOrIgnore([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                ]);
            }
        }
    }
}