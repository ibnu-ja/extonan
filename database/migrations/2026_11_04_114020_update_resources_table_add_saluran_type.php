<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE resources DROP CONSTRAINT IF EXISTS resources_type_check');
            DB::statement("ALTER TABLE resources ADD CONSTRAINT resources_type_check CHECK (type IN ('embed', 'link', 'saluran'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE resources DROP CONSTRAINT IF EXISTS resources_type_check');
            DB::statement("ALTER TABLE resources ADD CONSTRAINT resources_type_check CHECK (type IN ('embed', 'link'))");
        }
    }
};
