<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Auth\Traits\VerificationTrait;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RegisterApiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\UserNotification;
use App\Mail\WelcomeEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Helpers\Ip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use App\Models\Gender;
use App\Models\UserType;
use App\Models\User;
use App\Models\Newsletter;
use App\Http\Controllers\FrontController;
use Torann\LaravelMetaTags\Facades\MetaTag;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;
use LaravelLocalization;
use Propaganistas\LaravelPhone\PhoneNumber;

class RegisterController extends FrontController

{

	use RegistersUsers, VerificationTrait;

	

	/**

	 * Where to redirect users after login / registration.

	 *

	 * @var string

	 */

	protected $redirectTo = '/account';

	

	/**

	 * @var array

	 */

	public $msg = [];

	

	/**

	 * RegisterController constructor.

	 */

	public function __construct()

	{

		parent::__construct();

		

		// From Laravel 5.3.4 or above

		$this->middleware(function ($request, $next) {

			$this->commonQueries();

			

			return $next($request);

		});

	}

	

	/**

	 * Common Queries

	 */

	public function commonQueries()

	{

		$this->redirectTo = config('app.locale') . '/account';

	}

	




    /**
	 * Show the form the create a new user account.
	 *
	 * @return View
	 */

	public function showCloseAccount()

	{
		$data = [];
        // References
		$data['countries'] = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
		$data['genders'] = Gender::trans()->get();
		$data['userTypes'] = UserType::all();
		// Meta Tags
		MetaTag::set('title', getMetaTag('title', 'register'));
		MetaTag::set('description', strip_tags(getMetaTag('description', 'register')));
		MetaTag::set('keywords', getMetaTag('keywords', 'register'));
		return view('auth.register.closeAccount', $data);
	}



     /**
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
    public function saveCloseAccount(Request $request)
    {
        
        if ($request->close_account_confirmation == 1) {
            // Get User
            $user = User::findOrFail($request->user_id);

            // Don't delete admin users
            if ($user->is_admin or $user->is_admin == 1) { 
                return response()->json(['results'=>'Admin users cannot be deleted by this way.']);
            } 

            // Delete User
            $user->delete();
                        
            return response()->json(['results'=>'Your account has been deleted. We regret you. Re-register if that is a mistake .']);


        }
        
       
    }



	/**
	 * Show the form the create a new user account.
	 *
	 * @return View
	 */

	public function showRegistrationForm()

	{
		$data = [];
        // References
		$data['countries'] = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
		$data['genders'] = Gender::trans()->get();
		$data['userTypes'] = UserType::all();
		// Meta Tags
		MetaTag::set('title', getMetaTag('title', 'register'));
		MetaTag::set('description', strip_tags(getMetaTag('description', 'register')));
		MetaTag::set('keywords', getMetaTag('keywords', 'register'));
		return view('auth.register.index', $data);
	}

	

	/**

	 * Register a new user account.

	 *

	 * @param RegisterRequest $request

	 * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

	 */







	public function testfire()

	{

	    

	    

	    dd(LaravelLocalization::getLocalizedURL('ar','asdasdaasdasd.com'));

	dd(LaravelLocalization::getSupportedLocales());

	}


