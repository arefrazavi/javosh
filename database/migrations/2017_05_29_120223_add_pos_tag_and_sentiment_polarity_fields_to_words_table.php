<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPosTagAndSentimentPolarityToWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->text('sentiment_polarity')->after('entropy')->nullable();
            $table->string('pos_tag')->after('sentiment_polarity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->removeColumn('pos_tag');
            $table->removeColumn('sentiment_polarity');
        });
    }
}
