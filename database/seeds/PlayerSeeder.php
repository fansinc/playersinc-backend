<?php

use App\Entities\v1\Player;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Player::create(['name'=>'player name','created_by'=>1,'updated_by'=>1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        
        //
    }
}
