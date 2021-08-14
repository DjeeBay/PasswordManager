<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserPermissionsSeeder extends Seeder
{
    const TABLE = 'permissions';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $alreadyExists = DB::table(self::TABLE)
            ->where('name', '=', 'create user')
            ->orWhere('name', '=', 'edit user')
            ->orWhere('name', '=', 'delete user')
            ->orWhere('name', '=', 'manage user permissions')
            ->get();

        if (!count($alreadyExists)) {
            DB::table(self::TABLE)
                ->insert([
                    [
                        'name' => 'create user',
                        'guard_name' => 'web'
                    ],
                    [
                        'name' => 'edit user',
                        'guard_name' => 'web'
                    ],
                    [
                        'name' => 'delete user',
                        'guard_name' => 'web'
                    ],
                    [
                        'name' => 'manage user permissions',
                        'guard_name' => 'web'
                    ]
                ]);
        }
    }
}
