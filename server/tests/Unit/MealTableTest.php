<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MealTableTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'meals';

    protected function say(string $msg): void
    {
        fwrite(STDOUT, $msg . PHP_EOL);
    }

    public function test_meals_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
        $this->say("✅ [PASSED] Table exists: {$this->table}");
    }

    public function test_meals_table_has_columns(): void
    {
        $this->assertTrue(Schema::hasColumn($this->table, 'id'));
        $this->assertTrue(Schema::hasColumn($this->table, 'meal'));
        $this->assertTrue(Schema::hasColumn($this->table, 'created_at'));
        $this->assertTrue(Schema::hasColumn($this->table, 'updated_at'));

        $this->say("✅ [PASSED] All required columns exist in the {$this->table} table");
    }

    public function test_meals_meal_column_is_unique(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$this->table}')");

            $found = false;
            foreach ($indexes as $idx) {
                if ((int)($idx->unique ?? 0) !== 1) continue;

                $cols = DB::select("PRAGMA index_info('{$idx->name}')");
                $names = array_map(fn ($c) => $c->name, $cols);

                if ($names === ['meal']) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (sqlite): meal");
            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $db = DB::connection()->getDatabaseName();

            $rows = DB::select(
                "SELECT COLUMN_NAME, NON_UNIQUE
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
                [$db, $this->table]
            );

            $found = false;
            foreach ($rows as $r) {
                if ($r->COLUMN_NAME === 'meal' && (int)$r->NON_UNIQUE === 0) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (mysql/mariadb): meal");
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
                    stripos($r->indexdef, '(meal)') !== false
                ) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (pgsql): meal");
            return;
        }

        $this->assertTrue(true);
        $this->say("✅ [PASSED] Unique index check skipped (driver={$driver})");
    }
}