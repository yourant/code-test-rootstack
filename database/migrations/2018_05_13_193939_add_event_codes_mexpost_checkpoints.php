<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddEventCodesMexpostCheckpoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::beginTransaction();

            $checkpoint_code_events = [
                ['checkpoint_code_id' => 10136, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10126, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10111, 'event_code_id'  => 27],
                ['checkpoint_code_id' => 10043, 'event_code_id'  => 7],
                ['checkpoint_code_id' => 10116, 'event_code_id'  => 9],
                ['checkpoint_code_id' => 10042, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10006, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10090, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10135, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10095, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10098, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10117, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10094, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10049, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10034, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10031, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10007, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10076, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10077, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10097, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10087, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10096, 'event_code_id'  => 12],
                ['checkpoint_code_id' => 10092, 'event_code_id'  => 12],
                ['checkpoint_code_id' => 10110, 'event_code_id'  => 12],
                ['checkpoint_code_id' => 10008, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10078, 'event_code_id'  => 12],
                ['checkpoint_code_id' => 10050, 'event_code_id'  => 12],
                ['checkpoint_code_id' => 10081, 'event_code_id'  => 12],
                ['checkpoint_code_id' => 10109, 'event_code_id'  => 23],
                ['checkpoint_code_id' => 10108, 'event_code_id'  => 23],
                ['checkpoint_code_id' => 10133, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10086, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10084, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10139, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10085, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10134, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10103, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10137, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10123, 'event_code_id'  => 27],
                ['checkpoint_code_id' => 10082, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10029, 'event_code_id'  => 27],
                ['checkpoint_code_id' => 10119, 'event_code_id'  => 25],
                ['checkpoint_code_id' => 10065, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10073, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10088, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10026, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 10024, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 10132, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 10080, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 10010, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 10033, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 10032, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 10089, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 10036, 'event_code_id'  => 13],
                ['checkpoint_code_id' => 10028, 'event_code_id'  => 10],
                ['checkpoint_code_id' => 10079, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10009, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 10021, 'event_code_id'  => 14],
                ['checkpoint_code_id' => 10023, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 10056, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 10022, 'event_code_id'  => 15],
                ['checkpoint_code_id' => 10047, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 10018, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 10061, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 10114, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 10020, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 10025, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 10048, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 10046, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10053, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 10052, 'event_code_id'  => 16],
                ['checkpoint_code_id' => 10062, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10019, 'event_code_id'  => 18],
                ['checkpoint_code_id' => 10055, 'event_code_id'  => 18],
                ['checkpoint_code_id' => 10030, 'event_code_id'  => 18],
                ['checkpoint_code_id' => 10011, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10012, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10013, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10014, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10015, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10016, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10017, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10054, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10067, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10066, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10058, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10068, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10100, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10057, 'event_code_id'  => 19],
                ['checkpoint_code_id' => 10071, 'event_code_id'  => 20],
                ['checkpoint_code_id' => 10091, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10075, 'event_code_id'  => 25],
                ['checkpoint_code_id' => 10131, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10102, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10121, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10115, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10072, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10127, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10093, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10128, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10041, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10124, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10037, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10107, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10125, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10083, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10045, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10044, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10051, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10120, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10112, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10122, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10099, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10129, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10118, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10104, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10105, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10038, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10060, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10059, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10074, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10035, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10064, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10069, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10070, 'event_code_id'  => 27],
                ['checkpoint_code_id' => 10027, 'event_code_id'  => 23],
                ['checkpoint_code_id' => 10113, 'event_code_id'  => 27],
                ['checkpoint_code_id' => 10040, 'event_code_id'  => 21],
                ['checkpoint_code_id' => 10130, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10101, 'event_code_id'  => 26],
                ['checkpoint_code_id' => 10106, 'event_code_id'  => 27],
                ['checkpoint_code_id' => 10039, 'event_code_id'  => 17],
                ['checkpoint_code_id' => 10138, 'event_code_id'  => 11],
                ['checkpoint_code_id' => 10140, 'event_code_id'  => 16],
            ];

            foreach ($checkpoint_code_events as $checkpoint_code_event) {
                DB::table('checkpoint_code_event_code')->insert([
                    'checkpoint_code_id' => $checkpoint_code_event['checkpoint_code_id'],
                    'event_code_id'      => $checkpoint_code_event['event_code_id']
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            logger($e->getTraceAsString());
            throw new Exception($e->getMessage());
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
