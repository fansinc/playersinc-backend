<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Hashing\Hasher;
use Dingo\Api\Exception\ResourceException;
use App\Entities\Assets\Asset;
use App\Entities\User;
use App\Entities\UserAddress;
use App\Transformers\Users\UserTransformer;
use App\Transformers\Users\UserAddressTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;

class UserAddressController extends Controller
{
    use Helpers;


    public function __construct(UserAddress $model)
    {
        $this->model = $model;
        $this->middleware('permission:List user address')->only('index');
        $this->middleware('permission:List user address')->only('show');
        $this->middleware('permission:Create user address')->only('store');
        $this->middleware('permission:Update user address')->only('update');
        $this->middleware('permission:Delete user address')->only('destroy');
    }

    /**
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request)
    {
       
        $paginator = $this->model->paginate($request->get('limit', config('app.pagination_limit')));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }

        return $this->response->paginator($paginator, new UserAddressTransformer());
    }
    
    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {

        $user=User::findOrFail($request->user()->id);

        $useraddress = $user->userAddress;

        if($useraddress)
        {

            throw new StoreResourceFailedException('User Address Is Already Created. You Can Update Only');

        }
        $request['user_id'] = $request->user()->id;
        $request['created_by'] = $request->user()->id;
        $request['updated_by'] = $request->user()->id;

        $this->validate($request, [
         
            'address_line1' => 'required|string',
            'address_line2' => 'nullable|string',
            'country' => 'required|string',
            'state' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
           
        ]);
        $useraddress = $this->model->create($request->all());
        // if ($request->has('file')) {
        //     foreach ($request->file as $file) {
        //         $assets = $this->api->attach(['file' => $file])->post('api/assets');
        //         // $item = $this->model->create($request->all());
        //         $profile->assets()->save($assets);
        //     }
        // } else if ($request->has('url')) {
        //     $assets = $this->api->post('api/assets', ['url' => $request->url]);
        //     // $item = $this->model->create($request->all());
        //     $profile->assets()->save($assets);
        // } else if ($request->has('uuid')) {
        //     $a = Asset::byUuid($request->uuid)->get();
        //     $assets = Asset::findOrFail($a[0]->id);
        //     // $item = $this->model->create($request->all());
        //     $profile->assets()->save($assets);

        // } 
       
  
        return $this->response->created(url('api/me/createuseraddress'.$useraddress->id));
    }


    public function show(Request $request)
    {
        $user=User::findOrFail($request->user()->id);

        $useraddress = $user->userAddress;

        if(!$useraddress)
        {

            throw new StoreResourceFailedException('User Address Is Not Created Yet. Please Create It.');

        }

        // return $useraddress;
        
        return $this->response->item($useraddress, new UserAddressTransformer());
    }

    public function showUser()
    {
        return $this->response->item(Auth::user(), new UserTransformer());
    }


    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function update(Request $request)
    {
        $request['user_id'] = $request->user()->id;
        $request['updated_by'] = $request->user()->id;

        $user=User::findOrFail($request->user()->id);

        $useraddress = $user->userAddress;


       $rules= [
        'address_line1' => 'required|string',
        'address_line2' => 'nullable|string',
        'country' => 'required|string',
        'state' => 'required|string',
        'city' => 'required|string',
        'postal_code' => 'required|string',
           
        ];

        if ($request->method() == 'PATCH') {

           

            $rules= [
                'address_line1' => 'sometimes|required|string',
                'address_line2' => 'sometimes|nullable|string',
                'country' => 'sometimes|required|string',
                'state' => 'sometimes|required|string',
                'city' => 'sometimes|required|string',
                'postal_code' => 'sometimes|required|string',
          
            ];

        }

        $this->validate($request, $rules);

       

        $useraddress->update($request->except('created_by'));

      
        // if ($request->has('file')) {
        //     foreach ($request->file as $file) {
        //         $assets = $this->api->attach(['file' => $file])->post('api/assets');
        //         $profile->assets()->save($assets);
        //     }
        // }

        return $this->response->item($useraddress->fresh(), new UserAddressTransformer());
    }

    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        // verify the old password given is valid
        if (! app(Hasher::class)->check($request->get('current_password'), $user->password)) {
            throw new ResourceException('Validation Issue', [
                'old_password' => 'The current password is incorrect',
            ]);
        }
        $user->password = bcrypt($request->get('password'));
        $user->save();

        return $this->response->item($user->fresh(), new UserTransformer());
    }

     /**
     * @param Request $request
     * @param $uuid
     * @return mixed
     */
    public function destroy(Request $request)
    {
        // $profile->assets()->delete();
        $user=User::findOrFail($request->user()->id);

        $useraddress = $user->userAddress;

        $useraddress->delete();

        return $this->response->noContent();
    }
}
