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

namespace App\Http\Controllers\Search;

use App\Helpers\Search;
use App\Models\City;
use App\Helpers\Search_app;
use App\Models\Category;
use App\Models\CategoryField;
use Torann\LaravelMetaTags\Facades\MetaTag;
use App\Http\Controllers\Search\Traits\PreSearchTrait;
use Illuminate\Http\Request;
use DB;
class CategoryController extends BaseController
{
	
	use PreSearchTrait;
	
	public $isCatSearch = true;

    protected $cat = null;
    protected $subCat = null;

    /**
     * @param $countryCode
     * @param $catSlug
     * @param null $subCatSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index($countryCode, $catSlug, $subCatSlug = null)
    {
     //   print_r($_GET);
        // Check multi-countries site parameters
        if (!config('settings.seo.multi_countries_urls')) {
            $subCatSlug = $catSlug;
            $catSlug = $countryCode;
        }
		
		//echo 'aa';

        view()->share('isCatSearch', $this->isCatSearch);

        // Get Category
        $this->cat = Category::trans()->where('slug', '=', $catSlug)->firstOrFail();
        view()->share('cat', $this->cat);

        // Get common Data
        $catName = $this->cat->name;
        $catDescription = $this->cat->description;
	
		// Get Category nested IDs
		$catNestedIds = (object)[
			'parentId' => $this->cat->parent_id,
			'id'       => $this->cat->tid,
		];


        $lat = 0;$lng=0;
        // Check if this is SubCategory Request
        if (!empty($subCatSlug))
        {
            $this->isSubCatSearch = true;
            view()->share('isSubCatSearch', $this->isSubCatSearch);

            // Get SubCategory
            $this->subCat = Category::trans()->where('parent_id', $this->cat->tid)->where('slug', '=', $subCatSlug)->firstOrFail();
            view()->share('subCat', $this->subCat);

            // Get common Data
            $catName = $this->subCat->name;
            $catDescription = $this->subCat->description;
            
            // Get Category nested IDs
			$catNestedIds = (object)[
				'parentId' => $this->subCat->parent_id,
				'id'       => $this->subCat->tid,
			];
        }

		// Get Custom Fields
		$customFields = CategoryField::getFields($catNestedIds);
		view()->share('customFields', $customFields);

       
	   
	   
	   
	 
	   
	    // Search abdelhay
		 if(empty($_GET['distance'])){$_GET['distance']=300;}
		 $distance=$_GET['distance'];
		 if(!empty($_GET['location'])){
		 
					   
					   
					   
			   $city_query = \DB::table('posts')

	            ->select('lat','lon')
				 
				 ->where('city_name', 'like', '%'.$_GET['location'].'%')

                ->first();
				
        
           $lat = !empty($city_query->lat)?$city_query->lat:0;
		   $lng = !empty($city_query->lon)?$city_query->lon:0;
	 
	 if($lat == 0 && $lng == 0){
 
		  $city_query = \DB::table('cities')

	            ->select('latitude','longitude','id')
				 
				 ->where('name', 'like', '%'.$_GET['location'].'%')

                ->first();
				
        
           $lat = !empty($city_query->latitude)?$city_query->latitude:0;
		   $lng = !empty($city_query->longitude)?$city_query->longitude:0;
		if(!empty($city_query->id)){
		    $locationid = $city_query->id;
		}
	 
	 
	 }
	 
		
		
		
		}else{
			
					   
				    $getdetail = \DB::table('countries')

           ->select('latitude','longitude','capital')

           ->where('code','=', config('country.icode'))

           ->first();

	    

	     $capital = !empty($getdetail->capital)?$getdetail->capital:'';
	     $locationid = !empty($getdetail->id)?$getdetail->id:'';
        
		 
		  $lat = !empty($getdetail->latitude)?$getdetail->latitude:0;
		  $lng = !empty($getdetail->longitude)?$getdetail->longitude:0;
		  $_GET['location']=$capital;
			}
		
		
	//	echo $_GET['location']."xxx";
	 $city = $this->getCityfast($_GET['location']);
	 if(empty($distance)){$distance=300;}
		   
//echo "wwww".$city;
 
if($distance ==10){$Latitudinal_Degrees = 0.090909091;$Longitudinal_Degrees = 0.117647059;}
if($distance ==15){$Latitudinal_Degrees = 0.1764705882;$Longitudinal_Degrees = 0.1363636364;}
elseif($distance ==75){$Latitudinal_Degrees = 0.681818182;$Longitudinal_Degrees = 0.882352941;} 
elseif($distance ==150){$Latitudinal_Degrees = 1.363636364;$Longitudinal_Degrees = 1.764705882;} 
elseif($distance ==300){$Latitudinal_Degrees = 3.52941176;$Longitudinal_Degrees = 2.72727273;} 
elseif($distance ==500){$Latitudinal_Degrees = 4.545454545;$Longitudinal_Degrees = 5.882352941;} 
elseif($distance ==750){$Latitudinal_Degrees = 8.8235294118;$Longitudinal_Degrees = 6.8181818182;} 
elseif($distance ==1000){$Latitudinal_Degrees = 9.090909091;$Longitudinal_Degrees = 11.76470588;} 
elseif($distance ==50000){$Latitudinal_Degrees = 454.54545455;$Longitudinal_Degrees = 588.235294;} 
 
$Max_Latitudinal =  (float)$lat  +  (float)$Latitudinal_Degrees;
$Min_Latitudinal =  (float)$lat  -  (float)$Latitudinal_Degrees;
$Max_Longitudinal = (float)$lng + (float)$Longitudinal_Degrees;
$Min_Longitudinal = (float)$lng - (float)$Longitudinal_Degrees;

     			$Min_Latitudinal=str_replace(",",".",$Min_Latitudinal);
				$Max_Latitudinal=str_replace(",",".",$Max_Latitudinal);
				$Min_Longitudinal=str_replace(",",".",$Min_Longitudinal);
				$Max_Longitudinal=str_replace(",",".",$Max_Longitudinal);
				
				 $cities_query = DB::select('select distinct `city_name` from `posts` where `lat` between '.$Min_Latitudinal.' and '.$Max_Latitudinal.' and `lon` between '.$Min_Longitudinal.' and '.$Max_Longitudinal.'');
				 
				
			$cities=array();	
			$cities[]=request()->get('location');
		 	//print_r($cities_query);
		if(!empty($cities_query)){
			foreach($cities_query as $city_nm){
				$cities[]=$city_nm->city_name;
				
				}
			
			}	   
			   
		//print_r($cities);
		  //dd($cities);
		$preSearch = [
            'distance'  => (isset($distance) && !empty($distance)) ? $distance : 50000,
			'city'  => (isset($city) && !empty($city)) ? $city : null,
			'cities' => (isset($cities) && !empty($cities)) ? $cities : array(),			
			
		];

        //print_r($_SESSION);
		
		$search = new Search($preSearch);
		
     //   $search = new Search();
        if (isset($this->subCat) && !empty($this->subCat)) {
            $data = $search->setCategory($this->cat->tid, $this->subCat->tid)->setRequestFilters()->fetch();
           
        } else {
            $data = $search->setCategory($this->cat->tid)->setRequestFilters()->fetch();
        }
	

        // Get Titles
         $bcTab = $this->getBreadcrumb();
         $htmlTitle = $this->getHtmlTitle();
       
	     view()->share('bcTab', $bcTab);
        view()->share('htmlTitle', $htmlTitle);

        // SEO
        $title = $this->getTitle();
        //echo $title;exit();
        if (isset($catDescription) && !empty($catDescription)) {
            $description = \Illuminate\Support\Str::limit($catDescription, 200);
        } else {
            $description = \Illuminate\Support\Str::limit(t('Free ads :category in :location', [
                    'category' => $catName,
                    'location' => config('country.name')
                ]) . '. ' . t('Looking for a product or service') . ' - ' . config('country.name'), 200);
        }

        // Meta Tags
        MetaTag::set('title', $title);
        MetaTag::set('description', $description);

        // Open Graph
        $this->og->title($title)->description($description)->type('website');
        if ($data['count']->get('all') > 0) {
            if ($this->og->has('image')) {
                $this->og->forget('image')->forget('image:width')->forget('image:height');
            }
        }
       view()->share('og', $this->og);

        // Translation vars
        view()->share('uriPathCatSlug', $catSlug);
        if (!empty($subCatSlug)) {
            view()->share('uriPathSubCatSlug', $subCatSlug);
        }
        return view('search.serp', $data);
    }
	
		  public function getCityfast($location)
	{
		
		
	if (empty($cityId) && empty($location)) {
			return null;
		}
		
		// Search by administrative division name with magic word "area:" - Example: "area:New York"
		if (!empty($location)) {
			$location = preg_replace('/\s+\:/', ':', $location);
			if (str_contains($location, t('area:'))) {
				$adminName = array_slice(explode(t('area:'), $location), -1)[0];
				$adminName = trim($adminName);
				
				$fullUrl = url(Request::getRequestUri());
				$fullUrlNoParams = explode('?', $fullUrl)[0];
				$url = qsurl($fullUrlNoParams, array_merge(request()->except(['l', 'location']), ['d' => config('country.code'), 'r' => $adminName]));
				
				headerLocation($url);
			}
		}
		
		
		$cityName = rawurldecode($location);
		
		
						 $country_code = strtoupper(config('country.code'));	
						$this->city = City::currentCountry()->where('name', 'LIKE', '%'.$cityName.'%')->first();
					
	 
		view()->share('city', $this->city);
		
		return $this->city;
	
		
		
		}
	
  public function getlocationcity($string)
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
	
	
	
	 public function index1(Request $request)
    {
 
	$subCatSlug = $request->subCatSlug;
        $catSlug = $request->catSlug;
		$language = $request->translation_lang;
		$countryCode = $request->country;
		$country = $request->country;
		$user_id = $request->user_id;
       

        // Get Category
        $cat = Category::trans()->where('slug', '=', $catSlug)->firstOrFail();

        // Get common Data
        $catName = $cat->name;
        $catDescription = $cat->description;
	
		// Get Category nested IDs
		$catNestedIds = (object)[
			'parentId' => $cat->parent_id,
			'id'       => $cat->tid,
		];

        // Check if this is SubCategory Request
        if (!empty($subCatSlug))
        {
            $isSubCatSearch = true;

            // Get SubCategory
            $subCat = Category::trans()->where('parent_id', $cat->tid)->where('slug', '=', $subCatSlug)->firstOrFail();

            // Get common Data
            $catName = $subCat->name;
            $catDescription = $subCat->description;
            
            // Get Category nested IDs
			$catNestedIds = (object)[
				'parentId' => $subCat->parent_id,
				'id'       => $subCat->tid,
			];
        }
	
		// Get Custom Fields
		$customFields = CategoryField::getFields($catNestedIds);

        // Search
        $search = new Search_app($language,$country);
        if (isset($subCat) && !empty($subCat)) {
            $posts = $search->setCategory($cat->tid, $subCat->tid)->setRequestFilters()->fetch();
        } else {
            $posts = $search->setCategory($cat->tid)->setRequestFilters()->fetch();
        }
		
		
	$i=0;
		foreach($posts as $key => $post){
		    $getcurrencycountry = \DB::table('countries')
                                ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
                                ->select('currencies.*')
                                ->where('countries.code', '=', $post->country_code)
                                ->first();
            if ($post->price > 0)
            {
			    $get_currency = \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);
            }
			else
			{
			    $get_currency = t('Free');
			}
    
            $post->currency = $get_currency;
            
            $getimage = \DB::table('pictures')
                   ->select('filename')
                   ->where('post_id',$post->id)
                   ->where('position',1)
                   ->first();
                   
                   
            if(!empty($getimage->filename))
		    {
		        	$postImg = resize($getimage->filename, 'medium');
		    }
		    else
		    {
	        	$postImg = resize(config('larapen.core.picture.default'));
		    }
		    
		    $post->image = $postImg;
		    
		    
	        $package = '';
			if ($post->featured == 1) {
				$package = \App\Models\Package::findTrans($post->py_package_id);
			}
	        $post->package = $package;
		    
			
			
			
			if(!empty($post->py_package_id))
										{
								        $post->py_package_id = $post->py_package_id;
								        }
										else
										{
										$post->py_package_id = 'No Value';
										}
										
										
										if(!empty($package))
										{
								        $post->package = $package;
								        }
										else
										{
										$post->package = 'No Value';
										}
			
			$postType = \App\Models\PostType::findTransApp($post->post_type_id, $language);
											//return $postType;
										$post->postType = $postType;
										
										
										
										$liveCat = \App\Models\Category::findTransApp($post->category_id, $language);
											//return $liveCat;
										$post->liveCat = $liveCat;
										
										// Check parent
										if (empty($liveCat->parent_id)) {
											$liveCatParentId = $liveCat->id;
											$liveCatType = $liveCat->type;
										} else {
											$liveCatParentId = $liveCat->parent_id;
											
											$liveParentCat = \App\Models\Category::findTransApp($liveCat->parent_id, $language);
											//echo $liveParentCat;
											
										if(isset($language))
										{
										$lang1 = $language;
										}
										else
										{
										$lang1 = 'en';
										}	
											$bindings = [
            'translation_lang' => $language,
        ];
		
		$sql = 'select * from categories where translation_of="'.$liveParentCat->tid.'" and translation_lang="'.$language.'" ';

        $categories = DB::select($sql);
						//print_r($categories);
						//echo 'id='.$liveParentCat->tid;		
						//echo 'name='.$categories[0]->name;			
											$liveParentCat->name = $categories[0]->name;			
											
											$post->liveParentCat = $liveParentCat;
											
											
											$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';
										}
										
										// Check translation
										$liveCatName = $liveCat->name;
										
			
			
			
			
			
			if (!empty($user_id))
													{
													$scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post->id)->count();
													if($scount>0)
													{
													$post->saved = 'Yes';
													}
													else
													{
													$post->saved = 'No';
													}
													}
													else
													{
													$post->saved = 'No'; 
													}
			
			$post->created_at = \Carbon\Carbon::parse($post->created_at)->timezone(config('timezone.id'));
										$post->created_at = $post->created_at->ago();
			
			
			
			$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();
                                        $username = $getusernamedetail->username;  
										$user_created_at = $getusernamedetail->created_at;      
										$post->liveCatName = $liveCatName;
										$post->username = $username;
										//$post->user_created_at = $user_created_at;
										
										$post->user_created_at = \Carbon\Carbon::parse($user_created_at)->timezone(config('timezone.id'));
										$post->user_created_at = $post->user_created_at->ago();
			
			
			
			
		    
		}
		    
		

        
        
       // return $data;
    return response()->json(['results'=>$posts]);
	
	}
	
	/*public function index_app(Request $request)
    {
	//echo 'abc';
	 	$subCatSlug = $request->subCatSlug;
        $catSlug = $request->catSlug;
		$language = $request->translation_lang;
		$country = $request->country;
        // Get Category
        $cat = Category::trans()->where('slug', '=', $catSlug)->firstOrFail();
        // Check if this is SubCategory Request
        if (!empty($subCatSlug))
        {
            $subCat = Category::trans()->where('parent_id', $cat->tid)->where('slug', '=', $subCatSlug)->firstOrFail();
            
        }
        // Search
		$Search_app = new Search_app($language,$country);
        if (isset($subCat) && !empty($subCat)) {
            $posts = $Search_app->setCategory($cat->tid, $subCat->tid)->fetch();
        } else {
            $posts = $Search_app->setCategory($cat->tid)->fetch();
        }       
        
        
        $i=0;
		foreach($posts as $key => $post){
		    $getcurrencycountry = \DB::table('countries')
                                ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
                                ->select('currencies.*')
                                ->where('countries.code', '=', $post->country_code)
                                ->first();
            if ($post->price > 0)
            {
			    $get_currency = \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);
            }
			else
			{
			    $get_currency = t('Free');
			}
    
            $post->currency = $get_currency;
            
            $getimage = \DB::table('pictures')
                   ->select('filename')
                   ->where('post_id',$post->id)
                   ->where('position',1)
                   ->first();
                   
                   
            if(!empty($getimage->filename))
		    {
		        	$postImg = resize($getimage->filename, 'medium');
		    }
		    else
		    {
	        	$postImg = resize(config('larapen.core.picture.default'));
		    }
		    
		    $post->image = $postImg;
		    
		    
	        $package = '';
			if ($post->featured == 1) {
				$package = \App\Models\Package::findTrans($post->py_package_id);
			}
	        $post->package = $package;
		    
		    
		}
		    
		    
        
        //return view('search.serp', $data);
		return response()->json(['results'=>$posts]);
	
	}*/
	
	
	
