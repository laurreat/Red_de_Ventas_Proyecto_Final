<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MongoDB crea colecciones automáticamente al insertar documentos
        // Esta migración existe para documentar la estructura esperada

        Schema::connection('mongodb')->create('solicitudes_pago', function ($collection) {
            // Estructura del documento:
            // {
            //   "_id": ObjectId,
            //   "user_id": String (ID del usuario que solicita),
            //   "user_data": {
            //     "name": String,
            //     "email": String,
            //     "rol": String
            //   },
            //   "monto": Decimal128,
            //   "metodo_pago": String (transferencia|nequi|daviplata|efectivo),
            //   "datos_pago": String (Número de cuenta, teléfono, etc.),
            //   "observaciones": String,
            //   "estado": String (pendiente|aprobado|pagado|rechazado),
            //   "fecha_procesado": DateTime,
            //   "procesado_por": String (ID del admin),
            //   "comprobante": {
            //     "url": String,
            //     "nombre": String,
            //     "fecha_subida": DateTime
            //   },
            //   "notas_admin": String,
            //   "mysql_id": Integer (referencia opcional a MySQL),
            //   "created_at": DateTime,
            //   "updated_at": DateTime
            // }
        });

        // Crear índices para optimizar consultas
        Schema::connection('mongodb')->table('solicitudes_pago', function ($collection) {
            $collection->index('user_id');
            $collection->index('estado');
            $collection->index('metodo_pago');
            $collection->index('created_at');
            $collection->index(['user_id' => 1, 'estado' => 1]);
            $collection->index(['estado' => 1, 'created_at' => -1]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('solicitudes_pago');
    }
};
