<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alterar created_at para ter valor padrao CURRENT_TIMESTAMP
        DB::statement('ALTER TABLE audit_logs MODIFY created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE audit_logs MODIFY created_at TIMESTAMP');
    }
};
