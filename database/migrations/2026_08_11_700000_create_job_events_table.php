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
        Schema::create('job_events', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            
            // Job identification
            $table->string('job_id')->nullable()->index(); // Laravel queue job ID
            $table->string('job_uuid')->nullable()->index(); // Custom/generated UUID
            $table->string('job_name')->index(); // Job class name
            $table->string('queue')->nullable()->index();
            $table->string('connection')->nullable();
            
            // Status and attempts
            $table->string('status')->index(); // queued, started, completed, failed
            $table->unsignedSmallInteger('attempts')->default(1);
            
            // Timing
            $table->unsignedInteger('queued_at')->nullable();
            $table->unsignedInteger('started_at')->nullable();
            $table->unsignedInteger('completed_at')->nullable();
            $table->unsignedInteger('failed_at')->nullable();
            $table->float('duration_ms')->nullable(); // Execution duration
            
            // Correlation
            $table->string('request_id')->nullable()->index();
            
            // Failure info
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
            $table->index(['project_id', 'job_name']);
            $table->index(['project_id', 'created_at']);
            $table->index(['project_id', 'duration_ms']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfNull('job_events');
    }
};
