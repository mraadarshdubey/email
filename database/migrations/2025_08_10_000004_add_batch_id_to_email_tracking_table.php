<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_tracking', function (Blueprint $table) {
            $table->uuid('batch_id')->nullable()->after('user_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('email_tracking', function (Blueprint $table) {
            $table->dropColumn('batch_id');
        });
    }
};
