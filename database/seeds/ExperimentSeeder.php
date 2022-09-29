<?php

use App\Entities\v1\Experiment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExperimentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Experiment::create(['player_id'=>1,'created_by'=>1,'updated_by'=>1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
