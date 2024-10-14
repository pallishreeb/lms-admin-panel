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
        Schema::table('users', function (Blueprint $table) {
            $table->string('device_token')->nullable()->after('password'); // Adjust 'after' as needed
            $table->boolean('hasAdminAccess')->nullable()->after('device_token'); // Allows null, can store true or false
            $table->boolean('isLoggedIn')->nullable()->after('hasAdminAccess'); // Allows null, can store true or false
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['hasAdminAccess', 'isLoggedIn','device_token']);
        });
    }
};
