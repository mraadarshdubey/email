<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_tracking', function (Blueprint $table) {
            $table->string('last_open_ip', 64)->nullable()->after('open_count');
            $table->string('last_open_user_agent', 500)->nullable()->after('last_open_ip');
        });
    }

    public function down(): void
    {
        Schema::table('email_tracking', function (Blueprint $table) {
            $table->dropColumn(['last_open_ip', 'last_open_user_agent']);
        });
    }
};
