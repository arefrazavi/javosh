<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClosetAspectIdFieldToAspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aspects', function (Blueprint $table) {
            $table->integer('closest_aspect_id')->after('id')->default(0);

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
            $table->removeColumn('closest_aspect_id');
        });
    }
}
