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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('luggage_type_id')->references('id')->on('luggage_types');
            $table->string('travelling_from');
            $table->string('travelling_to');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('luggage_space');
            $table->integer('commission');
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
        Schema::dropIfExists('trips');
    }
};
