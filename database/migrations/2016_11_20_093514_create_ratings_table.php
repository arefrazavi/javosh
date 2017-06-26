<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->bigInteger('entity_id')->unsigned();
            $table->integer('entity_type_id')->unsigned();
            $table->string('rate');
            $table->integer('rating_type_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('ratings', function(Blueprint $table) {
            $table->primary(array('entity_id', 'entity_type_id', 'rating_type_id'));
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
