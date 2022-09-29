<?php

namespace App\Console;

use App\Console\Commands\InstallApp;
use App\Console\Commands\ResetDemoApp;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Http;




class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        InstallApp::class,
        // this is just for the demo, you can remove this on your application
        ResetDemoApp::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call('demo:reset')->hourly();

        // $schedule->command('serve');

        $schedule->call(function () {

            try{
                $respose = Http::withHeaders([
                    'Accept' => 'application/vnd.api.v1+json',
                    'Content-Type' => 'application/json'
                ])->post('http://devapi.gripauction.com/oauth/token', [
                    "grant_type" => "password",
                    "client_id" => 2,
                    "client_secret" => "Hridhamtech(GripCarAuctions)",
                    "username" => "admin@gripauction.com",
                    "password" => "12345678",
                    "scope" => ''
                ]);

                $token = $respose->json()['access_token'];
        // dd($respose);
                if($respose->ok()){
                    
                   
                    try{

                        $userresponse = Http::withToken($token)->withHeaders(['Accept'=>'application/vnd.api.v1+json','Content-Type'=>'application/json'])->get('http://devapi.gripauction.com/' .'api/me');
        
                        $profresponse = json_decode($userresponse->getBody()->getContents(), true);
        
                       $user_id = $profresponse['data']['user_id'];

                       $file = 'Current_Auctions.txt';//the path of your file
                       $conn = Storage::disk('local');//configured in the file filesystems.php
                       $stream =  $conn->readStream($file);
                       
                    //    $content = array_filter();
                       while (($line = fgets($stream, 4096)) !== false) {
                          
                          if($line!=="")
                          {
                           $ld = explode("|",$line);
               
                        //    if(\Carbon\Carbon::now()>$ld[2] && $ld[3]==1)
                        if($ld[3]==1)
                           {
                            //    echo "expired";
                           
                              
                              $auctionvehicles = \App\Entities\v1\Auction\AuctionVehicle::where("auction_id","=",$ld[0])->where("auction_vehicle_expiry","<",\Carbon\Carbon::now())
                             ->get();

                            //  echo count($auctionvehicles);
                            if(count($auctionvehicles)>0)
                            {
                                foreach($auctionvehicles as $av)
                                {
                                //    echo $av->vehicle_id;
                                $auctionbids = \App\Entities\v1\Auction\AuctionBid::where("auction_id","=",$ld[0])
                                ->where("vehicle_id","=",$av->vehicle_id)->orderBy('bid_amount', 'desc')->first();
            
                                if(!empty($auctionbids))
                                {
                                    // echo "sad"; 
                                    try{


                                        //    \App\Entities\v1\Auction\AuctionWin::create([
                                        //        "vehicle_id" => $av->vehicle_id,
                                        //        "auction_id" =>$ld[0],
                                        //        "user_id" => $auctionbids->user_id,
                                        //        "bid_amount" => $auctionbids->bid_amount,
                                        //        "approval_status" => "Approved",
                                        //        "payment_status" => "Paid",
                                        //        "created_by" => 1,
                                        //        "updated_by" => 1
            
                                        //    ]);


                                        $response2 = Http::withToken($token)->withHeaders(['Accept'=>'application/vnd.api.v1+json','Content-Type'=>'application/json'])->post('http://devapi.gripauction.com/'.'api/auctionWin',
        
                                        [
                                    
                                            "vehicle_id"=>$av->vehicle_id,
                                            "auction_id"=>$ld[0],
                                            "user_id"=>$auctionbids->user_id,
                                        
                                            "bid_amount"=>$auctionbids->bid_amount,
                                    
                                            "approval_status" => "Approved",
                                            "payment_status" => "Paid",
                                            "created_by" => $user_id,
                                            "updated_by" => $user_id,
                                        
                                        ]);
                                        
                                    // echo $response2->status();
                                    
                                        if($response2->status()===201 || $response2->status()===200 ){
                            
                                        echo "Auction Win Cron Job Successfully Done!";
                                        
                                        }else{
                                            
                                                                        
                                            throw new StoreResourceFailedException('Error occured while auction win cron job running!'); 
                                        }

                                        }
                                        catch(\Exception $e) {
                                        echo 'Message: ' .$e->getMessage();
                                        }
            
                                }
                                else{

                                    // echo "No Auction Vehicles Found!";
                                    throw new StoreResourceFailedException('There is no Auction Bid Done!'); 
                                }

                
                                }
                            }
                            else{

                                // echo "No Auction Vehicles Found!";
                                throw new StoreResourceFailedException('No Auction Vehicles Found!'); 
                            }
                           }
                           else{
               
                               // $data .= $line;
               
                           }
                       }
               
                     }
                       
                    }catch (\Exception $e){
                        //buy a beer
                        echo $e->getMessage();
        
                    }
                   
                    // echo $token;
                }

            }catch (\Exception $e){
                //buy a beer
                echo $e->getMessage();

            }


            

            // return;
            // foreach (\App\Entities\v1\Auction\Auction::where("auction_expiry",\Carbon\Carbon::now())->where("auction_status_id","1") as $auction) {
            //     // if (\Carbon\Carbon::now() > $auction->auction_expiry) {

            //         echo "expired";
                   
            //        $auctionvehicles = \App\Entities\v1\Auction\AuctionVehicle::where("auction_id","=",$auction->id)
            //       ->get();

            //       foreach($auctionvehicles as $av)
            //       {
            //         echo $av->vehicle_id;
            //         $auctionbids = \App\Entities\v1\Auction\AuctionBid::where("auction_id","=",$auction->id)
            //         ->where("vehicle_id","=",$av->vehicle_id)->orderBy('bid_amount', 'desc')->first();

            //         if(!empty($auctionbids))
            //         {
            //                 \App\Entities\v1\Auction\AuctionWin::create([
            //                     "vehicle_id" => $av->vehicle_id,
            //                     "auction_id" => $auction->id,
            //                     "user_id" => $auctionbids->user_id,
            //                     "bid_amount" => $auctionbids->bid_amount,
            //                     "approval_status" => "Approved",
            //                     "payment_status" => "Paid"

            //                 ]);

            //         }
            //       }

               
            //    }
            //    else
            //    {

            //         echo "active";
            //         // echo $auction->id;

                  
            //    }
            // }
        })->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
