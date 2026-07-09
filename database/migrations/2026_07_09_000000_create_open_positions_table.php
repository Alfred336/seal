<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('open_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 250)->unique();
            $table->string('type', 50);
            $table->string('location', 150);
            $table->string('tech_stack', 200)->nullable();
            $table->text('description');
            $table->string('status', 20)->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_positions');
    }
};
