<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryPermissionsSeeder extends Seeder
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
            ->where('name', '=', 'create category')
            ->orWhere('name', '=', 'edit category')
            ->orWhere('name', '=', 'delete category')
            ->get();

        if (!count($alreadyExists)) {
            DB::table(self::TABLE)
                ->insert([
                    [
                        'name' => 'create category',
                        'guard_name' => 'web'
                    ],
                    [
                        'name' => 'edit category',
                        'guard_name' => 'web'
                    ],
                    [
                        'name' => 'delete category',
                        'guard_name' => 'web'
                    ]
                ]);
        }
    }
}
