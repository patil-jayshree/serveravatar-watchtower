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
            $table->string('job_uuid', 100)->nullable()->after('request_id');
            $table->index(['project_id', 'job_uuid'], 'exc_occ_job_uuid_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exception_occurrences', function (Blueprint $table) {
            $table->dropIndex('exc_occ_job_uuid_index');
            $table->dropColumn('job_uuid');
        });
    }
};
