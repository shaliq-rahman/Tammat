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
use Jenssegers\Date\Date;
use Torann\LaravelMetaTags\Facades\MetaTag;
use App\PushNotification\firebase;
use App\PushNotification\push;

class DetailsController extends FrontController
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
    public function index($postId)
    {
      
        $data = [];

        // Get and Check the Controller's Method Parameters
        $parameters = Request::route()->parameters();

        // Show 404 error if the Post's ID is not numeric
        if (!isset($parameters['id']) || empty($parameters['id']) || !is_numeric($parameters['id'])) {
            abort(404);
        }

        // Set the Parameters
        $postId = $parameters['id'];
        if (isset($parameters['slug'])) {
            $slug = $parameters['slug'];
        }


//if(empty($post)){ $post = Post::where('id', $postId)->first();}

//dd($post);
        // GET POST'S DETAILS
        if (auth()->check()) {

            // Get post's details even if it's not activated and reviewed
            $cacheId = 'post.withoutGlobalScopes.with.user.city.pictures.' . $postId . '.' . config('app.locale');

            $post = Cache::remember($cacheId, $this->cacheExpiration, function () use ($postId) {
                $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
                    ->unarchived()
                    ->where('id', $postId)
                    ->with([
                        'category' => function ($builder) { $builder->with(['parent']); },
                        'postType',
                        'user',
                        'city',
                        'pictures',
                        'latestPayment' => function ($builder) { $builder->with(['package']); },
                    ])
                    ->first();
                return $post;
            });

            // If the logged user is not an admin user...
            if (auth()->user()->is_admin != 1) {
                // Then don't get post that are not from the user
                if (!empty($post) && $post->user_id != auth()->user()->id) {
                    $cacheId = 'post.with.user.city.pictures.' . $postId . '.' . config('app.locale');
                    $post = Cache::remember($cacheId, $this->cacheExpiration, function () use ($postId) {
                        $post = Post::unarchived()
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

                        return $post;
                    });
                }
            }
        } else {
            $cacheId = 'post.with.user.city.pictures.' . $postId . '.' . config('app.locale');
            $post = Cache::remember($cacheId, $this->cacheExpiration, function () use ($postId) {
                $post = Post::unarchived()
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

                return $post;
            });
        }
        // Preview Post after activation

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
        
		
			 
	if(!empty($post->postType)){$posttype=$post->postType;}else{$posttype="";}	
	if(!empty($post->category)){$postcategory=$post->category;}else{$postcategory="";}	
	if(!empty($post->city)){$postcity=$post->city;}else{$postcity="";}	
	 
if($posttype==""){
    $posttype=2;
}
        // Post not found
        if (empty($post) || empty($postcategory) || empty($posttype) || empty($postcity)) {
            
            abort(404, t('Post not found'));
        }

        // Share post's details
        view()->share('post', $post);


$sql = Category::trans()->where('id', $post->category->tid)->orderBy('lft')->get();
		foreach ($sql as $parent) {
		    if($parent->parent_id!=0){
	                 $sql2 = Category::trans()->where('id', $parent->parent_id)->orderBy('lft')->get();
	             foreach ($sql2 as $parent2) {
	                if($parent2->parent_id!=0){
	                $sql3 = Category::trans()->where('id', $parent2->parent_id)->orderBy('lft')->get();
	                    foreach ($sql3 as $parent3) {
	                        $data['cat3'][]= $parent2;
	                        if($parent3->parent_id!=0){
	                            $sql4 = Category::trans()->where('id', $parent3->parent_id)->orderBy('lft')->get();
	                            foreach ($sql4 as $parent4) {
	                                if($parent4->parent_id!=0){
	                                     $sql5 = Category::trans()->where('id', $parent4->parent_id)->orderBy('lft')->get();
        	                               foreach ($sql5 as $parent5) {
        	                                   $pid=$parent5->id; 
        	                               }
	                                    
	                                } else {
	                                   
	                                    $pid=$parent4->id; 
	                                }
	                            }
	                        } else {
	                            $pid=$parent3->id; 
	                        }
	                    }
	                } else {
	                     $pid=$parent2->id;
	                }
	             }
	        } else {
	            $pid=$parent->id;
	        }
	            }


        // Get Category nested IDs
        $catNestedIds = (object)[
           //'parentId' => '4188',
		    'parentId' => $post->category->parent_id,
            'id' => $post->category->tid,
        ];

        // Get Custom Fields
        $customFields = $this->getPostFieldsValues($catNestedIds, $post->id);
       
        //dd($customFields);
        view()->share('customFields', $customFields);

        // Get Post's user decision about comments activation
        $commentsAreDisabledByUser = false;
        // Get possible Post's user
        if (isset($post->user_id) && !empty($post->user_id)) {
            $possibleUser = User::find($post->user_id);
            if (!empty($possibleUser)) {
                if ($possibleUser->disable_comments == 1) {
                    $commentsAreDisabledByUser = true;
                }
            }
        }
        view()->share('commentsAreDisabledByUser', $commentsAreDisabledByUser);
//abdelhyreda
        // Increment Post visits counter
        Event::fire(new PostWasVisited($post));

        // GET SIMILAR POSTS
        $featured = $this->getCategorySimilarPosts($post->category, $post->id);
        // $featured = $this->getLocationSimilarPosts($post->city, $post->id);
        $data['featured'] = $featured;

        // SEO
        $title = $post->title . ', ' . $post->city->name;
        $description = str_limit(str_strip(strip_tags($post->description)), 200);

        // Meta Tags
        MetaTag::set('title', $title);
        MetaTag::set('description', $description);
        if (!empty($post->tags)) {
            MetaTag::set('keywords', str_replace(',', ', ', $post->tags));
        }

        // Open Graph
        $this->og->title($title)
            ->description($description)
            ->type('article')
            ->article(['author' => config('settings.social_link.facebook_page_url')])
            ->article(['publisher' => config('settings.social_link.facebook_page_url')]);
        if (!$post->pictures->isEmpty()) {
            if ($this->og->has('image')) {
                $this->og->forget('image')->forget('image:width')->forget('image:height');
            }
            foreach ($post->pictures as $picture) {
                $this->og->image(resize($picture->filename, 'large'), [
                    'width' => 600,
                    'height' => 600,
                ]);
            }
        }
        view()->share('og', $this->og);

        // Expiration Info
        $today = Date::now(config('timezone.id'));
        if ($today->gt($post->created_at->addMonths($this->expireTime))) {
            flash(t("Warning! This ad has expired. The product or service is not more available (may be)"))->error();
        }

        // Reviews Plugin Data
        if (isset($this->reviewsPlugin) and !empty($this->reviewsPlugin)) {
            try {
                $rvPost = \App\Plugins\reviews\app\Models\Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($post->id);
                view()->share('rvPost', $rvPost);
            } catch (\Exception $e) {
            }
        }

        // View
        return view('post.details', $data);
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
        $userId = auth()->user()->id;
        $user = \DB::table('users')
		            ->where('id', '=', $userId)
                   ->select('*')
                   ->first();
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
                   ->select('*')
                   ->first();
             $fcmId = $sellername->fcm_id;
                
                error_reporting(-1);
                ini_set('display_errors', 'On');
               
                $firebase = new Firebase();
                
                $push = new Push();
                $payload = array();
                
               $title = $request->input('message');  
                 $message = $user->first_name.' has send you a message';
               $data = array(
                    'fromname' =>$user->first_name,
                    'email'=>$user->email,
                    'fromphone'=>$user->phone,
                    'title'=>$title,
                    'message' => $message);
 
 
                array_push($payload, $data);
                
                // notification title
                // $title = $from_name.' has send you a message';             
                // // notification message
                // $message = "Send Delivery info"; 
                $type = "SendDeliveryinfo";                      
                // push type - single user / topic
                $push_type = "individual";     
                $push->setTitle($title);
                $push->setType($type);
                $push->setMessage($message);
                $push->setIsBackground(FALSE);
                $push->setPayload($payload);
                $json = '';
                $firebaseresponse = '';
                
                if ($push_type == 'topic') 
                {
                    $json = $push->getPush();
                    $firebaseresponse = $firebase->sendToTopic('global', $json);
                }
                else if ($push_type == 'individual') 
                {
                  $json = $push->getPush();
                  $firebaseresponse = $firebase->send($fcmId, $json);
                }           				
                        				
            $msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $sellername->username]);
            flash($msg)->success();
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }
       
        return redirect(config('app.locale') . '/' . $post->uri);
    }
	
	
	public function sendMessage_app($postId, SendMessageRequest $request)
    {
        //die();
        // Get the Post
        $post = Post::unarchived()->findOrFail($postId);
        $firebaseresponse = array();
        // New Message
        $message = new Message();
        $input = $request->only($message->getFillable());
       // print_r($input);
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
       // $message->buyer_address = 

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
            // print_r($message);
            // die();
            $message->save();
        }

        // Send a message to publisher
        //try {
            $post->notify(new SellerContacted($post, $message));
            
             $sellerDetail = \DB::table('users')
		            ->where('id', '=', $post->user_id)
                   ->select('*')
                   ->first();
                //   print_r($sellerDetail);
                //   die();
            //Send firebase notification if fcm id is present
            if($sellerDetail->fcm_id) {
                $fcmId = $sellerDetail->fcm_id;
                
                error_reporting(-1);
                ini_set('display_errors', 'On');
               
                $firebase = new Firebase();
                
                $push = new Push();
                $payload = array();
                
                $data = array(
                    'message' => 'test');
 
                array_push($payload, $data);
                
                // notification title
                $title = $post->contact_name.' has send you a message';             
                // notification message
                $message = "Send Delivery info"; 
                $type = "SendDeliveryinfo";                      
                // push type - single user / topic
                $push_type = "individual";     
                $push->setTitle($title);
                $push->setType($type);
                $push->setMessage($message);
                $push->setIsBackground(FALSE);
                $push->setPayload($payload);
                $json = '';
                $firebaseresponse = '';
                if ($push_type == 'topic') 
                {
                    $json = $push->getPush();
                    $firebaseresponse = $firebase->sendToTopic('global', $json);
                }
                else if ($push_type == 'individual') 
                {
                  $json = $push->getPush();
                  $firebaseresponse = $firebase->send($fcmId, $json);
                }
		
            }
            
            //$msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $sellername->username]);
            //flash($msg)->success();
			return response()->json(['msg'=>'Your message has sent successfully', 'success'=> 1, 'firebaseResponse' => $firebaseresponse]);
