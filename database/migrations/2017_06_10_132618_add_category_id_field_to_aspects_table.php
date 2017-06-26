<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryIdFieldToAspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aspects', function (Blueprint $table) {
            $table->integer('category_id')->unsigned();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aspects', function (Blueprint $table) {
            $table->removeColumn('category_id');
        });
    }
}
