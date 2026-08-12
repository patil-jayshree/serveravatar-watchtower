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
        Schema::create('scheduler_executions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('project_id');
            $table->uuid('scheduler_task_uuid');
            $table->string('status'); // started | completed | failed | missed
            $table->timestamp('expected_at')->nullable(); // When it was supposed to run
            $table->timestamp('started_at')->nullable(); // Actual start
            $table->timestamp('finished_at')->nullable(); // Actual finish
            $table->unsignedBigInteger('duration_ms')->nullable(); // Duration in ms
            $table->unsignedInteger('delay_ms')->nullable(); // Delay from expected time in ms
            $table->string('command_name')->nullable(); // Linked Artisan command
            $table->uuid('command_uuid')->nullable(); // Linked CommandEvent
            $table->string('job_name')->nullable(); // Linked Job name
            $table->uuid('job_uuid')->nullable(); // Linked JobEvent
            $table->uuid('exception_uuid')->nullable(); // Linked ExceptionOccurrence
            $table->string('exception_class')->nullable(); // Exception class if failed
            $table->string('exception_message')->nullable(); // Exception message if failed
            $table->text('stack_trace')->nullable(); // Stack trace if failed
            $table->string('environment')->nullable();
            $table->string('agent_version')->nullable();
            $table->string('laravel_version')->nullable();
            $table->string('php_version')->nullable();
            $table->string('server_name')->nullable();
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->foreign('scheduler_task_uuid')
                ->references('uuid')
                ->on('scheduler_tasks')
                ->onDelete('cascade');

            // Indexes for common queries
            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'scheduler_task_uuid']);
            $table->index(['scheduler_task_uuid', 'created_at']);
            $table->index('expected_at');
            $table->index('started_at');
            $table->index('command_uuid');
            $table->index('job_uuid');
            $table->index('exception_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduler_executions');
    }
};
