<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchGatesByTownTest extends TestCase
{
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function searchGatesByTown()
    {
        // Test for default gates
       /* $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'chile_town',
            'town' => '20'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => false,
                'data' => [
                    'errors' => ['No gates were found for the search by town']
                ]
            ]);

         // test for correct value
        $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'chile_town',
            'town' => 'Puerto Williams'
        ]);


        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => false,
                'data' => [
                    'errors' => null
                ]
            ]);*/


    }
}
