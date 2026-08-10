<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Project relation
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            // Request identification
            $table->string('request_id')->index(); // Unique per request, for correlation
            $table->string('method', 10); // GET, POST, etc.
            $table->string('path', 2048); // URL path
            $table->string('url', 2048)->nullable(); // Full URL
            $table->string('route_name')->nullable(); // Named route if available
            $table->string('controller_action')->nullable(); // Controller@action

            // Response
            $table->unsignedSmallInteger('status_code');
            $table->unsignedInteger('duration_ms'); // Response time in ms
            $table->unsignedInteger('memory_bytes')->nullable(); // Memory usage

            // Request context
            $table->string('host')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip', 45)->nullable(); // IPv4 or IPv6
            $table->string('environment')->default('production');
            $table->string('content_type')->nullable();

            // Timestamps
            $table->timestamp('requested_at'); // When request started
            $table->timestamps();

            // Indexes for common queries
            $table->index(['project_id', 'requested_at']);
            $table->index(['project_id', 'status_code']);
            $table->index(['project_id', 'method']);
            $table->index(['project_id', 'route_name']);
            $table->index('requested_at'); // For retention/cleanup jobs
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_events');
    }
};
