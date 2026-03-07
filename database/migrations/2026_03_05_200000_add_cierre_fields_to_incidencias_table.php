<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->string('foto_despues')->nullable()->after('foto');
            $table->decimal('lat_cierre', 10, 6)->nullable()->after('longitud');
            $table->decimal('lng_cierre', 10, 6)->nullable()->after('lat_cierre');
            $table->text('notas_cierre')->nullable()->after('lng_cierre');
            $table->timestamp('cerrado_en')->nullable()->after('notas_cierre');
        });
    }

    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropColumn(['foto_despues', 'lat_cierre', 'lng_cierre', 'notas_cierre', 'cerrado_en']);
        });
    }
};
