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
            $table->string('command_uuid', 100)->nullable()->after('job_uuid');
            $table->index(['project_id', 'command_uuid'], 'exc_occ_command_uuid_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exception_occurrences', function (Blueprint $table) {
            $table->dropIndex('exc_occ_command_uuid_index');
            $table->dropColumn('command_uuid');
        });
    }
};
