<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchGatesByValueTest extends TestCase
{
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function searchGatesByValue()
    {

        // Test for default gates
        $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'chile_value',
            'value' => '20'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'error' => false,
                    'errors' => ['No gates were found for the search by value']
                ]
            ]);

         // test for correct value
        $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'chile_value',
            'value' => '30.00'
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
