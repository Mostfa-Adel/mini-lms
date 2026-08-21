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
        DB::table('certificates')->select('user_id', 'course_id', 'issued_at')
            ->orderBy('id')
            ->each(function ($certificate) {
                DB::table('enrollments')
                    ->where('user_id', $certificate->user_id)
                    ->where('course_id', $certificate->course_id)
                    ->update(['completed_at' => $certificate->issued_at]);
            });
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
