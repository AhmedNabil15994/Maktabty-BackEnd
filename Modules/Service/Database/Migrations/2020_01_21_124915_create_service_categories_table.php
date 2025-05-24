<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('slug')->nullable();
            $table->json('title');
            $table->json('seo_keywords')->nullable();
            $table->json('seo_description')->nullable();
            $table->string('image')->nullable();
            $table->string('cover')->nullable();
            $table->string("banner_image")->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('show_in_home')->default(false);
            $table->boolean('has_children')->default(false);
            $table->boolean("open_sub_category")->default(true);
            $table->string("color", 50)->nullable();
            $table->integer("sort")->default(0);
            $table->bigInteger('service_category_id')->unsigned()->nullable();
            $table->foreign('service_category_id')->references('id')->on('service_categories')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_categories');
    }
}
