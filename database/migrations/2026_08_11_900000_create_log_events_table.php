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
        Schema::create('log_events', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('level', 20);
            $table->text('message');
            $table->json('context')->nullable();
            $table->string('channel', 100)->nullable();
            $table->string('request_id', 100)->nullable();
            $table->string('exception_class', 255)->nullable();
            $table->string('exception_message', 500)->nullable();
            $table->string('file', 500)->nullable();
            $table->unsignedInteger('line')->nullable();
            $table->string('environment', 50)->nullable();
            $table->string('host', 255)->nullable();
            $table->string('agent_version', 50)->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();

            // Indexes for filtering and high-volume queries
            $table->index(['project_id', 'level']);
            $table->index(['project_id', 'channel']);
            $table->index(['project_id', 'created_at']);
            $table->index('request_id');
            $table->index('level');
            $table->index('channel');
            $table->index('logged_at');

            // Composite index for common queries
            $table->index(['project_id', 'level', 'logged_at']);
            $table->index(['project_id', 'channel', 'logged_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_events');
    }
};
