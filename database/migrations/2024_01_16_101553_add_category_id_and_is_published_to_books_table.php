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
        Schema::table('books', function (Blueprint $table) {
            // Add foreign key column
            $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnDelete();

            // Add is_published column with a default value of true
            $table->boolean('is_published')->default(true);
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            // Drop foreign key column
            $table->dropForeign(['category_id']);

            // Drop columns
            $table->dropColumn(['category_id', 'is_published']);
        });
    }
};
