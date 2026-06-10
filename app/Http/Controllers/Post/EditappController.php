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

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Auth\Traits\VerificationTrait;
use App\Http\Controllers\Post\Traits\CustomFieldTrait;
use App\Http\Requests\PostRequest;
use App\Models\PostType;
use App\Models\Category;
use App\Models\Package;
use App\Models\PaymentMethod;
use App\Http\Controllers\FrontController;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;
use App\Http\Controllers\Post\Traits\EditTrait;

class EditappController extends FrontController
{
    use EditTrait, VerificationTrait, CustomFieldTrait;

    public $data;
    public $msg = [];
    public $uri = [];

    /**
     * EditController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // From Laravel 5.3.4 or above
        /*$this->middleware(function ($request, $next) {
            $this->commonQueries();

            return $next($request);
        });*/
    }

    /**
     * Common Queries
     */
    public function commonQueries()
    {
        // References
        $data = [];

        // Get Countries
        $data['countries'] = $this->countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
        $this->countries = $data['countries'];
        view()->share('countries', $data['countries']);

        // Get Categories
        $data['categories'] = Category::trans()->where('parent_id', 0)->with([
            'children' => function ($query) {
                $query->trans();
            },
        ])->orderBy('lft')->get();
        view()->share('categories', $data['categories']);

        // Get Post Types
        $data['postTypes'] = PostType::trans()->get();
        view()->share('postTypes', $data['postTypes']);
    
        // Count Packages
        // $data['countPackages'] = Package::trans()->applyCurrency()->count();
        
        $data['countPackages'] = Package::where('translation_lang','en')->where('currency_code','USD')->where('active','1')->count();
        view()->share('countPackages', $data['countPackages']);
    
        // Count Payment Methods
        $data['countPaymentMethods'] = $this->countPaymentMethods;
    
        // Save common's data
        $this->data = $data;
    }

    /**
     * Show the form the create a new ad post.
     *
     * @param $postId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getForm($postId)
    {
        return $this->getUpdateForm($postId);
    }

    /**
     * Update ad post.
     *
     * @param $postId
     * @param PostRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postForm($postId, PostRequest $request)
    {
        
		// return response()->json(['results'=>"We have not received your payment. Payment cancelled.".$request->input('city_name')]);
		// dd($request->input());
        $getlocation = $this->getlocationcity($request->input('city_name'));
        
         $lat = !empty($getlocation['lat'])?$getlocation['lat']:0;
		 $lng = !empty($getlocation['lng'])?$getlocation['lng']:0;
		
		
		$country_code = strtoupper(config('country.icode'));
		$cityname = $request->input('city_name');
		
		$subadmin1_code_query = \DB::table('subadmin1')
		    ->where('country_code','=',$country_code)
           ->select('code')
           ->first();
           
		$subadmin1_code = !empty($subadmin1_code_query->code)?$subadmin1_code_query->code:'';
		
		$subadmin2_code_query = \DB::table('subadmin2')
		   ->where('country_code','=',$country_code)
		   ->where('subadmin1_code','=',$subadmin1_code)
		   ->select('code')
           ->first();
           
        $subadmin2_code = !empty($subadmin2_code_query->code)?$subadmin2_code_query->code:'';           
           
     	$timezon_query = \DB::table('time_zones')
		    ->where('country_code','=',$country_code)
           ->select('time_zone_id')
           ->first();
           
        $timezon = !empty($timezon_query->time_zone_id)?$timezon_query->time_zone_id:'';           
      
      \DB::update('update cities set country_code = "'.$country_code.'",
      name = "'.$cityname.'",
      asciiname = "'.$cityname.'",
      latitude = "'.$lat.'",
      longitude = "'.$lng.'",
      subadmin1_code = "'.$subadmin1_code.'",
      subadmin2_code = "'.$subadmin2_code.'",
      time_zone = "'.$timezon.'",
      updated_at = "'.date('Y-m-d H:i:s').'" 
      where id = "'.$request->input('city_id').'" ');
      
        $checkpaymentpayaccount = \DB::table('payments')->where('post_id', '=', $postId)->where('active', '=', 1)->count();      
        if($checkpaymentpayaccount == '0')
        {
            \DB::table('posts')->where('id', $postId)->update(['reviewed' => 0]);
        }    
        //dd($request);
        //remove it from archive
         \DB::table('posts')->where('id', $postId)->update(['archived' => 0]);
		 \DB::table('posts')->where('id', $postId)->update(['is_rejected' => 0]);
        return $this->postUpdateForm_app($postId, $request); 
    }
    
    
    public	function  getlocationcity($string)
	{
	    $string = str_replace(" ", "+", urlencode($string));
        $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$string."&key=AIzaSyD3HKnsvpSAYaoQQ-wIeqDBTjb69hJ-vMw";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $details_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);
    
        if ($response['status'] != 'OK') {
            return null;
        }
    
        $geometry = $response['results'][0]['geometry'];
     
        $array = array(
            'lat' => $geometry['location']['lat'],
            'lng' => $geometry['location']['lng'],
        );
    
        return $array;
	}
    
}
