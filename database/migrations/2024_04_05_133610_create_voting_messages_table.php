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
        Schema::create('voting_messages', function (Blueprint $table) {
            $table->id();
            $table->text('content')->nullable();
            $table->string('file')->nullable();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('room_id');
            $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('room_id')->references('id')->on('voting_rooms')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_messages');
    }
};
