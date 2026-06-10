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

namespace App\Http\Controllers\Ajax;

use App\Models\Picture;
use App\Models\Post;
use App\Http\Controllers\FrontController;
use App\Models\SavedPost;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Currency;
use App\Models\SavedUser;
use App\Models\SavedSearch;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use Illuminate\Http\Request;
use Larapen\TextToImage\Facades\TextToImage;
use DB;

class PostController extends FrontController
{
    /**
     * PostController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    
	 
	/**
	 * GetPackages
	 * @param Request $request
     * @return \Illuminate\Http\JsonResponse
	 *  
	 */
	public function GetPackages(Request $request)
	{
		
		if(!empty($request->user_id)){
		 $info_user = DB::table('users')
              ->where('id', $request->user_id)
			 // ->where('id', '1277')
			->first();
			$no_points=$info_user->no_points;
		}else{
			$no_points=0;
			 
			}
			
	 $packages = Package::where('translation_lang',$request->translation_lang)->where('currency_code',$request->currency)->orderBy('lft')->get();
        
        
       
            $CurrencyObj = Currency::where('code',$request->currency)->first();
            foreach ($packages as $p){
                $p->price = $p->price;
                // $p->price = getCurrencyAmount(Session::get('currency'),$p->price);
                $p->currency = $CurrencyObj;
            }
       
       
        
		//$packages->count();
		
		
		 return response()->json(['CurrentUserPoints'=>$no_points,'packages'=>$packages]);
			
	}
	
	
	
	
	 
	/**
	 * AccountDetails
	 * @param Request $request
     * @return \Illuminate\Http\JsonResponse
	 *  
	 */
	public function AccountDetails(Request $request)
	{
		
	 
		 $result = DB::table('users')->where('id', $request->user_id)->first();
             
			$info_user=array(); 
			$info_user['name']=$result->name;
			$info_user['email']=$result->email;
			$info_user['phone']=$result->phone;
			$info_user['city']=$result->city;
			$info_user['address']=$result->address;
		    $info_user['profile_image']='https://www.tmmat.com/ProfilePictures/'.$result->profile_image; 
			$info_user['user_type']=$result->user_type_id; 
		 return response()->json(['info_user'=>$info_user]);
			
	}
	
	
	 
	/**
	 * GetMenuCounts
	 * @param Request $request
     * @return \Illuminate\Http\JsonResponse
	 *  
	 */
	public function GetMenuCounts(Request $request)
	{
		//count number of points
		if(!empty($request->user_id)){
		 $info_user = DB::table('users')
              ->where('id', $request->user_id)			 
			  ->first();
			$no_points=$info_user->no_points;
		    }else{$no_points=0;}
		
		//count unread recieved offers
		$unread_recieved_offers_count = DB::table('makeanoffers')->where('seller_id',$request->user_id)->where('is_read','>',0)->count();
		
		//count unread messages	
		$unread_messages_count = DB::table('messages')->where('to_user_id',$request->user_id)->where('is_read','>',0)->count();
		
		//count all notifications messages	
		$unread_notifications_count = $unread_recieved_offers_count+$unread_messages_count;
         
		
		 return response()->json(['CurrentUserPoints'=>$no_points,'unread_recieved_offers_count'=>$unread_recieved_offers_count,'unread_messages_count'=>$unread_messages_count,'unread_notifications_count'=>$unread_notifications_count]);
			
	}
	
	
	public function GetUserPackagesHistory(Request $request)
	{
		 $info_Packages = DB::table('payments')
		 ->leftjoin('packages','packages.id','=','payments.package_id')
		 ->select('packages.name','packages.price','packages.currency_code','packages.description','packages.no_points','payments.created_at as created_date')
		 ->where('packages.translation_lang',$request->translation_lang)
		 ->where('user_id', $request->user_id)
		 ->where('payments.active','>',0)
		 ->orderby('payments.created_at','desc')
		 ->get();
		
		 $info_user = DB::table('users')
              ->where('id', $request->user_id)
			 // ->where('id', '1277')
			->first();
			
	 
        
		
		
		 return response()->json(['CurrentUserPoints'=>$info_user->no_points,'packages'=>$info_Packages]);
			
	}
	
	
	
