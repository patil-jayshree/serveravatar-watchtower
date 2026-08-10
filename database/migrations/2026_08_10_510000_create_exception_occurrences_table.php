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
        Schema::create('exception_occurrences', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('exception_group_uuid')->constrained('exception_groups', 'uuid')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('request_id', 100)->nullable()->index(); // Link to request_events.request_id
            $table->text('message');
            $table->longText('stack_trace');
            $table->string('file', 500);
            $table->unsignedInteger('line');
            $table->unsignedSmallInteger('status_code')->nullable(); // HTTP status code when available
            $table->string('method', 10)->nullable(); // HTTP method
            $table->string('path', 500)->nullable(); // Request path
            $table->string('route_name', 255)->nullable(); // Laravel route name
            $table->string('controller_action', 255)->nullable(); // Controller@action
            $table->string('host', 255)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('environment', 50)->default('production');
            $table->string('laravel_version', 50)->nullable();
            $table->string('php_version', 50)->nullable();
            $table->string('agent_version', 50)->nullable();
            $table->timestamp('occurred_at')->useCurrent();

            // Indexes for common queries
            $table->index(['project_id', 'occurred_at']);
            $table->index(['exception_group_uuid', 'occurred_at']);
            $table->index(['request_id'], 'exc_occ_request_id_index');
            $table->index('status_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exception_occurrences');
    }
};
