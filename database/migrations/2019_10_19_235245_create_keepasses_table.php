<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeepassesTable extends Migration
{
    const TABLE = 'keepasses';

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
                $table->string('title');
                $table->unsignedBigInteger('category_id');
                $table->boolean('is_folder');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('login')->nullable();
                $table->string('password')->nullable();
                $table->text('url')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('updated_by')->nullable();
                $table->unsignedInteger('deleted_by')->nullable();
                $table->softDeletes();

                $table->foreign('category_id')
                    ->references('id')->on('categories')
                    ->onDelete('cascade');
                $table->foreign('parent_id')
                    ->references('id')->on(self::TABLE)
                    ->onDelete('cascade');
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
}
