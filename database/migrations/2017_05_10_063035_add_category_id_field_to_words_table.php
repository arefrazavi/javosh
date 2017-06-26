<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryIdFieldToWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words', function(Blueprint $table) {
            $table->unsignedInteger('category_id')->after('sentiment_score')->nullable();
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
        Schema::table('words', function(Blueprint $table) {
            $table->removeColumn('category_id');
        });
    }
}
