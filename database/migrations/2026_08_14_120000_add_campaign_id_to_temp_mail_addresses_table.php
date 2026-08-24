<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tag temporary recipient rows with the campaign they belong to, so the
     * background dispatcher can stream a specific campaign's recipients
     * without one import clobbering another.
     */
    public function up(): void
    {
        Schema::table('temp_mail_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_id')->nullable()->index()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('temp_mail_addresses', function (Blueprint $table) {
            $table->dropColumn('campaign_id');
        });
    }
};
