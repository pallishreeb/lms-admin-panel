<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            // Add is_free column
            $table->boolean('is_free')->default(false);

            // Add video_url column
            $table->string('video_url')->nullable();
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            // Reverse the changes made in the 'up' method
            $table->dropColumn('is_free');
            $table->dropColumn('video_url');
        });
    }
};
