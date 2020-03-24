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

use App\Models\Message;
use App\Http\Controllers\FrontController;
use Illuminate\Http\Request;
use DB;

use App\Models\Post;
use App\Models\Payment;
use App\Models\Makeanoffer;
use App\Models\SavedPost;
use App\Models\SavedSearch;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;

class ConversationController extends FrontController
{
	/**
	 * MessageController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function checkNewMessages(Request $request)
	{
	    
        $count_offer = 0;
        $getMakeofferCount = \DB::table('makeanoffers')
            ->where(function ($q) {
				$q->where('makeanoffers.buyer_id', auth()->user()->id)->orWhere('makeanoffers.seller_id', auth()->user()->id);
			})
            ->select('*')
            ->where('makeanoffers.status', '=', 1)
            ->join('posts', 'makeanoffers.post_id', '=', 'posts.id')
            ->orderBy('makeanoffers.id','desc')
            ->limit(50)
            ->get();
        
        if(count($getMakeofferCount) > 0)
        {
           foreach($getMakeofferCount as $value)
           {
               if($value->is_read == '0')
               {
                   if(auth()->user()->id != $value->offer_maker_id || $value->approve_seller == 1 || $value->approve_seller == 2)
                   {
                       if($value->approve_seller == 1)
                       {
                            if($value->counter_offer == '0')      
                            {
                                if(auth()->user()->id == $value->buyer_id)
                                {
                                    $count_offer++;
                                }
                                else
                                {
                                    
                                }
                            }
                            else
                            {
                                if(auth()->user()->id == $value->offer_maker_id)
                                {
                                    $count_offer++;
                                }
                            }
                       }
                       else
                       {
                           if($value->approve_seller == 2)
                           {
                                if(auth()->user()->id != $value->offer_maker_id)
                                {
                                    
                                }
                                else
                                {
                                    $count_offer++;
                                }
                           }
                           else
                           {
                               $count_offer++;
                           }
                       }
                    }
               }
           }
        }

		$countLimit = 20;
		$countConversationsWithNewMessages = 0;
		$oldValue = $request->input('oldValue');
		$languageCode = $request->input('languageCode');
		
		if (auth()->check()) {
		$countConversationsWithNewMessages = Message::countConversationsWithNewMessages($countLimit);
		}
		
		$result = [
			'logged'                            => (auth()->check()) ? auth()->user()->id : 0,
			'countLimit'                        => (int)$countLimit,
			'countConversationsWithNewMessages' => (int)$countConversationsWithNewMessages,
			'countConversationsWithNewOffer'    => (int)$count_offer,
			'countConversationsWithNewMessagesOffer' => (int)$countConversationsWithNewMessages+(int)$count_offer,
			'oldValue'                          => (int)$oldValue,
			'loginUrl'                          => url(config('lang.abbr') . '/' . trans('routes.login')),
		];
		
		return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	
	public function checkNewMessages_app(Request $request)
	{
	  	$user_id = $request->user_id;  
        $count_offer = 0;
        $getMakeofferCount = \DB::table('makeanoffers')
            ->where('makeanoffers.buyer_id', $user_id)->orWhere('makeanoffers.seller_id', $user_id)
            ->select('*')
            ->orderBy('id','desc')
            ->limit(50)
            ->get();
            
        if(count($getMakeofferCount) > 0)
        {
           foreach($getMakeofferCount as $value)
           {
               if($value->is_read == '0')
               {
                   if($user_id != $value->offer_maker_id || $value->approve_seller == 1 || $value->approve_seller == 2)
                   {
                       if($value->approve_seller == 1)
                       {
                            if($value->counter_offer == '0')      
                            {
                                if($user_id == $value->buyer_id)
                                {
                                    $count_offer++;
                                }
                                else
                                {
                                    
                                }
                            }
                            else
                            {
                                if($user_id == $value->offer_maker_id)
                                {
                                    $count_offer++;
                                }
                            }
                       }
                       else
                       {
                           if($value->approve_seller == 2)
                           {
                                if($user_id != $value->offer_maker_id)
                                {
                                    
                                }
                                else
                                {
                                    $count_offer++;
                                }
                           }
                           else
                           {
                               $count_offer++;
                           }
                       }
                    }
               }
           }
        }

		$countLimit = 20;
		$countConversationsWithNewMessages = 0;
		$oldValue = $request->input('oldValue');
		$languageCode = $request->input('languageCode');
		
		if ($user_id) {
			$qry = DB::select("select * from messages where to_user_id='".$user_id."' and is_read='0' and deleted_by IS NULL and parent_id='0' ");
			$countConversationsWithNewMessages = count($qry);
		}
		
		
		
		$myPosts = Post::where('user_id', $user_id)
            ->verified()
			->unarchived()
			->reviewed()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        $countMyPosts = $myPosts->count();
		
		
		
		// Archived Posts
        $archivedPosts = Post::where('user_id', $user_id)
            ->archived()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        $countArchivedPosts = $archivedPosts->count();

        // Favourite Posts
        $favouritePosts = SavedPost::whereHas('post', function($query) {
                // $query->currentCountry();
            })
            ->where('user_id', $user_id)
            ->with(['post.pictures', 'post.city'])
            ->orderByDesc('id');
        $countFavouritePosts = $favouritePosts->count();

        // Pending Approval Posts
        $pendingPosts = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
            // ->currentCountry()
            ->where('user_id', $user_id)
            ->unverified()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        $countPendingPosts = $pendingPosts->count();

        // Save Search
        $savedSearch = SavedSearch::
            where('user_id', $user_id)
            ->orderByDesc('id');
        $countSavedSearch = $savedSearch->count();
        
        // Conversations
		$conversations1 = Message::with('latestReply')
// 			->whereHas('post', function($query) {
// 				$query->currentCountry();
// 			})
			->byUserId($user_id)
			->where('parent_id', 0)
			->orderByDesc('id');
		$countConversations = $conversations1->count();
		
		// Payments
		$transactions1 = Payment::whereHas('post', function($query)  use ($user_id) {
				$query->currentCountry()->whereHas('user', function($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
			})
			->with(['post', 'paymentMethod', 'package' => function ($builder) { $builder->with(['currency']); }])
			->orderByDesc('id');
		$countTransactions = $transactions1->count();
		
		
		$seller_id = $user_id;
        $buyer_id = $user_id;
        $status = 1;
        $makeanoffers =  DB::table('makeanoffers')->where(function ($query) use ($seller_id, $buyer_id, $status)
        {
            // if(auth()->user()->user_type_id == 2)
            // {
            //     $query->where('makeanoffers.seller_id', '=', $seller_id);
            // }
            // elseif(auth()->user()->user_type_id == 3)
            // {
            //   $query->where('makeanoffers.buyer_id', '=', $buyer_id); 
            // }
             
            $query->where('makeanoffers.buyer_id', $seller_id)->orWhere('makeanoffers.seller_id', $seller_id);
            $query->where('makeanoffers.status', '=', $status);
        })
        ->join('posts', 'makeanoffers.post_id', '=', 'posts.id')
        ->select('makeanoffers.*', 'posts.country_code' , 'posts.user_id', 'posts.category_id', 'posts.post_type_id', 'posts.title', 'posts.description', 'posts.tags', 'posts.price', 'posts.negotiable', 'posts.contact_name', 'posts.email', 'posts.phone', 'posts.phone_hidden', 'posts.address', 'posts.city_id', 'posts.lon', 'posts.lat', 'posts.ip_addr', 'posts.visits', 'posts.email_token', 'posts.phone_token', 'posts.tmp_token', 'posts.verified_email', 'posts.verified_phone', 'posts.reviewed', 'posts.featured', 'posts.archived', 'posts.fb_profile', 'posts.partner')  
            ->orderByDesc('makeanoffers.id');
       $countMakeanoffers = $makeanoffers->count();  
		
		
		
		$result = [
			'logged'                            => $user_id,
			'countLimit'                        => (int)$countLimit,
			'countConversationsWithNewMessages' => (int)$countConversationsWithNewMessages,
			'countConversationsWithNewOffer'    => (int)$count_offer,
			'countConversationsWithNewMessagesOffer' => (int)$countConversationsWithNewMessages+(int)$count_offer,
			'oldValue'                          => (int)$oldValue,
			'loginUrl'                          => url(config('lang.abbr') . '/' . trans('routes.login')),
			'countMyPosts' => $countMyPosts,
			'countArchivedPosts' => $countArchivedPosts,
			'countFavouritePosts' => $countFavouritePosts,
			'countPendingPosts' => $countPendingPosts,
			'countSavedSearch' => $countSavedSearch,
			'countConversations' => $countConversations,
			'countTransactions' => $countTransactions,
			'countMakeanoffers' => $countMakeanoffers
		];
		
		return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
	}
}
