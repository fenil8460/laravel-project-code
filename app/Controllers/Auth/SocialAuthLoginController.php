<?php

namespace App\Controllers\Auth;

use App\Events\LoginActivity;
use App\Http\Controllers\Controller as Controller;;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthLoginController extends Controller
{

    public function redirect($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }


    public function handleCallback($provider)
    {

        $userSocial = Socialite::driver($provider)->stateless()->user();
        $user = User::where(['email' => $userSocial->getEmail()])->first();
        if($user){
            $user->provider = $provider;
            $user->provider_id = $userSocial->id;
            $user->save();
            Auth::login($user);
        }else{
        $user = User::create([
                'name'          => $userSocial->getName(),
                'email'         => $userSocial->getEmail(),
                'password'      => bcrypt(rand(10, 999)),
                'provider_id'   => $userSocial->getId(),
                'provider'      => $provider,
        ]);
        Auth::login($user);
        }
        event(new LoginActivity('login'));
        $user = Auth::user();
        if($user->active == 0 || $user->is_approved == 0)
        {
            return $this->error("User Is Deactivated, Can't Login",404);
        }
        $token =  $user->createToken('MyApp')-> accessToken;
        return redirect()->to('https://staging.web.ogt.sebipay.com/login?user='.$user->email.'&token='.$token);
    }

    public function redirectTwitter($provider)
    {
        return Socialite::driver($provider)->redirect();
    }


    public function handleTwitterCallback($provider)
    {
        $twitterSocial =   Socialite::driver($provider)->user();
        $user = User::where(['email' => $twitterSocial->getEmail()])->first();
        if($user){
            $user->provider = $provider;
            $user->provider_id = $twitterSocial->id;
            $user->save();
            Auth::login($user);
        }else{
        $user = User::create([
                'name'          => $twitterSocial->getName(),
                'email'         => $twitterSocial->getEmail(),
                'password'      => bcrypt(rand(10, 999)),
                'provider_id'   => $twitterSocial->getId(),
                'provider'      => $provider,
        ]);
        Auth::login($user);
        }
        event(new LoginActivity('login'));
        $user = Auth::user();
        if($user->active == 0 || $user->is_approved == 0)
        {
            return $this->error("User Is Deactivated, Can't Login",404);
        }
        $token =  $user->createToken('MyApp')-> accessToken;
        return redirect()->to('https://staging.web.ogt.sebipay.com/login?user='.$user->email.'&token='.$token);
    }

}
