<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('course_id');
        });

        // Backfill: set completed_at from certificate issued_at where a certificate exists
        DB::statement('
            UPDATE enrollments e
            INNER JOIN certificates c ON e.user_id = c.user_id AND e.course_id = c.course_id
            SET e.completed_at = c.issued_at
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};
