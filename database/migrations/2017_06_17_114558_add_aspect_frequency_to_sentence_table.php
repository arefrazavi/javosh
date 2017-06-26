<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAspectFrequencyToSentenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sentences', function (Blueprint $table) {
            $table->text('aspect_frequency')->after('entropy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sentences', function (Blueprint $table) {
            $table->removeColumn('aspect_frequency');
        });
    }
}
