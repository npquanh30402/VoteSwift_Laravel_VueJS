<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_title');
            $table->string('question_description');
            $table->boolean('allow_multiple_votes')->default(false);
            $table->string('question_image')->nullable();
            $table->unsignedBigInteger('voting_room_id');
            $table->foreign('voting_room_id')->references('id')->on('voting_rooms')->cascadeOnDelete();
            $table->timestamps();

            $table->index('voting_room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
