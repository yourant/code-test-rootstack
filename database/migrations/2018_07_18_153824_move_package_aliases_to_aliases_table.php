<?php

use Illuminate\Database\Migrations\Migration;

class MovePackageAliasesToAliasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add aliases from packages table
        DB::table('packages')
            ->select('packages.id as package_id')
            ->addSelect(DB::raw("case 
			when packages.alias like 'MLMX%' then 35
			when packages.alias like 'RB%' then 1
			when packages.alias like 'EE%' then 1
			when packages.alias like 'RZ%' then 1
			when packages.alias like 'ML%' then 2
			when packages.tracking_number like '533%' and packages.alias like '6%'then 23
			when packages.alias like '533%' then 23
			when packages.tracking_number like '1%' and packages.alias like '6%'then 23
			when packages.tracking_number like '1%' and packages.alias like '9%'then 14
			when packages.tracking_number like '0%' and packages.alias like '9%'then 14
			when packages.tracking_number like '0%' and packages.alias like '6%'then 14
			when packages.alias like 'MLBR%' then 27
			when packages.alias like 'MLCO%' then 29
			when packages.alias like 'MLCO%' and packages.alias like '6%'then 29
			when packages.tracking_number like 'UN%' and packages.alias is not null then 26
			when packages.tracking_number like 'MLMX%' and (packages.alias like '9%' or packages.alias like '1%') then 35
			when packages.tracking_number like 'MLCO%' and packages.alias like '6%'then 29
			when packages.alias like 'BLUE%' then 23
		end as provider_id"))
            ->addSelect(['packages.alias as code', 'packages.created_at', 'packages.updated_at'])
            ->where(function ($q) {
                return $q->whereNotNull('packages.alias')->where('packages.alias', '<>', '');
            })
            ->whereNotIn('packages.id', function ($q) {
                $q->select('aliases.package_id')->from('aliases')->whereColumn('packages.id', 'aliases.id');
            })->orderBy('packages.id')
            ->chunk(10000, function ($rows) {
                // Fetch
                $data = $rows->transform(function ($row) {
                    return (array)$row;
                })->toArray();

                // Insert
                DB::table('aliases')->insert($data);
            });
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
