<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMakeanoffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('makeanoffers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->nullable();
            $table->integer('next_post_id')->nullable();
            $table->string('original_price')->nullable();
            $table->string('offer_price')->nullable();
            $table->text('description_text')->nullable();
            $table->integer('buyer_id')->nullable();
            $table->integer('seller_id')->nullable();
            $table->integer('is_read_admin')->nullable();
            $table->integer('is_read_professional')->nullable();
            $table->integer('is_read_individual')->nullable();
            $table->integer('approve_seller')->nullable();
            $table->integer('approve_buyer')->nullable();
            $table->integer('approve_admin')->nullable();
            $table->integer('status')->nullable();
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
        Schema::drop('makeanoffers');
    }
}
