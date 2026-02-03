<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IngredientTableTest extends TestCase
{
    protected $table = 'ingredients';

    public static function expectedSchemaDataProvider(): array
    {
        return [
            'id' => ['id', 'bigint'],
            'recipe_id' => ['recipe_id', 'bigint'],
            'raw_ingredient_id' => ['raw_ingredient_id', 'bigint'],
            'amount' => ['amount', 'integer'],
            'unit_id' => ['unit_id', 'bigint'],
        ];
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_ingredients_table_has_columns(string $column, string $type): void
    {
        $this->assertTrue(
            Schema::hasColumn($this->table, $column),
            "A '{$column}' oszlop nem létezik"
        );
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_ingredients_table_column_types(string $column, string $type): void
    {
        $driver = DB::getDriverName();

        // SQLite-n a bigint/foreignId jellegű mezők gyakran "integer"-ként jelennek meg
        // Schema::getColumnType() szerint.
        if ($driver === 'sqlite') {
            if (in_array($type, ['bigint'], true)) {
                $this->assertEquals(
                    'integer',
                    Schema::getColumnType($this->table, $column),
                    "A '{$column}' oszlop típusa nem megfelelő (sqlite normalizálás)"
                );
                return;
            }
        }

        $this->assertEquals(
            $type,
            Schema::getColumnType($this->table, $column),
            "A '{$column}' oszlop típusa nem megfelelő"
        );
    }

    public function test_ingredients_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable($this->table),
            "Az ingredients tábla nem létezik"
        );
    }
}
