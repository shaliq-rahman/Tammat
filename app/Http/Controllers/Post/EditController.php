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

use App\Http\Requests\PackageRequest;
use App\Models\Scopes\StrictActiveScope;
use Illuminate\Support\Facades\Session;
use App\Helpers\Payment as PaymentHelper;
use App\Http\Controllers\Post\Traits\PaymentTrait;
use App\Models\Currency;
use App\Models\Post;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use Torann\LaravelMetaTags\Facades\MetaTag;

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
use DB;
class EditController extends FrontController
{
    use EditTrait, VerificationTrait, CustomFieldTrait;

    use PaymentTrait;

    public $data;
    public $request;
    public $packages;
    public $paymentMethods;


    
    public $msg = [];
    public $uri = [];

    /**
     * EditController constructor.
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
        
            // Messages
        if (getSegment(2) == 'create') {
            $this->msg['post']['success'] = t("Your ad has been created.");
        } else {
            $this->msg['post']['success'] = t("Your ad has been updated.");
        }
        $this->msg['checkout']['success'] = t("We have received your payment.");
        $this->msg['checkout']['cancel'] = t("We have not received your payment. Payment cancelled.");
        $this->msg['checkout']['error'] = t("We have not received your payment. An error occurred.");

        // Set URLs
        if (getSegment(2) == 'create') {
            $this->uri['previousUrl'] = config('app.locale') . '/posts/create/#entryToken/payment';
            $this->uri['nextUrl'] = config('app.locale') . '/posts/create/#entryToken/photos';
            $this->uri['paymentCancelUrl'] = url(config('app.locale') . '/posts/create/#entryToken/payment/cancel');
            $this->uri['paymentReturnUrl'] = url(config('app.locale') . '/posts/create/#entryToken/payment/success');
        } else {
            $this->uri['previousUrl'] = config('app.locale') . '/posts/#entryId/edit';
            // $this->uri['nextUrl'] = config('app.locale') . '/' . trans('routes.v-post', ['slug' => '#title', 'id' => '#entryId']);
            $this->uri['nextUrl'] = url(config('app.locale') . '/posts/#entryId/photos');
            $this->uri['paymentCancelUrl'] = url(config('app.locale') . '/posts/#entryId/payment/cancel');
            $this->uri['paymentReturnUrl'] = url(config('app.locale') . '/posts/#entryId/payment/success');
        }

        // Payment Helper init.
        PaymentHelper::$country = collect(config('country'));
        PaymentHelper::$lang = collect(config('lang'));
        PaymentHelper::$msg = $this->msg;
        PaymentHelper::$uri = $this->uri;

        // Get Packages
        // $this->packages = Package::trans()->applyCurrency()->with('currency')->orderBy('lft')->get();
        
        
        $this->packages = Package::where('translation_lang',config('app.locale'))->where('currency_code','USD')->orderBy('lft')->get();
        
        
        if (!empty(Session::get('currency'))) {
            $CurrencyObj = Currency::where('code',Session::get('currency'))->first();
            foreach ($this->packages as $p){
                $p->price = $p->price;
                // $p->price = getCurrencyAmount(Session::get('currency'),$p->price);
                $p->currency = $CurrencyObj;
            }
        }
        view()->share('packages', $this->packages);
        view()->share('countPackages', $this->packages->count());



        // References
        $data = [];

        // Get Countries
        $data['countries'] = $this->countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
        $this->countries = $data['countries'];
        view()->share('countries', $data['countries']);

        // Get Categories
    //    $data['categories'] = Category::trans()->where('parent_id', 0)->with([
      //      'children' => function ($query) {
        //        $query->trans();
          //  },
    //    ])->orderBy('lft')->get();
        
        
        
         $data['categories'] = Category::trans()->where('active', 1)->with([
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
        // dd($request);
          $postval=$request->input();
      if(!empty($postval['cf'])){
          $keys = array_keys($postval['cf']);
      
         $valuex = array_values($postval['cf']);
	  }else{
		  
		   $keys = array();
      
         $valuex = array();
		  }
        // dd($valuex);
         $keySearch = 530; 
		// print_r($value[4]);//exit(); 
        // print_r(array_keys($value[4]));//exit(); 
        // print_r(array_values($value[4]));exit(); 
		
 	/*	 for($i=0;$i<(sizeof($valuex));$i++) {
			 echo $keys[$i]."zzzzzz".sizeof($valuex)."zzzzz";
          if ($keys[$i] == $keySearch) {
              if($valuex[$i]==""){
                  $valuex[$i]="";
              }
              $optionId=array_keys($valuex[$i]);
              $value=array_values($valuex[$i]);
              for($j=0;$j<sizeof($optionId);$j++) {
				 if($optionId[$j] != 0){
              echo 'post_id =>'.$postId.',field_id => '.$keys[$i].',option_idx=>'.$optionId[$j].',value=>'.$value[$j]."<br />";
					}
              //DB::table('post_values')->insert($values);
             }
              //return true;
          } else {
			   
             if($valuex[$i]==""){
                  $valuex[$i]="";
             }
              $optionId=0;
           // $values = array('post_id' =>$postId,'field_id' => $keys[$i],'option_id'=>$optionId,'value'=>$value[$i]);
			 echo 'post_id =>'.$postId.',field_id => '.$keys[$i].',option_id=>'.$optionId.',value=>'.$valuex[$i]."<br />";
        // DB::table('post_values')->insert($values);
            
          }
        
      }
	  exit(); */
	  
