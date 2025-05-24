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
        Schema::create('experience_video', function (Blueprint $table) {
            $table->foreignId('experience_id')->constrained('experiences')->onDelete('cascade');
            $table->foreignId('video_id')->constrained('videos')->onDelete('cascade');
            $table->primary(['experience_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('experience_video');
    }
};
