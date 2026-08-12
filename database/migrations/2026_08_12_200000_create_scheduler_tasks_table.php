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
        Schema::create('scheduler_tasks', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('project_id');
            $table->string('task_name'); // e.g., "backup:database"
            $table->string('task_type')->default('command'); // command | job | closure | null
            $table->string('command_name')->nullable(); // Artisan command name if applicable
            $table->string('job_name')->nullable(); // Job class name if applicable
            $table->string('job_uuid')->nullable(); // Job UUID if applicable
            $table->string('expression')->nullable(); // Cron expression e.g., "0 2 * * *"
            $table->string('description')->nullable(); // Human-readable description
            $table->string('timezone')->nullable(); // e.g., "UTC", "Asia/Kolkata"
            $table->string('environment')->nullable(); // Production, Staging, etc.
            $table->timestamp('next_run_at')->nullable(); // Next expected run
            $table->timestamp('last_run_at')->nullable(); // Last actual run
            $table->string('last_status')->nullable(); // healthy | failed | missed
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->index(['project_id', 'task_name']);
            $table->index(['project_id', 'last_status']);
            $table->index(['project_id', 'environment']);
            $table->index('next_run_at');
            $table->index('last_run_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduler_tasks');
    }
};
