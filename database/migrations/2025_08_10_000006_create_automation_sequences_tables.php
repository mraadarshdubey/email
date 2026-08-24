<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('trigger_tag_id')->constrained('contact_tags')->cascadeOnDelete();
            $table->foreignId('email_account_id')->nullable()->constrained('email_accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('automation_sequence_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sequence_id')->constrained('automation_sequences')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->unsignedInteger('delay_minutes')->default(0);
            $table->foreignId('email_template_id')->constrained('email_templates')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('automation_sequence_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sequence_id')->constrained('automation_sequences')->cascadeOnDelete();
            $table->foreignId('email_contact_id')->constrained('email_contacts')->cascadeOnDelete();
            $table->unsignedInteger('current_step')->default(0);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['sequence_id', 'email_contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_sequence_enrollments');
        Schema::dropIfExists('automation_sequence_steps');
        Schema::dropIfExists('automation_sequences');
    }
};
