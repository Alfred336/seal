<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 150)->nullable()->after('password');
            $table->char('initials', 3)->nullable()->after('role');
            $table->char('color', 7)->nullable()->after('initials');
            $table->text('bio')->nullable()->after('color');
            $table->string('image_path')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'initials', 'color', 'bio', 'image_path']);
        });
    }
};
