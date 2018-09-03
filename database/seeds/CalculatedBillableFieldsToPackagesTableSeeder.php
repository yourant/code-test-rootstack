<?php

use Illuminate\Database\Seeder;

use Illuminate\Foundation\Bus\DispatchesJobs;

use Illuminate\Support\Facades\DB;

use App\Jobs\Packages\UpdatePackageBillableFieldsJob;

class CalculatedBillableFieldsToPackagesTableSeeder extends Seeder
{
    use DispatchesJobs;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        set_time_limit(0);

        $time_start = microtime(true);

        $chunk_count = 0;

        DB::table('packages')
            ->orderBy('id')
            ->chunk(1000, function ($packages) use (&$chunk_count) {
                ++$chunk_count;
                $job = (new UpdatePackageBillableFieldsJob($packages->pluck('id')->toArray()))->onQueue('tracking-packages')->delay(5);
                $this->dispatch($job);
                echo "Dispatched " . $chunk_count * 1000 . " jobs.\n";
            });

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "Update completed in {$time} seconds\n";
    }
}
