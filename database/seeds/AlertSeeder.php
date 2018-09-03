<?php

use App\Repositories\AlertRepository;
use App\Repositories\CheckpointCodeRepository;
use App\Repositories\ClassificationRepository;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{

    public function __construct(AlertRepository $alert, ClassificationRepository $classification, CheckpointCodeRepository $checkpoint_code)
    {
        $this->alert = $alert;
        $this->classification = $classification;
        $this->checkpoint_code = $checkpoint_code;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();

            $this->createClassifications();
            $this->createBenitoJuarezAlert();
            $this->createPantacoAlert();
            $this->createDistributionAlert();
            $this->createCongestedPostalOfficesAlert();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            echo $e->getTraceAsString() . PHP_EOL;

            throw $e;
        }
    }

    private function createClassifications()
    {
        // Arrival at the airport (MLA-2)
        $c = $this->classification->create(['key' => 'arrival_at_the_airport', 'name' => 'Arrival at the Airport']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('MLA', '2'));

        // In transit to the airport (MLA-1)
        $c = $this->classification->create(['key' => 'in_transit_to_the_airport', 'name' => 'In transit to the Airport']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('MLA', '1'));

        // Customs (IPS-31 / IPS-38 / MLA-5 / 30-0 / 3-0)
        $c = $this->classification->create(['key' => 'customs', 'name' => 'Customs']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('IPS', '31'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('IPS', '38'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('MLA', '5'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('30', '0'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('3 ', '0'));

        // Delivered to the Country (IPS-30)
        $c = $this->classification->create(['key' => 'delivered_to_the_country', 'name' => 'Delivered to the Country']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('IPS', '30'));

        // Receive in Office of Exchange (IPS-3)
        $c = $this->classification->create(['key' => 'receive_in_office_of_exchange', 'name' => 'Receive in Office of Exchange']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('IPS', '3'));

        // Send to deliver (RO-0 / RO-1 / BDE-13)
        $c = $this->classification->create(['key' => 'send_to_deliver', 'name' => 'Send to deliver']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('RO', '0'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('RO', '1'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '13'));

        // On route with mailman (CAR-1 / OEC-1 / BDE-16 / CAR-3 / CAR-2)
        $c = $this->classification->create(['key' => 'on_route_with_mailman', 'name' => 'On route with mailman']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('CAR', '1'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('OEC', '1'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '16'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('CAR', '3'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('CAR', '2'));

        // Others (IE-9 / AIE-6)
        $c = $this->classification->create(['key' => 'others', 'name' => 'Others']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('IE', '9'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('AIE', '6'));

        // Left notice (BDE-11 / BDE-15 / BDE-12 / MLA-3)
        $c = $this->classification->create(['key' => 'left_notice', 'name' => 'Left notice']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '11'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '12'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '15'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('MLA', '3'));

        // On window at Post Office for pick up (BDE-10 / LDI-1)
        $c = $this->classification->create(['key' => 'on_window_at_post_office_for_pick_up', 'name' => 'On window at Post Office for pick up']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '10'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('LDI', '1'));

        // Delivered (BDE-9)
        $c = $this->classification->create(['key' => 'delivered', 'name' => 'Delivered']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '9'));

        // Returned (BDE-1 / BDE-2 / BDE-3 / BDE-4 / BDE-5 / BDE-6 / BDE-7 / BDE-8)
        $c = $this->classification->create(['key' => 'returned', 'name' => 'Returned']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '1'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '2'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '3'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '4'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '5'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '6'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '7'));
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('BDE', '8'));

        // Items rejected by customs
        $c = $this->classification->create(['key' => 'items_rejected_by_customs', 'name' => 'Items rejected by customs']);
        $this->classification->addCheckpointCode($c, $this->checkpoint_code->findByTypeAndCode('MLA', '4'));
    }

    private function createBenitoJuarezAlert()
    {
        $a = $this->alert->create(['name' => 'Benito Juárez', 'type' => 'Benito Juárez']);
        $this->alert->addClassification($a, $this->classification->getByKey('arrival_at_the_airport'), 2);
        $this->alert->addClassification($a, $this->classification->getByKey('customs'), 2);
        $this->alert->addClassification($a, $this->classification->getByKey('delivered_to_the_country'), 2);
        $this->alert->addClassification($a, $this->classification->getByKey('in_transit_to_the_airport'), 2);
        $this->alert->addClassification($a, $this->classification->getByKey('receive_in_office_of_exchange'), 2);
    }

    private function createPantacoAlert()
    {
        $a = $this->alert->create(['name' => 'Pantaco (Federal District)', 'type' => 'Pantaco', 'subtype' => 'Federal District']);
        $this->alert->addClassification($a, $this->classification->getByKey('send_to_deliver'), 2);

        $a = $this->alert->create(['name' => 'Pantaco (Interior)', 'type' => 'Pantaco', 'subtype' => 'Interior']);
        $this->alert->addClassification($a, $this->classification->getByKey('send_to_deliver'), 3);

        $a = $this->alert->create(['name' => 'Pantaco (Unclassified)', 'type' => 'Pantaco', 'subtype' => 'Unclassified']);
        $this->alert->addClassification($a, $this->classification->getByKey('send_to_deliver'), 3);
    }

    private function createDistributionAlert()
    {
        $a = $this->alert->create(['name' => 'Distribution (Federal District)', 'type' => 'Distribution', 'subtype' => 'Federal District']);
        $this->alert->addClassification($a, $this->classification->getByKey('others'), 3);
        $this->alert->addClassification($a, $this->classification->getByKey('on_route_with_mailman'), 3);
        $this->alert->addClassification($a, $this->classification->getByKey('left_notice'), 5);
        $this->alert->addClassification($a, $this->classification->getByKey('on_window_at_post_office_for_pick_up'), 10);

        $a = $this->alert->create(['name' => 'Distribution (Interior)', 'type' => 'Distribution', 'subtype' => 'Interior']);
        $this->alert->addClassification($a, $this->classification->getByKey('others'), 3);
        $this->alert->addClassification($a, $this->classification->getByKey('on_route_with_mailman'), 4);
        $this->alert->addClassification($a, $this->classification->getByKey('left_notice'), 5);
        $this->alert->addClassification($a, $this->classification->getByKey('on_window_at_post_office_for_pick_up'), 10);

        $a = $this->alert->create(['name' => 'Distribution (Unclassified)', 'type' => 'Distribution', 'subtype' => 'Unclassified']);
        $this->alert->addClassification($a, $this->classification->getByKey('others'), 3);
        $this->alert->addClassification($a, $this->classification->getByKey('on_route_with_mailman'), 4);
        $this->alert->addClassification($a, $this->classification->getByKey('left_notice'), 5);
        $this->alert->addClassification($a, $this->classification->getByKey('on_window_at_post_office_for_pick_up'), 10);
    }

    public function createCongestedPostalOfficesAlert()
    {
        $a = $this->alert->create(['name' => 'Congested Postal Offices (DF)', 'type' => 'Congested Postal Offices', 'subtype' => 'Federal District']);
        $this->alert->addClassification($a, $this->classification->getByKey('send_to_deliver'), 4);
        $this->alert->addClassification($a, $this->classification->getByKey('others'), 8);
        $this->alert->addClassification($a, $this->classification->getByKey('on_route_with_mailman'), 8);
        $this->alert->addClassification($a, $this->classification->getByKey('left_notice'), 11);
        $this->alert->addClassification($a, $this->classification->getByKey('on_window_at_post_office_for_pick_up'), 18);

        $a = $this->alert->create(['name' => 'Congested Postal Offices (Interior)', 'type' => 'Congested Postal Offices', 'subtype' => 'Interior']);
        $this->alert->addClassification($a, $this->classification->getByKey('send_to_deliver'), 5);
        $this->alert->addClassification($a, $this->classification->getByKey('others'), 11);
        $this->alert->addClassification($a, $this->classification->getByKey('on_route_with_mailman'), 11);
        $this->alert->addClassification($a, $this->classification->getByKey('left_notice'), 16);
        $this->alert->addClassification($a, $this->classification->getByKey('on_window_at_post_office_for_pick_up'), 21);

//        $a = $this->alert->create(['name' => 'Congested Postal Offices (Unclassified)', 'type' => 'Congested Postal Offices', 'subtype' => 'Unclassified']);
//        $this->alert->addClassification($a, $this->classification->getByKey('others'), 11);
//        $this->alert->addClassification($a, $this->classification->getByKey('on_route_with_mailman'), 11);
//        $this->alert->addClassification($a, $this->classification->getByKey('left_notice'), 16);
//        $this->alert->addClassification($a, $this->classification->getByKey('on_window_at_post_office_for_pick_up'), 21);
    }
}