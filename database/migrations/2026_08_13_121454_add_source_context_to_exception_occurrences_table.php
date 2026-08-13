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
        Schema::table('exception_occurrences', function (Blueprint $table) {
            // Short relative file path for display (e.g., "app/Http/Controllers/UserController.php")
            $table->string('source_file', 500)->nullable()->after('function');

            // JSON array of {line, content, is_failing} for source code display
            $table->longText('source_context')->nullable()->after('source_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exception_occurrences', function (Blueprint $table) {
            $table->dropColumn(['source_file', 'source_context']);
        });
    }
};
