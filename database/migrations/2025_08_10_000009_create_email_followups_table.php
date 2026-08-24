<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('source_batch_id');
            $table->string('source_subject')->nullable();
            $table->enum('condition', ['not_clicked', 'not_opened'])->default('not_clicked');
            $table->unsignedInteger('wait_minutes')->default(1440);
            $table->foreignId('email_template_id')->constrained('email_templates')->cascadeOnDelete();
            $table->foreignId('email_account_id')->constrained('email_accounts')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sent_count')->default(0);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_followups');
    }
};
