<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoriesDescription extends Migration
{
    const TABLE = 'categories';
    const COLUMN = 'description';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable(self::TABLE) && !Schema::hasColumn(self::TABLE, self::COLUMN)) {
            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->text(self::COLUMN)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable(self::TABLE) && Schema::hasColumn(self::TABLE, self::COLUMN)) {
            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn(self::COLUMN);
            });
        }
    }
}
