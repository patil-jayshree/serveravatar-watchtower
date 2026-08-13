<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_events', function (Blueprint $table) {
            $table->text('response_body')->nullable()->after('status_code');
            $table->string('error_type', 100)->nullable()->after('response_body');
            $table->text('error_message')->nullable()->after('error_type');
        });
    }

    public function down(): void
    {
        Schema::table('request_events', function (Blueprint $table) {
            $table->dropColumn(['response_body', 'error_type', 'error_message']);
        });
    }
};
