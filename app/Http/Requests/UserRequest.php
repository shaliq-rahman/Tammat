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


class UserRequest extends Request
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return auth()->check();
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		// Check if these fields has changed
		$emailChanged = ($this->input('email') != auth()->user()->email);
		$phoneChanged = ($this->input('phone_number') != auth()->user()->phone);
		$usernameChanged = ($this->filled('username') && $this->input('username') != auth()->user()->username);
		
		// Validation Rules
		$rules = [
			//'gender_id'    => 'required|not_in:0',
			'gender_id'    => 'required',
			//'user_type_id' => 'required|not_in:0',
			'user_type_id' => 'required',
			'first_name' => 'required',
            'last_name' => 'required',
// 			'username'     => 'valid_username|allowed_username|between:3,100',
			'phone_number' => 'numeric|required',
			'email'        => 'required|email|whitelist_email|whitelist_domain',
			'city' => 'required',
			//'name'         => 'required|max:100',
			
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
			$rules['username'] = 'required|unique:users,username|' . $rules['username'];
		}
		
		
		if($this->input('zipcode')){
			$rules['zipcode'] = 'numeric';
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