   /* to remove it abdelhay 23-11-2024

    public function register(RegisterRequest $request)
    {
        // Step 1: Validate Input
        $request->validate([
            'email' => 'required|email', // Includes DNS check to verify email domain
            'phone_number' => 'required|string',
            'password' => 'required|min:8|confirmed',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);
    

        // Skip mail in local environment (no SMTP available)
        if (!app()->environment('local')) {
            try { Mail::send(new WelcomeEmail('abdelhy.reda1@gmail.com', $request->first_name)); } catch (\Exception $e) { logger()->error('WelcomeEmail failed: ' . $e->getMessage()); }
        }


        

 


    
        // Conditions to Verify User's Email or Phone
        $emailVerificationRequired = config('settings.mail.email_verification') == 1 && $request->filled('email');
        $phoneVerificationRequired = !app()->environment('local') && $request->filled('phone_number');
    
        // Create New User
        $user = new User();
        $user->fill($request->only($user->getFillable()));
    
        // Additional User Fields
        $user->user_type_id = $request->input('user_type_id');
        $user->country_code = config('country.code') ?? config('settings.geo_location.default_country_code') ?? 'KW';
        $user->password = Hash::make($request->input('password'));
        $user->phone = $request->input('phone_number');
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->city = $request->input('city');
        $user->no_points = config('settings.app.start_user_points');
        $user->language_code = config('app.locale');
        $user->ip_addr = Ip::get();
        $user->name = $user->first_name . ' ' . $user->last_name;
    
        if ($request->input('dob')) {
            $user->dob = $request->input('dob');
        }
    
        // Email verification key generation
        if ($emailVerificationRequired) {
            $user->email_token = md5(microtime() . mt_rand());
            $user->verified_email = 0; // Email is not verified yet
        }
    
        // Mobile activation key generation
        if ($phoneVerificationRequired) {
            $user->phone_token = mt_rand(100000, 999999);
            $user->verified_phone = 0; // Phone is not verified yet
        }
    
        // Save User
        $user->save();
    
        // Subscribe to newsletter if requested
        if ($request->input('newsletter') == 'yes') {
            Newsletter::create(['news_letter_email' => $request->input('email')]);
        }
    
        // Notify Admins of new registration (optional)
        if (!app()->environment('local') && config('settings.mail.admin_email_notification') == 1) {
            try {
                $admins = User::where('is_admin', 1)->get();
                foreach ($admins as $admin) {
                    Mail::send(new UserNotification($user, $admin));
                }
            } catch (\Exception $e) {
                logger()->error("Admin notification failed: " . $e->getMessage());
            }
        }
    
        // Redirect for Email or Phone Verification
        if ($emailVerificationRequired) {
            // Send Email Verification
            $this->sendVerificationEmail($user);
            session(['userNextUrl' => config('app.locale') . '/register/finish']);
            return redirect()->route('verification.notice')->with('message', 'Please verify your email address.');
        }
    
        if ($phoneVerificationRequired) {
            // Format phone number and send verification SMS
            $user->phone = PhoneNumber::make($user->phone, $user->country_code)->formatE164();
            session(['regUser' => $user, 'phone' => $user->phone]);
            return redirect(config('app.locale') . '/verify/user/phone/');
        }
    
        // Redirect to Account if no verification is required
        if (Auth::loginUsingId($user->id)) {
            return redirect()->intended(config('app.locale') . '/account')->with('message', 'Your account has been created.');
        }
    
        return redirect(config('app.locale') . '/register/finish');
    }
    
*/
 
        public function register(RegisterRequest $request)

        { 

        //dd($request); 
        // Conditions to Verify User's Email or Phone
        //abdelhy commit to disable email Verification $emailVerificationRequired = config('settings.mail.email_verification') == 1 && $request->filled('email');
       
       
       
      

     
       //  $request->email='abdelhy.reda1@gmail.com';

     
       /*
        try {
            // Attempt to send the email
            Mail::send(new WelcomeEmail($request->email, $request->first_name));
        
            // If successful, proceed with further actions (e.g., save user data, redirect)
            \Log::info('Email sent successfully to: ' . $request->email);
        } catch (\Exception $e) {
            // Log the error details for debugging
            \Log::error('Email sending failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
        
            // Redirect back with an error message
            return back()->withErrors([
                'email' => 'Failed to send email. Please check your email address and try again.',
            ]);
        }

*/

     
       
        $emailVerificationRequired=0;

            $phoneVerificationRequired = !app()->environment('local') && $request->filled('phone_number');



            // dd($request->filled('phone_number'));



            // New User

            $user = new User();

            $input = $request->only($user->getFillable());

            foreach ($input as $key => $value) {

                $user->{$key} = $value;

            }

                



            // if($request->input('user_type_id')) {

            $user->user_type_id   = $request->input('user_type_id');

            // } else {

                // $user->user_type_id   = '2';

            // }

            

            $user->country_code   = config('country.code') ?? config('settings.geo_location.default_country_code') ?? 'KW';

            $user->password       = Hash::make($request->input('password'));

            // $user->phone_hidden   = $request->input('phone_hidden');

            $user->gender_id      = $request->input('gender_id');

            $user->phone          = $request->input('phone_number');

            $user->first_name     = $request->input('first_name');

            $user->last_name      = $request->input('last_name');

            $user->city           = $request->input('city');

            $user->no_points           = config('settings.app.start_user_points');

            

            

            $user->language_code  = config('app.locale');

            

            

            

            $user->ip_addr        = Ip::get();

            $user->name = $request->input('first_name') . ' ' . $request->input('last_name');

            if($request->input('dob')){

                $user->dob = $request->input('dob');

            }



            //newsletter subscribe

            $newsletter = new Newsletter();

            $checknews = $request->input('newsletter');

            if($checknews == 'yes')

            {

                $newsletter->news_letter_email = $request->input('email');

                $newsletter->save();

            }



            // Email verification key generation

            if ($emailVerificationRequired) {

                $user->email_token = md5(microtime() . mt_rand());

                $user->verified_email = 1;

            }



            // Mobile activation key generation

        if ($phoneVerificationRequired) {

            $user->phone_token = mt_rand(100000, 999999);

                $user->verified_phone = 0;

        } else {

            $user->verified_phone = 1;

        }

    //        echo "<pre>";

            // print_r($user);

            // die;

            // Save

            $user->save();



            // Message Notification & Redirection

            $request->session()->flash('message', t("Your account has been created."));

            $nextUrl = config('app.locale') . '/register/finish';



            // Send Admin Notification Email

            if (!app()->environment('local') && config('settings.mail.admin_email_notification') == 1) {

                try {

                    $admins = User::where('is_admin', 1)->get();

                    if ($admins->count() > 0) {

                        foreach ($admins as $admin) {

                            Mail::send(new UserNotification($user, $admin));

                        }

                    }

                } catch (\Throwable $e) {

                    logger()->error("Admin notification failed: " . $e->getMessage());

                }

            }



            // Send Email Verification message

            // if ($emailVerificationRequired) {

                // Save the Next URL before verification

                // session(['userNextUrl' => $nextUrl]);



                // Send

                // $this->sendVerificationEmail($user);



                // Show the Re-send link

                // $this->showReSendVerificationEmailLink($user, 'user');

            // }



            // Send Phone Verification message

            if ($phoneVerificationRequired) {

                // dd('efs');

                // Save the Next URL before verification

                //session(['userNextUrl' => $nextUrl]);

                

                // Send

                // $this->sendVerificationSms($user);

                

                //Show the Re-send link

                //$this->showReSendVerificationSmsLink($user, 'user');

                //$user->phone = PhoneNumber::make($user->phone, $user->country_code)->formatE164();

                session(['regUser' => $user]);

                session(['phone' => PhoneNumber::make($user->phone, $user->country_code)->formatE164()]);

                

                // Go to Phone Number verification

                $nextUrl = config('app.locale') . '/verify/user/phone/';

            }



            // Redirect to the user area If Email or Phone verification is not required

            /*if (!$emailVerificationRequired && !$phoneVerificationRequired) {

                if (Auth::loginUsingId($user->id)) {

                    return redirect()->intended(config('app.locale') . '/account');

                }

            }

                 */

            // if (!$emailVerificationRequired) {

                // if (Auth::loginUsingId($user->id)) {

                    // return redirect()->intended(config('app.locale') . '/account');

                // }

            // }

            // Redirection  
            

             return redirect($nextUrl);
 
        }
   

	

