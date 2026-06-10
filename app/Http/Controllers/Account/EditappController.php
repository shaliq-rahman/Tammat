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



namespace App\Http\Controllers\Account;



use App\Http\Controllers\Auth\Traits\VerificationTrait;

use App\Http\Requests\UserRequest;

use App\Models\Scopes\VerifiedScope;

use App\Models\UserType;

use Creativeorange\Gravatar\Facades\Gravatar;

use App\Models\Post;

use App\Models\SavedPost;

use App\Models\Gender;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;

use Torann\LaravelMetaTags\Facades\MetaTag;

use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;

use App\Helpers\Localization\Country as CountryLocalization;

use App\Models\User;



class EditappController extends AccountappBaseController

{

	//use VerificationTrait;

	

	/**

	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View

	 */

	public function index()

	{

		$data = [];

		

		$data['countries'] = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());

		$data['genders'] = Gender::trans()->get();

		$data['userTypes'] = UserType::all();

		$data['gravatar'] = (!empty(auth()->user()->email)) ? Gravatar::fallback(url('images/user.jpg'))->get(auth()->user()->email) : null;

		

		// Mini Stats

		$data['countPostsVisits'] = DB::table('posts')

			->select('user_id', DB::raw('SUM(visits) as total_visits'))

			->where('country_code', config('country.code'))

			->where('user_id', auth()->user()->id)

			->groupBy('user_id')

			->first();

		$data['countPosts'] = Post::currentCountry()

			->where('user_id', auth()->user()->id)

			->count();

		$data['countFavoritePosts'] = SavedPost::whereHas('post', function ($query) {

			$query->currentCountry();

		})->where('user_id', auth()->user()->id)

			->count();

		

		// Meta Tags

		MetaTag::set('title', t('My account'));

		MetaTag::set('description', t('My account on :app_name', ['app_name' => config('settings.app.name')]));

		

