<?php

use App\Repositories\UserRepository;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Seeder;

class SentinelSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Permissions:
         *
         * Can import packages
         * Can view gross weights
         * Can add checkpoints
         * Can delete checkpoints
         * Can view reports
         * Can view dashboard
         * Can view settings
         * Can edit settings
         *
         *
         */

        try {
            DB::beginTransaction();

            $this->clear();
            $this->setUpAdministrators();
            $this->setUpOperators();
            $this->setUpAuditors();
            $this->setUpClients();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    private function clear()
    {
        DB::table('role_users')->delete();
        DB::table('roles')->delete();
        DB::table('users')->delete();
    }

    private function setUpAdministrators()
    {
        $repo = App::make(UserRepository::class);
        $role = Sentinel::getRoleRepository()->createModel()->create([
            'name'        => 'Administrator',
            'slug'        => 'administrator',
            'permissions' => [
                'admin'                       => true,
                'bags.checkpoints.create'     => true,
                'packages.import'             => true,
                'packages.view_gross_weight'  => true,
                'packages.view_dimensions'    => true,
                'packages.checkpoints.create' => true,
                'packages.checkpoints.delete' => true,
                'packages.checkpoints.sync'   => true,
                'dashboard.view'              => true,
                'reports.view'                => true,
                'settings.view'               => true,
                'settings.modify'             => true,
            ]
        ]);

        $users = [
            [
                'email'      => 'juan@theegg.com.ar',
                'first_name' => 'Juan Christian',
                'last_name'  => 'Cieri',
            ],
            [
                'email'      => 'juan@mailamericas.com',
                'first_name' => 'Juan Christian',
                'last_name'  => 'Cieri',
            ],
            [
                'email'      => 'jyacoy@mailamericas.com',
                'first_name' => 'Juliana',
                'last_name'  => 'Yacoy',
            ],
            [
                'email'      => 'jmarcos@mailamericas.com',
                'first_name' => 'Jorge',
                'last_name'  => 'Marcos',
            ],
            [
                'email'      => 'sblousson@mailamericas.com',
                'first_name' => 'Silvestre',
                'last_name'  => 'Blousson',
            ],
            [
                'email'      => 'ggarcia@mailamericas.com',
                'first_name' => 'Gotardo',
                'last_name'  => 'García',
            ],
            [
                'email'      => 'sarana@mailamericas.com',
                'first_name' => 'Santiago',
                'last_name'  => 'Arana',
            ],
            [
                'email'      => 'ntabanera@mailamericas.com',
                'first_name' => 'Nicolás',
                'last_name'  => 'Tabanera',
            ],
        ];
        foreach ($users as $au) {
            $u = $repo->create($au + ['password' => 'qweasd']);
            $role->users()->attach($u);
        }
    }

    private function setUpOperators()
    {
        $repo = App::make(UserRepository::class);
        $role = Sentinel::getRoleRepository()->createModel()->create([
            'name'        => 'Operator',
            'slug'        => 'operator',
            'permissions' => [
                'admin'                       => true,
                'bags.checkpoints.create'     => false,
                'packages.import'             => false,
                'packages.view_gross_weight'  => true,
                'packages.view_dimensions'    => true,
                'packages.checkpoints.create' => true,
                'packages.checkpoints.delete' => true,
                'packages.checkpoints.sync'   => true,
                'dashboard.view'              => true,
                'reports.view'                => true,
                'settings.view'               => true,
                'settings.modify'             => false,
            ]
        ]);

        $users = [
            [
                'email'      => 'customerservice@mailamericas.com',
                'first_name' => 'Customer',
                'last_name'  => 'Service',
            ],
        ];
        foreach ($users as $au) {
            $u = $repo->create($au + ['password' => 'qweasd']);
            $role->users()->attach($u);
        }
    }

    private function setUpAuditors()
    {
        $repo = App::make(UserRepository::class);
        $role = Sentinel::getRoleRepository()->createModel()->create([
            'name'        => 'Auditor',
            'slug'        => 'auditor',
            'permissions' => [
                'admin'                       => false,
                'bags.checkpoints.create'     => false,
                'packages.import'             => false,
                'packages.view_gross_weight'  => false,
                'packages.view_dimensions'    => false,
                'packages.checkpoints.create' => false,
                'packages.checkpoints.delete' => false,
                'packages.checkpoints.sync'   => false,
                'dashboard.view'              => true,
                'reports.view'                => false,
                'settings.view'               => false,
                'settings.modify'             => false,
            ]
        ]);

        $users = [
            [
                'email'      => 'jarodriguez@correosdemexico.gob.mx',
                'first_name' => 'Jorge Alberto',
                'last_name'  => 'Rodríguez Ceballos',
                'password'   => 'jarodriguez'
            ],
        ];
        foreach ($users as $au) {
            $u = $repo->create($au);
            $role->users()->attach($u);
        }
    }

    private function setUpClients()
    {
        $repo = App::make(UserRepository::class);
        $role = Sentinel::getRoleRepository()->createModel()->create([
            'name'        => 'Client',
            'slug'        => 'client',
            'permissions' => [
                'admin'                       => false,
                'bags.checkpoints.create'     => false,
                'packages.import'             => false,
                'packages.view_gross_weight'  => true,
                'packages.view_dimensions'    => true,
                'packages.checkpoints.create' => false,
                'packages.checkpoints.delete' => false,
                'packages.checkpoints.sync'   => false,
                'dashboard.view'              => false,
                'reports.view'                => false,
                'settings.view'               => false,
                'settings.modify'             => false,
            ]
        ]);

        $users = [
            [
                'email'      => 'cdavids@i-parcel.com',
                'first_name' => 'Claire',
                'last_name'  => 'Davids',
                'password'   => 'cdavids',
            ],
        ];
        foreach ($users as $au) {
            $u = $repo->create($au);
            $role->users()->attach($u);
        }
    }
}
