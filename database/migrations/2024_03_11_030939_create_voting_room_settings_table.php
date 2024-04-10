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
        Schema::create('voting_room_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voting_room_id');
            $table->foreign('voting_room_id')->references('id')->on('voting_rooms')->cascadeOnDelete();

            // Extra settings
            $table->boolean('invitation_only')->default(false);
            $table->boolean('wait_for_voters')->default(false);
            $table->boolean('public_visibility')->default(false);
            $table->string('password')->nullable();
            $table->string('password_qrcode')->nullable();
            $table->enum('results_visibility', ['after_voting', 'participants_only', 'restricted'])->default('restricted');

            // General voting room settings
            $table->boolean('allow_anonymous_voting')->default(false);

            // Chat Features
            $table->boolean('chat_enabled')->default(false);
            $table->boolean('chat_messages_saved')->default(false);
            $table->boolean('allow_voters_upload')->default(false);

            $table->timestamps();

            $table->index('voting_room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_room_settings');
    }
};
