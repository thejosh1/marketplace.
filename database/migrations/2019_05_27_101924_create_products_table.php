<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->double('price');
            $table->tinyInteger('in_stock')->default(1);
            $table->string('image');
            $table->text('description');
            $table->string('slug');
            $table->timestamps();
            $table->softDeletes();

            //$table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            //$table->foreign('image_id')->references('id')->on('product_images')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
