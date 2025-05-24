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
        Schema::create('experience_image', function (Blueprint $table) {
            $table->foreignId('experience_id')->constrained('experiences')->onDelete('cascade');
            $table->foreignId('image_id')->constrained('images')->onDelete('cascade');
            $table->primary(['experience_id', 'image_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('experience_image');
    }
};
