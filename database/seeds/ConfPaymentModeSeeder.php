<?php

use App\Entities\v1\Config\ConfPaymentMode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ConfPaymentModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConfPaymentMode::create(['name'=>'Online Payment','created_by'=>1,'updated_by'=>1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        ConfPaymentMode::create(['name'=>'Cheque/DD','created_by'=>1,'updated_by'=>1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        ConfPaymentMode::create(['name'=>'Cash','created_by'=>1,'updated_by'=>1,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
