<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ConfStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('conf_statuses')->insert([
            'name' => 'Active',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
        ]);
        DB::table('conf_statuses')->insert([
            'name' => 'Inactive',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
        ]);
    
    }
}
