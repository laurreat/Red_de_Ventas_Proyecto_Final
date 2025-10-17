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
        Schema::connection('mongodb')->create('mensajes_lider', function ($collection) {
            $collection->index('lider_id');
            $collection->index('vendedor_id');
            $collection->index('leido');
            $collection->index(['vendedor_id', 'leido']);
            $collection->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('mensajes_lider');
    }
};
