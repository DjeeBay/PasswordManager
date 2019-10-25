<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeepassPermissionsSeeder extends Seeder
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
            ->where('name', '=', 'create keepass')
            ->orWhere('name', '=', 'edit keepass')
            ->orWhere('name', '=', 'delete keepass')
            ->orWhere('name', '=', 'import keepass')
            ->get();

        if (!count($alreadyExists)) {
            DB::table(self::TABLE)
                ->insert([
                    [
                        'name' => 'create keepass',
                        'guard_name' => 'web'
                    ],
                    [
                        'name' => 'edit keepass',
                        'guard_name' => 'web'
                    ],
                    [
                        'name' => 'delete keepass',
                        'guard_name' => 'web'
                    ],
                    [
                        'name' => 'import keepass',
                        'guard_name' => 'web'
                    ]
                ]);
        }
    }
}
