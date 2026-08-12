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
            $table->uuid('scheduler_uuid')->nullable()->after('command_uuid');
            $table->index(['scheduler_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exception_occurrences', function (Blueprint $table) {
            $table->dropIndex(['scheduler_uuid']);
            $table->dropColumn('scheduler_uuid');
        });
    }
};
