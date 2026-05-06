<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('days', 'plan_week')) {
            Schema::table('days', function (Blueprint $table) {
                $table->unsignedInteger('plan_week')->default(1)->after('meal_requirement_id');
            });
        }

        $oldUnique = 'days_user_weekday_mealreq_unique';
        $newUnique = 'days_user_planweek_weekday_mealreq_unique';

        if (!$this->indexExists('days', $newUnique)) {
            Schema::table('days', function (Blueprint $table) use ($newUnique) {
                $table->unique(
                    ['user_id', 'plan_week', 'weekday_id', 'meal_requirement_id'],
                    $newUnique
                );
            });
        }

        if ($this->indexExists('days', $oldUnique)) {
            Schema::table('days', function (Blueprint $table) use ($oldUnique) {
                $table->dropUnique($oldUnique);
            });
        }
    }

    public function down(): void
    {
        $oldUnique = 'days_user_weekday_mealreq_unique';
        $newUnique = 'days_user_planweek_weekday_mealreq_unique';

        if ($this->indexExists('days', $newUnique)) {
            Schema::table('days', function (Blueprint $table) use ($newUnique) {
                $table->dropUnique($newUnique);
            });
        }

        if (!$this->indexExists('days', $oldUnique)) {
            Schema::table('days', function (Blueprint $table) use ($oldUnique) {
                $table->unique(
                    ['user_id', 'weekday_id', 'meal_requirement_id'],
                    $oldUnique
                );
            });
        }

        if (Schema::hasColumn('days', 'plan_week')) {
            Schema::table('days', function (Blueprint $table) {
                $table->dropColumn('plan_week');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            $rows = DB::select("PRAGMA index_list('{$table}')");

            foreach ($rows as $row) {
                if (($row->name ?? null) === $indexName) {
                    return true;
                }
            }

            return false;
        }

        $database = DB::getDatabaseName();
        $rows = DB::select(
            'SELECT INDEX_NAME FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$database, $table, $indexName]
        );

        return !empty($rows);
    }
};
