<?php

use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Output\ConsoleOutput;

class RebuildIndexes extends Migration
{
    protected $consoleOutput;

    public function __construct()
    {
        $this->consoleOutput = new ConsoleOutput();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        $database_name = DB::connection()->getDatabaseName();
        $schema_name = env('DB_SCHEMA');
        $indexes = DB::select(DB::raw("SELECT
  ns.nspname               AS schema_name,
  idx.indrelid :: REGCLASS AS table_name,
  i.relname                AS index_name,
  idx.indisunique          AS is_unique,
  idx.indisprimary         AS is_primary,
  idx.indkey,
       ARRAY(
           SELECT pg_get_indexdef(idx.indexrelid, k + 1, TRUE)
           FROM
             generate_subscripts(idx.indkey, 1) AS k
           ORDER BY k
       ) AS index_keys
FROM pg_index AS idx
  JOIN pg_class AS i
    ON i.oid = idx.indexrelid
  JOIN pg_am AS am
    ON i.relam = am.oid
  JOIN pg_namespace AS NS ON i.relnamespace = NS.OID
  JOIN pg_user AS U ON i.relowner = U.usesysid
 where ns.nspname = '{$schema_name}'"));

        try {
            DB::beginTransaction();
            foreach ($indexes as $index) {
                $primary_as_text = $index->is_primary ? 'X' : '-';
                $unique_as_text = $index->is_unique ? 'X' : '-';
                $this->consoleOutput->writeln("Original: {$index->index_name}");
                $this->consoleOutput->writeln("Primary: {$primary_as_text} | Unique: {$unique_as_text} | Table: {$index->table_name} | Fields: {$index->index_keys}");
                $new_index_name = null;
                if ($index->index_name == 'idx_33148_metrics' or $index->index_name == 'packages_metrics_index') {
                    $new_index_name = 'packages_metrics_index';
                } elseif ($index->index_name == 'idx_33148_checkpoint_dates' or $index->index_name == 'packages_checkpoint_dates_index') {
                    $new_index_name = 'packages_checkpoint_dates_index';
                } elseif ($index->index_name == 'idx_33026_batch_id' or $index->index_name == 'operation_metrics_batch_index') {
                    $new_index_name = 'operation_metrics_batch_index';
                } elseif ($index->index_name == 'idx_33115_batch_id' or $index->index_name == 'operation_state_milestone_metrics_batch_index') {
                    $new_index_name = 'operation_state_milestone_metrics_batch_index';
                } elseif ($index->index_name == 'idx_32875_checkpoint_code_classification_checkpoint_code_id_for' or $index->index_name == 'checkpoint_code_classification_checkpoint_code_id_foreign') {
                    $new_index_name = 'checkpoint_code_classification_checkpoint_code_id_foreign';
                } elseif ($index->index_name == 'idx_32875_checkpoint_code_classification_classification_id_fore' or $index->index_name == 'checkpoint_code_classification_classification_id_foreign') {
                    $new_index_name = 'checkpoint_code_classification_classification_id_foreign';
                } elseif ($index->index_name == 'idx_33039_operation_milestone_checkpoint_code_checkpoint_code_i' or $index->index_name == 'operation_milestone_checkpoint_code_checkpoint_code_id_foreign') {
                    $new_index_name = 'operation_milestone_checkpoint_code_checkpoint_code_id_foreign';
                } elseif ($index->index_name == 'idx_33039_operation_milestone_checkpoint_code_milestone_id_fore' or $index->index_name == 'operation_milestone_checkpoint_code_milestone_id_foreign') {
                    $new_index_name = 'operation_milestone_checkpoint_code_milestone_id_foreign';
                } elseif ($index->index_name == 'idx_33121_operation_state_performances_performance_formula_id_f' or $index->index_name == 'operation_state_performances_performance_formula_id_foreign') {
                    $new_index_name = 'operation_state_performances_performance_formula_id_foreign';
                } elseif ($index->index_name == 'idx_33063_operation_performances_performance_formula_id_foreign' or $index->index_name == 'operation_performances_performance_formula_id_foreign') {
                    $new_index_name = 'operation_performances_performance_formula_id_foreign';
                } elseif ($index->index_name == 'idx_33115_operation_state_milestone_metrics_state_milestone_id_' or $index->index_name == 'operation_state_milestone_metrics_state_milestone_id_foreign') {
                    $new_index_name = 'operation_state_milestone_metrics_state_milestone_id_foreign';
                } elseif ($index->index_name == 'idx_33142_operation_undelivered_state_metrics_state_milestone_i' or $index->index_name == 'operation_undelivered_state_metrics_state_milestone_id_foreign') {
                    $new_index_name = 'operation_undelivered_state_metrics_state_milestone_id_foreign';
                } elseif ($index->index_name == 'idx_33142_operation_undelivered_state_metrics_undelivered_id_fo' or $index->index_name == 'operation_undelivered_state_metrics_undelivered_id_foreign') {
                    $new_index_name = 'operation_undelivered_state_metrics_undelivered_id_foreign';
                } elseif ($index->is_primary) {
                    // Check if starts with IDX..
                    if (preg_match('/^idx_/', $index->index_name)) {
                        $new_index_name = "{$index->table_name}_pkey";
                    }
                } elseif ($index->is_unique) {
                    $keys = preg_replace('/{|}|\\\|\"/', "", $index->index_keys);
                    $parts = collect(explode(',', $keys));
                    $new_index_name = "{$index->table_name}_{$parts->implode('_')}_unique";
                } else {
                    $keys = preg_replace('/{|}|\\\|\"/', "", $index->index_keys);
                    $parts = collect(explode(',', $keys));
                    $new_index_name = "{$index->table_name}_{$parts->implode('_')}";
                    if (preg_match('/_index$/', $index->index_name)) {
                        $new_index_name .= "_index";
                    } elseif (preg_match('/_foreign/', $index->index_name)) {
                        $new_index_name .= "_foreign";
                    } else {
                        $new_index_name .= "_index";
                    }
                }

                if (strlen($new_index_name) > 63) {
                    $this->consoleOutput->writeln("Will be truncated: {$new_index_name}");
                }

                if (strlen($index->index_name) >= 63) {
                    $this->consoleOutput->writeln("Warning: {$index->index_name}");
                }

                if ($new_index_name && $new_index_name != $index->index_name) {
                    DB::statement('ALTER INDEX IF EXISTS ' . $index->index_name . ' RENAME TO ' . $new_index_name);
                    $this->consoleOutput->writeln("Renamed: {$new_index_name}");
                } else {
                    $this->consoleOutput->writeln("Skipped: {$index->index_name}");
                }

                $this->consoleOutput->writeln('');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
