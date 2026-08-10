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
        Schema::table('organizations', function (Blueprint $table) {
            // Drop old columns we no longer need
            if (Schema::hasColumn('organizations', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('organizations', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('organizations', 'owner_id')) {
                // Rename owner_id to user_id for simpler naming
                $table->renameColumn('owner_id', 'user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }
            if (!Schema::hasColumn('organizations', 'status')) {
                $table->enum('status', ['active', 'suspended', 'archived'])->default('active')->after('logo_path');
            }
            if (Schema::hasColumn('organizations', 'user_id')) {
                $table->renameColumn('user_id', 'owner_id');
            }
        });
    }
};
