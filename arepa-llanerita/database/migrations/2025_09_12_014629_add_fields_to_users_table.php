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
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellidos')->after('name');
            $table->string('cedula')->unique()->after('apellidos');
            $table->string('telefono')->nullable()->after('email');
            $table->text('direccion')->nullable()->after('telefono');
            $table->string('ciudad')->nullable()->after('direccion');
            $table->string('departamento')->nullable()->after('ciudad');
            $table->date('fecha_nacimiento')->nullable()->after('departamento');
            
            // Sistema de roles
            $table->enum('rol', ['cliente', 'vendedor', 'lider', 'administrador'])->default('cliente')->after('fecha_nacimiento');
            $table->boolean('activo')->default(true)->after('rol');
            $table->timestamp('ultimo_acceso')->nullable()->after('activo');
            
            // Sistema de referidos
            $table->unsignedBigInteger('referido_por')->nullable()->after('ultimo_acceso');
            $table->string('codigo_referido')->unique()->nullable()->after('referido_por');
            $table->integer('total_referidos')->default(0)->after('codigo_referido');
            $table->decimal('comisiones_ganadas', 12, 2)->default(0)->after('total_referidos');
            $table->decimal('comisiones_disponibles', 12, 2)->default(0)->after('comisiones_ganadas');
            
            // Información comercial
            $table->decimal('meta_mensual', 12, 2)->nullable()->after('comisiones_disponibles');
            $table->decimal('ventas_mes_actual', 12, 2)->default(0)->after('meta_mensual');
            $table->integer('nivel_vendedor')->default(1)->after('ventas_mes_actual');
            $table->json('zonas_asignadas')->nullable()->after('nivel_vendedor');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'apellidos', 'cedula', 'telefono', 'direccion', 'ciudad', 'departamento',
                'fecha_nacimiento', 'rol', 'activo', 'ultimo_acceso', 'referido_por',
                'codigo_referido', 'total_referidos', 'comisiones_ganadas', 'comisiones_disponibles',
                'meta_mensual', 'ventas_mes_actual', 'nivel_vendedor', 'zonas_asignadas'
            ]);
        });
    }
};
