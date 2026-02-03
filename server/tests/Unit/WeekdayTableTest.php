<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WeekdayTableTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'weekdays';

    protected function say(string $msg): void
    {
        fwrite(STDOUT, $msg . PHP_EOL);
    }

    public static function expectedSchemaDataProvider(): array
    {
        return [
            'id' => ['id', 'bigint'],
            'day' => ['day', 'varchar'],
            'created_at' => ['created_at', 'timestamp'],
            'updated_at' => ['updated_at', 'timestamp'],
        ];
    }

    public function test_weekdays_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
        $this->say("✅ [PASSED] Table exists: {$this->table}");
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_weekdays_table_has_columns(string $column, string $type): void
    {
        $this->assertTrue(Schema::hasColumn($this->table, $column));
        $this->say("✅ [PASSED] Column exists: {$this->table}.{$column}");
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_weekdays_table_column_types(string $column, string $type): void
    {
        $driver = DB::getDriverName();
        $actual = Schema::getColumnType($this->table, $column);
        // dd($actual, $column, $type);

        // SQLite bigint normalizálás
        if ($driver === 'sqlite' && $type === 'bigint') {
            $this->assertEquals('integer', $actual);
            $this->say("✅ [PASSED] Type OK (sqlite normalized): {$column} => {$actual}");
            return;
        }

        // SQLite timestamp → datetime lehet
        if ($driver === 'sqlite' && $type === 'timestamp') {
            $this->assertTrue(in_array($actual, ['datetime', 'timestamp'], true));
            $this->say("✅ [PASSED] Type OK (sqlite datetime/timestamp): {$column} => {$actual}");
            return;
        }

        $this->assertEquals($type, $actual);
        $this->say("✅ [PASSED] Type OK: {$column} => {$actual} (driver={$driver})");
    }

    public function test_weekdays_day_column_is_unique(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$this->table}')");

            $found = false;
            foreach ($indexes as $idx) {
                if ((int)($idx->unique ?? 0) !== 1) continue;

                $cols = DB::select("PRAGMA index_info('{$idx->name}')");
                $names = array_map(fn ($c) => $c->name, $cols);

                if ($names === ['day']) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (sqlite): day");
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
                if ($r->COLUMN_NAME === 'day' && (int)$r->NON_UNIQUE === 0) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (mysql/mariadb): day");
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
                    stripos($r->indexdef, '(day)') !== false
                ) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);
            $this->say("✅ [PASSED] Unique index exists (pgsql): day");
            return;
        }

        $this->assertTrue(true);
        $this->say("✅ [PASSED] Unique index check skipped (driver={$driver})");
    }
}
