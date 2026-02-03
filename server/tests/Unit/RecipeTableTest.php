<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RecipeTableTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'recipes';

    protected function say(string $msg): void
    {
        fwrite(STDOUT, $msg . PHP_EOL);
    }

    public function test_recipes_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
        $this->say("✅ [PASSED] Table exists: {$this->table}");
    }

    public function test_recipes_table_has_columns(): void
    {
        $this->assertTrue(Schema::hasColumn($this->table, 'id'));
        $this->assertTrue(Schema::hasColumn($this->table, 'name'));
        $this->assertTrue(Schema::hasColumn($this->table, 'description'));
        $this->assertTrue(Schema::hasColumn($this->table, 'picture'));
        $this->assertTrue(Schema::hasColumn($this->table, 'person'));
        $this->assertTrue(Schema::hasColumn($this->table, 'meal_id'));
        $this->assertTrue(Schema::hasColumn($this->table, 'created_at'));
        $this->assertTrue(Schema::hasColumn($this->table, 'updated_at'));

        $this->say("✅ [PASSED] All required columns exist in the {$this->table} table");
    }

    public function test_recipes_name_column_is_unique(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$this->table}')");

            $found = false;
            foreach ($indexes as $idx) {
                if ((int)($idx->unique ?? 0) !== 1) continue;

                $cols = DB::select("PRAGMA index_info('{$idx->name}')");
                $names = array_map(fn ($c) => $c->name, $cols);

                if ($names === ['name']) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (sqlite): name");
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
                if ($r->COLUMN_NAME === 'name' && (int)$r->NON_UNIQUE === 0) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (mysql/mariadb): name");
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
                    stripos($r->indexdef, '(name)') !== false
                ) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (pgsql): name");
            return;
        }

        $this->assertTrue(true);
        $this->say("✅ [PASSED] Unique index check skipped (driver={$driver})");
    }

    public function test_recipes_picture_column_is_unique(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$this->table}')");

            $found = false;
            foreach ($indexes as $idx) {
                if ((int)($idx->unique ?? 0) !== 1) continue;

                $cols = DB::select("PRAGMA index_info('{$idx->name}')");
                $names = array_map(fn ($c) => $c->name, $cols);

                if ($names === ['picture']) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (sqlite): picture");
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
                if ($r->COLUMN_NAME === 'picture' && (int)$r->NON_UNIQUE === 0) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (mysql/mariadb): picture");
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
                    stripos($r->indexdef, '(picture)') !== false
                ) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (pgsql): picture");
            return;
        }

        $this->assertTrue(true);
        $this->say("✅ [PASSED] Unique index check skipped (driver={$driver})");
    }

 public function test_recipes_meal_id_foreign_key(): void
{
    $this->assertTrue(
        Schema::hasColumn($this->table, 'meal_id'),
        "A meal_id oszlop nem létezik a {$this->table} táblában"
    );

    $driver = DB::getDriverName();

    // -------------------------
    // SQLITE
    // -------------------------
    if ($driver === 'sqlite') {
        $foreignKeys = DB::select("PRAGMA foreign_key_list({$this->table})");

        $foreignKeyFound = false;
        foreach ($foreignKeys as $fk) {
            if (
                $fk->from === 'meal_id' &&
                $fk->table === 'meals' &&
                $fk->to === 'id'
            ) {
                $foreignKeyFound = true;
                break;
            }
        }

        $this->assertTrue(
            $foreignKeyFound,
            "Nem található FK: meal_id → meals(id) (sqlite)"
        );

        $this->say("✅ [PASSED] Foreign key exists (sqlite): meal_id → meals(id)");
        return;
    }

    // -------------------------
    // MYSQL / MARIADB
    // -------------------------
    if (in_array($driver, ['mysql', 'mariadb'], true)) {
        $rows = DB::select("SHOW CREATE TABLE {$this->table}");

        $foreignKeyFound = false;
        foreach ($rows as $row) {
            $sql = $row->{'Create Table'} ?? '';

            if (
                stripos($sql, 'foreign key') !== false &&
                stripos($sql, 'meal_id') !== false &&
                stripos($sql, 'references meals') !== false
            ) {
                $foreignKeyFound = true;
                break;
            }
        }

        $this->assertTrue(
            $foreignKeyFound,
            "Nem található FK: meal_id → meals(id) (mysql/mariadb)"
        );

        $this->say("✅ [PASSED] Foreign key exists (mysql/mariadb): meal_id → meals(id)");
        return;
    }

    // -------------------------
    // POSTGRESQL
    // -------------------------
    if ($driver === 'pgsql') {
        $rows = DB::select(
            "SELECT
                conname,
                pg_get_constraintdef(c.oid) AS definition
             FROM pg_constraint c
             JOIN pg_class t ON t.oid = c.conrelid
             WHERE c.contype = 'f'
               AND t.relname = ?",
            [$this->table]
        );

        $foreignKeyFound = false;
        foreach ($rows as $row) {
            if (
                stripos($row->definition, '(meal_id)') !== false &&
                stripos($row->definition, 'references meals(id)') !== false
            ) {
                $foreignKeyFound = true;
                break;
            }
        }

        $this->assertTrue(
            $foreignKeyFound,
            "Nem található FK: meal_id → meals(id) (pgsql)"
        );

        $this->say("✅ [PASSED] Foreign key exists (pgsql): meal_id → meals(id)");
        return;
    }

    // -------------------------
    // FALLBACK
    // -------------------------
    $this->assertTrue(true);
    $this->say("✅ [PASSED] Foreign key check skipped (unknown driver={$driver})");
}


}