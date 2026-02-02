<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class DayTableTest extends TestCase
{
    protected $table = 'days';

    public static function expectedSchemaDataProvider(): array
    {
        return [
            ['id', 'bigint'],
            ['user_id', 'bigint'],
            ['day_id', 'bigint'],
            ['meal_of_days_id', 'bigint'],
            ['recipe_id', 'bigint'],
            ['meal_id', 'bigint'],
        ];
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_days_table_columns_exist(string $column, string $type): void
    {
        $this->assertTrue(Schema::hasColumn($this->table, $column));
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_days_table_column_types(string $column, string $type): void
    {
        $this->assertEquals($type, Schema::getColumnType($this->table, $column));
    }

    public function test_days_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }
}
