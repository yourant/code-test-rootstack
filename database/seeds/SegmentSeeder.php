<?php

use App\Repositories\BoundaryRepository;
use App\Repositories\CheckpointCodeRepository;
use App\Repositories\ClassificationRepository;
use App\Repositories\MilestoneRepository;
use App\Repositories\SegmentRepository;
use Illuminate\Database\Seeder;

class SegmentSeeder extends Seeder
{
    /**
     * @var SegmentRepository
     */
    protected $segmentRepository;

    /**
     * @var MilestoneRepository
     */
    protected $milestoneRepository;

    /**
     * @var BoundaryRepository
     */
    protected $boundaryRepository;

    /**
     * @var CheckpointCodeRepository
     */
    protected $checkpointCodeRepository;

    /**
     * @var ClassificationRepository
     */
    protected $classificationRepository;

    public function __construct(
        SegmentRepository $segmentRepository,
        MilestoneRepository $milestoneRepository,
        BoundaryRepository $boundaryRepository,
        CheckpointCodeRepository $checkpointCodeRepository,
        ClassificationRepository $classificationRepository
    ) {
        $this->segmentRepository = $segmentRepository;
        $this->milestoneRepository = $milestoneRepository;
        $this->boundaryRepository = $boundaryRepository;
        $this->checkpointCodeRepository = $checkpointCodeRepository;
        $this->classificationRepository = $classificationRepository;
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

            $m1 = $this->milestoneRepository->create(['name' => 'Label generated', 'position' => 1]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => 23])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m1, $cc);
            }

            $m2 = $this->milestoneRepository->create(['name' => 'Posted at origin', 'position' => 2]);
            foreach ($this->checkpointCodeRepository->search(['id' => [4727, 4902]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m2, $cc);
            }

            $m3 = $this->milestoneRepository->create(['name' => 'Dispatch assigned', 'position' => 3]);
            foreach ($this->checkpointCodeRepository->search(['id' => [4728, 4729, 4730, 4903]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m3, $cc);
            }

            $m4 = $this->milestoneRepository->create(['name' => 'In transit to airport', 'position' => 4]);
            foreach ($this->checkpointCodeRepository->search(['id' => [4381, 4502, 4598, 4632, 4633, 4644]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m4, $cc);
            }

            $m5 = $this->milestoneRepository->create(['name' => 'Received at the airline', 'position' => 5]);

            $m6 = $this->milestoneRepository->create(['name' => 'In transit to destination country', 'position' => 6]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => [2, 4]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m6, $cc);
            }

            $m7 = $this->milestoneRepository->create(['name' => 'Arrived at destination country', 'position' => 7]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => 6])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m7, $cc);
            }

            $m8 = $this->milestoneRepository->create(['name' => 'Customs', 'position' => 8]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => [9, 10, 11, 12]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m8, $cc);
            }

            $m9 = $this->milestoneRepository->create(['name' => 'In transit to delivery center', 'position' => 9]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => [14, 21]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m9, $cc);
            }

            $m10 = $this->milestoneRepository->create(['name' => 'Last mile', 'position' => 10]);
            foreach ($this->checkpointCodeRepository->search(['classification_id' => [15, 17, 18, 19, 20, 25]])->get() as $cc) {
                $this->milestoneRepository->addCheckpointCode($m10, $cc);
            }

            $s1 = $this->segmentRepository->create(['name' => 'Segment #1']);
            $this->segmentRepository->addBoundary($s1, 0, 1);
            $this->segmentRepository->addBoundary($s1, 1, 3);
            $this->segmentRepository->addBoundary($s1, 3, 5);
            $this->segmentRepository->addBoundary($s1, 5, 10);
            $this->segmentRepository->addBoundary($s1, 10);
            $this->segmentRepository->addMilestone($s1, $m1);

            $s2 = $this->segmentRepository->create(['name' => 'Segment #2']);
            $this->segmentRepository->addBoundary($s2, 0, 1);
            $this->segmentRepository->addBoundary($s2, 1, 2);
            $this->segmentRepository->addBoundary($s2, 2, 3);
            $this->segmentRepository->addBoundary($s2, 3, 4);
            $this->segmentRepository->addBoundary($s2, 4);
            $this->segmentRepository->addMilestone($s2, $m2);
            $this->segmentRepository->addMilestone($s2, $m3);
            $this->segmentRepository->addMilestone($s2, $m4);

            $s3 = $this->segmentRepository->create(['name' => 'Segment #3']);
            $this->segmentRepository->addBoundary($s3, 0, 1);
            $this->segmentRepository->addBoundary($s3, 1, 2);
            $this->segmentRepository->addBoundary($s3, 2, 3);
            $this->segmentRepository->addBoundary($s3, 3, 4);
            $this->segmentRepository->addBoundary($s3, 4, 5);
            $this->segmentRepository->addBoundary($s3, 5, 6);
            $this->segmentRepository->addBoundary($s3, 6, 7);
            $this->segmentRepository->addBoundary($s3, 7, 8);
            $this->segmentRepository->addBoundary($s3, 8, 9);
            $this->segmentRepository->addBoundary($s3, 9, 10);
            $this->segmentRepository->addBoundary($s3, 10);
            $this->segmentRepository->addMilestone($s3, $m5);
            $this->segmentRepository->addMilestone($s3, $m6);

            $s4 = $this->segmentRepository->create(['name' => 'Segment #4']);
            $this->segmentRepository->addBoundary($s4, 0, 1);
            $this->segmentRepository->addBoundary($s4, 1, 2);
            $this->segmentRepository->addBoundary($s4, 2, 3);
            $this->segmentRepository->addBoundary($s4, 3, 4);
            $this->segmentRepository->addBoundary($s4, 4, 5);
            $this->segmentRepository->addBoundary($s4, 5, 6);
            $this->segmentRepository->addBoundary($s4, 6, 7);
            $this->segmentRepository->addBoundary($s4, 7, 8);
            $this->segmentRepository->addBoundary($s4, 8, 9);
            $this->segmentRepository->addBoundary($s4, 9, 10);
            $this->segmentRepository->addBoundary($s4, 10);
            $this->segmentRepository->addMilestone($s4, $m7);
            $this->segmentRepository->addMilestone($s4, $m8);
            $this->segmentRepository->addMilestone($s4, $m9);
            $this->segmentRepository->addMilestone($s4, $m10);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
