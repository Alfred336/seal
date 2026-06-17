<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('slug', 100)->unique()->nullable();
            $table->char('color', 7)->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 200)->unique();
            $table->string('title', 300);
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('image_path')->nullable();
            $table->string('image_alt', 300)->nullable();
            $table->string('image_gradient', 200)->nullable();
            $table->text('image_icon')->nullable();
            $table->string('read_time', 20)->nullable();
            $table->boolean('featured')->default(false);
            $table->string('status', 20)->default('draft');
            $table->timestampTz('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('slug', 100)->unique()->nullable();
            $table->timestamps();
        });

        Schema::create('post_tags', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['post_id', 'tag_id']);
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('industry', 100)->nullable();
            $table->string('tech_stack', 150)->nullable();
            $table->text('description')->nullable();
            $table->string('client_name', 150)->nullable();
            $table->text('outcome')->nullable();
            $table->string('image_path')->nullable();
            $table->string('live_url')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email');
            $table->string('company', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('project_type', 50)->nullable();
            $table->text('message');
            $table->ipAddress('ip_address')->nullable();
            $table->string('status', 20)->default('new');
            $table->timestampTz('submitted_at')->useCurrent();
        });

        Schema::create('call_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 100);
            $table->string('email');
            $table->string('phone', 30);
            $table->date('preferred_date');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending');
            $table->ipAddress('ip_address')->nullable();
            $table->timestampTz('created_at')->useCurrent();
        });

        Schema::create('project_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 100);
            $table->string('email');
            $table->string('project_type', 50);
            $table->text('details');
            $table->string('status', 20)->default('new');
            $table->ipAddress('ip_address')->nullable();
            $table->timestampTz('created_at')->useCurrent();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('status', 20)->default('active');
            $table->string('source', 50)->nullable();
            $table->timestampTz('subscribed_at')->useCurrent();
            $table->timestampTz('unsubscribed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_tags');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('services');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('contact_submissions');
        Schema::dropIfExists('call_requests');
        Schema::dropIfExists('project_requests');
        Schema::dropIfExists('subscriptions');
    }
};
