<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('comment_id');
            $table->text('text');
            $table->text('entropy')->nullable();
            $table->timestamps();
        });

        Schema::table('sentences', function(Blueprint $table) {
            $table->foreign('comment_id')
                ->references('id')
                ->on('comments')
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
        Schema::dropIfExists('sentences');
    }
}
