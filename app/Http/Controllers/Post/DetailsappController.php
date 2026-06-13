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



use App\Events\PostWasVisited;

use App\Helpers\Arr;

use App\Helpers\DBTool;

use App\Helpers\Localization\Country as CountryLocalization;

use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;

use App\Http\Controllers\FrontController;

use App\Http\Controllers\Post\Traits\CustomFieldTrait;

use App\Http\Requests\MakeAnOfferRequest;

use App\Http\Requests\SendMessageRequest;

use App\Models\Category;

use App\Models\Makeanoffer;

use App\Models\Message;

use App\Models\Package;

use App\Models\Post;

use App\Models\Scopes\ReviewedScope;

use App\Models\Scopes\VerifiedScope;

use App\Models\User;

use App\Notifications\SellerContacted;

use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Event;

use Illuminate\Support\Facades\Request;



use Torann\LaravelMetaTags\Facades\MetaTag;



use Illuminate\Support\Facades\App;

use App\Models\FieldOption;



class DetailsappController extends FrontController

{

    use CustomFieldTrait;



    /**

     * Post expire time (in months)

     *

     * @var int

     */

    public $expireTime = 24;



    public $reviewsPlugin;



    /**

     * DetailsController constructor.

     */

    public function __construct()

    {

        //parent::__construct();



        // From Laravel 5.3.4 or above

        /*$this->middleware(function ($request, $next) {

            $this->commonQueries();



            return $next($request);

        });*/

		$this->timezone = 'Asia/Kolkata';

    }



    /**

     * Common Queries

     */

    public function commonQueries()

    {

        // Check Country URL for SEO

        $countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());

        view()->share('countries', $countries);



        // Count Packages

        $countPackages = Package::trans()->applyCurrency()->count();

        view()->share('countPackages', $countPackages);



        // Count Payment Methods

        view()->share('countPaymentMethods', $this->countPaymentMethods);



        // Check and Load the Reviews Plugin

        $this->reviewsPlugin = load_installed_plugin('reviews');

