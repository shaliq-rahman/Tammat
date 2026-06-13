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



use App\Http\Controllers\Search\Traits\PreSearchTrait;



use App\Models\CategoryField;



use Torann\LaravelMetaTags\Facades\MetaTag;



use DB;





class SearchController extends BaseController



{



	use PreSearchTrait;



	



	public $isIndexSearch = true;



	



	protected $cat = null;



	protected $subCat = null;



	protected $city = null;



	protected $admin = null;



	



	/**



	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View



	 */



	public function index()
	{	 

	// print_r(request()->all());

	  if(empty(request()->get('location'))){
		
		$getdetail = \DB::table('countries')
		->select('latitude','longitude','capital')
		->where('code','=', config('country.icode'))
		->first();
		 
		$capital = !empty($getdetail->capital)?$getdetail->capital:'';
		$locationid = !empty($getdetail->id)?$getdetail->id:'';
		$lat = !empty($getdetail->latitude)?$getdetail->latitude:0;
		$lng = !empty($getdetail->longitude)?$getdetail->longitude:0;
	
	    }else{ 		   

			   $city_query = \DB::table('posts')->select('lat','lon')->where('city_name', 'like', '%'.request()->get('location').'%')->first();

			   $lat = !empty($city_query->lat)?$city_query->lat:0;

			   $lng = !empty($city_query->lon)?$city_query->lon:0;

			   

			   if($lat == 0 && $lng == 0){

				   

		      $city_query = \DB::table('cities')->select('latitude','longitude','id')->where('name', 'like', '%'.request()->get('location').'%')->first();

			  $lat = !empty($city_query->latitude)?$city_query->latitude:0;

			  $lng = !empty($city_query->longitude)?$city_query->longitude:0;

		      if(!empty($city_query->id)){$locationid = $city_query->id;}

	 

	 if($lat == 0 && $lng == 0){

		 

		 

       $getlocation = $this->getlocationcity(request()->get('location'));

        

          $lat = !empty($getlocation['lat'])?$getlocation['lat']:0;

		  $lng = !empty($getlocation['lng'])?$getlocation['lng']:0;

		  

		  $country_code = strtoupper(config('country.code'));		

		

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

	 

			   }

		 

	   // print_r(request()->all()); die;

	   view()->share('isIndexSearch', $this->isIndexSearch);

	   // Pre-Search



		if (request()->filled('c')) {



			if (request()->filled('sc')) {



				$this->getCategory(request()->get('c'), request()->get('sc'));



				



				// Get Category nested IDs



				$catNestedIds = (object)[



					'parentId' => request()->get('c'),



					'id'       => request()->get('sc'),



				];



			} else {



				$this->getCategory(request()->get('c'));



				



				// Get Category nested IDs



				$catNestedIds = (object)[



					'parentId' => 0,



					'id'       => request()->get('c'),



				];



			}



			



			// Get Custom Fields



			$customFields = CategoryField::getFields($catNestedIds);


			$customFields =array();
			view()->share('customFields', $customFields);



		}



		if (request()->filled('l') || request()->filled('location')) {



			$city = $this->getCity(request()->get('l'), request()->get('location'));



		}



		else



		{

			$city = $this->getCity($locationid,$capital);			

		    //echo "kkkkk".$locationid."wwwww".$capital."qqqqqqqqqqqq";

			//print_r($city); 

		}

		

//print_r($this->city);

//print_r($city);



		//Code made by MonTech Team hashing by me

		/*if (request()->filled('location')) {

			

			$city = $this->getCityObj(request()->get('location'));

			

			}

	else



		{



	    	$city = $this->getCityObj($capital);



		}

*/

		



	//	$city =request()->filled('location');



		



		if (request()->filled('r') && !request()->filled('l')) {



			$admin = $this->getAdmin(request()->get('r'));



		}



		if (request()->filled('distance')) {



			$distance = request()->get('distance');



		}



		



		//$lat."xxxxxx".$lng;



		// Pre-Search values

if((isset($distance) && !empty($distance))){}else{$distance =300;}	





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







		/* $cities_query = \DB::table('cities')



	            ->select('name')

				 

				 //->where('name', 'like', '%'.request()->get('location').'%')

                  ->whereBetween('latitude', [$Min_Latitudinal, $Max_Latitudinal])

				   ->whereBetween('longitude', [$Min_Longitudinal, $Max_Longitudinal])

                ->distinct()

				->get();

				

				 $cities_query = \DB::table('posts')

	            ->select('city_name')

				 ->where('lat','>=', (float)$Min_Latitudinal)

				  ->where('lat','<=', (float)$Max_Latitudinal)

				   ->where('lon','>=', (float)$Min_Longitudinal)

				    ->where('lon','<=', (float)$Max_Longitudinal)

					

				 // ->whereBetween('lat', [$Min_Latitudinal,$Max_Latitudinal])

				 // ->whereBetween('lon', [$Min_Longitudinal, $Max_Longitudinal])

                ->distinct()

				->get();*/

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

			

		//	print_r($cities);

		//from post wherein(city_name,$cities_query);

				 

		$preSearch = [

            'photo'  => !empty(request()->get('photo'))?request()->get('photo'):'',
			'countrycode'  => config('country.code'),
			'city'  => (isset($city) && !empty($city)) ? $city : null,
			'distance'  => (isset($distance) && !empty($distance)) ? $distance : 300,
			'admin' => (isset($admin) && !empty($admin)) ? $admin : null,
			'cities' => (isset($cities) && !empty($cities)) ? $cities : array(),
 
		];
		//echo "<pre>";
		//print_r($preSearch); die;
		// Search
		// qedama 
		$search = new Search($preSearch);
		$data = $search->fechAll();
		//print_r($data); die;
		// Export Search Result
		view()->share('count', $data['count']);
		view()->share('paginator', $data['paginator']);
		// Get Titles
		$title = $this->getTitle();
		$this->getBreadcrumb();
		$this->getHtmlTitle();
		// Meta Tags
		MetaTag::set('title', $title);
		MetaTag::set('description', $title);
		return view('search.serp');
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

	

	

	



	public function index_app()



	{	   



	//echo 'aa';



	//print_r(request()->all());



	  //$icode = config('country.icode');



	  $icode = request()->get('country');

	  $lang = request()->get('lang');

	  $user_id = request()->get('user_id');



	  //echo 'icode='.$icode;



	     $getdetail = \DB::table('countries')



           ->select('*')



           ->where('code','=', $icode)



           ->first();



	    



	    $capital = !empty($getdetail->capital)?$getdetail->capital:'';



	    



    	$city_query = \DB::table('cities')



	            ->select('*')



    	        ->where('country_code','=',$icode)



                ->where('name', 'like', '%'.$capital.'%')



                ->first();



	    



	    $locationid = !empty($city_query->id)?$city_query->id:'';



	    



	   // if(!empty($capital))



	   // {



    // 	    $capital = explode(' ',$capital);



    // 	    $capital = $capital[0];    



	   // }



	    



	    



	   // print_r(request()->all()); die;



	  



		view()->share('isIndexSearch', $this->isIndexSearch);



		



		// Pre-Search



		if (request()->filled('c')) {



			if (request()->filled('sc')) {



				$this->getCategory(request()->get('c'), request()->get('sc'));



				



				// Get Category nested IDs



				$catNestedIds = (object)[



					'parentId' => request()->get('c'),



					'id'       => request()->get('sc'),



				];



			} else {



				$this->getCategory(request()->get('c'));



				



				// Get Category nested IDs



				$catNestedIds = (object)[



					'parentId' => 0,



					'id'       => request()->get('c'),



				];



			}



			



			// Get Custom Fields



			$customFields = CategoryField::getFields($catNestedIds);



			view()->share('customFields', $customFields);



		}



		if (request()->filled('l') || request()->filled('location')) {



			$city = $this->getCity(request()->get('l'), request()->get('location'));



		}



		else



		{



		    $city = $this->getCity($locationid,$capital);



		}



		//Code made by MonTech Team



		if (request()->filled('location')) {



			$city = $this->getCityObj(request()->get('location'));



		}



		else



		{



	    	$city = $this->getCityObj($capital);



		}



		



		



		



		if (request()->filled('r') && !request()->filled('l')) {



			$admin = $this->getAdmin(request()->get('r'));



		}



		if (request()->filled('distance')) {



			$distance = request()->get('distance');



		}



		



		



		// Pre-Search values



		$preSearch = [



			'countrycode'  => $icode,



			'city'  => (isset($city) && !empty($city)) ? $city : null,



			'distance'  => (isset($distance) && !empty($distance)) ? $distance : 300,



			'admin' => (isset($admin) && !empty($admin)) ? $admin : null,



		];



		//echo "<pre>";



		//print_r($preSearch); die;



		// Search



		$search = new Search($preSearch);



		$data = $search->fechAll_app();



		//print_r($data); die;



		// Export Search Result



		view()->share('count', $data['count']);



		view()->share('paginator', $data['paginator']);



		



		



		



		$posts = $data['paginator'];



		$i=0;



		foreach($posts as $key => $post){



				



	// Get Post's Pictures



										$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');



										if ($pictures->count() > 0) {



											$postImg = resize($pictures->first()->filename, 'medium');



										} else {



											$postImg = resize(config('larapen.core.picture.default'));



										}



										$post->postImg = $postImg;			



										



										



										



										$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();



                                        $post->contact_name = $getusernamedetail->username;  



										$post->created_at = \Carbon\Carbon::parse($post->created_at)->timezone(config('timezone.id'));

										$post->created_at = $post->created_at->ago();

										









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

										





 $package = '';

										if ($post->featured == 1) {

											$package = \App\Models\Package::findTransApp($post->py_package_id,$lang);

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

										

$postType = \App\Models\PostType::findTransApp($post->post_type_id, $lang);

											//return $postType;

										$post->postType = $postType;







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









				



	$i++;



								}	



		



		



		



		return response()->json(['getdetail'=>$posts]);



		



		//return view('search.serp');



	}



	



}



