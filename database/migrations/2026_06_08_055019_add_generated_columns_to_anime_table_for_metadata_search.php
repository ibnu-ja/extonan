<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            ALTER TABLE anime
            ADD COLUMN season VARCHAR(255) GENERATED ALWAYS AS (metadata->>'season') STORED,
            ADD COLUMN season_year INT GENERATED ALWAYS AS ((metadata->>'seasonYear')::int) STORED;
        SQL);

        Schema::table('anime', function (Blueprint $table): void {
            $table->index(['season', 'season_year']);
            $table->index('season_year');
            $table->index('is_current');
        });

        DB::statement(<<<'SQL'
            CREATE INDEX anime_metadata_tags_gin ON anime USING GIN ((metadata->'tags') jsonb_path_ops);
        SQL);

        DB::statement(<<<'SQL'
            CREATE INDEX anime_metadata_genres_gin ON anime USING GIN ((metadata->'genres') jsonb_path_ops);
        SQL);
    }

    public function down(): void
    {
        DB::statement(<<<'SQL'
            DROP INDEX IF EXISTS anime_metadata_tags_gin;
        SQL);

        DB::statement(<<<'SQL'
            DROP INDEX IF EXISTS anime_metadata_genres_gin;
        SQL);

        Schema::table('anime', function (Blueprint $table): void {
            $table->dropIndex(['season', 'season_year']);
            $table->dropIndex(['season_year']);
            $table->dropIndex(['is_current']);
            $table->dropColumn('season');
            $table->dropColumn('season_year');
        });
    }
};
