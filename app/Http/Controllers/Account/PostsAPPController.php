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

use Illuminate\Database\Eloquent\Model;


use App\Helpers\Arr;
use App\Helpers\Search;
use App\Http\Controllers\Search\Traits\PreSearchTrait;
use App\Models\Post;
use App\Models\Category;
use App\Models\SavedPost;
use App\Models\SavedSearch;
use App\Models\SavedUser;
use App\Models\Message;
use App\Models\Scopes\ReviewedScope;
use App\Mail\PostDeleted;
use App\Models\Scopes\VerifiedScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Mail;
use Torann\LaravelMetaTags\Facades\MetaTag;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Makeanoffer;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;
use DB;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Storage;
use App\PushNotification\firebase;
use App\PushNotification\push;



class PostsAPPController extends FrontController
{
    use PreSearchTrait;
    public $countries;
    public $myPosts;
    public $archivedPosts;
    public $favouritePosts;
    public $pendingPosts;
    public $conversations;
    public $transactions;
    public $makeanoffers;
	public $apiPlugin;
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
        switch ($pagePath) {
            case 'my-posts':
                return $this->getMyPosts($request);
                break;
            case 'archived':
                return $this->getArchivedPosts($pagePath);
                break;
            case 'favourite':
                return $this->getFavouritePosts();
                break;
            case 'pending-approval':
                return $this->getPendingApprovalPosts();
                break;
            default:
                abort(404);
        }
    }



	public function makeOffersNotDeal(Request $request)
	{
        
		$MyPostId=$request->MyPostId;
		$offerId=$request->offerId;
		//return $request;
	    $makeanofferget = Makeanoffer::find($offerId);
		// return $makeanofferget;
    	$offer_maker_id = $makeanofferget->offer_maker_id;
		$offer_seller_id = $makeanofferget->seller_id;
    	
    	$offer_maker_name = User::where(['id' => $offer_maker_id])->first();
		$offer_sender_name = User::where(['id' => $offer_seller_id])->first();

    	$to_name  = $offer_maker_name->username;
		$from_name = $offer_sender_name->username;
		
      
        $post = Post::unarchived()->find($MyPostId);

        
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        
        $data['toname'] = $to_name;
        $data['fromname'] = $from_name;
        
	    $fcmId = $offer_maker_name->fcm_id;
        error_reporting(-1);
        ini_set('display_errors', 'On');
        $firebase = new Firebase();
        $push = new Push();
        $payload = array();  
      
        array_push($payload, $data);                
        // notification title
        $title = 'Offer Rejected';             
        // notification message
        $message = "Offer Rejected Data"; 
        $type = "offerejected";                      
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
          //echo $firebaseresponse;
        }
        
	    
	    
		$makeanoffer = Makeanoffer::find($offerId);
		$makeanoffer->approve_seller = 2;
		$makeanoffer->is_read = 0;
		$makeanoffer->update();
		return response()->json(['results'=>'Success','makeanofferid'=>$offerId,'MyPostId'=>$MyPostId, 'firebaseresponse' => $firebaseresponse]);
	}




