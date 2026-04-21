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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('Identificador unico ex: users.view, services.create');
            $table->string('name')->comment('Nome legivel da permissao');
            $table->text('description')->nullable()->comment('Descricao detalhada');
            $table->string('module')->comment('Modulo ao qual a permissao pertence ex: users, services, settings');
            $table->string('action')->comment('Acao: view, create, edit, delete, manage');
            $table->boolean('is_active')->default(true)->comment('Permissao ativa ou inativa');
            $table->integer('sort_order')->default(0)->comment('Ordem de exibicao');
            $table->timestamps();

            $table->index(['module', 'action']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
