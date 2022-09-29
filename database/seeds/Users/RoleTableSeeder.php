<?php

use App\Entities\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Entities\Role::class)->create([
            'name' => 'Player',
        ]);

        $playerrole = Role::where('name', 'Player')->firstOrFail();

         $player_permissions = [
          
            'news'=>[
                // ['name'=>'List news'],
                ['name'=>'Create news'],
                ['name'=>'Delete news'],
                ['name'=>'Update news'],
            ],
            'experience'=>[
                // ['name'=>'List experience'],
                ['name'=>'Create experience'],
                ['name'=>'Delete experience'],
                ['name'=>'Update experience'],
            ],
            'myweek'=>[
              // ['name'=>'List myweek'],
              ['name'=>'Create myweek'],
              ['name'=>'Delete myweek'],
              ['name'=>'Update myweek'],
            ]
        ];


        $playerrole->syncPermissions($player_permissions);


        // factory(\App\Entities\Role::class)->create([
        //     'name' => 'Lender',
        // ]);
        // $permissions = [
        //     'vehicle_details'=>[
        //         ['name'=>'List vehicle details'],
        //         // ['name'=>'Create vehicle details'],
        //         // ['name'=>'Delete vehicle details'],
        //         // ['name'=>'Update vehicle details'],
        //     ],
        //     'vehicles'=>[
        //         ['name'=>'List vehicles'],
        //         // ['name'=>'Create vehicles'],
        //         // ['name'=>'Delete vehicles'],
        //         // ['name'=>'Update vehicles'],
        //     ],
        //     'auction_bids'=>[
        //         ['name'=>'List auction bids'],
        //         // ['name'=>'Create auction bids'],
        //         // ['name'=>'Delete auction bids'],
        //         // ['name'=>'Update auction bids'],
        //     ],
        //     'auction_vehicles'=>[
        //         ['name'=>'List auction vehicles'],
        //         // ['name'=>'Create auction vehicles'],
        //         // ['name'=>'Delete auction vehicles'],
        //         // ['name'=>'Update auction vehicles'],
        //     ],
        //     // 'auction_watchlists'=>[
        //     //     ['name'=>'List auction watchlists'],
        //     //     ['name'=>'Create auction watchlists'],
        //     //     ['name'=>'Delete auction watchlists'],
        //     //     ['name'=>'Update auction watchlists'],
        //     // ],
        //     'auction_wins'=>[
        //         ['name'=>'List auction wins'],
        //         // ['name'=>'Create auction wins'],
        //         // ['name'=>'Delete auction wins'],
        //         // ['name'=>'Update auction wins'],
        //     ],
        //     'auctions'=>[
        //         ['name'=>'List auctions'],
        //         // ['name'=>'Create auctions'],
        //         // ['name'=>'Delete auctions'],
        //         // ['name'=>'Update auctions'],
        //     ],      
        // ];
        // $role = Role::where('name', 'Lender')->firstOrFail();

        // $role->syncPermissions($permissions);
        // factory(\App\Entities\Role::class)->create([
        //     'name' => 'Bidder',
        // ]);
       
        // $bidder_role = Role::where('name', 'Bidder')->firstOrFail();

        // $bidder_role->syncPermissions($bidder_permissions);

        factory(\App\Entities\Role::class)->create([
            'name' => 'Fan',
        ]);

        $fanrole = Role::where('name', 'Fan')->firstOrFail();

        $fan_permissions = [
         
           'purchase'=>[
               // ['name'=>'List news'],
               ['name'=>'Create purchase'],
               ['name'=>'Delete purchase'],
               ['name'=>'Update purchase'],
           ]
       ];

       $fanrole->syncPermissions($fan_permissions);

    }
}
