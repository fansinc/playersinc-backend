<?php

namespace App\Http\Controllers\Api\v1\Config;

use App\Entities\v1\Config\City;
use App\Entities\v1\Config\Country;
use App\Http\Controllers\Controller;
use App\Entities\v1\Config\State;
use Illuminate\Http\Request;

use App\Transformers\V1\Config\CountryTransformer;
use App\Transformers\V1\Config\StateTransformer;
use App\Transformers\V1\Config\CityTransformer;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Contracts\Hashing\Hasher;


class CountryStateCityController extends Controller
{
    use Helpers;

    public function index(Request $request)
    {
     
        $paginator = Country::orderBy('id','ASC')->paginate($request->get('limit', config('app.pagination_limit')));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }
        return $this->response->paginator($paginator, new CountryTransformer());
    }


    public function getStates(Request $request)
    {
        // return $request->country_id;
        
        $this->validate($request, [
            'country_id' => 'required|integer|exists:countries,id',
           
        ]);
        
        $paginator = State::where("country_id",$request->country_id)->orderBy('id','ASC')->paginate($request->get('limit', config('app.pagination_limit')));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }
        return $this->response->paginator($paginator, new StateTransformer());
    }


    public function getCities(Request $request)
    {
        $this->validate($request, [
            'state_id' => 'required|integer|exists:states,id',
           
        ]);
        
        $paginator = City::where("state_id",$request->state_id)->orderBy('id','ASC')->paginate($request->get('limit', config('app.pagination_limit')));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }
        return $this->response->paginator($paginator, new CityTransformer());

    }
}
