<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE attendances DROP CONSTRAINT IF EXISTS attendances_status_check');
        DB::statement("ALTER TABLE attendances ADD CONSTRAINT attendances_status_check CHECK (status::text = ANY (ARRAY['hadir'::character varying, 'izin'::character varying, 'sakit'::character varying, 'alpha'::character varying, 'terlambat'::character varying, 'pulang'::character varying]::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE attendances DROP CONSTRAINT IF EXISTS attendances_status_check');
        DB::statement("ALTER TABLE attendances ADD CONSTRAINT attendances_status_check CHECK (status::text = ANY (ARRAY['hadir'::character varying, 'izin'::character varying, 'sakit'::character varying, 'alpha'::character varying, 'terlambat'::character varying]::text[]))");
    }
};
