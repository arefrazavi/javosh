<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSentimentPolarityFieldToSentencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sentences', function (Blueprint $table) {
            $table->integer('sentiment_polarity')->default(0);
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
            $table->dropColumn('gold_selected');
            $table->dropColumn('sentiment_polarity');
        });
    }
}
