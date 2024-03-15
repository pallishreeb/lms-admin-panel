<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('likes_dislikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('video_id');
            $table->boolean('is_like')->default(true); // Indicates whether it's a like or dislike
            $table->timestamps();
            
            // Define foreign key constraint for video_id
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
            
            // Ensure each user can like/dislike a video only once
            $table->unique(['user_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes_dislikes');
    }
};