		return view('account.edit', $data);

	}

	

	/**

	 * @param UserRequest $request

	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

	 */

 





	public function updateDetails_app(UserRequest $request)

	{

		// Check if these fields has changed

		$emailChanged = $request->filled('email') && $request->input('email') != auth()->user()->email;

		$phoneChanged = $request->filled('phone_number') && phoneFormatInt($request->input('phone_number'), auth()->user()->country_code) != auth()->user()->phone;

		$usernameChanged = $request->filled('username') && $request->input('username') != auth()->user()->username;

		

		// Conditions to Verify User's Email or Phone

		$emailVerificationRequired = config('settings.mail.email_verification') == 1 && $emailChanged;

		$phoneVerificationRequired = $phoneChanged;

		$user_ph = User::select('phone_number')->where('user_id',auth()->user()->id)->first();

		

		// Get User

		$user = User::withoutGlobalScopes([VerifiedScope::class])->find(auth()->user()->id);		

		// Update User

		$input = $request->only($user->getFillable());

		foreach ($input as $key => $value) {

			if (in_array($key, ['email']) && empty($value)) {

				continue;

			}

			

			$user->{$key} = $value;

		}

		$user->phone = $request->input('phone_number');

		$user->phone_hidden = $request->input('phone_hidden');

		$user->country_code = $request->input('country_code');

		if($user_ph->phone_number!=$request->input('phone_number'))

		{

			$user->verified_phone = 0;

		}

		// Email verification key generation

		if ($emailVerificationRequired) {

			$user->email_token = md5(microtime() . mt_rand());

			$user->verified_email = 0;

		}

		

		// Phone verification key generation

		if ($phoneVerificationRequired) {

			$user->phone_token = mt_rand(100000, 999999);

			$user->verified_phone = 0;

		}

		

		// Don't logout the User (See User model)

		if ($emailVerificationRequired || $phoneVerificationRequired) {

			session(['emailOrPhoneChanged' => true]);

		}

		

		// Save

		$user->save();

		 

		return response()->json(['results'=>'Profile has been updated successfully']);



	}

	



 

	


    /**
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
    public function submit_appxxxxx()
    {
        
        if ($request->close_account_confirmation == 1) {
            // Get User
            $user = User::findOrFail($request->userid);

            // Don't delete admin users
            if ($user->is_admin or $user->is_admin == 1) { 
                return response()->json(['results'=>'Admin users cannot be deleted by this way.']);
            } 

            // Delete User
            $user->delete();
                        
            return response()->json(['results'=>'Your account has been deleted. We regret you. Re-register if that is a mistake .']);


        }
        
       
    }	

	 

	public function updateDetails(Request $request)

	{

		//$userdata = User::where('id', '=', $request->userid)->first();

		$userdata = DB::table('users')

			->select('*')

			->where('id', $request->userid)

			->first();

		// Check if these fields has changed

		//print_r($userdata);

		if($userdata === null)

		{

		return response()->json(['results'=>'User is not existing','id'=>$request->userid]);

		}

		else

		{

		/*echo $userdata->email;

		$emailChanged = ($request->email != $userdata->email);

		$phoneChanged = ($request->phone != $userdata->phone);

		$usernameChanged = ($request->username != $userdata->username);

		

		// Validation Rules

		$rules = [

			'gender_id'    => 'required|not_in:0',

			'user_type_id' => 'required|not_in:0',

			'first_name' => 'required',

            'last_name' => 'required',

			'username'     => 'valid_username|allowed_username|between:3,100',

			'phone'        => 'required|max:20',

			'email'        => 'required|email|whitelist_email|whitelist_domain',

			'city' => 'required',

			//'name'         => 'required|max:100',

			

        ];

		

				

		if ($phoneChanged) {

			$rules['phone'] = 'unique:users,phone|' . $rules['phone'];

		}

		

		// Email

		if ($emailChanged) {

			$rules['email'] = 'unique:users,email|' . $rules['email'];

		}

		

		// Username

		if ($usernameChanged) {

			$rules['username'] = 'required|unique:users,username|' . $rules['username'];

		}

		

		if($this->input('zipcode')){

			$rules['zipcode'] = 'numeric';

		}

			

		

		

		return response()->json(['results'=>$rules]);*/

		

	

	

	

	

		// Check if these fields has changed

		$emailChanged = $request->email;

		$phoneChanged = $request->phone;

		$usernameChanged = $request->username;

		

		// Conditions to Verify User's Email or Phone

		$emailVerificationRequired = config('settings.mail.email_verification') == 1 && $emailChanged;

		$phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $phoneChanged;

		

		// Get User

		$user = User::withoutGlobalScopes([VerifiedScope::class])->find($request->userid);

		

		// Update User

		$input = $request->only($user->getFillable());

		foreach ($input as $key => $value) {

			if (in_array($key, ['email', 'phone', 'username']) && empty($value)) {

				continue;

			}

			$user->{$key} = $value;

		}

		

		$user->phone_hidden = $request->phone_hidden;

		$user->country_code = $request->country_code;

		

		// Email verification key generation

		/*if ($emailVerificationRequired) {

			$user->email_token = md5(microtime() . mt_rand());

			$user->verified_email = 0;

		}

		

		// Phone verification key generation

		if ($phoneVerificationRequired) {

			$user->phone_token = mt_rand(100000, 999999);

			$user->verified_phone = 0;

		}*/

		

		

		

		// Save

		$user->save();

		

		return response()->json(['results'=>'Profile has been updated successfully']);

	

	}

	}

	

	

	

	

	/**

	 * @param Request $request

	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

	 */

	public function updateSettings(Request $request)

	{

   

		// Get User

	

		$user = User::find($request->userid);

	    

		

		

		

		return response()->json(['results'=>$user]);

		

	}

	

	/**

	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View

	 */

	public function updatePreferences()

	{

		$data = [];

		

		return view('account.edit', $data);

	}

}

