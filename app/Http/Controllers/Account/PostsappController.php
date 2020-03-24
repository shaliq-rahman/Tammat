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

namespace App\Http\Controllers\Account;

use App\Helpers\Arr;
use App\Helpers\Search;

use App\Http\Controllers\Search\Traits\PreSearchTrait;
use App\Models\Post;
use App\Models\Category;
use App\Models\SavedPost;
use App\Models\SavedSearch;
use App\Models\Message;

use App\Models\Picture;

use App\Models\Scopes\ReviewedScope;
use App\Mail\PostDeleted;
use App\Models\Scopes\VerifiedScope;
//use Carbon\Carbon;
//use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Mail;
//use Illuminate\Support\Facades\Request;
use Torann\LaravelMetaTags\Facades\MetaTag;

use Illuminate\Http\Request;
use App\Http\Controllers\Post\Traits\CustomFieldTrait;

use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Models\HomeSection;
use App\Models\SubAdmin1;
use App\Helpers\DBTool;
use App\Models\City;
use App\Models\User;
use App\Models\Newsletter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

#use Illuminate\Support\Carbon;
use Carbon\Carbon;
//use DB;


class PostsappController extends AccountappBaseController
{
    use PreSearchTrait;

    private $perPage = 12;

    public function __construct()
    {
        parent::__construct();

        $this->perPage = (is_numeric(config('settings.listing.items_per_page'))) ? config('settings.listing.items_per_page') : $this->perPage;
    }