        view()->share('reviewsPlugin', $this->reviewsPlugin);

    }



    /**

     * Show Dost's Details.

     *

     * @param $postId

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View

     */





    private function single_post_data($cat, $currentPostId = 0,$country)

    {

        $limit = 20;

        $featured = null;

        // Get the sub-categories of the current ad parent's category

        $similarCatIds = [];

        // Get ads from same category

        $posts = [];

        $posts = Post::where('id', $currentPostId)->get(); 

        if (count($posts) > 0) {

            // Append the Posts 'uri' attribute

            $posts = collect($posts)->map(function ($post) {

				

				$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

                if ($pictures->count() > 0) {

                    $postImgnew = resize($pictures->first()->filename, 'medium');

                } else {

                    $postImgnew = resize(config('larapen.core.picture.default'));

                }

				$getcurrencycountry = \DB::table('countries')

                ->join('currencies', 'currencies.code', '=', 'countries.currency_code')

                ->select('currencies.*')

                ->where('countries.code', '=', $post->country_code)

                ->first();

		 if ($post->price > 0)

        {

            $get_currencynew = \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);

        }

        else

        {

            $get_currencynew = t('Free');

        }

		                               

									   $liveCat = \App\Models\Category::findTrans($post->category_id);

											//return $liveCat;

										$post->liveCat = $liveCat;										

										// Check parent

										if (empty($liveCat->parent_id)) {

											$liveCatParentId = $liveCat->id;

											$liveCatType = $liveCat->type;

										} else {

											$liveCatParentId = $liveCat->parent_id;

											

											$liveParentCat = \App\Models\Category::findTrans($liveCat->parent_id);

											$post->liveParentCat = $liveParentCat;

											$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';

										}										

										// Check translation

										$liveCatName = $liveCat->name;

                                        $catNestedIds = (object)[

                                            'parentId' => $liveCatParentId,

                                            'id' => $post->category_id,

                                        ];

										// $customFields = $this->getPostFieldsValues_app($catNestedIds, $post->id);

                                        $customFields = $this->ShowCustomFields_only($catNestedIds, $post->id);

                                       

									    $results = array();

										foreach($customFields as $field)

										{

										if (in_array($field->type, ['checkbox_multiple'])) 

										{

										$dvals = $field->default;

										$results1 = array();

										foreach($dvals as $index=>$key) 

										{

										 array_push($results1, $key);

										}									

																				

										$field->default = $results1;										

										}

                                        }

										

										

									  $pure_customFields=array();

									   if(!empty($customFields[0]['id'])){

										  foreach($customFields as $subcustomFields){										 

											$pure_customFields[]=$subcustomFields;

											}

										 } 

											

											

											

										$filter_customFields=array();

									 	foreach($customFields as $cstm){

											$new_row=array();

											$new_row['name']=$cstm->name;

											$dflt=$cstm->default;

											if(is_array($cstm->default)){

												

												$dflt=$cstm->default[0]['value'];

												

												}else{

												

											if(!empty($cstm->options)){

												foreach($cstm->options as $opt){

													

													if($opt->translation_of==$dflt){

														$dflt=$opt->value;

													}

													

												}

											}

											}

											$new_row['default']=$dflt;

											//$new_row['active']=$cstm->active;

										//	$new_row['options']=$cstm->options;

											

											$filter_customFields[]=$new_row;

											 

											} 

											

											  

											  

											 

									  $post->customFields = $filter_customFields;				

										

 

        









              // $post->xxxxxx = $get_currencynew;

               $post->currency=$get_currencynew;

               $post->postImg=$postImgnew;



                $post->title = mb_ucfirst($post->title);

                $post->uri = trans('routes.v-post', ['slug' => slugify($post->title), 'id' => $post->id]);



                return $post;

            })->toArray();



            // Randomize the Posts

            $posts = collect($posts)->shuffle()->toArray();



            // Featured Area Data

            $featured = [

                'title' => t('Similar Ads'),

                'link' => qsurl(config('app.locale') . '/' . trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except('c'), ['c' => $cat->tid])),

                'posts' => $posts,

            ];

            $featured = Arr::toObject($featured);

            

        }

		

		$posts1 = array();

		

		foreach($posts as $index=>$key) 

		{

		 array_push($posts1, $key);

		}



        return $posts1;

    }

	

	

	protected function check_fav_post($post_id,$user_id){

	

	         if (!empty($user_id)){

				 $scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post_id)->count();

													if($scount>0)

													{

													$saved = '1';

													}

													else

													{

													$saved = '0';

													}

													}

													else

													{

													$saved = '0'; 

													}

	return $saved;

	

	}	



   	



    public function index($postId,$country,$lang,$user_id=0)

    { 
        $data = [];

        // Get and Check the Controller's Method Parameters

        $parameters = Request::route()->parameters();
 

       //return response()->json(['parameters'=>$parameters]);

        // Set the Parameters

        $postId = $parameters['id'];

        if (isset($parameters['slug'])) {

            $slug = $parameters['slug'];

        }

		 //$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])

		$post = Post::where('id', $postId)

                    ->with([

                        'category' => function ($builder) { $builder->with(['parent']); },

                        'postType',

                        'user',

                        'city',

                        'pictures',

                        'latestPayment' => function ($builder) { $builder->with(['package']); },

                    ])

            ->first();

        //return $post->category->tid;

		$tid = $post->category;		

      

        

		

		 //start this for return full post details abdelhay 7-7-2022

		      $post_data = $this->single_post_data($tid, $post->id,$country);  			  

		      $FilterPostData=array();

			  $FilterPostData['id']=$post_data[0]['id'];

              $FilterPostData['post_user_id']=$post_data[0]['user_id'];

			  $FilterPostData['title']=$post_data[0]['title']; 

			  $FilterPostData['price']=$post_data[0]['price']; 		

			  $FilterPostData['formmated_price']=$post_data[0]['currency']; 	  

			  $FilterPostData['description']= strip_tags($post_data[0]['description']);

			  $FilterPostData['visits']=$post_data[0]['visits']; 

			  $FilterPostData['address']=$post_data[0]['address']; 

			 // $FilterPostData['country']=$post_data[0]['country'];			  

			  $FilterPostData['city_name']=$post_data[0]['city_name']; 			  

			  $FilterPostData['postImg']=$post_data[0]['postImg']; 

			  $FilterPostData['created_at_ta']=$post_data[0]['created_at_ta']; 

			  $FilterPostData['created_at']=$post_data[0]['created_at'];

			  $FilterPostData['featured']=$post_data[0]['featured'];

			  $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

              $username = $getusernamedetail->username;  

			  $FilterPostData['username']=$username;			  

			  $FilterPostData['user_img']='https://www.tmmat.com/ProfilePictures/'.$getusernamedetail->profile_image;

			  $FilterPostData['user_address']=$getusernamedetail->address;

			  $FilterPostData['user_phone']=$getusernamedetail->phone;

              $FilterPostData['user_email']=$getusernamedetail->email;

              $FilterPostData['email_hidden']=$getusernamedetail->email_hidden;

              $FilterPostData['phone_hidden']=$getusernamedetail->phone_hidden;

			  $FilterPostData['post_url']='https://www.tmmat.com/'.$lang.'/'.$post_data[0]['uri'];

			  $FilterPostData['favourite']= @$this->check_fav_post($post_data[0]['id'],$user_id);

			  $FilterPostData['customFields']=$post_data[0]['customFields'];

			  $post_data =$FilterPostData;

	     //end this for return full post details abdelhay 7-7-2022

		 

		 

		  //start this for  post Pictures abdelhay 7-7-2022		  

		    $pictures_q = \App\Models\Picture::where('post_id', $parameters['id'])->orderBy('position')->orderBy('id')->get();

			//echo 'count='.$pictures;

			$post_pictures=array();

			$j=0;

			foreach($pictures_q as $npic)

			{

			if($npic['active']==1){$post_pictures[$j]['filename'] = 'https://tmmat.com/storage/'.$npic['filename'];}

			$j++;

			}

			

			 

		 

			

		  //end this for  post Pictures abdelhay 7-7-2022

		 

		  //start this for return samiliers ads abdelhay 7-7-2022

		$similarads = $this->getCategorySimilarPosts1($tid, $post->id,$country); 

		$similarads = array_values($similarads);

		$filterAllPosts=array();$w=0;

		   

		   foreach($similarads as $s_post){

			   

			    $getcurrencycountry = \DB::table('countries')

		   ->join('currencies', 'currencies.code', '=', 'countries.currency_code')

		   ->select('currencies.*')

		   ->where('countries.code', '=', $s_post->country_code)

		   ->first();

		   if($s_post->price > 0)

		   {			   

		// Get Post's formatted price	   

   $get_currency = \App\Helpers\Number::money_price_latest($s_post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,   $getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);

            		                }

            						else

            						{

            						    $get_currency = t('Free');

            						}

				// Get Post's Pictures

		$pictures = \App\Models\Picture::where('post_id', $s_post->id)->orderBy('position')->orderBy('id');

					 if ($pictures->count() > 0) {

						$postImg = resize($pictures->first()->filename, 'medium');

						} else {

						$postImg = resize(config('larapen.core.picture.default'));

						}			 			 

									

			  $filterAllPosts[$w]['id']=$s_post->id;

			  $filterAllPosts[$w]['title']=$s_post->title; 

			  $filterAllPosts[$w]['formmated_price']=$get_currency;

			  $filterAllPosts[$w]['price']=$s_post->price; 

			  $filterAllPosts[$w]['city_name']=$s_post->city_name; 

			  $filterAllPosts[$w]['created_at']=$s_post->created_at; 		      

			  $filterAllPosts[$w]['postImg']=$postImg;

			  $filterAllPosts[$w]['featured']=$s_post->featured;

			  $filterAllPosts[$w]['favourite']= @$this->check_fav_post($s_post->id,$s_post->user_id);

			  $w++;

		   }

		    //$cat_posts[$x]['AllPosts']=$AllPosts;

		    $similarads=$filterAllPosts;

			//end this for return samiliers ads abdelhay 7-7-2022 

			 

		 

	 

		/* start no need abdelhay this code for return user ads me by need in user profile 8-7-2022

		

		

		//  $post_data->uri=$postImg;

		// $featured = $this->getLocationSimilarPosts($post->city, $post->id);

		$getdetail = \DB::table('posts')

                                ->leftJoin('payments', 'payments.post_id', '=', 'posts.id')

                                ->where('posts.user_id', '=', $post->user_id)

                                ->where('archived', '=', 0)

                                ->where('reviewed', '=', 1)

                                ->where('country_code', '=', $post->country_code)

                                ->orderBy('id', 'desc')

                                ->limit(4)

                                ->get(['posts.*','payments.package_id']);

                            if (!isset($cats)) {

                                $cats = collect([]);

                            }

		

		

		foreach($getdetail as $value_post)

		{

		                    $package = null;

                                // if ($value_post->featured == 1) {

                                //     $cacheId = 'package.' . $value_post->package_id . '.' . config('app.locale');

                                //     $package = \Illuminate\Support\Facades\Cache::remember($cacheId, $this->cacheExpiration, function () use ($value_post) {

                                //     $package = \App\Models\Package::findTrans($value_post->package_id);

                                //         return $package;

                                //     });

                                // }

                                $pictures = \App\Models\Picture::where('post_id', $value_post->id)->orderBy('position')->orderBy('id');

                                if ($pictures->count() > 0) {

                                    $postImg = resize($pictures->first()->filename, 'medium');

                                } else {

                                    $postImg = resize(config('larapen.core.picture.default'));

                                }

                                $cacheId = 'postType.' . $value_post->post_type_id . '.' . config('app.locale');

                                $postType = \Illuminate\Support\Facades\Cache::remember($cacheId, $this->cacheExpiration, function () use ($value_post) {

                                    $postType = \App\Models\PostType::findTrans($value_post->post_type_id);

                                    return $postType;

                                });

                                if (empty($postType)) continue;

                                // Get the Post's City

                                $cacheId = config('country.code') . '.city.' . $value_post->city_id;

                                $city = \Illuminate\Support\Facades\Cache::remember($cacheId, $this->cacheExpiration, function () use ($value_post) {

                                    $city = \App\Models\City::find($value_post->city_id);

                                    return $city;

                                });

                                if (empty($city)) continue;

                                $value_post->created_at = \Carbon\Carbon::parse($value_post->created_at)->timezone($this->timezone);

                                $value_post->created_at = $value_post->created_at->ago();

                                $getcategorydata = \DB::table('categories')

                                    ->select('*')

                                    ->where('id', '=', $value_post->category_id)

                                    ->first();

                                if (!empty($getcategorydata->parent_id)) {

                                    $getcategorydataparent = \DB::table('categories')

                                        ->select('*')

                                        ->where('id', '=', $getcategorydata->parent_id)

                                        ->first();



                                    $liveCatParentId = $getcategorydataparent->id;

                                    $liveCatName = $getcategorydataparent->name;

                                } else {

                                    $liveCatParentId = $getcategorydata->id;

                                    $liveCatName = $getcategorydata->name;

                                }

								

								

								

								

								$package = '';

										if ($value_post->featured == 1) {

											$package = \App\Models\Package::findTransApp($value_post->package_id,$lang);

										}

										

										

										if(!empty($value_post->package_id))

										{

								        $value_post->py_package_id = $value_post->package_id;

								        }

										else

										{

										$value_post->py_package_id = 'No Value';

										}

										

										

										if(!empty($package))

										{

								        $value_post->package = $package;

								        }

										else

										{

										$value_post->package = 'No Value';

										}

								

		

		}

		//print_r($featured);

			 

		

		$i=0;

		foreach($getdetail as $key => $post){

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

                                    $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $post->contact_name = $getusernamedetail->username;     

		    

		    

		    

		        	                    

								        

								        

								        

										

										$postType = \App\Models\PostType::findTrans($post->post_type_id);

											//return $postType;

										$post->postType = $postType;

										

										

							

										// Get Post's Pictures

										$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

										if ($pictures->count() > 0) {

											$postImg = resize($pictures->first()->filename, 'medium');

										} else {

											$postImg = resize(config('larapen.core.picture.default'));

										}

										$post->postImg = $postImg;

										

										$city = \App\Models\City::find($post->city_id);

										$post->cityy = $city;

										

										

										

										$post->created_at = \Carbon\Carbon::parse($post->created_at)->timezone($this->timezone);

										$post->created_at = $post->created_at->ago();

										

										$liveCat = \App\Models\Category::findTrans($post->category_id);

											//return $liveCat;

										$post->liveCat = $liveCat;

										

										// Check parent

										if (empty($liveCat->parent_id)) {

											$liveCatParentId = $liveCat->id;

											$liveCatType = $liveCat->type;

										} else {

											$liveCatParentId = $liveCat->parent_id;

											

											$liveParentCat = \App\Models\Category::findTrans($liveCat->parent_id);

											$post->liveParentCat = $liveParentCat;

											$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';

										}

										

										// Check translation

										$liveCatName = $liveCat->name;

										

											

                                		$catNestedIds = (object)[

                                            'parentId' => $liveCatParentId,

                                            'id' => $post->category_id,

                                        ];

                                

                                        $customFields = $this->getPostFieldsValues_app($catNestedIds, $post->id);

                                						

										

										$results = array();

										foreach($customFields as $field)

										{

										

										

										

										

										if (in_array($field->type, ['checkbox_multiple'])) 

										{

										$dvals = $field->default;

										$results1 = array();

										foreach($dvals as $index=>$key) 

										{

										 array_push($results1, $key);

										}

										

																				

										$field->default = $results1;										

										}

										

										

										

										}

										

										$post->customFields = $customFields;

										//$post->paymentpre = $results;

										$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $username = $getusernamedetail->username;  

										$user_created_at = $getusernamedetail->created_at;      

										$post->liveCatName = $liveCatName;

										$post->username = $username;

										//$post->user_created_at = $user_created_at;

										

										$post->user_created_at = \Carbon\Carbon::parse($user_created_at)->timezone($this->timezone);

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

										

										

										$i++;

						}

					

					  end no need abdelhay this code for return user ads me by need in user profile 8-7-2022	*/

		

//return response()->json(['post'=>$post,'customFields'=>$customFields,'similarads'=>$featured,'adsby'=>$getdetail]);







return response()->json(['post_data'=>$post_data,'pictures'=>$post_pictures,'similarads'=>$similarads]); 



        

    }















	public function index_custom($postId,$languageCode)

    {

        $data = [];



        // Get and Check the Controller's Method Parameters

        $parameters = Request::route()->parameters();

       //  print_r($parameters);

       // app()->setLocale($parameters['languageCode']);

        



        // Set the Parameters

        $postId = $parameters['id'];

        $languageCode = $parameters['languageCode'];

        



        // GET POST'S DETAILS

        

            $cacheId = 'post.with.user.city.pictures.' . $postId . '.' . config('app.locale');

            $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])

                    ->where('id', $postId)

                    ->with([

                        'category' => function ($builder) {

                            $builder->with(['parent']);

                        },

                        'postType',

                        'user',

                        'city',

                        'pictures',

                        'latestPayment' => function ($builder) {

                            $builder->with(['package']);

                        },

                    ])

                    ->first();

