<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_inquiries', function (Blueprint $table): void {
            $table->id();

            $table->string('plan', 30)->index();

            // Client
            $table->string('full_name', 100);
            $table->string('business_name', 150)->nullable();
            $table->string('position', 100)->nullable();
            $table->string('phone', 40);
            $table->string('email')->index();

            // Starter / Professional
            $table->string('business_category', 100)->nullable();
            $table->text('website_purpose')->nullable();
            $table->unsignedSmallInteger('number_of_pages')->nullable();

            $table->boolean('needs_cms')->nullable();
            $table->boolean('needs_blog')->nullable();
            $table->boolean('has_domain')->nullable();
            $table->boolean('has_logo')->nullable();
            $table->boolean('has_content')->nullable();

            $table->string('reference_website', 500)->nullable();
            $table->text('additional_requirements')->nullable();
            $table->text('additional_message')->nullable();

            // Enterprise
            $table->string('project_type', 120)->nullable();
            $table->string('expected_users', 120)->nullable();

            $table->boolean('ecommerce')->nullable();
            $table->boolean('online_payments')->nullable();
            $table->boolean('booking_system')->nullable();
            $table->boolean('user_accounts')->nullable();
            $table->boolean('admin_dashboard')->nullable();
            $table->boolean('api_integration')->nullable();
            $table->boolean('sms_notifications')->nullable();

            $table->text('existing_system')->nullable();
            $table->string('project_deadline', 120)->nullable();
            $table->string('budget_range', 120)->nullable();
            $table->text('project_description')->nullable();

            // Admin
            $table->string('status', 30)->default('new')->index();
            $table->text('notes')->nullable();

            $table->ipAddress('ip_address')->nullable();

            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_inquiries');
    }
};