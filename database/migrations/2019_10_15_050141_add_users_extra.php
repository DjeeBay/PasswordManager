<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsersExtra extends Migration
{
    const TABLE = 'users';
    const COLUMNS = ['deleted_by', 'is_admin', 'firstname', 'lastname'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable(self::TABLE) && !Schema::hasColumns(self::TABLE, self::COLUMNS)) {
            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->softDeletes();
                $table->boolean('is_admin')->default(0);
                $table->string('firstname')->nullable();
                $table->string('lastname')->nullable();
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
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn([self::COLUMNS]);
        });
    }
}
