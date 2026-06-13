<?php

/**

 * LaraClassified - Geo Classified Ads CMS

 * Copyright (c) BedigitCom. All Rights Reserved

 *

 * Website: http://www.bedigit.com

 *

 * LICENSE

 * -------

 * This software is furnished under a license and may be used and copied

 * only in accordance with the terms of such license and with the inclusion

 * of the above copyright notice. If you Purchased from Codecanyon,

 * Please read the full License from here - http://codecanyon.net/licenses/standard

 */



namespace App\Http\Controllers\Auth;



use App\Http\Controllers\FrontController;

use App\Http\Requests\LoginRequest;

use App\Events\UserWasLogged;

use App\Models\User;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Event;

use Torann\LaravelMetaTags\Facades\MetaTag;

use DB;

class LoginController extends FrontController

{

    use AuthenticatesUsers;

    

    /**

     * Where to redirect users after login / registration.

     *

     * @var string

     */

    // If not logged in redirect to

    protected $loginPath = 'login';

    

    // The maximum number of attempts to allow

    protected $maxAttempts = 5;

    

    // The number of minutes to throttle for

    protected $decayMinutes = 15;

    

    // After you've logged in redirect to

    protected $redirectTo = 'account';

    

    // After you've logged out redirect to

    protected $redirectAfterLogout = '/';

    

    /**

     * LoginController constructor.

     */

    public function __construct()

    {

        parent::__construct();

        

        $this->middleware('guest')->except(['except' => 'logout']);

	

		// Set default URLs

		$isFromLoginPage = str_contains(url()->previous(), '/' . trans('routes.login'));

		$this->loginPath = $isFromLoginPage ? config('app.locale') . '/' . trans('routes.login') : url()->previous();

		$this->redirectTo = $isFromLoginPage ? config('app.locale') . '/account' : url()->previous();

		// $this->redirectAfterLogout = config('app.locale') . '/' . trans('routes.login');

		$this->redirectAfterLogout = config('app.locale');

		

		// Get values from Config

		$this->maxAttempts = (int)config('settings.security.login_max_attempts', $this->maxAttempts);

		$this->decayMinutes = (int)config('settings.security.login_decay_minutes', $this->decayMinutes);

    }

    

    // -------------------------------------------------------

    // Laravel overwrites for loading LaraClassified views

    // -------------------------------------------------------

    



    
    /**
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
    public function submit_app(Request $request)
    {
        
        if ($request->close_account_confirmation == 1) {
            // Get User
            $user = User::find($request->userid);
            if(!empty($user->id)){
                // Don't delete admin users
                if ($user->is_admin or $user->is_admin == 1) { 
                    return response()->json(['results'=>'Admin users cannot be deleted by this way.']);
                } 
                // Delete User
                $user->delete();                            
                return response()->json(['results'=>'Your account has been deleted. We regret you. Re-register if that is a mistake .']);


            }else{
            return response()->json(['results'=>'Your account has been deleted Or not Active . We regret you. Re-register if that is a mistake .']);

            }

    }
 
       
    }	

    



    /**

     * Show the application login form.

     *

     * @return \Illuminate\Http\Response

     */

    public function showLoginForm()

    {

        // Remembering Login

        if (Auth::viaRemember()) {

            return redirect()->intended($this->redirectTo);

        }

        

        // Meta Tags

        MetaTag::set('title', getMetaTag('title', 'login'));

        MetaTag::set('description', strip_tags(getMetaTag('description', 'login')));

        MetaTag::set('keywords', getMetaTag('keywords', 'login'));

        

        return view('auth.login');

    }

    

	/**

	 * @param LoginRequest $request

	 * @return $this|\Illuminate\Http\RedirectResponse|void

	 * @throws \Illuminate\Validation\ValidationException

	 */

    public function login(LoginRequest $request)

    {

        // dd('hjbhj');

        // If the class is using the ThrottlesLogins trait, we can automatically throttle

        // the login attempts for this application. We'll key this by the username and

        // the IP address of the client making these requests into this application.

        if ($this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);

            

            return $this->sendLockoutResponse($request);

        }

        

        // Get the right login field

        $loginField = getLoginField($request->input('username'));

      //  echo Hash::make($request->input('password'));

        // Get credentials values

        $credentials = [

            $loginField => $request->input('username'),

            'password'  => $request->input('password'),

            'blocked'   => 0,

        ];

        if (in_array($loginField, ['email', 'phone'])) {

            $credentials['verified_' . $loginField] = 1;

        } else {

            $credentials['verified_email'] = 1;

            $credentials['verified_phone'] = 1;

        }

        

        // Auth the User

