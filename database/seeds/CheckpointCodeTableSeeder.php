<?php

use App\Repositories\CheckpointCodeRepository;
use Illuminate\Database\Seeder;

class CheckpointCodeTableSeeder extends Seeder {

    public function __construct(CheckpointCodeRepository $checkpoint_code)
    {
        $this->checkpoint_code = $checkpoint_code;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->checkpoint_code->create(['type' => 'IPS', 'code' => '30', 'description' => 'Delivered to the Country']);
        $this->checkpoint_code->create(['type' => 'IPS', 'code' => '31', 'description' => 'Piece sent to Customs']);
        $this->checkpoint_code->create(['type' => 'IPS', 'code' => '38', 'description' => 'Piece exit Customs']);
        $this->checkpoint_code->create(['type' => 'RO', 'code' => '0', 'description' => 'Send to Deliver']);
        $this->checkpoint_code->create(['type' => 'CAR', 'code' => '1', 'description' => 'On route with mailman']);
        $this->checkpoint_code->create(['type' => 'BDE', 'code' => '1', 'description' => 'Returned because wrong or incomplete address', 'final' => true]);
        $this->checkpoint_code->create(['type' => 'BDE', 'code' => '2', 'description' => 'Returned - Change of address', 'final' => true]);
        $this->checkpoint_code->create(['type' => 'BDE', 'code' => '3', 'description' => 'Returned - Unknown consignee', 'final' => true]);
        $this->checkpoint_code->create(['type' => 'BDE', 'code' => '4', 'description' => 'Returned - Not Claimed by consignee', 'final' => true]);
        $this->checkpoint_code->create(['type' => 'BDE', 'code' => '5', 'description' => 'Returned - Consignee is dead', 'final' => true]);
        $this->checkpoint_code->create(['type' => 'BDE', 'code' => '6', 'description' => 'Returned - Not enough or missing postage', 'final' => true]);
        $this->checkpoint_code->create(['type' => 'BDE', 'code' => '7', 'description' => 'Returned - Address is empty lot', 'final' => true]);
        $this->checkpoint_code->create(['type' => 'BDE', 'code' => '8', 'description' => 'Returned - Others', 'final' => true]);
        $this->checkpoint_code->create(['type' => 'BDE', 'code' => '9', 'description' => 'Delivered', 'final' => true]);
        $this->checkpoint_code->create(['type' => 'BDE', 'code' => '10', 'description' => 'On window at post Office for pick up']);
        $this->checkpoint_code->create(['type' => 'MLA', 'code' => '1', 'description' => 'In transit to airport']);
        $this->checkpoint_code->create(['type' => 'MLA', 'code' => '2', 'description' => 'Arrival at airport']);
    }
}