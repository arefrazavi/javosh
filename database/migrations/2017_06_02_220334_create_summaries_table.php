<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('summaries', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('sentence_id')->unsigned();
            $table->integer('aspect_id')->unsigned();
            $table->integer('method_id')->unsigned();
            $table->integer('user_id')->unsigned()->default(1);
            $table->integer('polarity')->nullable();
        });

        Schema::table('summaries', function (Blueprint $table) {
            //$table->unique(array('product_id', 'sentence_id', 'method_id', 'user_id'), 'summaries_unique_key');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('sentence_id')
                ->references('id')
                ->on('sentences')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('aspect_id')
                ->references('id')
                ->on('aspects')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('method_id')
                ->references('id')
                ->on('summarization_methods')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('summaries');
    }
}
