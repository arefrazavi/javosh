<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasCommentFieldToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_comment')->after('category_id')->default(1);
        });

        DB::statement("UPDATE products SET `has_comment` = 0 WHERE products.id NOT IN (SELECT product_id FROM comments WHERE products.id = comments.product_id)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->removeColumn('has_comment');
        });
    }
}
