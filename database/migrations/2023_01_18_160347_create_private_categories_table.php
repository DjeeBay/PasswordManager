<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE = 'private_categories';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE)) {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('color')->nullable();
                $table->unsignedBigInteger('owner_id');
                $table->foreign('owner_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
                $table->timestamps();
                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('updated_by')->nullable();
                $table->unsignedInteger('deleted_by')->nullable();
                $table->softDeletes();
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
        Schema::dropIfExists(self::TABLE);
    }
};
