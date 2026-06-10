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

namespace App\Http\Controllers;
use App\Http\Controllers\Traits\LocalizationTrait;
use App\Http\Controllers\Traits\RobotsTxtTrait;
use App\Http\Controllers\Traits\SettingsTrait;
use Illuminate\Support\Facades\Session;
use Request;

class FrontController extends Controller
{
	use LocalizationTrait, SettingsTrait, RobotsTxtTrait;
	
	public $request;
	public $data = [];
	
	/**
	 * FrontController constructor.
	 */
	public function __construct()
	{
	    if(empty(Session::get('currency'))){
            Session::put('currency', 'USD');
        }


 
    // From Laravel 5.3.4+
		$this->middleware(function ($request, $next)
		{
		    
			  $this->loadLocalizationData();
			 $this->checkDotEnvEntries();
			 $this->applyFrontSettings();
			$this->checkRobotsTxtFile();
			
			return $next($request);
		});
 

		
	}
}
