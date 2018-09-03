<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchGatesByWeightTest extends TestCase
{
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function searchGatesByWeight()
    {

        // Test for default gates
        $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'colombia_express',
            'weight' => '20'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'error' => false,
                    'errors' => ['No gates were found for the search by weight']
                ]
            ]);



        // Test for Colombia express by Weight
        $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'colombia_express',
            'weight' => '5'
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
