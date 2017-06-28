<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropPrimary(['entity_id', 'entity_type_id', 'rating_type_id']);
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unique(['entity_id', 'entity_type_id', 'rating_type_id'], 'rating_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropPrimary("id")->before('entity_id');
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->dropColumn("id");
            $table->primary(['entity_id', 'entity_type_id', 'rating_type_id']);
            $table->dropUnique(['entity_id', 'entity_type_id', 'rating_type_id']);
        });

    }
}