	  $deletedRows = DB::table('post_values')->where('post_id', $postId)->delete();
        for($i=0;$i<(sizeof($valuex));$i++) {
          if ($keys[$i] == $keySearch) {
              if($valuex[$i]==""){
                  $valuex[$i]="";
              }
              $optionId=array_keys($valuex[$i]);
              $value=array_values($valuex[$i]);
              for($j=0;$j<sizeof($optionId);$j++) {
				  if($optionId[$j] > 0){
             $values = array('post_id' =>$postId,'field_id' => $keys[$i],'option_id'=>$optionId[$j],'value'=>$value[$j]);
              DB::table('post_values')->insert($values);
			  }
             }
              //return true;
          } else {
			   
             if($valuex[$i]==""){
                  $valuex[$i]="";
             }
              $optionId=0;
              $valuesqwe=array();
              if(is_array($valuex[$i])==1){
                 
                  // dd($valuex[$i]);
                    foreach($valuex[$i] as $keyz => $valuez){

                       // $values= array('post_id' =>$postId,'field_id' => $keys[$i],'option_id'=>$optionId,'value'=>$valuex);
                       
                        DB::table('post_values')->insert([
                            'post_id' =>$postId,'field_id' => $keys[$i],'option_id'=>$optionId,'value'=>$valuez
                        ]);

                        $valuesqwe[] =$valuez;
                      //  DB::table('post_values')->insert($values);
                       
                    }
                   // dd($valuesqwe);

                }else{
                    
                    $vle=$valuex[$i];
                    $values = array('post_id' =>$postId,'field_id' => $keys[$i],'option_id'=>$optionId,'value'=>$vle);
                    DB::table('post_values')->insert($values);
                }

                
           // $values = array('post_id' =>$postId,'field_id' => $keys[$i],'option_id'=>$optionId,'value'=>$vle);
        
            
          }
        
      } 
	  
	  
	  
	  		
		$country_code = strtoupper(config('country.code'));
		$cityname = $request->input('city_name');
		
		
		
		 
					   
					   
			   $city_query = \DB::table('posts')->select('lat','lon','city_id')->where('city_name', 'like', '%'.$cityname.'%')->first();
			   $lat = !empty($city_query->lat)?$city_query->lat:0;
			   $lng = !empty($city_query->lon)?$city_query->lon:0;
			   $locationid =!empty($city_query->city_id)?$city_query->city_id:0;
			   
			   if($lat == 0 && $lng == 0){
				   
		      $city_query = \DB::table('cities')->select('latitude','longitude','id')->where('name', 'like', '%'.$cityname.'%')->first();
			  $lat = !empty($city_query->latitude)?$city_query->latitude:0;
			  $lng = !empty($city_query->longitude)?$city_query->longitude:0;
		      if(!empty($city_query->id)){$locationid = $city_query->id;}
	 
	 if($lat == 0 && $lng == 0){		 
		 
       $getlocation = $this->getlocationcity($cityname);
        
          $lat = !empty($getlocation['lat'])?$getlocation['lat']:0;
		  $lng = !empty($getlocation['lng'])?$getlocation['lng']:0;
		  
				
		
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
		
		   $city_data = \DB::insert('insert into cities (country_code, name, asciiname,latitude,longitude,subadmin1_code,subadmin2_code,active,time_zone,created_at,updated_at) 
       values ("'.$country_code.'", "'.request()->get('location').'", "'.request()->get('location').'", "'.$lat.'", "'.$lng.'", "'.$subadmin1_code.'", "'.$subadmin2_code.'", 1, "'.$timezon.'", "'.date('Y-m-d H:i:s').'", "'.date('Y-m-d H:i:s').'")');
	   
	    $locationid = \DB::getPdo()->lastInsertId();
	   
	 }
	 
	 }
	 
      
        $city_id = $locationid;
	  
	  
	  
	  $request->merge([
    'city_id' =>  $locationid,
]);
      
        $checkpaymentpayaccount = \DB::table('payments')->where('post_id', '=', $postId)->where('active', '=', 1)->count();
      
        if($checkpaymentpayaccount == '0')
        {
            \DB::table('posts')->where('id', $postId)->update(['reviewed' => 0]);
        }    


        \DB::table('posts')->where('id', $postId)->update(['is_rejected' => 0]);
        return $this->postUpdateForm($postId, $request);
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