//return response()->json(['post'=>$post]); 

                //return $post;

            

       

       /* // Preview Post after activation



        if (request()->filled('preview') && request()->get('preview') == 1) {

            // Get post's details even if it's not activated and reviewed

            $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])

                ->where('id', $postId)

                ->with([

                    'category' => function ($builder) {

                        $builder->with(['parent']);

                    },

                    'postType',

                    'user',

                    'city',

                    'pictures',

                    'latestPayment' => function ($builder) {

                        $builder->with(['package']);

                    },

                ])

                ->first();

        }

*/

        // Post not found

       



        // Share post's details

        //view()->share('post', $post);

      

        // Get Category nested IDs

        $catNestedIds = (object)[

            'parentId' => $post->category->id,

            'id' => $post->category->tid,

        ];



        // Get Custom Fields

      //  $customFields = $this->getPostFieldsValues($catNestedIds, $post->id);

      

      if(empty($languageCode)){ $languageCode='en';}

     

	   

      //self::$languageCodeForTranslation = $languageCode;

       //Session::put('locale', $languageCode);



       App::setLocale($languageCode);



      $customFields = $this->getPostFieldsValues_app($catNestedIds, $post->id);

		

		

		

		

		 

		

		

		//print_r($customFields);

		

		

		if (isset($customFields) and $customFields->count() > 0)

		{

		$arr = array();

		$i=0;

		foreach($customFields as $field)

            {

            if (in_array($field->type, ['radio', 'select'])) {

                if (is_numeric($field->default)) {

                    $option = \App\Models\FieldOption::findTrans($field->default);

                    if (!empty($option)) {

                        $field->default = $option->value;

                    }

                }

            }

            if (in_array($field->type, ['checkbox'])) {

                $field->default = ($field->default == 1) ? t('Yes') : t('No');

            }

            

			if ($field->type == 'file')

			{

			

			$arr[$i]['field'] = $field->name;

			$arr[$i]['value'] = array(\Storage::url($field->default));

			

			}

				

			else

			{

				if (!is_array($field->default))

			{	

				

				$arr[$i]['field'] = $field->name;

			$arr[$i]['value'] = array($field->default);

				

				

				}

					

					

				else

				{

					if (count($field->default) > 0)

					{

					$arr[$i]['field'] = $field->name;

			

			$results = array();

			foreach($field->default as $valueItem)

			{

									//continue(!isset($valueItem->value));

									

									array_push($results, $valueItem->value);

								}

			$arr[$i]['value'] = $results;

			

			

		

		

			

			

			

					}

				}

				}

				$i++;

				}

				

				}

			

		

		

		

		else

		{

		$arr = array();

		}

		//print_r($arr);

		

        //dd($customFields);

        //view()->share('customFields', $customFields);

