<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::update('update ht_oauth_clients set secret = "FANSINC" where id = 2');
        //
    }
}
