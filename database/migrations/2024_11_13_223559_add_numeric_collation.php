<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE COLLATION IF NOT EXISTS numeric (provider = icu, locale = 'en@colNumeric=yes');");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP COLLATION IF EXISTS numeric;");
    }
};
