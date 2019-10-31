<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoricPermissionsSeeder extends Seeder
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
            ->where('name', '=', 'read historic')
            ->orWhere('name', '=', 'restore historic')
            ->get();

        if (!count($alreadyExists)) {
            DB::table(self::TABLE)
                ->insert([
                    [
                        'name' => 'read historic',
                        'guard_name' => 'web'
                    ],
                    [
                        'name' => 'restore historic',
                        'guard_name' => 'web'
                    ]
                ]);
        }
    }
}
