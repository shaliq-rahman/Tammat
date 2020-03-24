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
use Propaganistas\LaravelPhone\PhoneNumber;

class EditController extends AccountBaseController
{
	use VerificationTrait;
	
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
	public function updateDetails(UserRequest $request)
	{
		// Check if these fields has changed
		$emailChanged = $request->filled('email') && $request->input('email') != auth()->user()->email;
		$phoneChanged = $request->filled('phone_number') && phoneFormatInt($request->input('phone_number'), auth()->user()->country_code) != auth()->user()->phone;
		$usernameChanged = $request->filled('username') && $request->input('username') != auth()->user()->username;
		
		// Conditions to Verify User's Email or Phone
		$emailVerificationRequired = config('settings.mail.email_verification') == 1 && $emailChanged;
		$phoneVerificationRequired = $phoneChanged;

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
		
		
		
		$userdata = User::find(auth()->user()->id);
		$data['user'] = $userdata;
		$toemail = !empty($userdata->email)?$userdata->email:'';
	    $from_email = 'admin@dealnotdeal.com';
        $fromname = 'Deal Not Deal';
        // \Mail::send('emails.user.updatesetting', $data, function($message) use ($from_email, $fromname,$toemail)
        // {
        //     $message->to($toemail);
        //     $message->subject('Profile Change Notification');
        //     $message->from($from_email, $fromname);
        // });    	
		
		
		
		// Message Notification & Redirection
		flash(t("Your details account has updated successfully."))->success();
		$nextUrl = config('app.locale') . '/account';
		
	
        
		
		
		
		// Send Email Verification message
		if ($emailVerificationRequired) {
			$this->sendVerificationEmail($user);
			$this->showReSendVerificationEmailLink($user, 'user');
		}
		
		// Send Phone Verification message
		if ($phoneVerificationRequired) {
			// Save the Next URL before verification
			session(['itemNextUrl' => $nextUrl]);
			
			// $this->sendVerificationSms($user);
			// $this->showReSendVerificationSmsLink($user, 'user');

			session(['regUser' => $user]);
            session(['phone' => PhoneNumber::make($user->phone, $user->country_code)->formatE164()]);
			
			// Go to Phone Number verification
			$nextUrl = config('app.locale') . '/verify/user/phone/';
		}
		// dd($nextUrl);
		
		// Redirection
		return redirect($nextUrl);
	}
	
	/**
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function updateSettings(Request $request)
	{
    
		// Validation
		if ($request->filled('password')) {
			$rules = ['password' => 'between:6,60|dumbpwd|confirmed'];
			$this->validate($request, $rules);
		}
		
		// Get User
	
		$user = User::find(auth()->user()->id);
	    
		// Update
		$user->disable_comments = (int)$request->input('disable_comments');
		if ($request->filled('password')) {
			$user->password = Hash::make($request->input('password'));
		}
		
		// Save
		$user->save();
		
	
    	$data['user'] = $user;
    	$toemail = $user->email;
	    $from_email = 'admin@dealnotdeal.com';
        $fromname = 'Deal Not Deal';
        \Mail::send('emails.user.updatesetting', $data, function($message) use ($from_email, $fromname,$toemail)
        {
            $message->to($toemail);
            $message->subject('Profile Change Notification');
            $message->from($from_email, $fromname);
        });    
        
		
		
		
		
		
		
		
		
		
		flash(t("Your settings account has updated successfully."))->success();
		
		return redirect(config('app.locale') . '/account');
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
