<?php

namespace App\Controllers;

use App\Http\Controllers\Controller as Controller;
use App\Models\Admin;
use App\Services\CompanyService;
use App\Services\RegisterService;
use App\Services\PasswordResetService;
use App\Services\WebhookService;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Client as OClient;
use App\Models\User;
use App\Events\LoginActivity;
use App\Traits\ResponseAPI;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Mail;
use App\Services\SmsMessageOutService;

class RegisterController extends Controller
{
    use ResponseAPI;
    public $successStatus = 200;
    protected $company_service;

    public function __construct()
    {
        $this->company_service = new CompanyService;
        $this->register_service = new RegisterService;
        $this->admin_service = new AdminService;
        $this->password_reset_service = new PasswordResetService;
        $this->webhook_service = new WebhookService;
        $this->sms_messageout_service = new SmsMessageOutService;
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required',
            'companyName' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['uu_id'] = (string)Str::uuid();
        $password = $input['password'];
        $input['password'] = bcrypt($input['password']);
        $user = $this->register_service->create($input);
        $data = [];
        $data = [
            'user_id' => $user->id,
            'name' => $request->companyName,
            'nick_name' => $user->name,
        ];
        $success['company']= $this->company_service->store($data);
        // $oClient = OClient::where('password_client', 1)->first();
        // return $this->getTokenAndRefreshToken($oClient, $user->email, $password);
        $success['token'] =  $user->createToken('OnlineGroupText')->accessToken;
        $success['username'] =  $user->name;
        $success['company_name'] = $request->companyName;
        $success['message'] = "User and Company registered successfully";
        return $this->success($success);
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            event(new LoginActivity('login'));
            $user = Auth::user();
            $company = $this->company_service->getCompanyByUser($user->id);
            if($user->active == 0 || $user->is_approved == 0)
            {
                return $this->error("User Is Deactivated, Can't Login",404);
            }
            if(count($company) == 1){
                $success['company_count'] =  count($company);
                $success['company_uu_id'] =  $company[0]->uu_id;
            }else{
                $success['company_count'] =  count($company);
            }
            $user_role='user';
            $success['token'] =  $user->createToken('user OGT')->accessToken;
            $success['name'] =  $user->name;
            $success['userRole'] =  $user_role;
            $success['expires_at'] = $user->createToken('user OGT')->token->expires_at;
            $success['roles'][] = "customer";
            $success['message'] = "Customer Login Successfully";
            return $this->success($success);
        }
        else{
            return $this->error([
                'auth'=>'Unauthorised',
            ],404);
        }
    }

    public function socialLogin(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();
        if($user){
            Auth::login($user);
            $success['token'] =  $user->createToken('OnlineGroupText')->accessToken;
            $success['name'] =  $user->name;
            $success['expires_at'] = $user->createToken('OnlineGroupText')->token->expires_at;
            $success['roles'][] = "customer";
            $success['message'] = "Customer Login Successfully";
            return $this->success($success);
        }
        else{
            return $this->error([
                'auth'=>'Unauthorised',
            ],404);
        }
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'allDevice' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = Auth::user();
        event(new LoginActivity('logout'));
        if($request->allDevice)
        {
            $user->tokens->each(function ($token){
                $token->delete();
            });

            return response()->json(['message' => 'Logged Out from all devices !!'] , 200);
        }
        $userToken = $user->token();
        $userToken->delete();
        return response()->json(['message' => 'Logged Out Successfully !!'] , 200);
    }

    public function userLoginByAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'admin_id' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        $user = User::where('email', $request->email)->first();
        $admin_user = $this->admin_service->findAdminUser($request->admin_id);
        if ($user) {
            event(new LoginActivity(['admin_id'=>$admin_user->id,'event'=>'admin-user-login','user_id'=>$user->id]));
            if ($request->password == $user->password)
            {
                $user_role='user';
            $success['token'] =  $user->createToken('user OGT')->accessToken;
            $success['name'] =  $user->name;
            $success['userRole'] =  $user_role;
            $success['expires_at'] = $user->createToken('user OGT')->token->expires_at;
            $success['roles'][] = "customer";
            $success['message'] = "Customer Login Successfully";
            return $this->success($success);
            }
            else
            {
                return $this->error([
                    'password'=>'Password Wrong',
                ],404);
            }
        }
        else{
            return $this->error([
                'email'=>'Email Wrong',
            ],404);
        }

    }

    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        $admin_user = Admin::where('email', '=', $request->email)->first();

        if (!$admin_user) {
            return $this->error([
                'email'=>'Login Fail, please check Email Id',
            ],404);
        }
        if (!Hash::check($request->password, $admin_user->password)) {
            return $this->error([
                'password'=>'Login Fail, please check Password',
            ],404);
        }
        $success['token'] =  $admin_user->createToken('admin OGT')->accessToken;
        $success['name'] =  $admin_user->name;
        $success['id'] =  $admin_user->uu_id;
        $success['expires_at'] = $admin_user->createToken('admin OGT')->token->expires_at;
        $roles = $admin_user->roles;

        foreach($roles as $role)
        {
            $success['roles'][] = $role->name;
        }
        $permissions = $admin_user->allPermissions();
        foreach($permissions as $permission)
        {
            $success['permissions'][] = $permission->name;
        }
        $success['message'] = "Admin Login Successfully";
        event(new LoginActivity(['admin_id'=>$admin_user->id,'event'=>'admin-login']));
        return $this->success($success);
    }

    public function adminBackDashboard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        $admin_user = Admin::where('email', '=', $request->email)->first();

        if (!$admin_user) {
            return $this->error('Login Fail, please check email id',404);
        }
        if ($request->password != $admin_user->password) {
            return $this->error('Login Fail, please check password',404);
        }
        $success['token'] =  $admin_user->createToken('admin OGT')->accessToken;
        $success['name'] =  $admin_user->name;
        $success['id'] =  $admin_user->uu_id;
        $success['expires_at'] = $admin_user->createToken('admin OGT')->token->expires_at;
        $roles = $admin_user->roles;

        foreach($roles as $role)
        {
            $success['roles'][] = $role->name;
        }
        $permissions = $admin_user->allPermissions();
        foreach($permissions as $permission)
        {
            $success['permissions'][] = $permission->name;
        }
        $success['message'] = "Admin Login Successfully";
        event(new LoginActivity(['admin_id'=>$admin_user->id,'event'=>'admin-login']));
        return $this->success($success);
    }

    public function adminLogout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'allDevice' => 'required'
        ]);

        if($validator->fails()){
            return $this->error($validator->errors());
        }

        $user = Auth::guard('admin')->user();
        event(new LoginActivity('admin-logout'));
        if($request->allDevice)
        {
            $user->tokens->each(function ($token){
                $token->delete();
            });
            return response()->json(['message' => 'Logged Out from all devices !!'] , 200);
        }
        $userToken = $user->token();
        $userToken->delete();
        return response()->json(['message' => 'Logged Out Successfully !!'] , 200);
    }

    // public function getTokenAndRefreshToken(OClient $oClient, $email, $password) {
    //     $oClient = OClient::where('password_client', 1)->first();
    //     $response = Http::asForm()->post('http://localhost:8000/api/oauth/token', [

    //             'grant_type' => 'password',
    //             'client_id' => $oClient->id,
    //             'client_secret' => $oClient->secret,
    //             'username' => $email,
    //             'password' => $password,
    //             'scope' => '*',

    //     ]);
    //     $result = json_decode((string) $response->getBody(), true);
    //     return response()->json($result, $this->successStatus);
    // }

    public function validatePasswordRequest(Request $request){
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'base_url' => 'required' ]);

            if ($validator->fails()) {
                return $this->error('Please enter Email and Base url');
            }
            $user = $this->register_service->findByEmail($request->email);

            if ($user == null) {
                return $this->error('User does not exist');
            }
            $data = [
                'email' => $request->email,
                'token' => Str::random(60),
                'created_at' => Carbon::now()
            ];
            $this->password_reset_service->create($data);
            $token_data = $this->password_reset_service->findByEmail($request->email);
            $base_url = $request->base_url;
            if ($this->sendResetEmail($request->email, $token_data->token,$base_url)) {
                return $this->success('A reset link has been sent to your email address.');
            } else {
                return $this->error('A Network Error occurred. Please try again.');
            }
    }

    public function sendResetEmail($email, $token,$base_url)
        {
        $user = $this->register_service->findByEmail($email);
        $link = $base_url . '/forgot-password/'. '?token=' . $token . '&email=' . urlencode($user->email);
            try {
            Mail::to($user->email)->send(new ForgotPassword($link));
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        public function resetPassword(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|confirmed',
                'token' => 'required' ]);

            if ($validator->fails()) {
                return $this->error('User does not exists');
            }

            $password = $request->password;
            $token_data = $this->password_reset_service->findByToken($request->token);
            if (!$token_data) return view('auth.passwords.email');

            $user = $this->register_service->findByEmail($token_data->email);
            if (!$user) return $this->error('Email not found');            ;
            $user->password = bcrypt($password);
            $user->update();

            $delete_token = $this->password_reset_service->delete($user->email);
            if ($delete_token) {
                return $this->success('Password successfully update');
            } else {
                return $this->error('A Network Error occurred. Please try again.');
            }

        }

    public function show(){
        $data = [];
        $user = $this->register_service->find(Auth::user()->id);
        if($user != null){
            $data = [
                "uu_id" => $user->uu_id,
                "name" => $user->name,
                "email" => $user->email,
                "email_verified_at" => $user->email_verified_at,
                "api_token" => $user->api_token,
                "remember_token" => $user->remember_token,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at,
                "provider" => $user->provider,
                "provider_id" => $user->provider_id,
                "provider_token" => $user->provider_token,
                "provider_refresh_token" => $user->provider_refresh_token,
                "active" => $user->active,
                "is_approved" => $user->is_approved,
            ];
            return $this->success($data);
        }else{
            return $this->error('User Not Fond');
        }
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|confirmed',
            'old_password' => 'required']);
            if ($validator->fails()) {
                return $this->error($validator->errors());
            }
        $data = $this->register_service->find(Auth::user()->id);
        if (!Hash::check($request->old_password, $data->password)) {
            return $this->error('please check your old password',404);
        }
        $user_data = [
            "name"=>$request->name,
            "password"=>bcrypt($request->password)
        ];
        $update_user = $this->register_service->update($user_data);
        return $this->success($update_user);
    }

}
