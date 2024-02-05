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
            $table->string('mobile_number')->after('password');
            $table->string('otp')->nullable()->after('mobile_number');
            $table->timestamp('otp_valid_until')->nullable()->after('otp');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mobile_number');
            $table->dropColumn('otp');
            $table->dropColumn('otp_valid_until');
        });
    }
};
