<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::statement('ALTER TABLE resources DROP CONSTRAINT resources_type_check');
            DB::statement('ALTER TABLE resources ADD CONSTRAINT resources_type_check CHECK (type::TEXT = ANY (ARRAY[\'embed\'::CHARACTER VARYING, \'link\'::CHARACTER VARYING, \'saluran\'::CHARACTER VARYING]::TEXT[]))');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::statement('ALTER TABLE resources DROP CONSTRAINT resources_type_check');
            DB::statement('ALTER TABLE resources ADD CONSTRAINT resources_type_check CHECK (type::TEXT = ANY (ARRAY[\'embed\'::CHARACTER VARYING, \'link\'::CHARACTER VARYING]::TEXT[]))');
        });
    }
};
