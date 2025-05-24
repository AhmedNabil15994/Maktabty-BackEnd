<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEducationalInstitutionIdToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('educational_institution_id')->unsigned()->nullable()->after('product_type');
            $table->foreign('educational_institution_id')->references('id')->on('educational_institutions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_educational_institution_id_foreign');
            $table->dropIndex('products_educational_institution_id_foreign');
            $table->dropColumn(['educational_institution_id']);
        });
    }
}
