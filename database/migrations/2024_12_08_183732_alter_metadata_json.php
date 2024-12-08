<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Use raw SQL to alter the column type
        DB::statement('ALTER TABLE anime ALTER COLUMN metadata TYPE jsonb USING metadata::jsonb');
        DB::statement('ALTER TABLE posts ALTER COLUMN metadata TYPE jsonb USING metadata::jsonb');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Reverse the migration (if necessary, change back to json)
        DB::statement('ALTER TABLE anime ALTER COLUMN metadata TYPE json USING metadata::json');
        DB::statement('ALTER TABLE posts ALTER COLUMN metadata TYPE json USING metadata::json');
    }
};
