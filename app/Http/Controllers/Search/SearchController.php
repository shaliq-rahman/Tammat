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

	

	//print_r(request()->all());

	  //echo 'icode='.config('country.icode');

	     $getdetail = \DB::table('countries')

           ->select('*')

           ->where('code','=', config('country.icode'))

           ->first();

	    

	    $capital = !empty($getdetail->capital)?$getdetail->capital:'';

	    

    	$city_query = \DB::table('cities')

	            ->select('*')

    	        ->where('country_code','=',config('country.icode'))

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
            'photo'  => !empty(request()->get('photo'))?request()->get('photo'):'',
			'countrycode'  => config('country.code'),

			'city'  => (isset($city) && !empty($city)) ? $city : null,

			'distance'  => (isset($distance) && !empty($distance)) ? $distance : 100,

			'admin' => (isset($admin) && !empty($admin)) ? $admin : null,

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

			'distance'  => (isset($distance) && !empty($distance)) ? $distance : null,

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

										$post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));
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
										
										$post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));
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