return response()->json(['posts'=> $post, 'customFields'=>$arr]); 

        

    }





    public function index_custom_ios($postId)

    {

        $data = [];



        // Get and Check the Controller's Method Parameters

        $parameters = Request::route()->parameters();

        //print_r($parameters);

        app()->setLocale($parameters['lang']);

        



        // Set the Parameters

        $postId = $parameters['id'];

        



        // GET POST'S DETAILS

        

            $cacheId = 'post.with.user.city.pictures.' . $postId . '.' . config('app.locale');

            $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])

                    ->where('id', $postId)

                    ->with([

                        'category' => function ($builder) {

                            $builder->with(['parent']);

                        },

                        'postType',

                        'user',

                        'city',

                        'pictures',

                        'latestPayment' => function ($builder) {

                            $builder->with(['package']);

                        },

                    ])

                    ->first();

//return response()->json(['post'=>$post]); 

                //return $post;

            

       

       /* // Preview Post after activation



        if (request()->filled('preview') && request()->get('preview') == 1) {

            // Get post's details even if it's not activated and reviewed

            $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])

                ->where('id', $postId)

                ->with([

                    'category' => function ($builder) {

                        $builder->with(['parent']);

                    },

                    'postType',

                    'user',

                    'city',

                    'pictures',

                    'latestPayment' => function ($builder) {

                        $builder->with(['package']);

                    },

                ])

                ->first();

        }

*/

        // Post not found

       



        // Share post's details

        //view()->share('post', $post);

      

        // Get Category nested IDs

        $catNestedIds = (object)[

            'parentId' => $post->category->parent_id,

            'id' => $post->category->tid,

        ];



        // Get Custom Fields

        $customFields = $this->getPostFieldsValues($catNestedIds, $post->id);

		

		

		

		

		

		

		 

		

		

		//print_r($customFields);

		

		

		if (isset($customFields) and $customFields->count() > 0)

		{

		$arr = array();

		$i=0;

		foreach($customFields as $field)

            {

            if (in_array($field->type, ['radio', 'select'])) {

                if (is_numeric($field->default)) {

                    $option = \App\Models\FieldOption::findTrans($field->default);

                    if (!empty($option)) {

                        $field->default = $option->value;

                    }

                }

            }

            if (in_array($field->type, ['checkbox'])) {

                $field->default = ($field->default == 1) ? t('Yes') : t('No');

            }

            

			if ($field->type == 'file')

			{

			

			$arr[$i]['field'] = $field->name;

			$arr[$i]['value'] = array(\Storage::url($field->default));

			

			}

				

			else

			{

				if (!is_array($field->default))

			{	

				

				$arr[$i]['field'] = $field->name;

			$arr[$i]['value'] = array($field->default);

				

				

				}

					

					

				else

				{

					if (count($field->default) > 0)

					{

					$arr[$i]['field'] = $field->name;

			

			$results = array();

			foreach($field->default as $valueItem)

			{

									//continue(!isset($valueItem->value));

									

									array_push($results, $valueItem->value);

								}

			$arr[$i]['value'] = $results;

			

			

		

		

			

			

			

					}

				}

				}

				$i++;

				}

				

				}

			

		

		

		

		else

		{

		$arr = array();

		}

		//print_r($arr);

		

        //dd($customFields);

        //view()->share('customFields', $customFields);

