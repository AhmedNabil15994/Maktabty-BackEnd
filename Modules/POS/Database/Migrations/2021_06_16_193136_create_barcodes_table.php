<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barcodes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->text('description')->nullable();
            $table->float('width', 22, 4)->nullable();
            $table->float('height', 22, 4)->nullable();
            $table->float('paper_width', 22, 4)->nullable();
            $table->float('paper_height', 22, 4)->nullable();
            $table->float('top_margin', 22, 4)->nullable();
            $table->float('left_margin', 22, 4)->nullable();
            $table->float('row_distance', 22, 4)->nullable();
            $table->float('col_distance', 22, 4)->nullable();
            $table->integer('stickers_in_one_row')->nullable();
            $table->boolean('is_default')->default(0);
            $table->boolean('is_continuous')->default(0);
            $table->integer('stickers_in_one_sheet')->nullable();
            $table->boolean("status")->default(true);

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
        Schema::dropIfExists('barcodes');
    }
}
