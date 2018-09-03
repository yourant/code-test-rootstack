<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchGatesByPostalOfficeTest extends TestCase
{
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function searchGatesByPostalOffice()
    {


        // Test for default gates
        $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'mexico_express',
            'postal_office' => 'default'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'error' => false,
                    'errors' => ['No gates were found for the search by post office']
                ]
            ]);


        //Test for mexico Express by Postal Office
         $response = $this->json('GET', 'api/v1/sorting_gates', [
            'access_token' => 'ABL3394S049HST09845',
            'service' => 'mexico_express',
            'postal_office' => '2'
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
            'service' => 'mexico_express',
            'zip_code' => '26080'
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