return response()->json(['posts'=> $post, 'customFields'=>$arr]); 

        

    }







	

    /**

     * @param $postId

     * @param SendMessageRequest $request

     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

     */

    public function sendMessage($postId, SendMessageRequest $request)

    {

        // Get the Post

        $post = Post::unarchived()->findOrFail($postId);



        // New Message

        $message = new Message();

        $input = $request->only($message->getFillable());

        foreach ($input as $key => $value) {

            $message->{$key} = $value;

        }



        $message->post_id = $post->id;

        $message->from_user_id = auth()->check() ? auth()->user()->id : 0;

        $message->to_user_id = $post->user_id;

        $message->to_name = $post->contact_name;

        $message->to_email = $post->email;

        $message->to_phone = $post->phone;

        $message->subject = $post->title;



        $attr = ['slug' => slugify($post->title), 'id' => $post->id];

        $message->message = $request->input('message')

            . '<br><br>'

            . t('Related to the ad')

            . ': <a href="' . lurl($post->uri, $attr) . '">' . t('Click here to see') . '</a>';



        // Save

        $message->save();



        // Save and Send user's resume

        if ($request->hasFile('filename')) {

            $message->filename = $request->file('filename');

            $message->save();

        }



        // Send a message to publisher

        try {

            $post->notify(new SellerContacted($post, $message));

            

             $sellername = \DB::table('users')

		            ->where('id', '=', $post->user_id)

                   ->select('username')

                   ->first();

                        				

                        				

            $msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $sellername->username]);

            flash($msg)->success();

        } catch (\Exception $e) {

            flash($e->getMessage())->error();

        }



        return redirect(config('app.locale') . '/' . $post->uri);

    }

	

    

    

     

    









	public function sendMessage_app(SendMessageRequest $request)

    {

        // Get the Post

        if(empty($request->date_time)){$request->date_time=date("Y-m-d 00:00:00");}

        $post = Post::unarchived()->findOrFail($request->post_id);



        // New Message

        $message = new Message();

        $input = $request->only($message->getFillable());

        foreach ($input as $key => $value) {

            $message->{$key} = $value;

        }



        $message->post_id = $post->id;

        $message->from_user_id = $request->from_user_id;

        $message->to_user_id = $post->user_id;

        $message->to_name = $post->contact_name;

        $message->to_email = $post->email;

        $message->to_phone = $post->phone;

        $message->subject = $post->title;

        $message->date_time = $request->date_time;



        $attr = ['slug' => slugify($post->title), 'id' => $post->id];

        $message->message = $request->input('message')

            . '<br><br>'

            . t('Related to the ad')

            . ': <a href="' . lurl($post->uri, $attr) . '">' . t('Click here to see') . '</a>';



        // Save

        $message->save();



        // Save and Send user's resume

        if ($request->hasFile('filename')) {

            $message->filename = $request->file('filename');

            $message->save();

        }



        if($message)

        {return response()->json(['msg'=>'Your message has sent successfully']);}

        else

        {return response()->json(['msg'=>'Please check Your Email']);}

        // Send a message to publisher

      /*  

      i hashed it temprorly until finish email fix (21-12-2022) by abdelhay

      try {

             $post->notify(new SellerContacted($post, $message));

             return response()->json(['msg'=>'Your message has sent successfully']);

        } catch (\Exception $e) {

             flash($e->getMessage())->error();

             return response()->json(flash($e->getMessage())->error());

			return response()->json(['msg'=>'Please check Your Email']);

        }

		*/	

       // return redirect(config('app.locale') . '/' . $post->uri);

    }





    // Make an Offer

    public function makeAnOffer($postId, MakeAnOfferRequest $request)

    {

      

        $post = Post::unarchived()->findOrFail($postId);

        

        $makeanoffer = new Makeanoffer();



        $makeanoffer->post_id = $post->id;

        $makeanoffer->original_price = $post->price;

        $makeanoffer->offer_price = $request->input('offer_price');

        //$makeanoffer->description_text = $request->input('description_text');

        $makeanoffer->description_text = 'Start negotiation with buyer';

        if (auth()->user()->user_type_id == 2) {

            $makeanoffer->buyer_id = 0;

        } else {

            $makeanoffer->buyer_id = auth()->user()->id;

        }

        $makeanoffer->seller_id = $post->user_id;

        if (auth()->user()->user_type_id == 1) {

            $makeanoffer->is_read_admin = 1;

        } else {

            $makeanoffer->is_read_admin = 0;

        }

        if (auth()->user()->user_type_id == 2) {

            $makeanoffer->is_read_professional = 1;

        } else {

            $makeanoffer->is_read_professional = 0;

        }

        if (auth()->user()->user_type_id == 3) {

            $makeanoffer->is_read_individual = 1;

        } else {

            $makeanoffer->is_read_individual = 1;

        }

        $makeanoffer->approve_seller = 0;

        $makeanoffer->approve_buyer = 0;

        $makeanoffer->approve_admin = 0;

        $makeanoffer->status = 1;

        $makeanoffer->save();



        try {

            $post->notify(new SellerContacted($post, $message));



            $msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $post->contact_name]);

            flash($msg)->success();

        } catch (\Exception $e) {

            flash($e->getMessage())->error();

        }

          



       return redirect(config('app.locale') . '/' . $post->uri);



    }

    

    



    /**

     * Get similar Posts (Posts in the same Category)

     *

     * @param $cat

     * @param int $currentPostId

     * @return array|null|\stdClass

     */

    private function getCategorySimilarPosts($cat, $currentPostId = 0)

    {

        $limit = 20;

        $featured = null;



        // Get the sub-categories of the current ad parent's category

        $similarCatIds = [];

        if (!empty($cat)) {

            if ($cat->tid == $cat->parent_id) {

                $similarCatIds[] = $cat->tid;

            } else {

                if (!empty($cat->parent_id)) {

                    $similarCatIds = Category::trans()->where('parent_id', $cat->parent_id)->get()->keyBy('id')->keys()->toArray();

                    $similarCatIds[] = (int)$cat->parent_id;

                } else {

                    $similarCatIds[] = (int)$cat->tid;

                }

            }

        }



        // Get ads from same category

        $posts = [];

        if (!empty($similarCatIds)) {

            if (count($similarCatIds) == 1) {

                $similarPostSql = 'AND a.category_id=' . ((isset($similarCatIds[0])) ? (int)$similarCatIds[0] : 0) . ' ';

            } else {

                $similarPostSql = 'AND a.category_id IN (' . implode(',', $similarCatIds) . ') ';

            }

            $reviewedCondition = '';

            if (config('settings.single.posts_review_activation')) {

                $reviewedCondition = ' AND a.reviewed = 1';

            }

            $sql = 'SELECT a.* ' . '

				FROM ' . DBTool::table('posts') . ' as a

				WHERE a.country_code = :countryCode ' . $similarPostSql . '

					AND (a.verified_email=1 AND a.verified_phone=1)

					AND a.archived!=1 

					AND a.deleted_at IS NULL ' . $reviewedCondition . '

					AND a.id != :currentPostId

				ORDER BY a.id DESC

				LIMIT 0,' . (int)$limit;

            $bindings = [

                'countryCode' => config('country.code'),

                'currentPostId' => $currentPostId,

            ];



            $cacheId = 'posts.similar.category.' . $cat->tid . '.post.' . $currentPostId;

            $posts = Cache::remember($cacheId, $this->cacheExpiration, function () use ($sql, $bindings) {

                try {

                    $posts = DB::select($sql, $bindings);

                } catch (\Exception $e) {

                    return [];

                }



                return $posts;

            });

        }



        if (count($posts) > 0) {

            // Append the Posts 'uri' attribute

            $posts = collect($posts)->map(function ($post) {

                $post->title = mb_ucfirst($post->title);

                $post->uri = trans('routes.v-post', ['slug' => slugify($post->title), 'id' => $post->id]);



                return $post;

            })->toArray();



            // Randomize the Posts

            $posts = collect($posts)->shuffle()->toArray();



            // Featured Area Data

            $featured = [

                'title' => t('Similar Ads'),

                'link' => qsurl(config('app.locale') . '/' . trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except('c'), ['c' => $cat->tid])),

                'posts' => $posts,

            ];

            $featured = Arr::toObject($featured);

        }



        return $featured;

    }

	

	

	

	

	private function getCategorySimilarPosts1($cat, $currentPostId = 0,$country)

    {

        $limit = 20;

        $featured = null;



        // Get the sub-categories of the current ad parent's category

        $similarCatIds = [];

        if (!empty($cat)) {

          

            if ($cat->tid == $cat->parent_id) {

                $similarCatIds[] = $cat->tid;

            } else {

                if (!empty($cat->parent_id)) {

                    $similarCatIds = Category::trans()->where('parent_id', $cat->parent_id)->get()->keyBy('id')->keys()->toArray();

                    $similarCatIds[] = (int)$cat->parent_id;

                } else {

                    $similarCatIds[] = (int)$cat->tid;

                }

            }

        }

   



        // Get ads from same category

        $posts = [];

        if (!empty($similarCatIds)) {

            if (count($similarCatIds) == 1) {

                $similarPostSql = 'AND a.category_id=' . ((isset($similarCatIds[0])) ? (int)$similarCatIds[0] : 0) . ' ';

            } else {

                $similarPostSql = 'AND a.category_id IN (' . implode(',', $similarCatIds) . ') ';

            }

            $reviewedCondition = '';

            if (config('settings.single.posts_review_activation')) {

                $reviewedCondition = ' AND a.reviewed = 1';

            }

            $sql = 'SELECT a.* ' . '

				FROM ' . DBTool::table('posts') . ' as a

				WHERE a.country_code = :countryCode ' . $similarPostSql . '

					AND (a.verified_email=1 AND a.verified_phone=1)

					AND a.archived!=1 

					 ' . $reviewedCondition . '

					AND a.id != :currentPostId

				ORDER BY a.id DESC';

            $bindings = [

                'countryCode' => $country,

                'currentPostId' => $currentPostId,

            ];



            $cacheId = 'posts.similar.category.' . $cat->tid . '.post.' . $currentPostId;

			//$posts = DB::select($sql, $bindings);

            /*$posts = Cache::remember($cacheId, $this->cacheExpiration, function () use ($sql, $bindings) {

                try {

                    $posts = DB::select($sql, $bindings);

                } catch (\Exception $e) {

                    return [];

                }



                return $posts;

            });*/

			

			$posts = DB::select($sql, $bindings);

			





        }

        







        if (count($posts) > 0) {

            // Append the Posts 'uri' attribute

            $posts = collect($posts)->map(function ($post) {

                $post->title = mb_ucfirst($post->title);

                $post->uri = trans('routes.v-post', ['slug' => slugify($post->title), 'id' => $post->id]);



                return $post;

            })->toArray();



            // Randomize the Posts

            $posts = collect($posts)->shuffle()->toArray();



            // Featured Area Data

            $featured = [

                'title' => t('Similar Ads'),

                'link' => qsurl(config('app.locale') . '/' . trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except('c'), ['c' => $cat->tid])),

                'posts' => $posts,

            ];

            $featured = Arr::toObject($featured);

            

        }

		

		$posts1 = array();

		

		foreach($posts as $index=>$key) 

		{

		 array_push($posts1, $key);

		}



        return $posts1;

    }













	private function getCategorySimilarPosts2($cat, $currentPostId = 0)

    {

        $limit = 20;

        $featured = null;



        // Get the sub-categories of the current ad parent's category

        $similarCatIds = [];

        if (!empty($cat)) {

            if ($cat->tid == $cat->parent_id) {

                $similarCatIds[] = $cat->tid;

            } else {

                if (!empty($cat->parent_id)) {

                    $similarCatIds = Category::trans()->where('parent_id', $cat->parent_id)->get()->keyBy('id')->keys()->toArray();

                    $similarCatIds[] = (int)$cat->parent_id;

                } else {

                    $similarCatIds[] = (int)$cat->tid;

                }

            }

        }



        // Get ads from same category

        $posts = [];

        if (!empty($similarCatIds)) {

            if (count($similarCatIds) == 1) {

                $similarPostSql = 'AND a.category_id=' . ((isset($similarCatIds[0])) ? (int)$similarCatIds[0] : 0) . ' ';

            } else {

                $similarPostSql = 'AND a.category_id IN (' . implode(',', $similarCatIds) . ') ';

            }

            $reviewedCondition = '';

            if (config('settings.single.posts_review_activation')) {

                $reviewedCondition = ' AND a.reviewed = 1';

            }

            $sql = 'SELECT a.* ' . '

				FROM ' . DBTool::table('posts') . ' as a

				WHERE a.country_code = :countryCode ' . $similarPostSql . '

					AND (a.verified_email=1 AND a.verified_phone=1)

					AND a.archived!=1 

					AND a.deleted_at IS NULL ' . $reviewedCondition . '

					AND a.id != :currentPostId

				ORDER BY a.id DESC

				LIMIT 0,' . (int)$limit;

            $bindings = [

                'countryCode' => 'kw',

                'currentPostId' => $currentPostId,

            ];



            $posts = DB::select($sql, $bindings);

			

			//dd($posts);

        }



        if (count($posts) > 0) {

            // Append the Posts 'uri' attribute

            $posts = collect($posts)->map(function ($post) {

                $post->title = mb_ucfirst($post->title);

                $post->uri = trans('routes.v-post', ['slug' => slugify($post->title), 'id' => $post->id]);



                return $post;

            })->toArray();



            // Randomize the Posts

            $posts = collect($posts)->shuffle()->toArray();



        }



        return $posts;

    }







    /**

     * Get Posts in the same Location

     *

     * @param $city

     * @param int $currentPostId

     * @return array|null|\stdClass

     */

    private function getLocationSimilarPosts($city, $currentPostId = 0)

    {

        $distance = 50; // km OR miles

        $limit = 10;

        $featured = null;



        if (!empty($city)) {

            // Get ads from same location (with radius)

            $reviewedCondition = '';

            if (config('settings.single.posts_review_activation')) {

                $reviewedCondition = ' AND a.reviewed = 1';

            }

            $sql = 'SELECT a.*, 3959 * acos(cos(radians(' . $city->latitude . ')) * cos(radians(a.lat))'

                . '* cos(radians(a.lon) - radians(' . $city->longitude . '))'

                . '+ sin(radians(' . $city->latitude . ')) * sin(radians(a.lat))) as distance

				FROM ' . DBTool::table('posts') . ' as a

				INNER JOIN ' . DBTool::table('categories') . ' as c ON c.id=a.category_id AND c.active=1

				WHERE a.country_code = :countryCode

					AND (a.verified_email=1 AND a.verified_phone=1)

					AND a.archived!=1  ' . $reviewedCondition . '

					AND a.id != :currentPostId

				HAVING distance <= ' . $distance . ' 

				ORDER BY distance ASC, a.id DESC

				LIMIT 0,' . (int)$limit;

            $bindings = [

                'countryCode' => config('country.code'),

                'currentPostId' => $currentPostId,

            ];



            $cacheId = 'posts.similar.city.' . $city->id . '.post.' . $currentPostId;

            $posts = Cache::remember($cacheId, $this->cacheExpiration, function () use ($sql, $bindings) {

                try {

                    $posts = DB::select($sql, $bindings);

                } catch (\Exception $e) {

                    return [];

                }



                return $posts;

            });



            if (count($posts) > 0) {

                // Append the Posts 'uri' attribute

                $posts = collect($posts)->map(function ($post) {

                    $post->title = mb_ucfirst($post->title);

                    $post->uri = trans('routes.v-post', ['slug' => slugify($post->title), 'id' => $post->id]);



                    return $post;

                })->toArray();



                // Randomize the Posts

                $posts = collect($posts)->shuffle()->toArray();



                // Featured Area Data

                $featured = [

                    'title' => t('More ads at :distance :unit around :city', [

                        'distance' => $distance,

                        'unit' => unitOfLength(config('country.code')),

                        'city' => $city->name,

                    ]),

                    'link' => qsurl(config('app.locale') . '/' . trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except(['l', 'location']), ['l' => $city->id])),

                    'posts' => $posts,

                ];

                //$featured = Arr::toObject($featured);

            } else {

                $featured = [

                    

                    'posts' => array()

                ];

            }

        }



        return $featured;

    }

}

