<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // For whereDate('scanned_at', $today) queries
            $table->index('scanned_at');
            // For status filtering (COUNT CASE WHEN status = ...)
            $table->index('status');
            // For join + whereDate combo queries
            $table->index(['student_id', 'scanned_at']);
        });

        Schema::table('students', function (Blueprint $table) {
            // For WHERE class_name and GROUP BY class_name
            $table->index('class_name');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['scanned_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['student_id', 'scanned_at']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['class_name']);
        });
    }
};
