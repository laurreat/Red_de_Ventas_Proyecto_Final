<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de categorías
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Tabla de productos
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->unsignedBigInteger('categoria_id');
            $table->boolean('activo')->default(true);
            $table->string('imagen')->nullable();
            $table->timestamps();
            
            $table->foreign('categoria_id')->references('id')->on('categorias');
        });

        // Tabla de pedidos
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_pedido')->unique();
            $table->unsignedBigInteger('user_id'); // Cliente
            $table->unsignedBigInteger('vendedor_id')->nullable();
            $table->enum('estado', ['pendiente', 'confirmado', 'en_preparacion', 'listo', 'en_camino', 'entregado', 'cancelado'])->default('pendiente');
            $table->decimal('total', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total_final', 10, 2);
            $table->text('direccion_entrega');
            $table->string('telefono_entrega');
            $table->text('notas')->nullable();
            $table->timestamp('fecha_entrega_estimada')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vendedor_id')->references('id')->on('users');
        });

        // Tabla de detalles de pedidos
        Schema::create('detalle_pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id');
            $table->unsignedBigInteger('producto_id');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            
            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos');
        });

        // Tabla de comisiones
        Schema::create('comisiones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Vendedor que recibe la comisión
            $table->unsignedBigInteger('pedido_id');
            $table->enum('tipo', ['venta_directa', 'referido_nivel_1', 'referido_nivel_2', 'bono_liderazgo']);
            $table->decimal('porcentaje', 5, 2);
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'aprobada', 'pagada'])->default('pendiente');
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('pedido_id')->references('id')->on('pedidos');
        });

        // Tabla de referidos (para seguimiento detallado)
        Schema::create('referidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referidor_id'); // Quien refiere
            $table->unsignedBigInteger('referido_id'); // Quien es referido
            $table->timestamp('fecha_registro');
            $table->boolean('activo')->default(true);
            $table->decimal('comisiones_generadas', 10, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('referidor_id')->references('id')->on('users');
            $table->foreign('referido_id')->references('id')->on('users');
            $table->unique(['referidor_id', 'referido_id']);
        });

        // Tabla de notificaciones
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('titulo');
            $table->text('mensaje');
            $table->enum('tipo', ['pedido', 'comision', 'sistema', 'promocion']);
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_lectura')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Tabla de movimientos de inventario
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('user_id'); // Usuario responsable del movimiento
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->text('motivo')->nullable();
            $table->timestamps();
            
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('referidos');
        Schema::dropIfExists('comisiones');
        Schema::dropIfExists('detalle_pedidos');
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('categorias');
    }
};