	public function valid_mobile(Request $request)

    {

		 

		

		     $users = User::where('phone', $request->phone)->get();

                if ($users->count() > 0) {

                   $result['valid']=false;

                }else{

					$result['valid']=true;

					}

				

	    return response()->json(['results'=>$result]);

	}

	

	

	

	public function register_app(RegisterApiRequest $request)

    {
        // dd($request);

        // Conditions to Verify User's Email or Phone

        $emailVerificationRequired = config('settings.mail.email_verification') == 1 && $request->filled('email');

        $phoneVerificationRequired = $request->filled('phone_number');

        // New User

        $user = new User(); 

		$input = $request->only($user->getFillable());

		foreach ($input as $key => $value) {            

            if($key=="phone" && $value==""){

                $user->{$key} = "";

            }

            else{

                $user->{$key} = $value;

            } 

        }

        $pass = Hash::make($request->input('password'));

        $user->password       = $pass;

        $user->language_code  = config('app.locale');

        $user->ip_addr        = Ip::get();

        $user->name = $request->input('first_name') . ' ' . $request->input('last_name');

        $user->user_type_id   = $request->input('user_type_id');

        $user->gender_id      = $request->input('gender_id');

        $user->phone          = $request->input('phone_number');

        $user->country_code   = $request->input('country_code');

		$auth_token= md5(microtime() . mt_rand());

		$user->auth_token   = $auth_token;

        $user->email_token = md5(microtime() . mt_rand());

        $user->verified_email = 1;

        if ($phoneVerificationRequired) {

            $user->phone_token = mt_rand(100000, 999999);

            $user->verified_phone = 0;

        }

            if (isset($request->verified_phone)) {

                $user->phone_token = $request->input('phone_token');

                 $user->verified_phone = $request->input('verified_phone');

            }

        if(!$user->country_code) {$user->country_code = config('country.code') ?? config('settings.geo_location.default_country_code') ?? 'KW';}

        $user->save();

        

        //newsletter subscribe

        $newsletter = new Newsletter();

        $checknews = $request->input('newsletter');

        if($checknews == 'yes')

        {

            $newsletter->news_letter_email = $request->input('email');

            $newsletter->save();

        }





        // Send Admin Notification Email

        if (config('settings.mail.admin_email_notification') == 1) {

            try {

                // Get all admin users

                $admins = User::where('is_admin', 1)->get();

                if ($admins->count() > 0) {

                    foreach ($admins as $admin) {

                        Mail::send(new UserNotification($user, $admin));

                    }

                }

            } catch (\Exception $e) {

                flash($e->getMessage())->error();

            }

        }



        // Send Email Verification message

        if ($emailVerificationRequired) {            

            // $this->sendVerificationEmail($user);

            // Show the Re-send link

            // $this->showReSendVerificationEmailLink($user, 'user');

        }

        // Send Phone Verification message

        if ($phoneVerificationRequired) {

            // Save the Next URL before verification

            // session(['userNextUrl' => $nextUrl]);

            // Send

            // $this->sendVerificationSms($user);

            // Show the Re-send link

            // $this->showReSendVerificationSmsLink($user, 'user');

            // Go to Phone Number verification

            // $nextUrl = config('app.locale') . '/verify/user/phone/';

        } 

 

        if(!empty($user)){





		return response()->json(['results'=>"Your account has been created. Update your Profile", 'user'=>$user['message']]);

         }else{

            return response()->json(['results'=>"Your account Not created.Please check Again", 'user'=>$user]);

         }		

    }



