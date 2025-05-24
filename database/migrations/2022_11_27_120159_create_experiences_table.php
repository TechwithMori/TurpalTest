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
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('language')->nullable();
            $table->text('inclusions')->nullable();
            $table->text('exclusions')->nullable();
            $table->text('itinerary')->nullable();
            $table->text('what_to_bring')->nullable();
            $table->text('what_to_wear')->nullable();
            $table->text('what_to_expect')->nullable();
            $table->text('what_to_know')->nullable();
            $table->text('remarks')->nullable();
            $table->string('meeting_instructions')->nullable();
            $table->string('cancellation_policy')->nullable();
            $table->string('refund_policy')->nullable();
            $table->string('health_and_safety')->nullable();
            $table->string('thumbnail')->nullable();
            $table->double('latitude', 10, 8)->nullable();
            $table->double('longitude', 11, 8)->nullable();
            $table->string('city_id')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->unsignedFloat('rating', 5)->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('is_active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('experiences');
    }
};
