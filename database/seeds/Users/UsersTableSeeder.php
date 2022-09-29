<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Entities\User::class, 1)->create()->each(function($user) {
            $user->assignRole('Player');
        });
        factory(\App\Entities\User::class, 1)->create()->each(function($user) {
            $user->assignRole('Fan');
        });
        
    }
}
