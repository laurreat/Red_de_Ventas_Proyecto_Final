<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Laravel\Connection;
use Illuminate\Support\Facades\DB;

class CreateMongoCollections extends Command
{
    protected $signature = 'mongo:collections {--recreate : Drop existing collections and recreate them}';
    protected $description = 'Create MongoDB collections with proper indexes and validation';

    public function handle()
    {
        $recreate = $this->option('recreate');

        if ($recreate) {
            $this->info('Recreating collections...');
        } else {
            $this->info('Creating MongoDB collections...');
        }

        try {
            $this->createUsersCollection($recreate);
            $this->createProductosCollection($recreate);
            $this->createPedidosCollection($recreate);
            $this->createComisionesCollection($recreate);
            $this->createReferidosCollection($recreate);
            $this->createNotificacionesCollection($recreate);
            $this->createAuditoriasCollection($recreate);
            $this->createConfiguracionesCollection($recreate);
            $this->createPasswordResetsCollection($recreate);

            $this->info('✅ All collections created successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Error creating collections: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function createUsersCollection($recreate = false)
    {
        $collection = 'users';

        if ($recreate) {
            DB::connection('mongodb')->getCollection($collection)->drop();
        }

        // Create collection
        DB::connection('mongodb')->createCollection($collection, [
            'validator' => [
                '$jsonSchema' => [
                    'bsonType' => 'object',
                    'required' => ['name', 'email', 'rol'],
                    'properties' => [
                        'name' => ['bsonType' => 'string'],
                        'email' => ['bsonType' => 'string'],
                        'rol' => ['enum' => ['administrador', 'lider', 'vendedor', 'cliente']],
                        'telefono' => ['bsonType' => 'string'],
                        'activo' => ['bsonType' => 'bool'],
                        'created_at' => ['bsonType' => 'date'],
                        'updated_at' => ['bsonType' => 'date']
                    ]
                ]
            ]
        ]);

        // Create indexes
        $collection = DB::connection('mongodb')->getCollection($collection);
        $collection->createIndex(['email' => 1], ['unique' => true]);
        $collection->createIndex(['rol' => 1]);
        $collection->createIndex(['activo' => 1]);
        $collection->createIndex(['created_at' => -1]);

        $this->info("✅ Users collection created with indexes");
    }

    private function createProductosCollection($recreate = false)
    {
        $collection = 'productos';

        if ($recreate) {
            DB::connection('mongodb')->getCollection($collection)->drop();
        }

        DB::connection('mongodb')->createCollection($collection);

        $collection = DB::connection('mongodb')->getCollection($collection);
        $collection->createIndex(['nombre' => 1]);
        $collection->createIndex(['categoria' => 1]);
        $collection->createIndex(['activo' => 1]);
        $collection->createIndex(['precio' => 1]);

        $this->info("✅ Productos collection created with indexes");
    }

    private function createPedidosCollection($recreate = false)
    {
        $collection = 'pedidos';

        if ($recreate) {
            DB::connection('mongodb')->getCollection($collection)->drop();
        }

        DB::connection('mongodb')->createCollection($collection);

        $collection = DB::connection('mongodb')->getCollection($collection);
        $collection->createIndex(['cliente_id' => 1]);
        $collection->createIndex(['vendedor_id' => 1]);
        $collection->createIndex(['estado' => 1]);
        $collection->createIndex(['created_at' => -1]);
        $collection->createIndex(['fecha_entrega' => 1]);
        $collection->createIndex(['total_final' => 1]);

        $this->info("✅ Pedidos collection created with indexes");
    }

    private function createComisionesCollection($recreate = false)
    {
        $collection = 'comisiones';

        if ($recreate) {
            DB::connection('mongodb')->getCollection($collection)->drop();
        }

        DB::connection('mongodb')->createCollection($collection);

        $collection = DB::connection('mongodb')->getCollection($collection);
        $collection->createIndex(['vendedor_id' => 1]);
        $collection->createIndex(['estado' => 1]);
        $collection->createIndex(['fecha_calculo' => -1]);
        $collection->createIndex(['periodo' => 1]);
        $collection->createIndex(['monto' => 1]);

        $this->info("✅ Comisiones collection created with indexes");
    }

    private function createReferidosCollection($recreate = false)
    {
        $collection = 'referidos';

        if ($recreate) {
            DB::connection('mongodb')->getCollection($collection)->drop();
        }

        DB::connection('mongodb')->createCollection($collection);

        $collection = DB::connection('mongodb')->getCollection($collection);
        $collection->createIndex(['referidor_id' => 1]);
        $collection->createIndex(['referido_id' => 1]);
        $collection->createIndex(['estado' => 1]);
        $collection->createIndex(['created_at' => -1]);
        $collection->createIndex(['codigo_referido' => 1], ['unique' => true]);

        $this->info("✅ Referidos collection created with indexes");
    }

    private function createNotificacionesCollection($recreate = false)
    {
        $collection = 'notificaciones';

        if ($recreate) {
            DB::connection('mongodb')->getCollection($collection)->drop();
        }

        DB::connection('mongodb')->createCollection($collection);

        $collection = DB::connection('mongodb')->getCollection($collection);
        $collection->createIndex(['user_id' => 1]);
        $collection->createIndex(['leida' => 1]);
        $collection->createIndex(['created_at' => -1]);
        $collection->createIndex(['tipo' => 1]);

        $this->info("✅ Notificaciones collection created with indexes");
    }

    private function createAuditoriasCollection($recreate = false)
    {
        $collection = 'auditorias';

        if ($recreate) {
            DB::connection('mongodb')->getCollection($collection)->drop();
        }

        DB::connection('mongodb')->createCollection($collection);

        $collection = DB::connection('mongodb')->getCollection($collection);
        $collection->createIndex(['usuario_id' => 1]);
        $collection->createIndex(['accion' => 1]);
        $collection->createIndex(['tabla' => 1]);
        $collection->createIndex(['created_at' => -1]);
        $collection->createIndex(['ip' => 1]);

        $this->info("✅ Auditorias collection created with indexes");
    }

    private function createConfiguracionesCollection($recreate = false)
    {
        $collection = 'configuraciones';

        if ($recreate) {
            DB::connection('mongodb')->getCollection($collection)->drop();
        }

        DB::connection('mongodb')->createCollection($collection);

        $collection = DB::connection('mongodb')->getCollection($collection);
        $collection->createIndex(['clave' => 1], ['unique' => true]);
        $collection->createIndex(['categoria' => 1]);

        $this->info("✅ Configuraciones collection created with indexes");
    }

    private function createPasswordResetsCollection($recreate = false)
    {
        $collection = 'password_resets';

        if ($recreate) {
            DB::connection('mongodb')->getCollection($collection)->drop();
        }

        DB::connection('mongodb')->createCollection($collection);

        $collection = DB::connection('mongodb')->getCollection($collection);
        $collection->createIndex(['email' => 1]);
        $collection->createIndex(['token' => 1]);
        $collection->createIndex(['created_at' => 1], ['expireAfterSeconds' => 3600]); // TTL index - expire after 1 hour

        $this->info("✅ Password resets collection created with TTL index");
    }
}