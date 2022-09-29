<?php

namespace App\Http\Controllers\Api\Users;

use App\Entities\Role;
use App\Entities\User;
use App\Entities\v1\Users\UserProfile;
use App\Http\Controllers\Controller;
use App\Transformers\Users\UserTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Entities\PasswordReset;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Token;
use Dingo\Api\Exception\StoreResourceFailedException;

/**
 * Class UsersController.
 *
 * @author Jose Fonseca <jose@ditecnologia.com>
 */
class UsersController extends Controller
{
    use Helpers;

    /**
     * @var User
     */
    protected $model;

    /**
     * UsersController constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
        $this->middleware('permission:List users')->only('index');
        $this->middleware('permission:List users')->only('show');
        $this->middleware('permission:Create users')->only('store');
        $this->middleware('permission:Update users')->only('update');
        $this->middleware('permission:Delete users')->only('destroy');
    }

    /**
     * Returns the Users resource with the roles relation.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $paginator = $this->model->with('roles.permissions')->paginate($request->get('limit', config('app.pagination_limit')));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }

        return $this->response->paginator($paginator, new UserTransformer());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $user = $this->model->with('roles.permissions')->byUuid($id)->firstOrFail();

        return $this->response->item($user, new UserTransformer());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'mobile' => 'unique:users,mobile|digits:11',
        ]);
        $user = $this->model->create($request->all());
        // $lender_uuid = Role::where('name', 'Lender')->get()[0]->uuid;
        // $bider_uuid = Role::where('name', 'Bidder')->get()[0]->uuid;
        if ($request->has('roles')) {
            $user->syncRoles($request['roles']);
            
            // if (in_array($lender_uuid,$request['roles'])) {
            //     $l = new Lender();
            //     $l->lender_name=$user->name;
            //     $l->user_id = $user->id;
            //     $l->created_by = $request->user()->id;
            //     $l->updated_by = $request->user()->id;
            //     $user->lender()->save($l);
            // }
            // if (in_array($bider_uuid,$request['roles'])) {
            //     $b = new BidderDeposit();
            //     $s = new Subscription();
            //     $p = new UserProfile();
            //     $pan = new UserPanDetail();
            //     $cl = new UserCheckLeafDetail();
            //     $aa = new UserAadharDetail();
            //     $str = $user->id;
            //     $gai = str_pad($str, 6, "0", STR_PAD_LEFT);
            //     $gai = 'GA-' . $gai;
            //     $p->create([
            //         'user_id' => $user->id,
            //         'grip_auction_id' => $gai,
            //         'user_address' => null,
            //         'created_by' => $request->user()->id,
            //         'updated_by' => $request->user()->id,
            //     ]);
            //     $pan->create([
            //         'user_id' => $user->id,
            //         'pan_no' => null,
            //         'created_by' => $request->user()->id,
            //         'updated_by' => $request->user()->id,
            //     ]);
            //     $cl->create([
            //         'user_id' => $user->id,
            //         'check_leaf_no' => null,
            //         'created_by' => $request->user()->id,
            //         'updated_by' => $request->user()->id,
            //     ]);
            //     $aa->create([
            //         'user_id' => $user->id,
            //         'aadhar_no' => null,
            //         'created_by' => $request->user()->id,
            //         'updated_by' => $request->user()->id,
            //     ]);
            //     $s->create([
            //         'user_id' => $user->id,
            //         'sub_payment_ref_id' => null,
            //         'starts_at' => null,
            //         'ends_at' => null,
            //         'status' => false,
            //         'pay_status_id' => ConfigPaymentStatus::where('status', 'Pending')->first()->id,
            //         'created_by' => $request->user()->id,
            //         'updated_by' => $request->user()->id,
            //     ]);
            //     $b->create([
            //         'user_id' => $user->id,
            //         'deposit_amount' => 0,
            //         'buying_limit' => 0,
            //         'available_limit' => 0,
            //         'pay_status_id' => ConfigPaymentStatus::where('status', 'Pending')->first()->id,
            //         'created_by' => $request->user()->id,
            //         'updated_by' => $request->user()->id,
            //     ]);
            // }
        }

        return $this->response->created(url('api/users/' . $user->uuid));
    }
    public function bidderProfile(Request $request)
    {
        $user_id = $request->user()->id;
        $user = $this->model->find($user_id);
        return $this->response->item($user, new BidderUserTransformer());
    }
    // public function playerRegister(Request $request)
    // {
    //     $this->validate($request, [
    //         'name' => 'required|unique:users,name',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:8|confirmed',
    //         'mobile' => 'unique:users,mobile|digits:10',
    //     ]);
    //     $user = $this->model->create($request->all());
    //     $player_uuid = Role::where('name', 'Player')->get()[0]->uuid;
    //     $user->syncRoles($player_uuid);

    //     return $this->response->created(url('api/users/' . $user->uuid));
    // }


    public function fanRegister(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'mobile' => 'unique:users,mobile|digits:11',
        ]);
        $user = $this->model->create($request->all());
        $player_uuid = Role::where('name', 'Fan')->get()[0]->uuid;
        $user->syncRoles($player_uuid);

        return $this->response->created(url('api/users/' . $user->uuid));
    }


    public function playerRegister(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'mobile' => 'unique:users,mobile|digits:11',
        ]);
        $user = $this->model->create($request->all());
        $player_uuid = Role::where('name', 'Player')->get()[0]->uuid;
        $user->syncRoles($player_uuid);

        return $this->response->created(url('api/users/' . $user->uuid));
    }


    public function playersList(Request $request)
    {
             
        $paginator = $this->model->whereHas('roles', function($q){$q->where('name', 'Player');})->paginate($request->get('limit', config('app.pagination_limit', 20)));
        if($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }

        return $this->response->paginator($paginator, new UserTransformer());


      
    }

    public function fansList(Request $request)
    {
             
        $paginator = $this->model->whereHas('roles', function($q){$q->where('name', 'Fan');})->paginate($request->get('limit', config('app.pagination_limit', 20)));
        if($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }

        return $this->response->paginator($paginator, new UserTransformer());


      
    }
    // public function biderRegister(Request $request)
    // {
    //     $this->validate($request, [
    //         'name' => 'required|unique:users,name',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:8|confirmed',
    //         'mobile' => 'digits:10|unique:users,mobile',
    //     ]);
    //     $user = $this->model->create($request->all());
    //     $bider_uuid = Role::where('name', 'Bidder')->get()[0]->uuid;
    //     $user->syncRoles($bider_uuid);

       

    //     return $this->response->created(url('api/users/' . $user->uuid));
    // }
    /**
     * @param Request $request
     * @param $uuid
     * @return mixed
     */
    public function update(Request $request, $uuid)
    {
        $user = $this->model->byUuid($uuid)->firstOrFail();
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'digits:11|unique:users,mobile,' . $user->id,
        ];
        if ($request->method() == 'PATCH') {
            $rules = [
                'name' => 'sometimes|required|string',
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'mobile' => 'sometimes|digits:11|unique:users,mobile,' . $user->id,
            ];
        }
        $this->validate($request, $rules);
        // Except password as we don't want to let the users change a password from this endpoint
        $user->update($request->except('_token', 'password'));
        if ($request->has('roles')) {
            $user->syncRoles($request['roles']);
        }

