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
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('source', 20)->default('manual')->after('sort_order');
            $table->string('email')->nullable()->after('source');
            $table->string('author_url')->nullable()->after('email');
        });

        // Marcar depoimentos existentes com role "Avaliacao Google" como source=google
        DB::table('testimonials')->where('role', 'Avaliacao Google')->update(['source' => 'google']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['source', 'email', 'author_url']);
        });
    }
};
