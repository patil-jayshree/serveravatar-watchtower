<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('query_events', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            // Request correlation
            $table->string('request_id', 100)->nullable()->index();

            // Query details
            $table->text('sql'); // Raw SQL (sanitized - no sensitive bindings)
            $table->string('normalized_sql', 1000)->index(); // For grouping similar queries

            // Bindings (already sanitized - redacted sensitive values)
            $table->json('bindings')->nullable();

            // Performance
            $table->unsignedInteger('duration_ms')->index();
            $table->boolean('is_slow')->default(false)->index();

            // Connection info
            $table->string('connection_name', 100)->nullable()->index();
            $table->string('driver', 50)->nullable();
            $table->string('database_name', 255)->nullable();

            // Query classification
            $table->string('query_type', 20)->default('other')->index(); // select, insert, update, delete, other

            // Transaction context (if within a transaction)
            $table->string('transaction_id', 100)->nullable();

            // Timestamps
            $table->timestamp('occurred_at')->useCurrent()->index();
            $table->timestamps();

            // Composite indexes for common queries
            $table->index(['project_id', 'occurred_at']);
            $table->index(['project_id', 'is_slow']);
            $table->index(['project_id', 'query_type']);
            $table->index(['request_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('query_events');
    }
};
