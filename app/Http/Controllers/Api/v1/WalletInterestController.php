<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\v1\WalletInterest;
use App\Entities\User;
use Dingo\Api\Routing\Helpers;

class WalletInterestController extends Controller
{

    use Helpers;
    protected $model;

    public function __construct(WalletInterest $model)
    {
        $this->model = $model;
        // $this->middleware('permission:List experience')->only('index');
        $this->middleware('permission:List wallet interest')->only('setWalletInterest');
        $this->middleware('permission:Create wallet interest')->only('getWalletInterest');
        // $this->middleware('permission:Update experience')->only('update');
        // $this->middleware('permission:Delete experience')->only('destroy');
    }
    public function setWalletInterest(Request $request)
    {

        $user=User::findOrFail($request->user()->id);

        WalletInterest::create(["user_id"=>$request->user()->id]);

        return $this->response->array(['Message' => "Wallet Interest is set successfully"]);
    }

    public function getWalletInterest(Request $request)
    {

        $user=User::findOrFail($request->user()->id);

        $wi = WalletInterest::where(["user_id"=>$request->user()->id])->count();

        if($wi>0)
        {
            return $this->response->array(['wallet_interest_status' => true]);
        }
        else
        {
            return $this->response->array(['wallet_interest_status' => false]);
        }
    }
}
