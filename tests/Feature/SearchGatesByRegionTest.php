<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchGatesByRegionTest extends TestCase
{
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function searchGatesByRegion()
    {


        // Test for default gates
        $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'colombia_registered',
            'region' => 'default'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'error' => false,
                    'errors' => ['No gates were found for the search by region']
                ]
            ]);


        //Test for colombia Registered by Region
         $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'colombia_registered',
            'region' => 'ORIENTE'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'error' => false,
                    'errors' => null
                ]
            ]);

         //Test for mexico Registered using zip code
         $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'colombia_registered',
            'zip_code' => '27099008'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'error' => false,
                    'errors' => null
                ]
            ]);
    }
}
