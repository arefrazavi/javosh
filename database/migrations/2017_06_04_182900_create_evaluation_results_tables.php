<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluationResultsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_results', function (Blueprint $table) {
            $table->integer('method_id')->unsigned();
            $table->integer('measure_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('aspect_id')->unsigned();
            $table->double('result');
        });

        Schema::table('evaluation_results', function (Blueprint $table) {
            $table->primary(array('method_id', 'measure_id', 'category_id', 'aspect_id'), 'evaluation_results_primary_key');

            $table->foreign('measure_id')
                ->references('id')
                ->on('evaluation_measures')
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
        //
    }
}