        return $this->response->item($user->fresh(), new UserTransformer());
    }

    /**
     * @param Request $request
     * @param $uuid
     * @return mixed
     */
    public function destroy(Request $request, $uuid)
    {
        $user = $this->model->byUuid($uuid)->firstOrFail();
        $user->delete();

        return $this->response->noContent();
    }


    
    public function forgotPassword(Request $request)
    {
        // $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';

        $credentials = $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'url' => 'required|url'
        ]);

        // $this->validate($request, [
        //              'email' => 'required|email|exists:users,email',
        //              'url' => 'required|url'
        // ]);

        $url = $request->url;

        $email = $request->email;

        $user = User::where("email", "=", $request->email)->first();

        $ps = PasswordReset::where("email", "=", $request->email)->first();
        
        if(!$ps)
        {
            $ran = Str::random(40);

            $token = Hash::make($ran);

            PasswordReset::create(["email"=>$user->email,"token"=>$token,"created_at"=>Carbon::now()]);

           
        } 
        else
        {
            $ran = Str::random(40);

            $token = Hash::make($ran);

            PasswordReset::where("email", "=", $request->email)->update(["token"=>$token,"created_at"=>Carbon::now()]);

        }  
        // $user->notify(new ResetPasswordNotification($ran, $url, $email));

        $to = $user->email;
        $subject = 'Your Fansinc Account Password Reset Link';
        $body = ' 
        <html> 
        <head> 
            <title>Hi'.$user->name .'</title> 
        </head> 
        <body style="background-color: #e0e0e0;"> 
            <h3 style="text-align:center;padding:10px;">You are receiving this email because we received a password reset request for your account.</h3> 
            <table cellspacing="0" style="padding:10px; width: 100%;background-color: #e0e0e0; "> 
                <tr> 
                   <td align="center"><a style="background-color:#000;color:white;padding:10px; text-decoration:none;"href="'.$request->url.'?email='.$user->email.'&token='.$ran.'">Reset Password</a></td> 
                </tr> <br>
                <tr style="background-color: #e0e0e0; padding:10px"> 
                    <td align="center">If you did not request a password reset, no further action is required.</td> 
                </tr> 
                <br>
                <tr style="background-color: #e0e0e0; padding:10px"> 
                <td>Regards,</td> 
                </tr> 
              
                <tr style="background-color: #e0e0e0; padding:10px"> 
                <td>Admin - Fansinc</td> 
                </tr> 
             
            </table> 
        </body> 
        </html>'; 

        $headers = 'From: Fansinc fansinc.io@gmail.com' . "\r\n" ;
        $headers .='Reply-To: '. $to . "\r\n" ;
        $headers .='X-Mailer: PHP/' . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";   


        if(mail($to, $subject, $body,$headers)) {
            return response()->json(["message" => 'Reset password link sent on your email id.']);
        } 
        else 
        {
            return response()->json(["message" => 'Error! Reset password mail delivery is failed.']);
        }
        // return $response;
        //    return $user->uuid;

        
    }


    public function resetPassword(Request $request)
    {
        //    return csrf_token();

        // return $request->all();
        $credentials = $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::where("email", "=", $request->email)->first();

        $password = $user->email;

     

        $passwordReset = PasswordReset::where('email', $request->email)
            ->first();


            if(!Hash::check($request->token,$passwordReset->token)==true)
            {
                return response()->json([
                    'message' => 'This password reset token is invalid.'
                ], 404);
     
            }
            else
            {
                    // return "ok";
            }
        
       
            // return password_verify($request->token,$passwordReset->token);
            
            // return $this->hasher->check($token, $passwordReset->token); //throw error, token invalid
            
      
        //    return Hash::check($request->token, $passwordReset->token);
            
      
        // if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
        //     $passwordReset->delete();
        //     return response()->json([
        //         'message' => 'This password reset token is invalid.'
        //     ], 404);
        // }
   
      
          
         $user = User::where('email', $passwordReset->email)->first();
    
        if (!$user)
        {
            return response()->json(['message' => "We can't find a user with that e-mail address."], 404);

        }
        $user->password = bcrypt($request->password);
        $user->save();
        PasswordReset::where('email', $passwordReset->email)->delete();

        return response()->json(["message" => "Password has been successfully changed"],201);



     

        // $reset_password_status = Password::reset($credentials, function ($user, $password) {

        //     $user->password = $password;
        //     $user->save();

        // });
        // return $reset_password_status;

        // if ($reset_password_status == Password::INVALID_TOKEN) {
        //     return response()->json(["msg" => "Invalid token provided"], 400);
        // }

        // return response()->json(["msg" => "Password has been successfully changed"]);
    }


    public function playerLogin(Request $request) {

            $rules = [
                'app_id' => 'required|integer',
                'username' => 'required|exists:users,email',
                'password' => 'required',
            ];
        
            $this->validate($request, $rules);

            $user = User::where('email', $request->username)->first();

            // return $user->roles[0]->name;

            if ($user->roles[0]->name!="Player") {

                throw new StoreResourceFailedException('Error! Only Players Are Allowed In This App');
            }

            if ($request->app_id!=1) {

                throw new StoreResourceFailedException('Error! Only Players Are Allowed In This App');
            }
            $request->request->add([
            'username' => $request->username,
            'password' => $request->password,
            'grant_type' => 'password',
            'client_id' => 2,
            'client_secret' => "FANSINC",
            'scope' => '']);

            $newRequest = Request::create('/oauth/token', 'post');

            $tokenres =  Route::dispatch($newRequest)->getContent();

            $token = json_decode($tokenres,true);

            return $token;
    }

    public function fanLogin(Request $request) {

        $rules = [
            'app_id' => 'required|integer',
            'username' => 'required|exists:users,email',
            'password' => 'required',
        ];
    
        $this->validate($request, $rules);

        $user = User::where('email', $request->username)->first();

        // return $user->roles[0]->name;

        if ($user->roles[0]->name!="Fan") {

            throw new StoreResourceFailedException('Error! Only Fans Are Allowed In This App');
        }


        if ($request->app_id!=2) {

            throw new StoreResourceFailedException('Error! Only Fans Are Allowed In This App');
        }
        $request->request->add([
        'username' => $request->username,
        'password' => $request->password,
        'grant_type' => 'password',
        'client_id' => 2,
        'client_secret' => "FANSINC",
        'scope' => '']);

        $newRequest = Request::create('/oauth/token', 'post');

        $tokenres =  Route::dispatch($newRequest)->getContent();

        $token = json_decode($tokenres,true);

        return $token;
}


}
