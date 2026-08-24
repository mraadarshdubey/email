<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_tracking', function (Blueprint $table) {
            $table->timestamp('followed_up_at')->nullable()->after('click_count');
        });
    }

    public function down(): void
    {
        Schema::table('email_tracking', function (Blueprint $table) {
            $table->dropColumn('followed_up_at');
        });
    }
};