        if (Auth::attempt($credentials)) {

            

            // Update last user logged Date

            event(new UserWasLogged(User::find(Auth::user()->id)));

            if(!empty(Auth::user()->user_new))

            {

                return redirect()->intended($this->redirectTo);    

            }

            else

            {

                $userfine = User::find(Auth::user()->id);

                $userfine->user_new = 1;

                $userfine->save();

                return redirect(config('app.locale') .'/account');    

            }

            

            

            

            

        }

        

        // If the login attempt was unsuccessful we will increment the number of attempts

        // to login and redirect the user back to the login form. Of course, when this

        // user surpasses their maximum number of attempts they will get locked out.

        $this->incrementLoginAttempts($request);

        

        // Check and retrieve previous URL to show the login error on it.

        if (session()->has('url.intended')) {

			$this->loginPath = session()->get('url.intended');

		}

        

        return redirect($this->loginPath)->withErrors(['error' => trans('auth.failed')])->withInput();

    }

    

	

	public function login_app(LoginRequest $request)

    {

        // If the class is using the ThrottlesLogins trait, we can automatically throttle

        // the login attempts for this application. We'll key this by the username and

        // the IP address of the client making these requests into this application.

        // if ($this->hasTooManyLoginAttempts($request)) {

        //     $this->fireLockoutEvent($request);

            

        //     return response()->json(['results'=>"Too many login attempts. Please try again in 606 seconds."]);

        // }

        

        // Get the right login field

        $loginField = getLoginField($request->input('username'));

        //print_r($loginField);

        // Get credentials values

        //echo Hash::make($request->input('password'));

        $pass = Hash::make($request->input('password'));

        $credentials = [

            'username' => $request->input('username'),

            'password'  => $request->input('password'),

            'blocked'   => 0,

        ];

        $u = $request->input('username');

        $p = $request->input('password');

        $getusernamedetail = \DB::table('users')->where('username', '=', $u)->first();

        $getemaildetail = \DB::table('users')->where('email', '=', $u)->first();

        $this->incrementLoginAttempts($request);

        // Auth the User

       // if (Auth::attempt($credentials)) {

       

       if($getusernamedetail || $getemaildetail){

           //event(new UserWasLogged(User::find(Auth::user()->id)));

           if(!empty($getusernamedetail)){

		    $userId = $getusernamedetail->id;

		    if(Hash::check($request->input('password'), $getusernamedetail->password)){

                	DB::table('users')->where("users.id", '=',  $userId)->update(['users.fcm_id'=> $request->input('fcm_id')]);

					$auth_token= md5(microtime() . mt_rand());

					DB::table('users')->where("users.id", '=',  $userId)->update(['users.auth_token'=> $auth_token]);

           // $userData = User::find($userId);

            $userData = array(

            "id" =>(int)$getusernamedetail->id, 

            "country_code" =>$getusernamedetail->country_code,

			"auth_token" =>$auth_token,

            "language_code" =>$getusernamedetail->language_code,

            "user_type_id" =>$getusernamedetail->user_type_id,

            "gender_id" =>$getusernamedetail->gender_id,

            "profile_image"=>'https://www.tmmat.com/ProfilePictures/'.$getusernamedetail->profile_image,              

            "name" =>$getusernamedetail->name,

            "first_name" =>$getusernamedetail->first_name,

            "last_name" =>$getusernamedetail->last_name,

            "dob" =>$getusernamedetail->dob,

            "newsletter" =>$getusernamedetail->newsletter,

            "state" =>$getusernamedetail->state,

            "city" =>$getusernamedetail->city,

            "zipcode" =>$getusernamedetail->zipcode,

            "address" =>$getusernamedetail->address,            

            "phone" =>$getusernamedetail->phone,            

            "username" =>$getusernamedetail->username,

            "email" =>$getusernamedetail->email,

            "password" =>$getusernamedetail->password,

            "remember_token" =>$getusernamedetail->remember_token,

            "receive_newsletter" =>$getusernamedetail->receive_newsletter,            

            "email_token" =>$getusernamedetail->email_token,

            "phone_token" =>$getusernamedetail->phone_token,

            "verified_email" =>$getusernamedetail->verified_email,

            "verified_phone" =>$getusernamedetail->verified_phone,

            "blocked" =>$getusernamedetail->blocked,

            "closed" =>$getusernamedetail->closed,

            "user_new" =>$getusernamedetail->user_new,

            "last_login_at" =>date_create($getusernamedetail->last_login_at),

            "created_at" =>date_create($getusernamedetail->created_at),

            "updated_at" =>date_create($getusernamedetail->updated_at),

            "deleted_at" =>date_create($getusernamedetail->deleted_at),

            "fcm_id" =>$request->input('fcm_id'),

            "phone_hidden" =>$getusernamedetail->phone_hidden?$getusernamedetail->phone_hidden:0,

            "email_hidden" =>$getusernamedetail->email_hidden?$getusernamedetail->email_hidden:0,

            "profile_image_hidden" =>$getusernamedetail->profile_image_hidden?$getusernamedetail->profile_image_hidden:0,             

            "created_at_ta"=>""

            );

			

			

			

            return response()->json(['status'=>"1",'results'=>"Login Successful",'data' => $userData]);

		    }

            else{

                return response()->json(['status' => "0",'results'=>"Password not match"]);

            }

           }

           else{

              $userId = $getemaildetail->id;

              if(Hash::check($request->input('password'), $getemaildetail->password)){

                	DB::table('users')->where("users.id", '=',  $userId)->update(['users.fcm_id'=> $request->input('fcm_id')]);

					$auth_token= md5(microtime() . mt_rand());

					DB::table('users')->where("users.id", '=',  $userId)->update(['users.auth_token'=> $auth_token]);

					

            //$userData = User::find($userId);

            

             $userData = array(

            "id" => (int)$getemaildetail->id, 

            "country_code" =>$getemaildetail->country_code,

            "language_code" =>$getemaildetail->language_code,

			"auth_token" =>$auth_token,

            "user_type_id" =>$getemaildetail->user_type_id,

            "gender_id" =>$getemaildetail->gender_id,

            "name" =>$getemaildetail->name,

            "first_name" =>$getemaildetail->first_name,

            "last_name" =>$getemaildetail->last_name,

            "dob" =>$getemaildetail->dob,

            "newsletter" =>$getemaildetail->newsletter,

            "state" =>$getemaildetail->state,

            "city" =>$getemaildetail->city,

            "zipcode" =>$getemaildetail->zipcode,

            "address" =>$getemaildetail->address,

            "about" =>$getemaildetail->about,

            "phone" =>$getemaildetail->phone,

            "phone_hidden" =>$getemaildetail->phone_hidden,

            "username" =>$getemaildetail->username,

            "email" =>$getemaildetail->email,

            "password" =>$getemaildetail->password,

            "remember_token" =>$getemaildetail->remember_token,

            "is_admin" =>$getemaildetail->is_admin,

            "can_be_impersonated" =>$getemaildetail->can_be_impersonated,

            "disable_comments" =>$getemaildetail->disable_comments,

            "receive_newsletter" =>$getemaildetail->receive_newsletter,

            "receive_advice" =>$getemaildetail->receive_advice,

            "ip_addr" =>$getemaildetail->ip_addr,

            "provider" =>$getemaildetail->provider,

            "provider_id" =>$getemaildetail->provider_id,

            "email_token" =>$getemaildetail->email_token,

            "phone_token" =>$getemaildetail->phone_token,

            "verified_email" =>$getemaildetail->verified_email,

            "verified_phone" =>$getemaildetail->verified_phone,

            "blocked" =>$getemaildetail->blocked,

            "closed" =>$getemaildetail->closed,

            "user_new" =>$getemaildetail->user_new,

            "last_login_at" =>date_create($getemaildetail->last_login_at),

            "created_at" =>date_create($getemaildetail->created_at),

            "updated_at" =>date_create($getemaildetail->updated_at),

            "deleted_at" =>date_create($getemaildetail->deleted_at),

            "fcm_id" =>$getemaildetail->fcm_id,

            "created_at_ta"=>""

            );

            return response()->json(['status'=>1,'results'=>"success",'data' => $userData]);

              }

            else{

                return response()->json(['status' => "0",'results'=>"Password not match"]);

            }

           }

		

        }

		else

		{

		return response()->json(['status' => "0",'results'=>trans('auth.failed')]);

		}

        

        // If the login attempt was unsuccessful we will increment the number of attempts

        // to login and redirect the user back to the login form. Of course, when this

        // user surpasses their maximum number of attempts they will get locked out.

        

        

        // Check and retrieve previous URL to show the login error on it.

        

        

    }

    

    /**

     * @param Request $request

     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

     */

    public function logout(Request $request)

    {

        // Get the current Country

        if (session()->has('country_code')) {

            $countryCode = session('country_code');

        }

        

        // Remove all session vars

        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        

        // Retrieve the current Country

        if (isset($countryCode) && !empty($countryCode)) {

            session(['country_code' => $countryCode]);

        }

        

        $message = t('You have been logged out.') . ' ' . t('See you soon.');

        flash($message)->success();

        

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');

    }

    

    

    public function savenewsletteremail(Request $request)

    {

        $email = $request->newsLetterVal;

        

        $getdetail = DB::table('newsletter')

           ->where('news_letter_email', '=', $email)

           ->get();

        

       if(empty(count($getdetail)))

       {

           $responce = \DB::table('newsletter')->insert(

                ['news_letter_email' => $email, 

                 'created_at' => date('Y-m-d H:i:s'),

                 'updated_at' => date('Y-m-d H:i:s'),

                ]

            );

       }

        

        $json['msg'] = t('Newsletter successfully subscribe');

        echo json_encode($json);

        

    }



}

