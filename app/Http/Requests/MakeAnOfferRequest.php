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

class MakeAnOfferRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		$rules = [
			'offer_price'           => 'max:20',
		];
	
		$description_text = $request->input('description_text');
		echo $description_text;
		die();
		
		if (isEnabledField('offer_price')) {
			if (isEnabledField('offer_price') && isEnabledField('email')) {
				$rules['offer_price'] = 'required_without:from_email|' . $rules['offer_price'];
			} else {
				$rules['offer_price'] = 'required|' . $rules['offer_price'];
			}
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
