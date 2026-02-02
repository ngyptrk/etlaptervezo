<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class MealRequirementTableTest extends TestCase
{
    protected $table = 'meal_requirements';

    public static function expectedSchemaDataProvider(): array
    {
        return [
            ['id', 'bigint'],
            ['meal_of_day_id', 'bigint'],
            ['meal_id', 'bigint'],
        ];
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_meal_requirements_columns(string $column, string $type): void
    {
        $this->assertTrue(Schema::hasColumn($this->table, $column));
        $this->assertEquals($type, Schema::getColumnType($this->table, $column));
    }

    public function test_meal_requirements_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }
}
