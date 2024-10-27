<?php

// database/migrations/xxxx_xx_xx_xxxxxx_remove_image_from_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveImageFromProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable();
        });
    }
}