    /**
     * @param $pagePath
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getPage($pagePath, Request $request)
    {
	if(!empty($request->lang))
	{
	$lang = $request->lang;
	}
	else
	{
	$lang = 'en';
	}
        switch ($pagePath) {
            case 'my-posts':
                return $this->getMyPosts($request->userid,$lang);
                break;
            case 'archived':
                return $this->getArchivedPosts($request->userid,$lang);
                break;
            case 'favourite':
                return $this->getFavouritePosts($request->userid,$lang);
                break;
            case 'pending-approval':
                return $this->getPendingApprovalPosts($request->userid,$lang);
                break;
            default:
                abort(404);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getMyPosts($userid,$lang)
    {
		$myPosts = Post::where('user_id', $userid)
             ->verified()
			 ->unarchived()
			 ->reviewed()
			 //->select('*',\DB::raw('(SELECT CONCAT("https://www.dealnotdeal.com/storage/", pictures.filename) as filename  FROM pictures WHERE pictures.post_id = posts.id AND pictures.position = 1 ) AS image'))  
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        
		$count = $myPosts->count();
		$posts = $myPosts->get();
		
		if($count>0)
		{
		$i=0;
		foreach($posts as $key => $post){
		$post->fetchdate = date('d F Y h:i',strtotime($post->created_at));
		
		$res = $this->getimage($post->id);
		$post->image = $res['image'];
		$post->picture = $res['picture'];
		
		$getcurrencycountry = \DB::table('countries')
                        ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
                        ->select('currencies.*')
                        ->where('countries.code', '=', strtoupper($post->country_code))
                        ->first();
						
		if ($post->price > 0)
            		                {
            						    $get_currency = \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);
            		                }
            						else
            						{
            						    $get_currency = t('Free');
            						}
									
									$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();
                                        $username = $getusernamedetail->username;  
										$user_created_at = $getusernamedetail->created_at;       
										//$post->liveCatName = $liveCatName;
										$post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));
										$post->user_created_at = $post->user_created_at->ago();
										
										
										$post->username = $username;
                            
						                $post->currency = $get_currency;
		
		
		
										
		
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
		
		$post->liveCatName = $liveCatName;
		
		
		$qrypack = DB::select("select * from payments where post_id='".$post->id."' and active='1' ");
		if(!empty($qrypack))
		{
		$qrypack1 = DB::select("select * from packages where id='".$qrypack[0]->package_id."' and active='1' ");
		$post->py_package_id = $qrypack[0]->package_id;
		$post->package = $qrypack1;
		}
		else
		{
		$post->py_package_id = 'No Value';
		$post->package = 'No Value';
		} 
		
		
		$postType = \App\Models\PostType::findTransApp($post->post_type_id,$lang);
											//return $postType;
		$post->postType = $postType;
		
		
		
		if (!empty($userid))
													{
													$scount = \App\Models\SavedPost::where('user_id', $userid)->where('post_id', $post->id)->count();
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
		}
		return response()->json(['results'=>$posts,'numrecords'=>$count]);
    }

    /**
     * @param $pagePath
     * @param null $postId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getArchivedPosts($userid,$lang)
    {
        $archivedPosts = Post::where('user_id', $userid)
            ->archived()
        	///->select('*',\DB::raw('(SELECT CONCAT("https://www.dealnotdeal.com/storage/", pictures.filename) as filename  FROM pictures WHERE pictures.post_id = posts.id AND pictures.position = 1 ) AS image'))  
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
		$count = $archivedPosts->count();
		$posts = $archivedPosts->get();
		
		
		if($count>0)
		{
		$i=0;
		foreach($posts as $key => $post){
		$post->fetchdate = date('d F Y h:i',strtotime($post->created_at));
		
		$res = $this->getimage($post->id);
		$post->image = $res['image'];
		$post->picture = $res['picture'];
		
		
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
									
									$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();
                                        $username = $getusernamedetail->username;  
										$user_created_at = $getusernamedetail->created_at;       
										//$post->liveCatName = $liveCatName;
										$post->username = $username;
										
                            			$post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));
										$post->user_created_at = $post->user_created_at->ago();
							
						                $post->currency = $get_currency;
										
										
										
		
		
		$qrypack = DB::select("select * from payments where post_id='".$post->id."' and active='1' ");
		if(!empty($qrypack))
		{
		$qrypack1 = DB::select("select * from packages where id='".$qrypack[0]->package_id."' and active='1' ");
		$post->py_package_id = $qrypack[0]->package_id;
		$post->package = $qrypack1;
		}
		else
		{
		$post->py_package_id = 'No Value';
		$post->package = 'No Value';
		} 
								
								
			$postType = \App\Models\PostType::findTransApp($post->post_type_id,$lang);
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
		
		$post->liveCatName = $liveCatName;
				
		if (!empty($userid))
													{
													$scount = \App\Models\SavedPost::where('user_id', $userid)->where('post_id', $post->id)->count();
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
		}
		
		
		//echo $archivedPosts->toSql();
		return response()->json(['results'=>$posts,'numrecords'=>$count]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFavouritePosts($userid,$lang)
    {
		$favouritePosts = SavedPost::whereHas('post', function($query) {
               /*$query->currentCountry();*/
            })
            ->where('user_id', $userid)
        	//->select('*',\DB::raw('(SELECT CONCAT("https://www.dealnotdeal.com/storage/", pictures.filename) as filename  FROM pictures WHERE pictures.post_id = saved_posts.post_id AND pictures.position = 1 ) AS image'))  
            //->with(['post.pictures', 'post.city'])
            ->orderByDesc('id');			
			//echo $favouritePosts;
        
		$count = $favouritePosts->count();
		$posts = $favouritePosts->get();
		
		if($count>0)
		{
		$i=0;
		foreach($posts as $key => $post){
		$post->fetchdate = date('d F Y h:i',strtotime($post->post->created_at));
		
		$res = $this->getimage($post->post->id);
		$post->image = $res['image'];
		$post->picture = $res['picture'];
		
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
									
									$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();
                                        $username = $getusernamedetail->username;  
										$user_created_at = $getusernamedetail->created_at;       
										//$post->liveCatName = $liveCatName;
										
										
										$post->username = $username;
                            			
										$post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));
										$post->user_created_at = $post->user_created_at->ago();
										
						                $post->currency = $get_currency;
										
										
		
		
		$qrypack = DB::select("select * from payments where post_id='".$post->id."' and active='1' ");
		if(!empty($qrypack))
		{
		$qrypack1 = DB::select("select * from packages where id='".$qrypack[0]->package_id."' and active='1' ");
		$post->py_package_id = $qrypack[0]->package_id;
		$post->package = $qrypack1;
		}
		else
		{
		$post->py_package_id = 'No Value';
		$post->package = 'No Value';
		} 
			
			
			$postType = \App\Models\PostType::findTransApp($post->post->post_type_id,$lang);
											//return $postType;
		$post->post->postType = $postType;							
					$liveCat = \App\Models\Category::findTransApp($post->post->category_id, $lang);
											//return $liveCat;
										$post->liveCat = $liveCat;
										
										// Check parent
										if (empty($liveCat->post->parent_id)) {
											$liveCatParentId = $liveCat->id;
											$liveCatType = $liveCat->type;
										} else {
											$liveCatParentId = $liveCat->post->parent_id;
											
											$liveParentCat = \App\Models\Category::findTransApp($liveCat->post->parent_id, $lang);
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
		
		$post->liveCatName = $liveCatName;
		if (!empty($userid))
													{
													$scount = \App\Models\SavedPost::where('user_id', $userid)->where('post_id', $post->id)->count();
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
		}
		
		return response()->json(['results'=>$posts,'numrecords'=>$count]);	
	}




	public function getimage($postid)
	{
		$qryimg = Picture::where(['post_id'=>$postid,'position'=>1]);
		
		if($qryimg->count()>0)
		{
		$pics = $qryimg->get();
		$image = "https://www.dealnotdeal.com/storage/".$pics[0]->filename;
		$picture = $qryimg->get();
		}
		else
		{
		$picture = array();
		$image = '';
		}
		return array('picture' => $picture, 'image' => $image);
	}




    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPendingApprovalPosts($userid,$lang)
    {
		$pendingPosts = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
            ->where('user_id', $userid)
            ->unverified()
            //->select('*',\DB::raw('(SELECT CONCAT("https://www.dealnotdeal.com/storage/", pictures.filename) as filename  FROM pictures WHERE pictures.post_id = posts.id AND pictures.position = 1 ) AS image'))  
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        
		$count = $pendingPosts->count();
		$posts = $pendingPosts->get();
		
		
		if($count>0)
		{
		$i=0;
		foreach($posts as $key => $post){
		$post->fetchdate = date('d F Y h:i',strtotime($post->created_at));
		
		$res = $this->getimage($post->id);
		$post->image = $res['image'];
		$post->picture = $res['picture'];
		
		
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
									
									$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();
                                        $username = $getusernamedetail->username;  
										$user_created_at = $getusernamedetail->created_at;       
										//$post->liveCatName = $liveCatName;
										$post->username = $username;
                            			
										$post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));
										$post->user_created_at = $post->user_created_at->ago();
											
						                $post->currency = $get_currency;
										
										
										
		
		
		$qrypack = DB::select("select * from payments where post_id='".$post->id."' and active='1' ");
		if(!empty($qrypack))
		{
		$qrypack1 = DB::select("select * from packages where id='".$qrypack[0]->package_id."' and active='1' ");
		$post->py_package_id = $qrypack[0]->package_id;
		$post->package = $qrypack1;
		}
		else
		{
		$post->py_package_id = 'No Value';
		$post->package = 'No Value';
		} 
			$postType = \App\Models\PostType::findTransApp($post->post_type_id,$lang);
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
		
		$post->liveCatName = $liveCatName;
		
		if (!empty($userid))
													{
													$scount = \App\Models\SavedPost::where('user_id', $userid)->where('post_id', $post->id)->count();
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
		}
		
		return response()->json(['results'=>$posts,'numrecords'=>$count]);
    }

    /**
     * @param HttpRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSavedSearch(HttpRequest $request)
    {
        $data = [];

        // Get QueryString
        $tmp = parse_url(url(Request::getRequestUri()));
        $queryString = (isset($tmp['query']) ? $tmp['query'] : 'false');
        $queryString = preg_replace('|\&pag[^=]*=[0-9]*|i', '', $queryString);

        // CATEGORIES COLLECTION
        $cats = Category::trans()->orderBy('lft')->get();
        $cats = collect($cats)->keyBy('translation_of');
        view()->share('cats', $cats);

        // Search
        $savedSearch = SavedSearch::currentCountry()
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->simplePaginate($this->perPage,['*'],'pag');
		
        if (collect($savedSearch->getCollection())->keyBy('query')->keys()->contains($queryString))
        {
            parse_str($queryString, $queryArray);

            // QueryString vars
            $cityId = isset($queryArray['l']) ? $queryArray['l'] : null;
            $location = isset($queryArray['location']) ? $queryArray['location'] : null;
            $adminName = (isset($queryArray['r']) && !isset($queryArray['l'])) ? $queryArray['r'] : null;

            // Pre-Search
            $preSearch = [
                'city'  => $this->getCity($cityId, $location),
                'admin' => $this->getAdmin($adminName),
            ];
			
            if ($savedSearch->getCollection()->count() > 0) {
                // Search
                $search = new Search($preSearch);
                $data = $search->fechAll();
            }
        }
        $data['savedSearch'] = $savedSearch;

        // Meta Tags
        MetaTag::set('title', t('My saved search'));
        MetaTag::set('description', t('My saved search on :app_name', ['app_name' => config('settings.app.name')]));

        view()->share('pagePath', 'saved-search');

        return view('account.saved-search', $data);
    }
	
	
	
	public function repost($pagePath, $postId = null)
    {
            $res = false;
            if (is_numeric($postId) and $postId > 0) {
                $res = Post::find($postId)->update([
                    'archived'   => 0,
                    'created_at' => Carbon::now(),
                ]);
            }
            if (!$res) {
				return response()->json(['results'=>"The repost has done successfully."]);
            } else {
				return response()->json(['results'=>"The repost has failed. Please try again."]);
            } 
    }
	
	
	/**
	 * @param $pagePath
	 * @param null $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	 
    public function destroy($pagePath, $id = null)
    {
        // Get Entries ID
        $ids = [];
        if (request()->filled('entries')) {
            $ids = request()->input('entries');
        } else {
            if (!is_numeric($id) && $id <= 0) {
                $ids = [];
            } else {
                $ids[] = $id;
            }
        }
        
        Post::where('user_id',  auth()->user()->id)->whereIn('id', $ids)->update(['archived' => 1]);
        

        // Delete
    // $nb = 1;
    //     if ($pagePath == 'favourite') {
    //         $savedPosts = SavedPost::where('user_id', auth()->user()->id)->whereIn('post_id', $ids);
    //         if ($savedPosts->count() > 0) {
    //             $nb = $savedPosts->delete();
    //         }
    //     } elseif ($pagePath == 'saved-search') {
    //         $nb = SavedSearch::destroy($ids);
    //     } else {
    //         foreach ($ids as $item) {
    //             $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($item);
    //             if (!empty($post)) {
    //                 $tmpPost = Arr::toObject($post->toArray());

    //                 // Delete Entry
    //                 $nb = $post->delete();

    //                 // Send an Email confirmation
				// 	if (!empty($tmpPost->email)) {
				// 		try {
				// 			Mail::send(new PostDeleted($tmpPost));
				// 		} catch (\Exception $e) {
				// 			flash($e->getMessage())->error();
				// 		}
				// 	}
    //             }
    //         }
    //     }

        // Confirmation
        // if ($nb == 0) {
            // flash(t("No deletion is done. Please try again."))->error();
        // } else {
        // }
		return response()->json(['results'=>'No deletion is done. Please try again.']);
    }
    
    
    
    
    public function destroypost($pagePath, $id = null)
    {
        // Get Entries ID
        $ids = [];
        if (request()->filled('entries')) {
            $ids = request()->input('entries');
        } else {
            if (!is_numeric($id) && $id <= 0) {
                $ids = [];
            } else {
                $ids[] = $id;
            }
        }

        // Delete
        $nb = 0;
        if ($pagePath == 'saved-search') {
            $nb = SavedSearch::destroy($ids);
        } else {
            foreach ($ids as $item) {
                $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($item);
                if (!empty($post)) {
                    $tmpPost = Arr::toObject($post->toArray());

                    // Delete Entry
                    $nb = $post->delete();

                    // Send an Email confirmation
					if (!empty($tmpPost->email)) {
						try {
							Mail::send(new PostDeleted($tmpPost));
						} catch (\Exception $e) {
							flash($e->getMessage())->error();
						}
					}
                }
            }
        }

        // Confirmation
        if ($nb == 0) {
			return response()->json(['results'=>'No deletion is done. Please try again.']);
        } else {
            $count = count($ids);
            return response()->json(['results'=>'entities has been deleted successfully']);
        }

        
    }
    
    
    
    
    
     public function destroyfavpost($pagePath, $id = null, Request $request)
    {
        // Get Entries ID
        $ids = [];
        if (request()->filled('entries')) {
            $ids = request()->input('entries');
        } else {
            if (!is_numeric($id) && $id <= 0) {
                $ids = [];
            } else {
                $ids[] = $id;
            }
        }

        // Delete
        $nb = 0;
        if ($pagePath == 'favourite') {
            $savedPosts = SavedPost::where('user_id', $request->userid)->whereIn('post_id', $ids);
            if ($savedPosts->count() > 0) {
                $nb = $savedPosts->delete();
            }
        } elseif ($pagePath == 'saved-search') {
            $nb = SavedSearch::destroy($ids);
        } else {
            foreach ($ids as $item) {
                $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($item);
                if (!empty($post)) {
                    $tmpPost = Arr::toObject($post->toArray());

                    // Delete Entry
                    $nb = $post->delete();

                    // Send an Email confirmation
					if (!empty($tmpPost->email)) {
						try {
							Mail::send(new PostDeleted($tmpPost));
						} catch (\Exception $e) {
							flash($e->getMessage())->error();
						}
					}
                }
            }
        }

        // Confirmation
        if ($nb == 0) {
			return response()->json(['results'=>'No deletion is done. Please try again.']);
        } else {
            $count = count($ids);
            return response()->json(['results'=>'entities has been deleted successfully']);
        }

        
    }
    
    
    
    
    
    
    
    
    public function DeliveryPost(HttpRequest $request)
    {
        
        $Messagevalue = Message::find($request->message_id);
        
        $data['message_id'] = $request->message_id;
        $data['timeofpick'] = $request->timeofpick;
        $data['dateofpick'] = $request->dateofpick;
        $data['buyername'] = $request->buyername;
        $data['message_string'] = $request->message;
        $data['postsubject'] = $request->postsubject;
        $data['sellerusername'] = $request->sellerusername;
        
        
        
        $responce = \DB::table('delivery')->insert(
            ['message_id' => $request->message_id, 
             'timeofpick' => $request->timeofpick,
             'dateofpick' => $request->dateofpick,
             'buyername' =>  $request->buyername,
             'message' =>    $request->message,
             'created_at' => date('Y-m-d H:i:s'),
             'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        
        $buyername = $request->buyername;
        $from_email =$Messagevalue->from_email;
         
        Mail::send('emails.post.delivery', $data, function($message) use ($buyername, $from_email)
        {
            $message->to('delivery@dealnotdeal.com');
            $message->subject('Request a Delivery');
            if(!empty($from_email))
            {
                $message->replyTo($from_email, $buyername);        
            }
        });    
        
        return redirect()->back()->with('success',t('Message successfully sent'));
        
    }
    
}
