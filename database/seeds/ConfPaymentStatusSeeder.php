<?php

use App\Entities\v1\Config\ConfPaymentStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ConfPaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConfPaymentStatus::create(['name'=>'Payment Settled','created_by'=>1,'updated_by'=>1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        ConfPaymentStatus::create(['name'=>'Partially Settled','created_by'=>1,'updated_by'=>1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        ConfPaymentStatus::create(['name'=>'Not Settled','created_by'=>1,'updated_by'=>1,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
