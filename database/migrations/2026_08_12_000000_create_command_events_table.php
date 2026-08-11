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
        Schema::create('command_events', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            // Command identification
            $table->string('command_name')->index(); // e.g. "migrate", "cache:clear"

            // Status
            $table->string('status')->index(); // started, completed, failed

            // Exit code (null for started)
            $table->smallInteger('exit_code')->nullable();

            // Timing
            $table->unsignedInteger('started_at')->nullable();
            $table->unsignedInteger('finished_at')->nullable();
            $table->float('duration_ms')->nullable();

            // Arguments and options (JSON, sanitized)
            $table->jsonb('arguments')->nullable();
            $table->jsonb('options')->nullable();

            // Correlation
            $table->string('request_id')->nullable()->index();

            // Failure info (populated if command failed)
            $table->string('exception_class')->nullable();
            $table->text('exception_message')->nullable();
            $table->string('exception_file')->nullable();
            $table->unsignedInteger('exception_line')->nullable();
            $table->longText('stack_trace')->nullable();

            // Metadata
            $table->string('environment')->nullable();
            $table->string('agent_version')->nullable();
            $table->string('laravel_version')->nullable();
            $table->string('php_version')->nullable();
            $table->string('server_name')->nullable();

            // Timestamps
            $table->timestamps();

            // Composite indexes for common queries
            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'command_name']);
            $table->index(['project_id', 'created_at']);
            $table->index(['project_id', 'duration_ms']);
            $table->index(['project_id', 'exit_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfNull('command_events');
    }
};
