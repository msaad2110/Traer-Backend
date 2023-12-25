<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->references('id')->on('trips')->cascadeOnDelete();
            $table->foreignId('luggage_type_id')->references('id')->on('luggage_types')->cascadeOnDelete();
            $table->integer('product_space');
            $table->integer('product_value');
            $table->text('description');
            $table->boolean('is_insured')->default(false);
            $table->foreignId('created_by_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('updated_by_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
            $table->integer('deleted_by_id')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