public function leftMenuInfoApp(Request $request)
    {
		
		$data = array();
		$user_id = $request->user_id;
		//dd(Request);
		   // My Posts
       /* $this->myPosts = Post::where('user_id', $user_id)
            ->verified()
			->unarchived()
			->reviewed()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
          $data['countMyPosts']=$this->myPosts->count();*/


        // Approved Posts
        $this->approvedPosts = Post::where('user_id', $user_id)
            ->verified()
			->unarchived()
			->reviewed()
			->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        $data['countApprovedPosts']=$this->approvedPosts->count();


        // Archived Posts
        $this->archivedPosts = Post::where('user_id', $user_id)
            ->archived()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        $data['countArchivedPosts']=$this->archivedPosts->count();

        // Rejected Posts
        $this->rejectedPosts = Post::where('user_id', $user_id)
            ->where('is_rejected',1)
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        $data['countRejectedPosts']=$this->rejectedPosts->count();

        // Favourite Posts
        $this->favouritePosts = SavedPost::whereHas('post', function($query) {
                // $query->currentCountry();
            })
            ->where('user_id', $user_id)
            ->with(['post.pictures', 'post.city'])
            ->orderByDesc('id');
        $data['countFavouritePosts']= $this->favouritePosts->count();

        // Pending Approval Posts
        $this->pendingPosts = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
            // ->currentCountry()
            ->where('user_id', $user_id)
            ->unverified()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        $data['countPendingPosts']= $this->pendingPosts->count();

      
        // Conversations
		$this->conversations = Message::
// 			->whereHas('post', function($query) {
// 				$query->currentCountry();
// 			})
			byUserId($user_id)
			->where('parent_id', 0)
			->orderByDesc('id');
		$data['countConversations']= $this->conversations->count();
	 
		
		return response()->json(['results'=>$data]);
    }




    public function phone_verification(Request $request)
	{
		$userdata = DB::table('users')
		->select('*')
		->where('id', $request->user_id)
		->first();
		 
		if($userdata === null)
		{
		return response()->json(['results'=>'User is not existing','id'=>$request->user_id]);
		}
		else
		{
		   $request->verified_phone;
		   
		   $user = User::withoutGlobalScopes([VerifiedScope::class])->find($request->user_id);		
	
		$user->verified_phone = $request->verified_phone;
		$user->phone_token = $request->phone_token;
	
		$user->save();
		
		return response()->json(['results'=>'Profile has been updated successfully']);

		}
    }
    

    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getMyPosts(Request $request)
    {
        $myPosts = Post::currentCountry()
            ->where('user_id', $request->user_id)
            ->verified()
			->unarchived()
			->reviewed()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');
        //view()->share('countMyPosts', $this->myPosts->count());
		//print_r($myPosts);
		$data = $myPosts->get();
		return response()->json(['results'=>$data]);
       
    }

    public function ChangePassword(Request $request)
	{
       
        $pass = Hash::make($request->input('password'));
        $new_pass = Hash::make($request->input('new_password'));
        
        $userdata = DB::table('users')
			->select('*')
            ->where('id', $request->user_id)                     
			->first();
            $usercheck=Hash::check($request->input('password'), $userdata->password);    
    if($usercheck==1)
		{
        $user = User::withoutGlobalScopes([VerifiedScope::class])->find($request->user_id);		
		$user->password = $new_pass;	 
		$user->save();		
        return response()->json(['results'=>'Password Is Changed Succesfully']);
    }
		else
		{
	     return response()->json(['results'=>'Old Password ('.$request->input('password').') Is  Wrong !']);
		
	
	    }
	}



    public function cancel_account_app(Request $request)
    {
       //close_account_confirmation =1 approved  ,user_id
        
        if ($request->close_account_confirmation == 1) {
            // Get User
            $user = User::findOrFail($request->user_id);
            // Don't delete admin users
            if ($user->is_admin or $user->is_admin == 1) {
                return response()->json(['results'=>"Admin users cann't be deleted by this way.",'id'=>$request->user_id]);                
            }
            
            // Delete User
            $user->delete();
            
            // Close User's session
            auth()->logout();
            return response()->json(['results'=>"Your account has been deleted. We regret you. Re-register if that is a mistake.",'id'=>$request->user_id,'logout'=>true]); 
         
        }
         
    }


    public function updateDetails(UpdateUserRequest $request)
	{
		$user = User::find($request->user_id);

        $usernameChanged = ($request->username != $user->username);

       // return response()->json(['results'=>'User is not existing','id'=>$usernameChanged]);

        
		if($user === null)
		{
		return response()->json(['results'=>'User is not existing','id'=>$request->user_id]);
		}
		else
		{
			
			 
		$emailChanged = (!empty($request->email) && ($request->email != $user->email));
		$phoneChanged = (!empty($request->phone_number) && ($request->phone_number != $user->phone));
		$usernameChanged = (!empty($request->username) && ($request->username != $user->username));
		
		    if($emailChanged){
			$user->email = $request->input('email');
			}
			if($phoneChanged){
			$user->phone = $request->input('phone_number');
			}
			if($usernameChanged){
			$user->username = $request->input('username');
			}
			//$user->first_name = $request->input('first_name');
			//$user->last_name = $request->input('last_name');
            $user->name = $request->input('name');
			$user->city = $request->input('city');			 
			$user->address = $request->input('address');
	       if($request->file('profile_image')){
			   $image = $request->file('profile_image');
			   $input['profile_image_name'] = time().'.'.$image->getClientOriginalExtension();
			   $destinationPath = 'ProfilePictures';
			   $image->move($destinationPath, $input['profile_image_name']);
			   $data['profile_image_name'] = $input['profile_image_name']; 
           }
           else{
            $data['profile_image_name'] ="";  
           }
		   
		   if(!empty($data['profile_image_name'])){
		   $user->profile_image = $data['profile_image_name'];
		   }
		
           $user->profile_image_hidden = $request->input('profile_image_hidden')?$request->input('profile_image_hidden'):0;
           $user->phone_hidden = $request->input('phone_hidden')?$request->input('phone_hidden'):0;
           $user->email_hidden = $request->input('email_hidden')?$request->input('email_hidden'):0;
		// Save
		$user->save();
	 
		$usr = DB::table('users')->where('id','=',$request->user_id)->first(); 
        $filename="";
if(!empty($usr->profile_image)){
    $filename="https://www.tmmat.com/ProfilePictures/".$usr->profile_image;
}
  
$object = (object) [     

    'id' => $usr->id,
    'user_type_id' =>$usr->user_type_id,
    'name' => $usr->name,
    'first_name' => $usr->first_name,
    'last_name' => $usr->last_name,
    'state' => $usr->state,
    'city' => $usr->city,
    'country' => $usr->country_code,
    'phone' => $usr->phone,    
    'username' => $usr->username,
    'email' => $usr->email,    
    'filename' => $filename,
    'gender_id' => $usr->gender_id,
    'address' => $usr->address,
    'email_hidden' => $usr->email_hidden?$usr->email_hidden:0,
    'phone_hidden' => $usr->phone_hidden?$usr->phone_hidden:0,
    'profile_image_hidden' => $usr->profile_image_hidden?$usr->profile_image_hidden:0,

  ];

  

	
		return response()->json(['results'=>'Profile has been updated successfully','UserInfo'=>$object]);
	
	}
	}
    
    


    
    
    
    
    public function getMyPosts_app(Request $request)
    {
        $myPosts = Post::where('user_id', $request->userid)
            //->currentCountry()
            ->verified()
			->unarchived()
			->reviewed()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id');      
		$myPosts = $myPosts->get();				 
		$filterAllPosts=array();$w=0;
		foreach($myPosts as $post){
			
		 $pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');
										if ($pictures->count() > 0) {
											$postImg = resize($pictures->first()->filename, 'medium');
										} else {
											$postImg = resize(config('larapen.core.picture.default'));
										}
		 $post->postImg = $postImg;
		 if(empty($post->country_code)) {$country_code='KW';}else{$country_code=$post->country_code;}
		 $getcurrencycountry = \DB::table('countries')
		   ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		   ->select('currencies.*')
		   ->where('countries.code', '=', $country_code)
		   ->first();
		   if ($post->price > 0)
		   {
            $get_currency = \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);
			}
			else
			{$get_currency = t('Free');}
			$post->currency = $get_currency;				
									
									
									
			  $filterAllPosts[$w]['id']=$post->id;
			  $filterAllPosts[$w]['title']=$post->title; 
              $filterAllPosts[$w]['category_id']=$post->category_id; 
			  $filterAllPosts[$w]['price']=$post->price; 
			  $filterAllPosts[$w]['formmated_price']=$post->currency;
			  $filterAllPosts[$w]['city_name']=$post->city_name; 
			  $filterAllPosts[$w]['created_at']=$post->created_at; 			   
			  $filterAllPosts[$w]['postImg']=$post->postImg; 			 
			  $w++;
		   }
			
      
		$data = $filterAllPosts;
		return response()->json(['results'=>$data]);
       
    }

    /**
     * @param $pagePath
     * @param null $postId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getArchivedPosts($pagePath, $postId = null)
    {
        // If repost
        if (str_contains(url()->current(), $pagePath . '/' . $postId . '/repost')) {
            $res = false;
            if (is_numeric($postId) and $postId > 0) {
                $res = Post::find($postId)->update([
                    'archived'   => 0,
                    'created_at' => Carbon::now(),
                ]);
            }
            if (!$res) {
                flash(t("The repost has done successfully."))->success();
            } else {
                flash(t("The repost has failed. Please try again."))->error();
            }

            return redirect(config('app.locale') . '/account/' . $pagePath);
        }

        $data = [];
        $data['posts'] = $this->archivedPosts->paginate($this->perPage);

        // Meta Tags
        MetaTag::set('title', t('My archived ads'));
        MetaTag::set('description', t('My archived ads on :app_name', ['app_name' => config('settings.app.name')]));

        view()->share('pagePath', $pagePath);

        return view('account.posts', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFavouritePosts()
    {
        $data = [];
        $data['posts'] = $this->favouritePosts->paginate($this->perPage);

        // Meta Tags
        MetaTag::set('title', t('My favourite ads'));
        MetaTag::set('description', t('My favourite ads on :app_name', ['app_name' => config('settings.app.name')]));

        return view('account.posts', $data);
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

public function get_notification_app(Request $request)
    {
        $user_id = $request->user_id;
        $messages = \DB::table('messages')->where('is_read', '1')->where('to_user_id', $user_id)->get();                
        $massage_content=array();
        $w=0;
        foreach($messages as $message){	
          $massage_content[$w]['content']=$message->subject." - From : ".$message->from_name;
          $massage_content[$w]['From']=$message->from_name;
          $massage_content[$w]['created_at']=$message->created_at;
          $massage_content[$w]['type']='msg';
        
          $w++;
        }

        $offers = \DB::table('makeanoffers')->where('makeanoffers.status', '1')
                    ->where('makeanoffers.seller_id', $user_id)
                    ->join('posts', 'makeanoffers.post_id', '=', 'posts.id')
        ->select('makeanoffers.created_at', 'makeanoffers.approve_seller', 'posts.title')  
        ->orderByDesc('makeanoffers.id')->get();                
        
        foreach($offers as $offer){	
          $massage_content[$w]['content']="New Offer On ".$offer->title." - At : ".$offer->created_at;
          $massage_content[$w]['created_at']=$offer->created_at;
          $massage_content[$w]['type']='offer';
        
          $w++;
        }
        
        return response()->json(['results'=>$massage_content]);
	}
		
    public function getFavourite_users_app(Request $request)
    {
		$user_id = $request->user_id;
		$favouriteUsers =  SavedUser::leftjoin('users','users.id','saved_users.user_id')->where('user_id','=',$user_id)->get();
		
		 $filterAllPosts=array();
		   $w=0;
		   foreach($favouriteUsers as $post){	
            
            $rate = \DB::table('ratings')->where('rated', $post['fav_user_id'])->select('rate')->get();
            $rate = count($rate) ? ($rate->sum('rate') * 5) / ($rate->count() * 5) : 0;
           
            $posts_count = \DB::table('posts')
            ->where('user_id', $post['fav_user_id'])
            ->where('reviewed', '1')
            ->count();
            

			  $filterAllPosts[$w]['fav_user_id']=$post['fav_user_id'];
              $filterAllPosts[$w]['rate']=round($rate);
              $filterAllPosts[$w]['posts_count']=$posts_count;
			  $filterAllPosts[$w]['name']=$post['name']; 
			  $filterAllPosts[$w]['email']=$post['email']; 
			  $filterAllPosts[$w]['phone']=$post['phone']; 		
              $filterAllPosts[$w]['profile_image']=url('ProfilePictures/'.$post['profile_image'].''); 	 
			  $w++;
		 }       
		
		   return response()->json(['results'=>$filterAllPosts]);
	}
		
		
    public function getFavouritePosts_app(Request $request)
    {
        $user_id = $request->user_id;
       //  $user_id =1063;
        $favouritePosts = SavedPost::whereHas('post', function($query) {
            // $query->currentCountry();
        })
        ->where('user_id', $user_id)
        ->with(['post.pictures', 'post.city'])
        ->orderByDesc('id')->get();

       
            foreach($favouritePosts as $key => $post){

              $postimg=  $post['post']['pictures'];

             // $post['shrt_img']=  $post['shrt_img']['id'];
                foreach($postimg as  $pictures){
 
                    $post['shrt_img']=  "https://www.tmmat.com/storage/".$pictures['filename'];

                }

         } 
		 
		 
		  $filterAllPosts=array(); $price=0;
		   $w=0;
		   foreach($favouritePosts as $key => $post){
			   
			    $postimg=  $post['post']['pictures'];
				foreach($postimg as  $pictures){
					$post['shrt_img']=  "https://www.tmmat.com/storage/".$pictures['filename'];
					}

   if ($post['post']['price'] > 0){

    if(empty($post['post']['country_code'])) {$country_code='KW';}else{$country_code=$post['post']['country_code'];}

    $getcurrencycountry = \DB::table('countries')->join('currencies', 'currencies.code', '=', 'countries.currency_code')
			                     ->select('currencies.*')->where('countries.code', '=', $country_code)->first();
	
    $price = \App\Helpers\Number::money_price_latest($post['post']['price'],$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);}
	
			  $filterAllPosts[$w]['id']=$post['post']['id'];
			  $filterAllPosts[$w]['title']=$post['post']['title']; 
			  $filterAllPosts[$w]['price']=$price; 
			//  $filterAllPosts[$w]['formmated_price']=$post['post']['currency'];
			  $filterAllPosts[$w]['city_name']=$post['post']['city_name']; 
             
              $middle = strtotime($post['post']['created_at']);             // returns bool(false)
			  $new_date = date('Y-m-d', $middle);
                        
			  $filterAllPosts[$w]['created_at']=(object) [  'date' => $new_date];
			  		   
			  $filterAllPosts[$w]['postImg']=$post['shrt_img']; 
			  $filterAllPosts[$w]['featured']=$post['post']['featured'];
			//  $filterAllPosts[$w]['favourite']= @$this->check_fav_post($post['post']['id'],$post['post']['user_id']);
			  
			  $w++;
			 
		   }
		       
		 
		 
   
        return response()->json(['results'=>$filterAllPosts]);
    }


// we return archive ads and 
// paramaters user_id  , ads_type(archive -  )
    public function getmyads(Request $request)
    {
     
     $Posts=array();
     $user_id = $request->user_id;
	 //  $user_id =1063;
	 if(!empty($request->ads_type) && $request->ads_type=='archive'){		 
		  // Archived Posts
      $Posts = Post::where('user_id', $user_id)
      ->archived()
      ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
      ->orderByDesc('id')->get();
		 
		 }

         if(!empty($request->ads_type) && $request->ads_type=='pending'){		 
           
        //pending Posts
        $Posts = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
        ->where('user_id', $user_id)
        ->unverified()
        ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
        ->orderByDesc('id')->get();

           }


         if(!empty($request->ads_type) && $request->ads_type=='active'){		 
           
            $Posts = Post::where('user_id', $user_id)           
            ->verified()
			->unarchived()
			->reviewed()
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id')->get();      
		   

           }

         if(!empty($request->ads_type) && $request->ads_type=='rejected'){		 
           
            $Posts = Post::where('user_id', $user_id)           
            ->where('is_rejected',1)
            ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
            ->orderByDesc('id')->get();      
		   

           }
     
	      $filterAllPosts=array();
		   $w=0;
		   foreach($Posts as  $post){
			   
			    $postimg=  $post['pictures'];
				foreach($postimg as  $pictures){
                    if(!empty($pictures['filename'])){
					$post['shrt_img']=  "https://www.tmmat.com/storage/".$pictures['filename'];
                     }
					}
              $price=0;
			  $filterAllPosts[$w]['id']=$post['id'];
			  $filterAllPosts[$w]['title']=$post['title'];
              if(empty($post['country_code'])) {$post['country_code']='KW';}
              $getcurrencycountry = \DB::table('countries')->join('currencies', 'currencies.code', '=', 'countries.currency_code')
			                     ->select('currencies.*')->where('countries.code', '=', $post['country_code'])->first();
	if ($post['price'] > 0){
        $price = \App\Helpers\Number::money_price_latest($post['price'],$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);}
	

 
			  $filterAllPosts[$w]['price']=$price; 
			 // $filterAllPosts[$w]['formmated_price']=$post['currency'];
			  $filterAllPosts[$w]['category_id']=$post['category_id'];
              $filterAllPosts[$w]['city_name']=$post['city_name']; 
			  $filterAllPosts[$w]['created_at']=$post['created_at']; 			   
			  $filterAllPosts[$w]['postImg']=$post['shrt_img']; 
              $filterAllPosts[$w]['description']=$post['description']; 
			  $filterAllPosts[$w]['featured']=$post['featured'];
              $filterAllPosts[$w]['phone']=$post['phone'];
              $filterAllPosts[$w]['email']=$post['email'];
			//  $filterAllPosts[$w]['favourite']= @$this->check_fav_post($post['post']['id'],$post['post']['user_id']);
			  
			  $w++;
			 
		   }
		       


   
        return response()->json(['results'=>$filterAllPosts]);
    }





    
// we return archive ads and 
// paramaters user_id  , ads_type(archive -  )
public function getUserAds(Request $request)
{
 
 $Posts=array();
 $user_id = $request->user_id;
 $loged_user_id = $request->loged_user_id;

 $user_id = $request->user_id;

		$User_fav =  SavedUser::leftjoin('users','users.id','saved_users.user_id')
        ->where('fav_user_id','=',$user_id)
        ->where('user_id','=',$loged_user_id)
        ->first();
		$isFollow=false;
		if(!empty($User_fav)){$isFollow=true;}

        $rate = \DB::table('ratings')->where('rated', $user_id)->select('rate')->get();
        $rate = count($rate) ? ($rate->sum('rate') * 5) / ($rate->count() * 5) : 0;

 
     $user = User::find($request->user_id);     		 
       
        $Posts = Post::where('user_id', $user_id)           
        ->verified()
        ->unarchived()
        ->reviewed()
        ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
        ->orderByDesc('id')->get();      
       
 
    
      $filterAllPosts=array();
       $w=0;
       foreach($Posts as  $post){
           
            $postimg=  $post['pictures'];
            foreach($postimg as  $pictures){
                if(!empty($pictures['filename'])){
                $post['shrt_img']=  "https://www.tmmat.com/storage/".$pictures['filename'];
                 }
                }
          $price=0;
          $filterAllPosts[$w]['id']=$post['id'];
          $filterAllPosts[$w]['title']=$post['title'];
          if(empty($post['country_code'])) {$post['country_code']='KW';}
          $getcurrencycountry = \DB::table('countries')->join('currencies', 'currencies.code', '=', 'countries.currency_code')
                             ->select('currencies.*')->where('countries.code', '=', $post['country_code'])->first();
if ($post['price'] > 0){
    $price = \App\Helpers\Number::money_price_latest($post['price'],$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);}



          $filterAllPosts[$w]['price']=$price;        
         
          //$filterAllPosts[$w]['formmated_price']=$post['currency'];
          $filterAllPosts[$w]['city_name']=$post['city_name']; 
          $filterAllPosts[$w]['created_at']=$post['created_at']; 
          
          
              $middle = strtotime($post['created_at']);             // returns bool(false)
			  $new_date = date('Y-m-d', $middle);
                        
			  $filterAllPosts[$w]['created_at']=(object) [  'date' => $new_date];
			  		   


          $filterAllPosts[$w]['postImg']=$post['shrt_img']; 
          $filterAllPosts[$w]['description']=$post['description']; 
          $filterAllPosts[$w]['featured']=$post['featured'];
          $filterAllPosts[$w]['phone']=$post['phone'];
          $filterAllPosts[$w]['email']=$post['email'];
          
        //  $filterAllPosts[$w]['favourite']= @$this->check_fav_post($post['post']['id'],$post['post']['user_id']);
          
          $w++;
         
       }
      
       $user_data=array();       
       $user_data['username']=$user->username;
       $user_data['phone']=$user->phone;
       $user_data['email']=$user->email;      
            $user_data['profile_image']='https://www.tmmat.com/ProfilePictures/'.$user->profile_image; 
            $user_data['phone_hidden']=$user->phone_hidden?$user->phone_hidden:0; 
            $user_data['email_hidden']=$user->email_hidden?$user->email_hidden:0; 
            $user_data['profile_image_hidden']=$user->profile_image_hidden?$user->profile_image_hidden:0; 
       $user_data['isFollow']=$isFollow; 
       $user_data['rate']=round($rate); 



    return response()->json(['user_data'=>$user_data,'results'=>$filterAllPosts]);
}


//we will remove it we replace it by getmyads abdelhay 1-8-2022
public function getArchivedPosts_app(Request $request)
    {
     $user_id = $request->user_id;
     //  $user_id =1063;
     // Archived Posts
      $archivedPosts = Post::where('user_id', $user_id)
      ->archived()
      ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
      ->orderByDesc('id')->get();


      $user_id = $request->user_id;
      //  $user_id =1063;
       $favouritePosts = SavedPost::whereHas('post', function($query) {
           // $query->currentCountry();
       })
       ->where('user_id', $user_id)
       ->with(['post.pictures', 'post.city'])
       ->orderByDesc('id')->get();

      
           foreach($archivedPosts as $post){

             $postimg=  $post['pictures'];

            // $post['shrt_img']=  $post['shrt_img']['id'];
               foreach($postimg as  $pictures){

                   $post['shrt_img']=  "https://www.tmmat.com/storage/".$pictures['filename'];

               }

        } 


   
        return response()->json(['results'=>$archivedPosts]);
    }

public function get_banners_app(Request $request)
{
   $country_code = $request->country_code; 
  $Banners = DB::table('banner')
  ->where('banner_type', 'top')
  ->where('country_code', $country_code)
  ->get();
foreach($Banners as $banner){	
	       if(!empty($banner->tracking_code_large)){$banner->tracking_code_large="https://www.tmmat.com/banner/".$banner->tracking_code_large;}
		   if(!empty($banner->tracking_code_medium)){$banner->tracking_code_medium="https://www.tmmat.com/banner/".$banner->tracking_code_medium;}
		   if(!empty($banner->tracking_code_small)){$banner->tracking_code_small="https://www.tmmat.com/banner/".$banner->tracking_code_small;}
            //$banner->tracking_code_medium=;
            //$banner->tracking_code_small=;
	
	}
    return response()->json(['results'=>$Banners]);
}
    
public function getRejectedPosts_app(Request $request)
{
 $user_id = $request->user_id;
 //  $user_id =1063;
 // RejectedPosts 
  $RejectedPosts = Post::where('user_id', $user_id)
  ->where('is_rejected',1)
  ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
  ->orderByDesc('id')->get();

    return response()->json(['results'=>$RejectedPosts]);
}



    public function getPendingApprovalPosts_app(Request $request)
    {
     $user_id = $request->user_id;
     //  $user_id =1063;
     // Pending Approval Posts
     $pendingPosts = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
     // ->currentCountry()
     ->where('user_id', $user_id)
     ->unverified()
     ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
     ->orderByDesc('id')->get();
   
        return response()->json(['results'=>$pendingPosts]);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPendingApprovalPosts()
    {
        $data = [];
        $data['posts'] = $this->pendingPosts->paginate($this->perPage);

        // Meta Tags
        MetaTag::set('title', t('My pending approval ads'));
        MetaTag::set('description', t('My pending approval ads on :app_name', ['app_name' => config('settings.app.name')]));

        return view('account.posts', $data);
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
            $count = count($ids);
            if ($count > 1) {
                $message = t("x :entities has been deleted successfully.", ['entities' => t('ads'), 'count' => $count]);
            } else {
                $message = t("1 :entity has been deleted successfully.", ['entity' => t('ad')]);
            }
            flash($message)->success();
        // }

        return redirect(config('app.locale') . '/account/' . $pagePath);
    }
    
    
    
    
    public function destroypost(Request $request)
    {
        $item = $request->id;
        $nb = 0;
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
                    $pictures= \DB::table('pictures')->where('post_id',$item)->get();					
					foreach($pictures as $pich){
                    Storage::delete($pich->filename);					
					  $query_update =  \DB::table('pictures')
                       ->where('id', $pich->id)
                       ->delete();
                    }
                }
                if($nb == 0){
            $Success=false;
            $result="No deletion is done. Please try again.";
        }else{
            $Success=true;
            $result="deleted successfully.";
        }
        return response()->json(['results'=>$result,'success'=>$Success]);
    }


public function unarchivepost(Request $request)
    {

           $postId = $request->id;
         
            $res = false;
            if (is_numeric($postId) and $postId > 0) {
               
                $res = Post::where('id',$postId)->update([
                    'archived'   => 0,                    
                ]);
            }

            if (!empty($res)) {
                $Success=true;
                $result=t("Success");
            } else {
                $Success=false;
                $result=t("failed");
            }

 
                    
            return response()->json(['results'=>$result,'success'=>$Success]);

            
       
    }

    public function deletepostfavourite(Request $request)
    {
        // need method post paramaters userid,id
        $item = $request->id;
        
        $nb = 0;
        $ids[] = $item;
        $savedPosts = SavedPost::where('user_id', $request->userid)->whereIn('post_id', $ids);
            if ($savedPosts->count() > 0) {
                $nb = $savedPosts->delete();
            }
 
                if($nb == 0){
            $Success=false;
            $result=t("No deletion is done. Please try again.");
        }else{
            $Success=true;
            $result=t("Done.");
        }
        return response()->json(['results'=>$result,'success'=>$Success]);
    }


    public function deleteAllpostfavourite(Request $request)
    {
        
        $nb = 0;
        
        $savedPosts = SavedPost::where('user_id', $request->user_id);
            if ($savedPosts->count() > 0) {
                $nb = $savedPosts->delete();
            }
 
                if($nb == 0){
            $Success=false;
            $result=t("No deletion is done. Please try again.");
        }else{
            $Success=true;
            $result=t("Done.");
        }
        return response()->json(['results'=>$result,'success'=>$Success]);
    }

  public function deleteAllfavouriteUsers(Request $request)
    {
        
        $nb = 0;
        
 
        $savedPosts = SavedUser::where('user_id', $request->user_id);
            if ($savedPosts->count() > 0) {
                $nb = $savedPosts->delete();
            }
 
                if($nb == 0){
            $Success=false;
            $result=t("No deletion is done. Please try again.");
        }else{
            $Success=true;
            $result=t("Done.");
        }
        return response()->json(['results'=>$result,'success'=>$Success]);
    }

    
    public function updatePostVisit(Request $request)
    {
        // need method post paramaters userid,id
        $no_visit=0;
        $postid = $request->postid;
        $loged_userid = $request->loged_userid;
         
        $isvld_user=\DB::table('posts')->select('id')->where('user_id', $loged_userid)->where('id', $postid)->first();

        if(empty($isvld_user->id)){

            $post_info=\DB::table('posts')->select('visits')->where('id', $postid)->first();
            $no_visit=$post_info->visits+1;

        \DB::table('posts')->where('id', $postid)->update(['visits' => $no_visit]);
        $Success=true;
        $result=t("success");
        }else{
            $Success=false;
            $result=t("failed");
        }
   
                
        return response()->json(['results'=>$result,'success'=>$Success,'no_visit'=>$no_visit]);
    }
    
    
    
    
    
    
    
    
    public function archivepost(Request $request)
    {
        // need method post paramaters userid,id
        $postId = $request->id;
        
        $res = false;          
      
        if (is_numeric($postId) and $postId > 0) {

                $res = Post::where('id', $postId)->update([
                    'archived'   => 1,    
                ]);

            }
 
            if (!$res) {
            $Success=false;
            $result=t("No Archive is done. Please try again.");
        }else{
            $Success=true;
            $result=t("Archived Successfully.");
        }
        return response()->json(['results'=>$result,'success'=>$Success]);
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
            $message->to('delivery@tmmat.com');
            $message->subject('Request a Delivery');
            if(!empty($from_email))
            {
                $message->replyTo($from_email, $buyername);        
            }
        });    
        
        return redirect()->back()->with('success',t('Message successfully sent'));
        
    }
    
}
