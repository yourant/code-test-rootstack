<?php

namespace Tests\Feature;

use App\Models\Marketplace;
use App\Repositories\MarketplaceRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ObservableMarketplaceUpdateTest extends TestCase
{
    /** @test */
    public function marketplace_update()
    {
        $marketplaceRepository = (new MarketplaceRepository((new Marketplace)));
        $marketplaceRepository->search()->chunk(10, function($marketplaces) use ($marketplaceRepository){
            foreach ($marketplaces as $marketplace) {
                $name = $marketplace->name . '1';
                $marketplaceRepository->update($marketplace, compact('name'));
            }
        });
        $this->assertTrue(true);
    }
}
