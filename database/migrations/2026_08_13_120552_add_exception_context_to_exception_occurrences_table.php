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
            // Add exception class (RuntimeException, ValidationException, etc.)
            $table->string('exception_class', 255)->nullable()->after('exception_group_uuid');

            // Add the class where the exception occurred
            $table->string('exception_class_name', 255)->nullable()->after('exception_class');

            // Add the function/method where the exception occurred
            $table->string('function', 255)->nullable()->after('exception_class_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exception_occurrences', function (Blueprint $table) {
            $table->dropColumn(['exception_class', 'exception_class_name', 'function']);
        });
    }
};
