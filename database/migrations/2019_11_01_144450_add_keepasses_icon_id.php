<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeepassesIconId extends Migration
{
    const TABLE = 'keepasses';
    const COLUMN = 'icon_id';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable(self::TABLE) && !Schema::hasColumn(self::TABLE, self::COLUMN)) {
            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger(self::COLUMN)->nullable()->after('notes');
                $table->foreign(self::COLUMN)
                    ->references('id')->on('icons')
                    ->onDelete('set null');
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
                $table->dropForeign('keepasses_icon_id_foreign');
                $table->dropColumn(self::COLUMN);
            });
        }
    }
}