	public function index_app(Request $request)
	{
	
		$subCatSlug = $request->subCatSlug;
        $catSlug = $request->catSlug;
		$language = $request->translation_lang;
		$country = $request->country;
		$country_code = $request->country;
		$lang = $request->translation_lang;
	
       
		
		//echo 'aa';

        //view()->share('isCatSearch', $this->isCatSearch);

        // Get Category
        $this->cat = Category::trans()->where('slug', '=', $catSlug)->firstOrFail();
        //view()->share('cat', $this->cat);

        // Get common Data
        $catName = $this->cat->name;
        $catDescription = $this->cat->description;
	
		// Get Category nested IDs
		$catNestedIds = (object)[
			'parentId' => $this->cat->parent_id,
			'id'       => $this->cat->tid,
		];

        // Check if this is SubCategory Request
        if (!empty($subCatSlug))
        {
            $this->isSubCatSearch = true;
            //view()->share('isSubCatSearch', $this->isSubCatSearch);

            // Get SubCategory
            $this->subCat = Category::trans()->where('parent_id', $this->cat->tid)->where('slug', '=', $subCatSlug)->firstOrFail();
            //view()->share('subCat', $this->subCat);

            // Get common Data
            $catName = $this->subCat->name;
            $catDescription = $this->subCat->description;
            
            // Get Category nested IDs
			$catNestedIds = (object)[
				'parentId' => $this->subCat->parent_id,
				'id'       => $this->subCat->tid,
			];
        }
	
		// Get Custom Fields
		$customFields = CategoryField::getFields($catNestedIds);
		//view()->share('customFields', $customFields);

        // Search
        $search = new Search_app($language,$country);
        if (isset($this->subCat) && !empty($this->subCat)) {
            $posts = $search->setCategory($this->cat->tid, $this->subCat->tid)->setRequestFilters()->fetch();
        } else {
            $posts = $search->setCategory($this->cat->tid)->setRequestFilters()->fetch();
        }
		
		
	
        $i=0;
		foreach($posts as $key => $post){
		    $getcurrencycountry = \DB::table('countries')
                                ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
                                ->select('currencies.*')
                                ->where('countries.code', '=', $post->country_code)
                                ->first();
            if ($post->price > 0)
            {
			    $get_currency = \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);
            }
			else
			{
			    $get_currency = t('Free');
			}
    
            $post->currency = $get_currency;
            
            $getimage = \DB::table('pictures')
                   ->select('filename')
                   ->where('post_id',$post->id)
                   ->where('position',1)
                   ->first();
                   
                   
            if(!empty($getimage->filename))
		    {
		        	$postImg = resize($getimage->filename, 'medium');
		    }
		    else
		    {
	        	$postImg = resize(config('larapen.core.picture.default'));
		    }
		    
		    $post->image = $postImg;
		    
		    
	        $package = '';
			if ($post->featured == 1) {
				$package = \App\Models\Package::findTrans($post->py_package_id);
			}
	        
			
										
										
										if(!empty($post->py_package_id))
										{
								        $post->py_package_id = $post->py_package_id;
								        }
										else
										{
										$post->py_package_id = 'No Value';
										}
										
										
										if(!empty($package))
										{
								        $post->package = $package;
								        }
										else
										{
										$post->package = 'No Value';
										}
								        
								        
										
										$postType = \App\Models\PostType::findTransApp($post->post_type_id, $lang);
											//return $postType;
										$post->postType = $postType;
										
		  
		  
		  $liveCat = \App\Models\Category::findTransApp($post->category_id, $lang);
											//return $liveCat;
										$post->liveCat = $liveCat;
										
										// Check parent
										if (empty($liveCat->parent_id)) {
											$liveCatParentId = $liveCat->id;
											$liveCatType = $liveCat->type;
										} else {
											$liveCatParentId = $liveCat->parent_id;
											
											$liveParentCat = \App\Models\Category::findTransApp($liveCat->parent_id, $lang);
											//echo $liveParentCat;
											
										if(isset($lang))
										{
										$lang1 = $lang;
										}
										else
										{
										$lang1 = 'en';
										}	
											$bindings = [
            'translation_lang' => $lang,
        ];
		
		$sql = 'select * from categories where translation_of="'.$liveParentCat->tid.'" and translation_lang="'.$lang.'" ';

        $categories = DB::select($sql);
						//print_r($categories);
						//echo 'id='.$liveParentCat->parent_id;		
						//echo 'name='.$categories[0]->name;			
											$liveParentCat->name = $categories[0]->name;			
											
											$post->liveParentCat = $liveParentCat;
											
											
											$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';
										}
										
										// Check translation
										$liveCatName = $liveCat->name;
										
										
										$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();
                                        $username = $getusernamedetail->username;  
										$user_created_at = $getusernamedetail->created_at;      
										$post->liveCatName = $liveCatName;
										$post->username = $username;
										//$post->user_created_at = $user_created_at;
										
										$post->user_created_at = \Carbon\Carbon::parse($user_created_at)->timezone(config('timezone.id'));
										$post->user_created_at = $post->user_created_at->ago();
										
										
										
										if (!empty($user_id))
													{
													$scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post->id)->count();
													if($scount>0)
													{
													$post->saved = 'Yes';
													}
													else
													{
													$post->saved = 'No';
													}
													}
													else
													{
													$post->saved = 'No'; 
													}
										
		  
		  
		    
		    
		}
		

        

        
        
        return response()->json(['results'=>$posts]);
    
	}
	
	public function customsearch()
	{
	   
	$cat = Category::where('slug', '=', $catSlug)->firstOrFail();
	}
	
	
	
	
	
	
}
