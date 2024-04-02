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
        Schema::create('voting_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_name');
            $table->longText('room_description')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('timezone')->default('UTC');
            $table->unsignedBigInteger('user_id')->comment('Room creator');
            $table->boolean('is_published')->default(false);
            $table->boolean('vote_started')->default(false);
            $table->boolean('has_ended')->default(false);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->index('start_time');
            $table->index('end_time');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_rooms');
    }
};
