<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class WeekdayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function weekdays_table_exists()
    {
        $this->assertTrue(Schema::hasTable('weekdays'));
    }

    /** @test */
    public function weekdays_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasColumns('weekdays', [
            'id',
            'day',
            'created_at',
            'updated_at',
        ]));
    }

    /** @test */
    public function day_column_is_unique()
    {
        DB::table('weekdays')->insert([
            'day' => 'Monday',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('weekdays')->insert([
            'day' => 'Monday',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}