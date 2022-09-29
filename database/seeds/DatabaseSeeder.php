<?php

use App\Entities\v1\Config\ConfOrderStatus;
use App\Entities\v1\Lenders\Lender;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
    
        $this->call(PassportSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(StatesSeeder::class);
        $this->call(CitiesSeeder::class);
        $this->call(ConfStatusSeeder::class);
       
        $this->call(ConfPaymentStatusSeeder::class);
        $this->call(ConfPaymentModeSeeder::class);
    
  

    }
}
