<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('headline')->nullable();
            $table->text('description')->nullable();
            $table->json('fields_config')->nullable();
            $table->foreignId('tag_id')->nullable()->constrained('contact_tags')->nullOnDelete();
            $table->string('success_message', 500)->default('Thanks for signing up!');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('submissions_count')->default(0);
            $table->timestamps();
        });

        Schema::create('lead_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_form_id')->constrained('lead_forms')->cascadeOnDelete();
            $table->foreignId('email_contact_id')->constrained('email_contacts')->cascadeOnDelete();
            $table->string('ip_address', 64)->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_form_submissions');
        Schema::dropIfExists('lead_forms');
    }
};
