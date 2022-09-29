<?php

namespace App\Http\Controllers\Api\v1;

use Stripe;
use Exception;
use Illuminate\Http\Request;
use App\Entities\v1\Purchase;
use App\Entities\Assets\Asset;
use Dingo\Api\Routing\Helpers;
use App\Entities\v1\Experience;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Transformers\V1\PurchaseTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;

class PurchaseController extends Controller
{
    use Helpers;
    protected $model;

    public function __construct(Purchase $model)
    {
        $this->model = $model;
        // $this->middleware('permission:List purchase')->only('index');
        // $this->middleware('permission:List purchase')->only('show');
        $this->middleware('permission:Create purchase')->only('store');
        $this->middleware('permission:Update purchase')->only('update');
        $this->middleware('permission:Delete purchase')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $paginator = $this->model->orderBy('created_at', 'desc')->paginate($request->get('limit', config('app.pagination_limit')));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }
        return $this->response->paginator($paginator, new PurchaseTransformer());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['created_by'] = $request->user()->id;
        $request['updated_by'] = $request->user()->id;

        $request['user_id'] = $request->user()->id;
        $request['status_id'] = 1;

        $experience = Experience::find($request->experience_id);

        $exp_price = $experience->price * 100;

        $req_price = $request->price * 100;

        // return $experience;

        $request['player_id'] = $experience->player_id;

        if (number_format($exp_price, 3) != number_format($req_price,3)) {

            throw new StoreResourceFailedException('Experience price is mismatch Actual Price is-' .$exp_price . " REQ-" . $req_price);
        }

        $rules = [
            // 'player_id' => 'required|integer|exists:users,id',
            'experience_id' => 'required|integer|exists:experiences,id',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'card_no' => 'required|integer',
            'expiry_month' => 'required|integer',
            'expiry_year' => 'required|integer',
            'cvv' => 'required|integer',
            // 'user_id' => 'required|integer|exists:users,id',
            // 'status_id' => 'required|integer|exists:conf_statuses,id',
        
          
        ];
        $this->validate($request, $rules);
        // $Purchase = $this->model->create($request->all());

        // $user_profile = $this->api->get('api/me/viewprofile');
        // return $user_profile;

        $stripe_secret = Config::get('app.STRIPE_SECRET');
        $stripe = Stripe\Stripe::setApiKey($stripe_secret);


        try {
            $response = \Stripe\Token::create(array(
                "card" => array(
                    "number"    => $request->input('card_no'),
                    "exp_month" => $request->input('expiry_month'),
                    "exp_year"  => $request->input('expiry_year'),
                    // "name"       => $user_profile->first_name . " " . $user_profile->first_name,
                    // "address_line1"       => $user_profile->address_line1,
                    // "address_line2"       => $user_profile->address_line2,
                    // "address_city"       => $user_profile->city,
                    // "address_state"       => $user_profile->state,
                    // "address_zip"       =>  $user_profile->zip,
                    // "address_country"       => $user_profile->country,

                )
            ));


            // return $response->card->id;



            // $stripe_resp = response()->json($response);




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

        } catch (\Exception $e) {
            // $e = ['errors' => ["error"=>$e->getMessage()]];
            return response()->json(["errors"=>["error"=>$e->getMessage()]], 500);
            // return $this->response->error($e->getMessage(), 404);
        }
        try {
            $Purchase = $this->model->create($request->all());
            $token_id = $response->id;

            $card_id = $response->card->id;

            $Purchase->update(['token_id' => $token_id, 'card_id' => $card_id]);
            
            $charge = \Stripe\Charge::create([
                'amount' => intval(($req_price).''),
                'currency' => 'gbp',//gbp
                'description' => 'Purchase Price',
                'source' => $Purchase->token_id,
            ]);
            // return $charge;
            $Purchase->update(['stripeRS'=>json_encode($charge)]);
            // $booking->booking_status_id=5;      
            // return $charge->id;
            if(isset($charge['id']))
            {
            
            // $Purchase->update(['status_id'=>1]); 
            }
            else
            {
                // return $this->response->error($e->getMessage(), 404);
            return response()->json(["errors"=>['error' => "Unable to purchase, Please try later"]], 500);
            }
    
        } catch (Exception $e) {
            return $this->response->error($e->getMessage(), 404);
        }


        return $this->response->created(url('api/Purchase/' . $Purchase->id));
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entities\v1\Purchase  $Purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $Purchase)
    {
        return $this->response->item($Purchase, new PurchaseTransformer());
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\v1\Purchase  $Purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $Purchase)
    {
        $request['updated_by'] = $request->user()->id;

        $request['user_id'] = $request->user()->id;

        $experience = Experience::find($request->experience_id);

        // return $experience;

        $request['player_id'] = $experience->player_id;

        $exp_price = $experience->price * 100;

        $req_price = $request->price * 100;

        if (number_format($exp_price, 3) != number_format($req_price,3)) {

            throw new StoreResourceFailedException('Experience price is mismatch Actual Price is-' .$exp_price . " REQ-" . $req_price);
        }


        $request['price'] = $req_price;

        $rules = [
            // 'player_id' => 'required|integer|exists:users,id',
            'experience_id' => 'required|integer|exists:experiences,id',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            // 'user_id' => 'required|integer|exists:users,id',
            'status_id' => 'required|integer|exists:conf_statuses,id',
        ];

        if ($request->method() == 'PATCH') {
            $rules = [
                // 'player_id' => 'sometimes|required|integer|exists:users,id',
                'experience_id' => 'sometimes|required|integer|exists:experiences,id',
                'price' => 'sometimes|required|regex:/^\d+(\.\d{1,2})?$/',
                // 'user_id' => 'sometimes|required|integer|exists:users,id',
                'status_id' => 'sometimes|required|integer|exists:conf_statuses,id',

            ];
        }
        $this->validate($request, $rules);

        $Purchase->update($request->except('created_by'));

        return $this->response->item($Purchase->fresh(), new PurchaseTransformer());
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entities\v1\Purchase  $Purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $Purchase)
    {
        //
        $record = $this->model->findOrFail($Purchase->id);
        $record->delete();
        return $this->response->noContent();
    }
}
