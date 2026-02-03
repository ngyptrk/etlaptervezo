<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class MealRequirementTableTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'meal_requirements';

    protected function say(string $message): void
    {
        fwrite(STDOUT, $message . PHP_EOL);
    }

    public static function expectedSchemaDataProvider(): array
    {
        return [
            'id' => ['id', 'bigint'],
            'meal_of_day_id' => ['meal_of_day_id', 'bigint'],
            'meal_id' => ['meal_id', 'bigint'],
            'created_at' => ['created_at', 'timestamp'],
            'updated_at' => ['updated_at', 'timestamp'],
        ];
    }

    public function test_meal_requirements_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable($this->table),
            "A '{$this->table}' tábla nem létezik"
        );

        $this->say("✅ [PASSED] Table exists: {$this->table}");
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_meal_requirements_table_has_columns(string $column, string $type): void
    {
        $this->assertTrue(
            Schema::hasColumn($this->table, $column),
            "A '{$column}' oszlop nem létezik az '{$this->table}' táblában"
        );

        $this->say("✅ [PASSED] Column exists: {$this->table}.{$column}");
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_meal_requirements_table_column_types(string $column, string $type): void
    {
        $driver = DB::getDriverName();
        $actual = Schema::getColumnType($this->table, $column);

        // SQLite: bigint mezők gyakran integer-ként jönnek vissza
        if ($driver === 'sqlite' && $type === 'bigint') {
            $this->assertEquals(
                'integer',
                $actual,
                "A '{$column}' oszlop típusa nem megfelelő (sqlite). Várt: integer, kapott: {$actual}"
            );

            $this->say("✅ [PASSED] Type OK (sqlite normalized): {$this->table}.{$column} {$actual}");
            return;
        }

        // SQLite timestamp → datetime lehet
        if ($driver === 'sqlite' && $type === 'timestamp') {
            $this->assertTrue(
                in_array($actual, ['datetime', 'timestamp'], true),
                "A '{$column}' oszlop típusa nem megfelelő (sqlite)"
            );

            $this->say("✅ [PASSED] Type OK (sqlite datetime/timestamp): {$this->table}.{$column} {$actual}");
            return;
        }

        $this->assertEquals(
            $type,
            $actual,
            "A '{$column}' oszlop típusa nem megfelelő"
        );

        $this->say("✅ [PASSED] Type OK: {$this->table}.{$column} {$actual} (driver={$driver})");
    }

    public function test_meal_requirements_unique_index_exists(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$this->table}')");

            $found = false;
            foreach ($indexes as $idx) {
                if ((int)($idx->unique ?? 0) !== 1) continue;

                $cols = DB::select("PRAGMA index_info('{$idx->name}')");
                $names = array_map(fn ($c) => $c->name, $cols);

                $sorted = $names;
                sort($sorted);

                $expected = ['meal_of_day_id', 'meal_id'];
                $expectedSorted = $expected;
                sort($expectedSorted);

                if ($sorted === $expectedSorted) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (sqlite): (meal_of_day_id, meal_id)");
            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $db = DB::connection()->getDatabaseName();

            $rows = DB::select(
                "SELECT INDEX_NAME, COLUMN_NAME, NON_UNIQUE, SEQ_IN_INDEX
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
                [$db, $this->table]
            );

            $byIndex = [];
            foreach ($rows as $r) {
                $byIndex[$r->INDEX_NAME]['non_unique'] = (int)$r->NON_UNIQUE;
                $byIndex[$r->INDEX_NAME]['cols'][(int)$r->SEQ_IN_INDEX] = $r->COLUMN_NAME;
            }

            $expected = ['meal_of_day_id', 'meal_id'];

            $found = false;
            foreach ($byIndex as $data) {
                if (($data['non_unique'] ?? 1) !== 0) continue;
                ksort($data['cols']);
                if (array_values($data['cols']) === $expected) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (mysql/mariadb): (" . implode(', ', $expected) . ")");
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
                    stripos($def, '(meal_of_day_id, meal_id)') !== false
                ) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (pgsql): (meal_of_day_id, meal_id)");
            return;
        }

        $this->assertTrue(true);
        $this->say("✅ [PASSED] Unique index check skipped (driver={$driver})");
    }

    public function test_meal_requirements_fk_columns_exist(): void
    {
        foreach (['meal_of_day_id', 'meal_id'] as $col) {
            $this->assertTrue(Schema::hasColumn($this->table, $col));
            $this->say("✅ [PASSED] FK column exists: {$this->table}.{$col}");
        }
    }
}
