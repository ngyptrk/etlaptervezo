<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class DayTableTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'days';

    protected function say(string $message): void
    {
        fwrite(STDOUT, $message . PHP_EOL);
    }

    public static function expectedSchemaDataProvider(): array
    {
        return [
            'id' => ['id', 'bigint'],
            'user_id' => ['user_id', 'bigint'],
            'day_id' => ['day_id', 'bigint'],
            'meal_of_days_id' => ['meal_of_days_id', 'bigint'],
            'recipe_id' => ['recipe_id', 'bigint'],
            'meal_id' => ['meal_id', 'bigint'],
            'created_at' => ['created_at', 'timestamp'],
            'updated_at' => ['updated_at', 'timestamp'],
        ];
    }

    public function test_days_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable($this->table),
            "A '{$this->table}' tábla nem létezik"
        );

        $this->say("✅ [PASSED] Table exists: {$this->table}");
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_days_table_has_columns(string $column, string $type): void
    {
        $this->assertTrue(
            Schema::hasColumn($this->table, $column),
            "A '{$column}' oszlop nem létezik a '{$this->table}' táblában"
        );

        $this->say("✅ [PASSED] Column exists: {$this->table}.{$column}");
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_days_table_column_types(string $column, string $type): void
    {
        $driver = DB::getDriverName();
        $actual = Schema::getColumnType($this->table, $column);

        // SQLite: a bigint/foreignId mezők gyakran "integer"-ként jönnek vissza
        if ($driver === 'sqlite' && $type === 'bigint') {
            $this->assertEquals(
                'integer',
                $actual,
                "A '{$column}' oszlop típusa nem megfelelő (sqlite normalizálás). Várt: integer, kapott: {$actual}"
            );

            $this->say("✅ [PASSED] Type OK (sqlite normalized): {$this->table}.{$column} expected={$type} actual={$actual}");
            return;
        }

        // created_at / updated_at SQLite-on sokszor "datetime"
        if ($driver === 'sqlite' && $type === 'timestamp') {
            $this->assertTrue(
                in_array($actual, ['datetime', 'timestamp'], true),
                "A '{$column}' oszlop típusa nem megfelelő (sqlite). Várt: datetime vagy timestamp, kapott: {$actual}"
            );

            $this->say("✅ [PASSED] Type OK (sqlite datetime/timestamp): {$this->table}.{$column} expected={$type} actual={$actual}");
            return;
        }

        $this->assertEquals(
            $type,
            $actual,
            "A '{$column}' oszlop típusa nem megfelelő. Várt: {$type}, kapott: {$actual}"
        );

        $this->say("✅ [PASSED] Type OK: {$this->table}.{$column} expected={$type} actual={$actual} (driver={$driver})");
    }

    public function test_days_table_has_unique_index_for_user_mealofday_recipe(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$this->table}')");

            $found = false;
            foreach ($indexes as $idx) {
                if ((int)($idx->unique ?? 0) !== 1) {
                    continue;
                }

                $indexName = $idx->name ?? null;
                if (!$indexName) {
                    continue;
                }

                $cols = DB::select("PRAGMA index_info('{$indexName}')");
                $colNames = array_map(fn ($c) => $c->name, $cols);

                $sorted = $colNames;
                sort($sorted);

                $expected = ['meal_of_days_id', 'recipe_id', 'user_id'];
                $expectedSorted = $expected;
                sort($expectedSorted);

                if ($sorted === $expectedSorted) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue(
                $found,
                "Nem található a várt UNIQUE index (user_id, meal_of_days_id, recipe_id) a '{$this->table}' táblában (sqlite)"
            );

            $this->say("✅ [PASSED] Unique index exists (sqlite): (user_id, meal_of_days_id, recipe_id)");
            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $dbName = DB::connection()->getDatabaseName();

            $rows = DB::select(
                "SELECT INDEX_NAME, COLUMN_NAME, NON_UNIQUE, SEQ_IN_INDEX
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
                [$dbName, $this->table]
            );

            $byIndex = [];
            foreach ($rows as $r) {
                $byIndex[$r->INDEX_NAME]['non_unique'] = (int)$r->NON_UNIQUE;
                $byIndex[$r->INDEX_NAME]['cols'][(int)$r->SEQ_IN_INDEX] = $r->COLUMN_NAME;
            }

            $expectedCols = ['user_id', 'meal_of_days_id', 'recipe_id'];

            $found = false;
            foreach ($byIndex as $data) {
                if (($data['non_unique'] ?? 1) !== 0) continue;
                ksort($data['cols']);
                if (array_values($data['cols']) === $expectedCols) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue(
                $found,
                "Nem található a várt UNIQUE index (user_id, meal_of_days_id, recipe_id) a '{$this->table}' táblában (mysql/mariadb)"
            );

            $this->say("✅ [PASSED] Unique index exists (mysql/mariadb): (" . implode(', ', $expectedCols) . ")");
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
                $def = $r->indexdef ?? '';
                if (
                    stripos($def, 'unique') !== false &&
                    stripos($def, '(user_id, meal_of_days_id, recipe_id)') !== false
                ) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue(
                $found,
                "Nem található a várt UNIQUE index (user_id, meal_of_days_id, recipe_id) a '{$this->table}' táblában (pgsql)"
            );

            $this->say("✅ [PASSED] Unique index exists (pgsql): (user_id, meal_of_days_id, recipe_id)");
            return;
        }

        $this->assertTrue(true);
        $this->say("✅ [PASSED] Unique index check skipped (unknown driver={$driver})");
    }

    public function test_days_table_has_expected_foreign_key_columns_present(): void
    {
        foreach (['user_id', 'day_id', 'meal_of_days_id', 'recipe_id', 'meal_id'] as $col) {
            $this->assertTrue(
                Schema::hasColumn($this->table, $col),
                "A '{$col}' FK oszlop nem létezik a '{$this->table}' táblában"
            );

            $this->say("✅ [PASSED] FK column exists: {$this->table}.{$col}");
        }
    }
}
