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

use App\Http\Controllers\FrontController;
use App\Models\Post;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Makeanoffer;
use App\Models\SavedPost;
use App\Models\SavedUser;
use App\Models\SavedSearch;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;
use DB;

abstract class AccountBaseController extends FrontController
{
    public $countries;
    public $myPosts;
    public $archivedPosts;
    public $rejectedPosts;
    public $favouritePosts;
    public $pendingPosts;
    public $conversations;
    public $transactions;
    public $makeanoffers;
	public $apiPlugin;

    /**
     * AccountBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->middleware(function ($request, $next) {
            $this->leftMenuInfo();
            return $next($request);
        });
	
		view()->share('pagePath', '');
    }

    public function leftMenuInfo()
    {
    	// Get & Share Countries
        $this->countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
        view()->share('countries', $this->countries);
        
        // Share User Info
        view()->share('user', auth()->user());

        // My Posts
        $this->myPosts = Post::where('user_id', auth()->user()->id)
            ->verified()
			->unarchived()
			->reviewed()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        view()->share('countMyPosts', $this->myPosts->count());


        // Approved Posts
        $this->approvedPosts = Post::where('user_id', auth()->user()->id)
            ->verified()
			->unarchived()
			->reviewed()
			->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        view()->share('countApprovedPosts', $this->approvedPosts->count());


        // Archived Posts
        $this->archivedPosts = Post::where('user_id', auth()->user()->id)
            ->archived()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        view()->share('countArchivedPosts', $this->archivedPosts->count());

        // Rejected Posts
        $this->rejectedPosts = Post::where('user_id', auth()->user()->id)
            ->where('is_rejected',1)
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        view()->share('countRejectedPosts', $this->rejectedPosts->count());

        // Favourite Posts
        $this->favouritePosts = SavedPost::whereHas('post', function($query) {
                // $query->currentCountry();
            })
            ->where('user_id', auth()->user()->id)
            ->with(['post.pictures', 'post.city'])
            ->orderByDesc('id');
        view()->share('countFavouritePosts', $this->favouritePosts->count());
		
		  // Favourite users
        $this->favouriteUsers = SavedUser::where('user_id', auth()->user()->id)->orderByDesc('id');
        view()->share('countFavouriteUsers', $this->favouriteUsers->count());
		
		

        // Pending Approval Posts
        $this->pendingPosts = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
            // ->currentCountry()
            ->where('user_id', auth()->user()->id)
            ->unverified()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        view()->share('countPendingPosts', $this->pendingPosts->count());

        // Save Search
        $savedSearch = SavedSearch::
            where('user_id', auth()->user()->id)
            ->orderByDesc('id');
        view()->share('countSavedSearch', $savedSearch->count());
        
        // Conversations
		$this->conversations = Message::
// 			->whereHas('post', function($query) {
// 				$query->currentCountry();
// 			})
			byUserId(auth()->user()->id)
			->where('parent_id', 0)
			->orderByDesc('id');
		view()->share('countConversations', $this->conversations->count());
		
		// Payments
		$this->transactions = Payment::whereHas('post_latest', function($query) {
				$query->currentCountry()->whereHas('user', function($query) {
                    $query->where('user_id', auth()->user()->id);
                });
			})
			->with(['post_latest', 'paymentMethod', 'package' => function ($builder) { $builder->with(['currency']); }])
			->orderByDesc('id');
		view()->share('countTransactions', $this->transactions->count());

    
        $seller_id = auth()->user()->id;
        $buyer_id = auth()->user()->id;
        $status = 1;
        $this->makeanoffers =  DB::table('makeanoffers')->where(function ($query) use ($seller_id, $buyer_id, $status)
        {
            // if(auth()->user()->user_type_id == 2)
            // {
            //     $query->where('makeanoffers.seller_id', '=', $seller_id);
            // }
            // elseif(auth()->user()->user_type_id == 3)
            // {
            //   $query->where('makeanoffers.buyer_id', '=', $buyer_id); 
            // }
             
            $query->where('makeanoffers.buyer_id', auth()->user()->id)->orWhere('makeanoffers.seller_id', auth()->user()->id);
            $query->where('makeanoffers.status', '=', $status);
        })
        ->join('posts', 'makeanoffers.post_id', '=', 'posts.id')
        ->select('makeanoffers.*', 'posts.country_code' , 'posts.user_id', 'posts.category_id', 'posts.post_type_id', 'posts.title', 'posts.description', 'posts.tags', 'posts.price', 'posts.negotiable', 'posts.contact_name', 'posts.email', 'posts.phone', 'posts.phone_hidden', 'posts.address', 'posts.city_id', 'posts.lon', 'posts.lat', 'posts.ip_addr', 'posts.visits', 'posts.email_token', 'posts.phone_token', 'posts.tmp_token', 'posts.verified_email', 'posts.verified_phone', 'posts.reviewed', 'posts.featured', 'posts.archived', 'posts.fb_profile', 'posts.partner')  
            ->orderByDesc('makeanoffers.id');
        view()->share('countMakeanoffers', $this->makeanoffers->count());       
	
		// Check and Load the API Plugin
		$this->apiPlugin = load_installed_plugin('api');
		view()->share('apiPlugin', $this->apiPlugin);
    }
}
