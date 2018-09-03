<?php

use App\Repositories\CheckpointCodeRepository;
use App\Repositories\CheckpointRepository;
use Illuminate\Database\Migrations\Migration;

class UnifyImpuestoAduanalCheckpointCodes extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var CheckpointCodeRepository $checkpointcodeRepository */
        $checkpointCodeRepository = app(CheckpointCodeRepository::class);

        /** @var CheckpointRepository $checkpointRepository */
        $checkpointRepository = app(CheckpointRepository::class);

        // Fetch all checkpoint codes to be unified/replaced
        $checkpointCodes = $checkpointCodeRepository
            ->search()
            ->where('description', 'like', 'Se determino un impuesto aduanal %')
            ->get();

        try {
            DB::beginTransaction();

            // Create new checkpoint code
            $unifiedCheckpointCode = $checkpointCodeRepository->create([
                'provider_id'       => 1,
                'classification_id' => 11,
                'key'               => 'MLA-23',
                'type'              => 'MLA',
                'code'              => '23',
                'description'       => 'Se determinó un impuesto aduanal',
                'category'          => 'Held by customs',
                'delivered'         => 0,
                'returned'          => 0,
                'canceled'          => 0,
                'returning'         => 0,
                'stalled'           => 1,
                'clockstop'         => 2,
                'virtual'           => 0
            ]);

            foreach ($checkpointCodes as $checkpointCode) {
                // Fetch all checkpoints that match checkpoint code to be replaced
                $checkpoints = $checkpointRepository->search(['checkpoint_code_id' => $checkpointCode->id])->get();

                foreach ($checkpoints as $checkpoint) {
                    $checkpointRepository->update($checkpoint, ['checkpoint_code_id' => $unifiedCheckpointCode->id]);
                }

                // Delete old checkpoint code
                $checkpointCodeRepository->delete($checkpointCode);
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