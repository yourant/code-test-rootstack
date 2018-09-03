<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchGatesByStateTest extends TestCase
{
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function searchGatesByState()
    {


        // Test for default gates
        $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'mexico_registered',
            'state' => 'default'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'error' => false,
                    'errors' => ['No gates were found for the search by state']
                ]
            ]);



        //Test for mexico Registered by State
         $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'mexico_registered',
            'state' => 'Aguascalientes'
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
            'service' => 'mexico_registered',
            'zip_code' => '20339'
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
