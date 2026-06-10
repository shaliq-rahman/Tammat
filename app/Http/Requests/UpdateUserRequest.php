<?php
/**
 * LaraClassified - Geo Classified Ads Software
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

namespace App\Http\Requests;
use App\Models\User;

class UpdateUserRequest extends Request
{ 
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		
		$user = User::find($this->input('user_id'));
		
		// Check if these fields has changed
		$emailChanged = ($this->input('email') != $user->email);
		$phoneChanged = ($this->input('phone_number') !=$user->phone);
		$usernameChanged = ($this->input('username') && $this->input('username') != $user->username);
		

		// Validation Rules
		$rules = [
			 
			
			'first_name' => 'required',
            'last_name' => 'required',
			'phone_number' => 'numeric|required',
			'email'        => 'required|email|whitelist_email|whitelist_domain',
			'city' => 'required',
			 
			
        ];
		
		// phone_number
		if (config('settings.sms.phone_verification') == 1) {
			if ($this->filled('phone_number')) {
				$countryCode = $this->input('country_code', config('country.code'));
				if ($countryCode == 'UK') {
					$countryCode = 'GB';
				}
				$rules['phone_number'] = 'phone:' . $countryCode . ',mobile|' . $rules['phone_number'];
			}
		}
		
// 		if ($phoneChanged) {
// 			$rules['phone_number'] = 'unique:users,phone|' . $rules['phone_number'];
// 		}
		
		// Email
		if ($emailChanged) {
			$rules['email'] = 'unique:users,email|' . $rules['email'];
		}
		
		// Username
		if ($usernameChanged) {
			
			$rules['username'] = 'required|min:5|alpha_dash|unique:users,username';
			$rules['username'] = 'required|unique:users,username|' . $rules['username'];
		}
		
		
		 
		
		return $rules;
	}
	
	/**
	 * @return array
	 */
	public function messages()
	{
		$messages = [];
		
		return $messages;
	}
}