    public function savePost(Request $request)
    {
        $postId = $request->input('postId');
        
        $status = 0;
        if (auth()->check()) {
            $savedPost = SavedPost::where('user_id', auth()->user()->id)->where('post_id', $postId);
            if ($savedPost->count() > 0) {
                // Delete SavedPost
                $savedPost->delete();
            } else {
                // Store SavedPost
                $savedPostInfo = [
                    'user_id' => auth()->user()->id,
                    'post_id' => $postId,
                ];
                $savedPost = new SavedPost($savedPostInfo);
                $savedPost->save();
                $status = 1;
            }
        }
        
        $result = [
            'logged'   => (auth()->check()) ? auth()->user()->id : 0,
            'postId'   => $postId,
            'status'   => $status,
            'loginUrl' => url(config('lang.abbr') . '/' . trans('routes.login')),
        ];
        
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
	
	
	  /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveFavUser(Request $request)
    {
        $FavUserId = $request->input('FavUserId');
        $user_id = $request->input('user_id');
        
        $status = 0;
        if ($user_id) {
            $savedPost = SavedUser::where('user_id', $user_id)->where('fav_user_id', $FavUserId);
            if ($savedPost->count() > 0) {
                // Delete SavedPost
                $savedPost->delete();
            } else {
                // Store SavedPost
                $savedPostInfo = [
                    'user_id' => $user_id,
                    'fav_user_id' => $FavUserId,
                ];
                $savedPost = new SavedUser($savedPostInfo);
                $savedPost->save();
                $status = 1;
            }
        }
        
        $result = [            
            'FavUserId'   => $FavUserId,
            'status'   => $status,          
        ];
        
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
	
	
	
	public function savePost_app(Request $request)
    {
        $postId = $request->input('postId');
		$userid = $request->input('user_id');
        
        $status = 0;
        if ($userid) {
            $savedPost = SavedPost::where('user_id', $userid)->where('post_id', $postId);
            if ($savedPost->count() > 0) {
                // Delete SavedPost
                $savedPost->delete();
            } else {
                // Store SavedPost
                $savedPostInfo = [
                    'user_id' => $userid,
                    'post_id' => $postId,
                ];
                $savedPost = new SavedPost($savedPostInfo);
                $savedPost->save();
                $status = 1;
            }
        }
        
        
        
        return response()->json(['results'=>'Post has been saved']);
    }
    
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSearch(Request $request)
    {
        $queryUrl = $request->input('url');
        $tmp = parse_url($queryUrl);
        $query = $tmp['query'];
        parse_str($query, $tab);
        $keyword = $tab['q'];
        $countPosts = $request->input('countPosts');
        if ($keyword == '') {
            return response()->json([], 200, [], JSON_UNESCAPED_UNICODE);
        }
        
        $status = 0;
        if (auth()->check()) {
            $savedSearch = SavedSearch::where('user_id', auth()->user()->id)->where('keyword', $keyword)->where('query', $query);
            if ($savedSearch->count() > 0) {
                // Delete SavedSearch
                $savedSearch->delete();
            } else {
                // Store SavedSearch
                $savedSearchInfo = [
                    'country_code' => config('country.code'),
                    'user_id'      => auth()->user()->id,
                    'keyword'      => $keyword,
                    'query'        => $query,
                    'count'        => $countPosts,
                ];
                $savedSearch = new SavedSearch($savedSearchInfo);
                $savedSearch->save();
                $status = 1;
            }
        }
        
        $result = [
            'logged'   => (auth()->check()) ? auth()->user()->id : 0,
            'query'    => $query,
            'status'   => $status,
            'loginUrl' => url(config('lang.abbr') . '/' . trans('routes.login')),
        ];
        
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPhone(Request $request)
    {
        $postId = $request->input('postId', 0);
        
        $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->where('id', $postId)->first();
        
        if (empty($post)) {
            return response()->json(['error' => ['message' => t("Error. Post doesn't exist."),], 404]);
        }
        
        $post->phone = TextToImage::make($post->phone, IMAGETYPE_PNG, ['color' => '#FFFFFF']);
        
        return response()->json(['phone' => $post->phone], 200, [], JSON_UNESCAPED_UNICODE);
    }
	
	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function picturesReorder(Request $request)
	{
		$params = $request->input('params');
		
		$result = ['status' => 0];
		
		if (auth()->check()) {
			if (isset($params['stack']) && count($params['stack']) > 0) {
				$statusOk = false;
				foreach ($params['stack'] as $position => $item) {
					if (isset($item['key']) && !empty($item['key'])) {
						$picture = Picture::find($item['key']);
						if (!empty($picture)) {
							$picture->position = $position;
							$picture->save();
							
							$statusOk = true;
						}
					}
				}
				if ($statusOk) {
					$result = ['status' => 1];
				}
			}
		}
		
		return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
	}
}
