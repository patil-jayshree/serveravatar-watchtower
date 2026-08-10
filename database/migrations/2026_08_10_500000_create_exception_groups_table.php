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
        Schema::create('exception_groups', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('exception_type', 255); // e.g., RuntimeException, QueryException
            $table->string('group_signature', 255)->index(); // Normalized signature for grouping
            $table->text('normalized_message')->nullable();
            $table->string('file', 500)->nullable(); // Primary file location
            $table->unsignedInteger('line')->nullable();
            $table->timestamp('first_seen_at')->useCurrent();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->unsignedInteger('occurrence_count')->default(1);
            $table->enum('status', ['open', 'resolved'])->default('open');
            $table->timestamps();

            // Indexes for common queries
            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'last_seen_at']);
            $table->index(['exception_type', 'group_signature']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exception_groups');
    }
};
