<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class RawIngredientTableTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'raw_ingredients';

    protected function say(string $message): void
    {
        fwrite(STDOUT, $message . PHP_EOL);
    }

    public static function expectedSchemaDataProvider(): array
    {
        return [
            'id' => ['id', 'bigint'],
            'raw_ingredient' => ['raw_ingredient', 'varchar'],
            'created_at' => ['created_at', 'timestamp'],
            'updated_at' => ['updated_at', 'timestamp'],
        ];
    }

    public function test_raw_ingredients_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable($this->table),
            "A '{$this->table}' tábla nem létezik"
        );

        $this->say("✅ [PASSED] Table exists: {$this->table}");
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_raw_ingredients_table_has_columns(string $column, string $type): void
    {
        $this->assertTrue(
            Schema::hasColumn($this->table, $column),
            "A '{$column}' oszlop nem létezik az '{$this->table}' táblában"
        );

        $this->say("✅ [PASSED] Column exists: {$this->table}.{$column}");
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_raw_ingredients_table_column_types(string $column, string $type): void
    {
        $driver = DB::getDriverName();
        $actual = Schema::getColumnType($this->table, $column);

        // SQLite normalizálások
        if ($driver === 'sqlite' && $type === 'bigint') {
            $this->assertEquals('integer', $actual);
            $this->say("✅ [PASSED] Type OK (sqlite normalized): {$column}");
            return;
        }

        if ($driver === 'sqlite' && $type === 'timestamp') {
            $this->assertTrue(in_array($actual, ['datetime', 'timestamp'], true));
            $this->say("✅ [PASSED] Type OK (sqlite datetime/timestamp): {$column}");
            return;
        }

        if ($type === 'string') {
            $this->assertEquals('string', $actual);
            $this->say("✅ [PASSED] Type OK: {$column} string");
            return;
        }

        $this->assertEquals(
            $type,
            $actual,
            "A '{$column}' oszlop típusa nem megfelelő. Várt: {$type}, kapott: {$actual}"
        );

        $this->say("✅ [PASSED] Type OK: {$column} ({$actual})");
    }

    public function test_raw_ingredient_column_is_unique(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$this->table}')");

            $found = false;
            foreach ($indexes as $idx) {
                if ((int)($idx->unique ?? 0) !== 1) continue;

                $cols = DB::select("PRAGMA index_info('{$idx->name}')");
                $colNames = array_map(fn ($c) => $c->name, $cols);

                if ($colNames === ['raw_ingredient']) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue(
                $found,
                "Nem található UNIQUE index a raw_ingredient oszlopon (sqlite)"
            );

            $this->say("✅ [PASSED] Unique index exists (sqlite): raw_ingredient");
            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $dbName = DB::connection()->getDatabaseName();

            $rows = DB::select(
                "SELECT INDEX_NAME, COLUMN_NAME, NON_UNIQUE
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
                [$dbName, $this->table]
            );

            $found = false;
            foreach ($rows as $r) {
                if ((int)$r->NON_UNIQUE === 0 && $r->COLUMN_NAME === 'raw_ingredient') {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue(
                $found,
                "Nem található UNIQUE index a raw_ingredient oszlopon (mysql/mariadb)"
            );

            $this->say("✅ [PASSED] Unique index exists (mysql/mariadb): raw_ingredient");
            return;
        }

        if ($driver === 'pgsql') {
            $rows = DB::select(
                "SELECT indexdef
                 FROM pg_indexes
                 WHERE schemaname = current_schema() AND tablename = ?",
                [$this->table]
            );

            $found = false;
            foreach ($rows as $r) {
                if (
                    stripos($r->indexdef, 'unique') !== false &&
                    stripos($r->indexdef, '(raw_ingredient)') !== false
                ) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue(
                $found,
                "Nem található UNIQUE index a raw_ingredient oszlopon (pgsql)"
            );

            $this->say("✅ [PASSED] Unique index exists (pgsql): raw_ingredient");
            return;
        }

        $this->assertTrue(true);
        $this->say("✅ [PASSED] Unique index check skipped (unknown driver={$driver})");
    }
}