//         } catch (\Exception $e) {
//             //flash($e->getMessage())->error();
// 			return response()->json(['success' => 0, 'msg'=>$e]);
//         }
			
       // return redirect(config('app.locale') . '/' . $post->uri);
    }
public function sendMessageContact_app(SendMessageRequest $request)
    {
        $postId = $request->input('post_id');
        $from_name = $request->input('from_name');
        // Get the Post
        $post = Post::unarchived()->findOrFail($postId);

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
            
             $sellerDetail = \DB::table('users')
		            ->where('id', '=', $post->user_id)
                   ->select('*')
                   ->first();
                      				
            //Send firebase notification if fcm id is present
            if($sellerDetail->fcm_id) {
                $fcmId = $sellerDetail->fcm_id;
                
                error_reporting(-1);
                ini_set('display_errors', 'On');
               
                $firebase = new Firebase();
                
                $push = new Push();
                $payload = array();
                
               $title = $request->input('message');  
                 $message = $request->input('from_name').' has send you a message';
               $data = array(
                    'fromname' =>$request->input('from_name'),
                    'email'=>$request->input('from_email'),
                    'fromphone'=>$request->input('from_phone'),
                    'title'=>$title,
                    'message' => $message);
 
 
                array_push($payload, $data);
                
                // notification title
                // $title = $from_name.' has send you a message';             
                // // notification message
                // $message = "Send Delivery info"; 
                $type = "SendDeliveryinfo";                      
                // push type - single user / topic
                $push_type = "individual";     
                $push->setTitle($title);
                $push->setType($type);
                $push->setMessage($message);
                $push->setIsBackground(FALSE);
                $push->setPayload($payload);
                $json = '';
                $firebaseresponse = '';
                
                if ($push_type == 'topic') 
                {
                    $json = $push->getPush();
                    $firebaseresponse = $firebase->sendToTopic('global', $json);
                }
                else if ($push_type == 'individual') 
                {
                  $json = $push->getPush();
                  $firebaseresponse = $firebase->send($fcmId, $json);
                }
		
            }
            
            //$msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $sellername->username]);
            //flash($msg)->success();
			return response()->json(['msg'=>'Your message has sent successfully', 'success'=> 1, 'firebaseResponse' => $firebaseresponse]);
        } catch (\Exception $e) {
            //flash($e->getMessage())->error();
			return response()->json(['success' => 0, 'msg'=>$e]);
        }
			
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
					' . $reviewedCondition . '
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
                    $posts = DB::select(DB::raw($sql), $bindings);
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
                    $posts = DB::select(DB::raw($sql), $bindings);
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
                $featured = Arr::toObject($featured);
            }
        }

        return $featured;
    }
}
