<?php

use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder {

    public function __construct(UserRepository $user, ClientRepository $client)
    {
        $this->user = $user;
        $this->client = $client;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->user->create([
            'name' => 'Operaciones',
            'email' => 'operaciones@mailamericas.com',
            'password' => bcrypt('qweasd'),
            'admin' => true
        ]);

        $client = $this->client->getByName('I-Parcel');
        $this->user->create([
            'name' => 'Claire Davids',
            'email' => 'cdavids@i-parcel.com',
            'password' => bcrypt('cdavids'),
            'client_id' => $client->id,
        ]);
    }
}
