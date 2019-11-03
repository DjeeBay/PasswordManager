<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UserPermissionsSeeder::class);
         $this->call(CategoryPermissionsSeeder::class);
         $this->call(KeepassPermissionsSeeder::class);
         $this->call(HistoricPermissionsSeeder::class);
         $this->call(DefaultIconsSeeder::class);
    }
}
