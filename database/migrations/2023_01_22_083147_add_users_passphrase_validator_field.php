<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE = 'users';
    const COLUMN = 'passphrase_validator';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn(self::TABLE, self::COLUMN)) return;
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string(self::COLUMN)->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropColumns(self::TABLE, [self::COLUMN]);
    }
};
