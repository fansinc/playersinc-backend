<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Stripe;

class StripeController extends Controller
{
    public function stripeToken(Request $request)
    {
       
        $this->validate($request, [
            'card_no' => 'required',
            'expiry_month' => 'required',
            'expiry_year' => 'required',
            'cvv' => 'required',
        ]);
 
        $stripe = Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $response = \Stripe\Token::create(array(
                "card" => array(
                    "number"    => $request->input('card_no'),
                    "exp_month" => $request->input('expiry_month'),
                    "exp_year"  => $request->input('expiry_year'),
                    "cvc"       => $request->input('cvv')
                )));


              
            return response()->json($response);

           
            // if (!isset($response['id'])) {
            //     return redirect()->route('addmoney.paymentstripe');
            // }
            // $charge = \Stripe\Charge::create([
            //     'card' => $response['id'],
            //     'currency' => 'USD',
            //     'amount' =>  100 * 100,
            //     'description' => 'wallet',
            // ]);
 
            // if($charge['status'] == 'succeeded') {
            //     return redirect('stripe')->with('success', 'Payment Success!');
 
            // } else {
            //     return redirect('stripe')->with('error', 'something went to wrong.');
            // }
 
        }
        catch (Exception $e) {
            return response()->json($e->getMessage());
        }
 
    }
}
