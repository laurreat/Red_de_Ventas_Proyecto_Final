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
        Schema::create('notificacions', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // ID del usuario que recibe la notificación
            $table->string('tipo'); // tipo de notificación (pedido, venta, comision, etc.)
            $table->string('titulo'); // título de la notificación
            $table->text('mensaje'); // mensaje de la notificación
            $table->json('data')->nullable(); // datos adicionales en JSON
            $table->boolean('leida')->default(false); // si fue leída o no
            $table->string('relacionado_tipo')->nullable(); // tipo del objeto relacionado (Pedido, User, etc.)
            $table->string('relacionado_id')->nullable(); // ID del objeto relacionado
            $table->string('prioridad')->default('normal'); // normal, alta, critica
            $table->timestamp('fecha_expiracion')->nullable(); // cuando expira la notificación
            $table->timestamps();

            // Índices para mejorar performance
            $table->index(['user_id', 'leida']);
            $table->index(['tipo', 'created_at']);
            $table->index('fecha_expiracion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacions');
    }
};