	public function register_bk(RegisterRequest $request)

	{

        // Conditions to Verify User's Email or Phone

		$emailVerificationRequired = config('settings.mail.email_verification') == 1 && $request->filled('email');

		$phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $request->filled('phone');



        // New User

		$user = new User();

		$input = $request->only($user->getFillable());

		foreach ($input as $key => $value) {

			$user->{$key} = $value;

		}



        

		$user->country_code   = config('country.code');

		$user->password       = Hash::make($request->input('password'));

		$user->phone_hidden   = $request->input('phone_hidden');

		$user->ip_addr        = Ip::get();

		$user->name = $request->input('first_name') . ' ' . $request->input('last_name');



		//newsletter subscribe

		$newsletter = new Newsletter();

		$checknews = $request->input('newsletter');

		if($checknews == 'yes')

		{

		    $newsletter->news_letter_email = $request->input('email');

		    $newsletter->save();

		}

		

		// Email verification key generation

		if ($emailVerificationRequired) {

			$user->email_token = md5(microtime() . mt_rand());

			$user->verified_email = 0;

		}

		

		// Mobile activation key generation

		if ($phoneVerificationRequired) {

			$user->phone_token = mt_rand(100000, 999999);

			$user->verified_phone = 0;

		}

		

		// Save

		$user->save();

		

		// Message Notification & Redirection

		$request->session()->flash('message', t("Your account has been created."));

		$nextUrl = config('app.locale') . '/register/finish';

		

		// Send Admin Notification Email

		if (config('settings.mail.admin_email_notification') == 1) {

			try {

				// Get all admin users

				$admins = User::where('is_admin', 1)->get();

				if ($admins->count() > 0) {

					foreach ($admins as $admin) {

						Mail::send(new UserNotification($user, $admin));

					}

				}

			} catch (\Exception $e) {

				flash($e->getMessage())->error();

			}

		}

		

		// Send Email Verification message

		if ($emailVerificationRequired) {

			// Save the Next URL before verification

			session(['userNextUrl' => $nextUrl]);

			

			// Send

			$this->sendVerificationEmail($user);

			

			// Show the Re-send link

			$this->showReSendVerificationEmailLink($user, 'user');

		}

		

		// Send Phone Verification message

		if ($phoneVerificationRequired) {

			// Save the Next URL before verification

			session(['userNextUrl' => $nextUrl]);

			

			// Send

			$this->sendVerificationSms($user);

			

			// Show the Re-send link

			$this->showReSendVerificationSmsLink($user, 'user');

			

			// Go to Phone Number verification

			$nextUrl = config('app.locale') . '/verify/user/phone/';

		}

		

		// Redirect to the user area If Email or Phone verification is not required

		if (!$emailVerificationRequired && !$phoneVerificationRequired) {

			if (Auth::loginUsingId($user->id)) {

				return redirect()->intended(config('app.locale') . '/account');

			}

		}

		// Redirection

		return redirect($nextUrl);

	}



    

    

    public function register_approve_app(Request $request)

	{



       // user_id   

       //verified_phone





		 

		$userdata = DB::table('users')

			->select('*')

			->where('id', $request->user_id)

			->first();

		// Check if these fields has changed		 

		if($userdata === null)

		{

		return response()->json(['results'=>'User is not existing','id'=>$request->user_id]);

		}

		else

		{

           // if ($request->verified_phone==1) {

            

            $user = DB::table('users')->where('id', $request->user_id)

            ->update(['verified_phone' => 1]);



            // }



		

		

		return response()->json(['results'=>'Mobile Phone has been Approved successfully']);

	

	}

	}

    

	

	/**

	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View

	 */

	public function finish()

	{

		// Keep Success Message for the page refreshing

		session()->keep(['message']);

		if (!session()->has('message')) {

			return redirect(config('app.locale') . '/');

		}

		

		// Meta Tags

		MetaTag::set('title', session('message'));

		MetaTag::set('description', session('message'));

		

		return view('auth.register.finish');

	}

}

