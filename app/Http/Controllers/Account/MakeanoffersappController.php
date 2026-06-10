<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\FrontController;
use Torann\LaravelMetaTags\Facades\MetaTag;
use App\Models\Makeanoffer;
use App\Models\Post;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Picture;
use App\Http\Requests\MakeAnOfferEditRequest;
use App\Http\Requests\MakeAnOfferRequest;
use Illuminate\Http\Request;
use DB;
use App\Models\User;
use App\Notifications\SellerContacted;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;
use App\PushNotification\firebase;
use App\PushNotification\push;

class MakeanoffersappController extends AccountappBaseController
{
	private $perPage = 10;
	
	public function __construct()
	{
		//parent::__construct();
		
		//$this->perPage = (is_numeric(config('settings.listing.items_per_page'))) ? config('settings.listing.items_per_page') : $this->perPage;
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


	/**
	 * List Transactions
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index(Request $request) 
	{		
	    \App::setLocale(request()->get('language') ?: 'en');
// 	  echo "hello";
// 	  die();
		$seller_id = $request->userid;
        $buyer_id = $request->userid;
        //$receiverId = $request->receiverId;
       // $receivers = DB::table('users')->where('id', '=',$receiverId)->get();
        $users = DB::table('users')->where('id', '=',$seller_id)->get();
        if(!empty($users)){
        foreach($users as $user){
           $title = $user->name.'has send you an offer';
        }
        $status = 1;
		$makeanoffers =  DB::table('makeanoffers')->where(function ($query) use ($seller_id, $buyer_id, $status)
        {
            $query->where('makeanoffers.buyer_id', $seller_id)->orWhere('makeanoffers.seller_id', $seller_id);
            $query->where('makeanoffers.status', '=', $status);
        })
        ->join('posts', 'makeanoffers.post_id', '=', 'posts.id')
        ->select('makeanoffers.*', 'posts.country_code' , 'posts.user_id', 'posts.category_id', 'posts.post_type_id', 'posts.title', 'posts.description', 'posts.tags', 'posts.price', 'posts.negotiable', 'posts.contact_name', 'posts.email', 'posts.phone', 'posts.phone_hidden', 'posts.address', 'posts.city_id', 'posts.lon', 'posts.lat', 'posts.ip_addr', 'posts.visits', 'posts.email_token', 'posts.phone_token', 'posts.tmp_token', 'posts.verified_email', 'posts.verified_phone', 'posts.reviewed', 'posts.featured', 'posts.archived', 'posts.fb_profile', 'posts.partner')  
            ->orderByDesc('makeanoffers.id');
        
		$num = $makeanoffers->count();
		$data = $makeanoffers->limit(7)->get();
		$result = $data->toArray();
		//print_r($result);
		$i=0;
		foreach($result as $key => $post){
		   $buyer_product_1 = $post->buyer_product_1;
		    $pId = $post->post_id;
		    //$postIdData = DB::table('posts')->where('id', $pId);
		    $postIdData = Post::where(['id' => $pId])->first();
		    
		    $post->post_owner_id = $postIdData->user_id;
			if($post->approve_seller==0)
			{
			$post->seller_product_status = "New Offer";
			$post->buyer_product_status = "Awaiting Response";
			$post->counter_offer = 0;
			}
			elseif($post->approve_seller==1)
			{
			//$post->seller_product_status = 'deal';
			$post->seller_product_status = 'Accepted';
			$post->buyer_product_status = 'Accepted';
			$post->counter_offer = 0;
			}
			else{
			   //$post->seller_product_status = 'Counter Offer'; 
			   $post->seller_product_status = 'Rejected'; 
			   $post->buyer_product_status = 'Rejected';
			   $post->counter_offer = 1;
			}
		   if(!empty($post->seller_id))
			{
			$user1 = User::findorfail($post->seller_id);
			
			$post->seller_name = $user1->username;
			}
			else
			{
			$post->seller_name = '';
			}
			if(!empty($post->buyer_id))
			{
			$user2 = User::findorfail($post->buyer_id);			
			$post->buyer_name = $user2->username;
			}
			else
			{
			$post->buyer_name = '';
			}
		   $buyer_product_2 = $post->buyer_product_2;
		   $buyer_product_3 = $post->buyer_product_3;
		   $seller_product_1 = $post->seller_product_1;
		   $seller_product_2 = $post->seller_product_2;
		   $pictures_1 = \App\Models\Picture::where('post_id', $buyer_product_1)->orderBy('position')->orderBy('id');
			if ($pictures_1->count() > 0) {
				$post->buyer_product_1_image = resize($pictures_1->first()->filename, 'medium');
			} else {
				$post->buyer_product_1_image = '';
			}
			
			  $pictures_2 = \App\Models\Picture::where('post_id', $buyer_product_2)->orderBy('position')->orderBy('id');
			if ($pictures_2->count() > 0) {
				$post->buyer_product_2_image = resize($pictures_2->first()->filename, 'medium');
			} else {
				$post->buyer_product_2_image = '';
			}
			
			  $pictures_3 = \App\Models\Picture::where('post_id', $buyer_product_3)->orderBy('position')->orderBy('id');
			if ($pictures_3->count() > 0) {
				$post->buyer_product_3_image = resize($pictures_3->first()->filename, 'medium');
			} else {
				$post->buyer_product_3_image = '';
			}
			
			  $pictures_4 = \App\Models\Picture::where('post_id', $seller_product_1)->orderBy('position')->orderBy('id');
			if ($pictures_4->count() > 0) {
				$post->seller_product_1_image = resize($pictures_4->first()->filename, 'medium');
			} else {
				$post->seller_product_1_image = '';
			}
			
			  $pictures_5 = \App\Models\Picture::where('post_id', $seller_product_2)->orderBy('position')->orderBy('id');
			if ($pictures_5->count() > 0) {
				$post->seller_product_2_image = resize($pictures_5->first()->filename, 'medium');
			} else {
				$post->seller_product_2_image = '';
			}
		   $bp1 = Post::where(['id' => $buyer_product_1])->first();
		   $bp2 = Post::where(['id' => $buyer_product_2])->first();
		   $bp3 = Post::where(['id' => $buyer_product_3])->first();
		   $sp1 = Post::where(['id' => $seller_product_1])->first();
		   $sp2 = Post::where(['id' => $seller_product_2])->first();
		   if($bp1['title']!=''){
		   $post->buyer_product_1_title = $bp1['title'];
		   }
		   else{
		      $post->buyer_product_1_title = ''; 
		   }
		   if($bp1['price']!=''){
		   $post->buyer_product_1_price = $bp1['price'];
		   }
		   else{
		      $post->buyer_product_1_price = ''; 
		   }
		   if($bp2['title']!=''){
		   $post->buyer_product_2_title = $bp2['title'];
		   }
		   else{
		      $post->buyer_product_2_title = ''; 
		   }
		   if($bp2['price']!=''){
		   $post->buyer_product_2_price = $bp2['price'];
		   }
		   else{
		      $post->buyer_product_2_price = ''; 
		   }
		   if($bp3['title']!=''){
		   $post->buyer_product_3_title = $bp3['title'];
		   }
		   else{
		      $post->buyer_product_3_title = ''; 
		   }
		   if($bp3['price']!=''){
		   $post->buyer_product_3_price = $bp3['price'];
		   }
		   else{
		      $post->buyer_product_3_price = ''; 
		   }
		   
		   if($sp1['title']!=''){
		   $post->seller_product_1_title = $sp1['title'];
		   }
		   else{
		      $post->seller_product_1_title = ''; 
		   }
		   if($sp1['price']!=''){
		   $post->seller_product_1_price = $sp1['price'];
		   }
		   else{
		      $post->seller_product_1_price = ''; 
		   }
		   
		   if($sp2['title']!=''){
		   $post->seller_product_2_title = $sp2['title'];
		   }
		   else{
		      $post->seller_product_2_title = ''; 
		   }
		   if($sp2['price']!=''){
		   $post->seller_product_2_price = $sp2['price'];
		   }
		   else{
		      $post->seller_product_2_price = ''; 
		   }
		   
		   //$data['buyer_product_1_image'] = '';
		  //  $bp1 =  DB::table('posts')->where('id', '=', $buyer_product_1);
		  // print_r($bp1);
		    //$data['picture'] = Picture::where(['post_id' => $data['post']['id'] , 'position' => 1])->first();
		   
		// Get Post's Pictures
										$pictures = \App\Models\Picture::where('post_id', $post->post_id)->orderBy('position')->orderBy('id');
										if ($pictures->count() > 0) {
											$postImg = resize($pictures->first()->filename, 'medium');
										} else {
											$postImg = resize(config('larapen.core.picture.default'));
										}
										$post->postImg = $postImg;
										$i++;
										}
		
		
		    
		
		return response()->json(['status'=>1,'message'=>'success','results'=>$data,'num'=>$num]);
        }
        else{
            return response()->json(['status'=>0,'message'=>'Invalid user','results'=>'','num'=>'']);
        }
	}
	
	
	
	/**
	 * List Recieved Offers 
	 *
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function RecievedOffers(Request $request) 
	{		
	    \App::setLocale(request()->get('language') ?: 'en');
 
		$seller_id = $request->userid;
      
        //$receiverId = $request->receiverId;
        //$receivers = DB::table('users')->where('id', '=',$receiverId)->get();
        $users = DB::table('users')->where('id', '=',$seller_id)->get();
        if(!empty($users)){
        foreach($users as $user){
           $title = $user->name.'has send you an offer';
        }
        $status = 1;
		$makeanoffers =  DB::table('makeanoffers')->where(function ($query) use ($seller_id, $status)
        {
            $query->where('makeanoffers.seller_id', $seller_id);
            $query->where('makeanoffers.status', '=', $status);
        })
        ->join('posts', 'makeanoffers.post_id', '=', 'posts.id')
        ->select('makeanoffers.offer_maker_id','makeanoffers.created_at','makeanoffers.id as offer_id', 'makeanoffers.approve_seller', 'makeanoffers.buyer_id', 'makeanoffers.buyer_product_1', 'makeanoffers.buyer_product_2', 'makeanoffers.buyer_product_3', 'posts.country_code' , 'posts.title' , 'makeanoffers.post_id', 'posts.price', 'posts.contact_name')  
        ->orderByDesc('makeanoffers.id');
        
		$num = $makeanoffers->count();
		$data = $makeanoffers->limit(7)->get();
		$result = $data->toArray();
		//print_r($result);
		$i=0;
		foreach($result as $key => $post){
		   
		    $pId = $post->post_id;
		    //$postIdData = DB::table('posts')->where('id', $pId);
		    $postIdData = Post::where(['id' => $pId])->first();
		    
		    $post->post_owner_id = $postIdData->user_id;
			if($post->approve_seller==0)
			{
			$post->seller_product_status = "New Offer";			 
			$post->counter_offer = 0;
			}
			elseif($post->approve_seller==1)
			{
			//$post->seller_product_status = 'deal';
			$post->seller_product_status = 'Accepted';			
			$post->counter_offer = 0;
			}
			else{
			   //$post->seller_product_status = 'Counter Offer'; 
			   $post->seller_product_status = 'Rejected';
			  
			   $post->counter_offer = 1;
			}
		    
			if(!empty($post->buyer_id))
			{
			$user2 = User::findorfail($post->buyer_id);			
			$post->buyer_name = $user2->username;
			}
			else
			{
			$post->buyer_name = '';
			}
		   
		   $buyer_product_1 = $post->buyer_product_1;
		   $buyer_product_2 = $post->buyer_product_2;
		   $buyer_product_3 = $post->buyer_product_3;
		   
			 
			 
		   $bp1 = Post::where(['id' => $buyer_product_1])->first();
		   $bp2 = Post::where(['id' => $buyer_product_2])->first();
		   $bp3 = Post::where(['id' => $buyer_product_3])->first();
		   
		   if($bp1['title']!=''){
		   $post->buyer_product_1_title = $bp1['title'];
		   }
		   else{
		      $post->buyer_product_1_title = ''; 
		   }
		   
		   if($bp2['title']!=''){
		   $post->buyer_product_2_title = $bp2['title'];
		   }
		   else{
		      $post->buyer_product_2_title = ''; 
		   }
		   
		   if($bp3['title']!=''){
		   $post->buyer_product_3_title = $bp3['title'];
		   }
		   else{
		      $post->buyer_product_3_title = ''; 
		   }
		    
		    
										  
										$i++;
										}
		
		$all_offers=array();
		$x=0;
		foreach($result as $offer){
			
			if(empty($offer->country_code)) {$country_code='KW';}else{$country_code=$offer->country_code;}

			$getcurrencycountry = \DB::table('countries')
			->join('currencies', 'currencies.code', '=', 'countries.currency_code')
			->select('currencies.*')
			->where('countries.code', '=', $country_code)
			->first();



	if ($post->price > 0)
	{
		$get_currency = \App\Helpers\Number::money_price_latest($offer->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);
	}
	else
	{
		$get_currency = t('Free');
	}

 

           
			
			$all_offers[$x]['offer_id']=$offer->offer_id;
			$all_offers[$x]['current_user_id']=$seller_id;
			$all_offers[$x]['offer_status']=$offer->seller_product_status;
			$all_offers[$x]['offer_color']= $this->offer_color($offer->seller_product_status);
			$all_offers[$x]['from_user']=$offer->buyer_name;
			$middle = strtotime($offer->created_at);             // returns bool(false)
			$new_date = date('Y-m-d  H:i', $middle);  

			$all_offers[$x]['created_at']=$new_date;
			$all_offers[$x]['product']=$offer->title;
			$all_offers[$x]['price']=$get_currency;
			$x++;
			}    
		
		return response()->json(['results'=>$all_offers]);
        }
        else{
            return response()->json(['status'=>0,'message'=>'Invalid user','results'=>'','num'=>'']);
        }
	}
	
	/**
	 * Return Offer Color 
	 *
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	
	public function offer_color($status){
			
		if($status=='Rejected'){
		   $color='#d9000d';//red
		}
		elseif($status=='Accepted')
		{
		   $color='#67c760';//green
		}
		else{

		   $color='#FF000000';//black
	   }

	   return $color;
	   }

	
	
	/**
	 * List Recieved Offers 
	 *
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function makeOffersDeal(Request $request) 
	{
		 
    
		
		$MyPostId=$request->MyPostId;
		$offerId=$request->offerId;
		
		$makeanofferget = Makeanoffer::find($offerId);
    	$offer_maker_id = $makeanofferget->offer_maker_id;
		$offer_seller_id = $makeanofferget->seller_id;
    	
    	$offer_maker_name = User::where(['id' => $offer_maker_id])->first();
		$offer_sender_name = User::where(['id' => $offer_seller_id])->first();

    	$to_name  = $offer_maker_name->username;
		$from_name = $offer_sender_name->username;
		
		
		
        
        $post = Post::unarchived()->findOrFail($MyPostId);
        
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
        $title = 'Offer accepted';             
        // notification message
        $message = "Offer accepted Data"; 
        $type = "Offeraccepted";                      
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
		$makeanoffer->approve_seller = 1;
		$makeanoffer->is_read = 0;
		$makeanoffer->update();
		//return redirect('account/makeanoffers/'.$postId.'/edit/'.$offerId);
		return response()->json(['results'=>'Success','makeanofferid'=>$offerId,'MyPostId'=>$MyPostId, 'firebaseresponse' => $firebaseresponse]);
		
	}
	
	
	
	/**
	 * List Sent Offers by user_id
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function SentOffers(Request $request) 
	{		
	    \App::setLocale(request()->get('language') ?: 'en');
// 	  echo "hello";
// 	  die();
		$seller_id = $request->userid;
        $buyer_id = $request->userid;
        //$receiverId = $request->receiverId;
       // $receivers = DB::table('users')->where('id', '=',$receiverId)->get();
        $users = DB::table('users')->where('id', '=',$seller_id)->get();
        if(!empty($users)){
        foreach($users as $user){
           $title = $user->name.'has send you an offer';
        }
        $status = 1;
		$makeanoffers =  DB::table('makeanoffers')->where(function ($query) use ($seller_id, $buyer_id, $status)
        {
            $query->where('makeanoffers.buyer_id', $seller_id);
			//->orWhere('makeanoffers.seller_id', $seller_id);
            $query->where('makeanoffers.status', '=', $status);
        })
        ->join('posts', 'makeanoffers.post_id', '=', 'posts.id')
        ->select('makeanoffers.*','makeanoffers.id as offer_id', 'posts.country_code' , 'posts.user_id', 'posts.category_id', 'posts.post_type_id', 'posts.title', 'posts.description', 'posts.tags', 'posts.price', 'posts.negotiable', 'posts.contact_name', 'posts.email', 'posts.phone', 'posts.phone_hidden', 'posts.address', 'posts.city_id', 'posts.lon', 'posts.lat', 'posts.ip_addr', 'posts.visits', 'posts.email_token', 'posts.phone_token', 'posts.tmp_token', 'posts.verified_email', 'posts.verified_phone', 'posts.reviewed', 'posts.featured', 'posts.archived', 'posts.fb_profile', 'posts.partner')  
            ->orderByDesc('makeanoffers.id');
        
		$num = $makeanoffers->count();
		$data = $makeanoffers->limit(7)->get();
		$result = $data->toArray();
		//print_r($result);
		$i=0;
		foreach($result as $key => $post){
		   $buyer_product_1 = $post->buyer_product_1;
		    $pId = $post->post_id;
		    //$postIdData = DB::table('posts')->where('id', $pId);
		    $postIdData = Post::where(['id' => $pId])->first();
		    
		    $post->post_owner_id = $postIdData->user_id;
			if($post->approve_seller==0)
			{
			$post->seller_product_status = "New Offer";
			$post->buyer_product_status = "Awaiting Response";
			$post->counter_offer = 0;
			}
			elseif($post->approve_seller==1)
			{
			//$post->seller_product_status = 'deal';
			$post->seller_product_status = 'Accepted';
			$post->buyer_product_status = 'Accepted';
			$post->counter_offer = 0;
			}
			else{
			   //$post->seller_product_status = 'Counter Offer'; 
			   $post->seller_product_status = 'Rejected';
			   $post->buyer_product_status = 'Rejected';
			   $post->counter_offer = 1;
			}
		   if(!empty($post->seller_id))
			{
			$user1 = User::findorfail($post->seller_id);
			
			$post->seller_name = $user1->username;
			}
			else
			{
			$post->seller_name = '';
			}
			if(!empty($post->buyer_id))
			{
			$user2 = User::findorfail($post->buyer_id);			
			$post->buyer_name = $user2->username;
			}
			else
			{
			$post->buyer_name = '';
			}
		   $buyer_product_2 = $post->buyer_product_2;
		   $buyer_product_3 = $post->buyer_product_3;
		   $seller_product_1 = $post->seller_product_1;
		   $seller_product_2 = $post->seller_product_2;
		   $pictures_1 = \App\Models\Picture::where('post_id', $buyer_product_1)->orderBy('position')->orderBy('id');
			if ($pictures_1->count() > 0) {
				$post->buyer_product_1_image = resize($pictures_1->first()->filename, 'medium');
			} else {
				$post->buyer_product_1_image = '';
			}
			
			  $pictures_2 = \App\Models\Picture::where('post_id', $buyer_product_2)->orderBy('position')->orderBy('id');
			if ($pictures_2->count() > 0) {
				$post->buyer_product_2_image = resize($pictures_2->first()->filename, 'medium');
			} else {
				$post->buyer_product_2_image = '';
			}
			
			  $pictures_3 = \App\Models\Picture::where('post_id', $buyer_product_3)->orderBy('position')->orderBy('id');
			if ($pictures_3->count() > 0) {
				$post->buyer_product_3_image = resize($pictures_3->first()->filename, 'medium');
			} else {
				$post->buyer_product_3_image = '';
			}
			
			  $pictures_4 = \App\Models\Picture::where('post_id', $seller_product_1)->orderBy('position')->orderBy('id');
			if ($pictures_4->count() > 0) {
				$post->seller_product_1_image = resize($pictures_4->first()->filename, 'medium');
			} else {
				$post->seller_product_1_image = '';
			}
			
			  $pictures_5 = \App\Models\Picture::where('post_id', $seller_product_2)->orderBy('position')->orderBy('id');
			if ($pictures_5->count() > 0) {
				$post->seller_product_2_image = resize($pictures_5->first()->filename, 'medium');
			} else {
				$post->seller_product_2_image = '';
			}
		   $bp1 = Post::where(['id' => $buyer_product_1])->first();
		   $bp2 = Post::where(['id' => $buyer_product_2])->first();
		   $bp3 = Post::where(['id' => $buyer_product_3])->first();
		   $sp1 = Post::where(['id' => $seller_product_1])->first();
		   $sp2 = Post::where(['id' => $seller_product_2])->first();
		   if($bp1['title']!=''){
		   $post->buyer_product_1_title = $bp1['title'];
		   }
		   else{
		      $post->buyer_product_1_title = ''; 
		   }
		   if($bp1['price']!=''){
		   $post->buyer_product_1_price = $bp1['price'];
		   }
		   else{
		      $post->buyer_product_1_price = ''; 
		   }
		   if($bp2['title']!=''){
		   $post->buyer_product_2_title = $bp2['title'];
		   }
		   else{
		      $post->buyer_product_2_title = ''; 
		   }
		   if($bp2['price']!=''){
		   $post->buyer_product_2_price = $bp2['price'];
		   }
		   else{
		      $post->buyer_product_2_price = ''; 
		   }
		   if($bp3['title']!=''){
		   $post->buyer_product_3_title = $bp3['title'];
		   }
		   else{
		      $post->buyer_product_3_title = ''; 
		   }
		   if($bp3['price']!=''){
		   $post->buyer_product_3_price = $bp3['price'];
		   }
		   else{
		      $post->buyer_product_3_price = ''; 
		   }
		   
		   if($sp1['title']!=''){
		   $post->seller_product_1_title = $sp1['title'];
		   }
		   else{
		      $post->seller_product_1_title = ''; 
		   }
		   if($sp1['price']!=''){
		   $post->seller_product_1_price = $sp1['price'];
		   }
		   else{
		      $post->seller_product_1_price = ''; 
		   }
		   
		   if($sp2['title']!=''){
		   $post->seller_product_2_title = $sp2['title'];
		   }
		   else{
		      $post->seller_product_2_title = ''; 
		   }
		   if($sp2['price']!=''){
		   $post->seller_product_2_price = $sp2['price'];
		   }
		   else{
		      $post->seller_product_2_price = ''; 
		   }
		   
		   //$data['buyer_product_1_image'] = '';
		  //  $bp1 =  DB::table('posts')->where('id', '=', $buyer_product_1);
		  // print_r($bp1);
		    //$data['picture'] = Picture::where(['post_id' => $data['post']['id'] , 'position' => 1])->first();
		   
		// Get Post's Pictures
										$pictures = \App\Models\Picture::where('post_id', $post->post_id)->orderBy('position')->orderBy('id');
										if ($pictures->count() > 0) {
											$postImg = resize($pictures->first()->filename, 'medium');
										} else {
											$postImg = resize(config('larapen.core.picture.default'));
										}
										$post->postImg = $postImg;
										$i++;
										}
		
		
		$all_offers=array();$x=0;
  
		foreach($data as $offer){
			if(empty($offer->country_code)) {$country_code='KW';}else{$country_code=$offer->country_code;}
			$getcurrencycountry = \DB::table('countries')
			->join('currencies', 'currencies.code', '=', 'countries.currency_code')
			->select('currencies.*')
			->where('countries.code', '=', $country_code)
			->first();
	if ($post->price > 0)
	{
		$get_currency = \App\Helpers\Number::money_price_latest($offer->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);
	}
	else
	{
		$get_currency = t('Free');
	}
 

			$all_offers[$x]['offer_id']=$offer->offer_id;
			$all_offers[$x]['current_user_id']=$seller_id;
			$all_offers[$x]['offer_status']=$offer->buyer_product_status;
			$all_offers[$x]['offer_color']= $this->offer_color($offer->buyer_product_status);
			$all_offers[$x]['from_user']=$offer->seller_name;

			$middle = strtotime($offer->created_at);             // returns bool(false)
			$new_date = date('Y-m-d  H:i', $middle);  

			$all_offers[$x]['created_at']=$new_date;
			 
			$all_offers[$x]['product']=$offer->title;
			$all_offers[$x]['price']=$get_currency;
			$x++;
		}  
			
		
		return response()->json(['results'=>$all_offers]);
        }
        else{
            return response()->json(['status'=>0,'message'=>'Invalid user','results'=>'','num'=>'']);
        }
	}
	
	
	
	
	public function makeanofferDetail(Request $request,$old_offer=null,$offer_type=null) 
	{		
 		if(!empty($request->offerId)){$offerId = $request->offerId;}else{$offerId = $old_offer;}
		if(!empty($request->offer_type)){$offer_type = $request->offer_type;}else{$offer_type = $offer_type;}
		         
        
		$status = 1;
		$makeanoffers =  DB::table('makeanoffers')->where('id','=',$offerId);
		$num = $makeanoffers->count();
		$data = $makeanoffers->get();
		$result = $data->toArray(); 
		$i=0;		
		
		foreach($result as $key => $post){

		    $buyer_product_1 = $post->buyer_product_1;
		    $pId = $post->post_id;
		    $postIdData = Post::where(['id' => $pId])->first();
		   	$data1 = $postIdData->get();
		    $resultPost = $data1->toArray();
			
			

		   $post->title = $postIdData->title; 
		   $post->price = $postIdData->price;
		   $post->negotiable = $postIdData->negotiable;
		   $post->contact_name = $postIdData->contact_name;
		   $post->user_id = $postIdData->user_id;
		   $post->country_code = $postIdData->country_code;
		   $post->post_owner_id = $postIdData->user_id;
		   if($post->approve_seller==0)
			{
			$post->seller_product_status = "New Offer";
			$post->buyer_product_status = "Awaiting Response";
			$post->counter_offer = 0;
			}
			elseif($post->approve_seller==1)
			{
			//$post->seller_product_status = 'deal';
			$post->seller_product_status = 'Accepted';
			$post->buyer_product_status = 'Accepted';
			$post->counter_offer = 0;
			}
			else{
			   //$post->seller_product_status = 'Counter Offer'; 
			   $post->seller_product_status = 'Rejected';
			   $post->buyer_product_status = 'Rejected';
			   $post->counter_offer = 1;
			}
		   if(!empty($post->seller_id))
			{
			$user1 = User::findorfail($post->seller_id);			
			$post->seller_name = $user1->username;
			}
			else{$post->seller_name = '';}			
			if(!empty($post->buyer_id))
			{
			$user2 = User::findorfail($post->buyer_id);			
			$post->buyer_name = $user2->username;
			}
			else{$post->buyer_name = '';}
		  if(!empty($post->buyer_product_1)){
			$bp1 = Post::where(['id' => $post->buyer_product_1])->first();
			if($bp1['title']!=''){$post->buyer_product_1_title = $bp1['title'];}else{$post->buyer_product_1_title = '';}
			if($bp1['price']!=''){$post->buyer_product_1_price = $bp1['price'];}else{$post->buyer_product_1_price = '';}			
		  } 
		   if(!empty($post->buyer_product_2)){
		   $bp2 = Post::where(['id' => $post->buyer_product_2])->first();
		   if($bp2['title']!=''){$post->buyer_product_2_title = $bp2['title'];}else{$post->buyer_product_2_title = '';}
		   if($bp2['price']!=''){$post->buyer_product_2_price = $bp2['price'];}else{$post->buyer_product_2_price = '';}
		   } 
		   if(!empty($post->buyer_product_3)){
		   $bp3 = Post::where(['id' => $post->buyer_product_3])->first();
		   if($bp3['title']!=''){$post->buyer_product_3_title = $bp3['title'];}else{$post->buyer_product_3_title = '';}
		   if($bp3['price']!=''){$post->buyer_product_3_price = $bp3['price'];}else{$post->buyer_product_3_price = '';}		
		   } 
		   if(!empty($post->seller_product_1)){
		   $sp1 = Post::where(['id' => $post->seller_product_1])->first();
		   if($sp1['title']!=''){$post->seller_product_1_title = $sp1['title'];}else{$post->seller_product_1_title = '';}
		   if($sp1['price']!=''){$post->seller_product_1_price = $sp1['price'];}else{$post->seller_product_1_price = '';}	
		    } 
		   if(!empty($post->seller_product_2)){
		   $sp2 = Post::where(['id' => $post->seller_product_2])->first();
		   if($sp2['title']!=''){$post->seller_product_2_title = $sp2['title'];}else{$post->seller_product_2_title = '';}
		   if($sp2['price']!=''){$post->seller_product_2_price = $sp2['price'];}else{$post->seller_product_2_price = '';}
		   } 
		   
		   $i++;
		}
	    
	$all_offers=array();$products=array();$price = t('Free');$offer_price = t('Free');$old_offer="";	
	
	foreach($data as $offer){
		if(empty($offer->country_code)) {$country_code='KW';}else{$country_code=$offer->country_code;}
	$getcurrencycountry = \DB::table('countries')->join('currencies', 'currencies.code', '=', 'countries.currency_code')
			                     ->select('currencies.*')->where('countries.code', '=', $country_code)->first();

								 
	if ($post->price > 0){$price = \App\Helpers\Number::money_price_latest($offer->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);}
	if ($post->price > 0){$offer_price = \App\Helpers\Number::money_price_latest($offer->offer_price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);}
	 
	        $all_offers['offer_id']=$offerId; 
			
			if($offer_type=="sent"){
				
				$makeanoffers =  DB::table('makeanoffers')->where('makeanoffers.seller_id', $offer->post_owner_id)->where('id','<',$offerId)->first();
                if(!empty($makeanoffers->id)){$old_offer=$makeanoffers->id;}
				$all_offers['offer_status']=$offer->buyer_product_status;
				$all_offers['offer_color']= $this->offer_color($offer->buyer_product_status);
				$all_offers['to_user']=$offer->seller_name;
				$all_offers['wanted_product']=$offer->title;	
			    $all_offers['wanted_product_price']=$price;				
			    $all_offers['my_offer_price']=$offer_price;
				
			if(!empty($offer->buyer_product_1_title))
			{$products[] = (object) array('title' => $offer->buyer_product_1_title, 'price' => $offer->buyer_product_1_price);}
			if(!empty($offer->buyer_product_2_title))
			{$products[] = (object) array('title' => $offer->buyer_product_2_title, 'price' => $offer->buyer_product_2_price);}
			if(!empty($offer->buyer_product_3_title))
			{$products[] = (object) array('title' => $offer->buyer_product_3_title, 'price' => $offer->buyer_product_3_price);}
				
			}
			else{
				
				$makeanoffers =  DB::table('makeanoffers')->where('makeanoffers.buyer_id', $offer->post_owner_id)->where('id','<',$offerId)->first();
                if(!empty($makeanoffers->id)){$old_offer=$makeanoffers->id;}

				$all_offers['offer_status']=$offer->seller_product_status;
				$all_offers['offer_color']= $this->offer_color($offer->seller_product_status);
				$all_offers['from_user']=$offer->buyer_name;	
				$all_offers['from_user_id']=$offer->offer_maker_id;	
				$all_offers['post_id']=$offer->post_id;				
				$all_offers['my_product']=$offer->title;	
				$all_offers['my_product_price']=$price;
				$all_offers['recieved_offer_price']=$offer_price;
				
				if(!empty($offer->buyer_product_1_title))
				{$products[] = (object) array('title' => $offer->buyer_product_1_title, 'price' => $offer->buyer_product_1_price);}
				if(!empty($offer->buyer_product_2_title))
				{$products[] = (object) array('title' => $offer->buyer_product_2_title, 'price' => $offer->buyer_product_2_price);}
				if(!empty($offer->buyer_product_3_title))
				{$products[] = (object) array('title' => $offer->buyer_product_3_title, 'price' => $offer->buyer_product_3_price);}	
			}
			 $all_offers['products']=$products;	
			 $all_offers['created_at']=$offer->created_at;
			 $all_offers['post_id']=$offer->post_id;



			}
             $old_offer_details=null;
			if($offer_type=="sent"){				
				 if(!empty($old_offer)){$old_offer_details=$this->makeanofferDetailNext($old_offer,'sent');}			 
			 }else{
				 if(!empty($old_offer)){$old_offer_details=$this->makeanofferDetailNext($old_offer,'recieved');}
				} 

	  return response()->json(['new_offer'=>$all_offers,'old_offer'=>$old_offer_details]);
	}	
	
	
	
	
	
	
	
	public function makeanofferDetailNext($old_offer=null,$offer_type=null) 
	{		
 		$offerId = $old_offer;
		$offer_type = $offer_type;          
		$status = 1;
		$data =  DB::table('makeanoffers')->where('id','=',$offerId)->get();
		$result = $data->toArray();	  
	 
		$i=0;		
		foreach($result as $key => $post){



			//return $post;
		    $buyer_product_1 = $post->buyer_product_1;
		    $pId = $post->post_id;

			if(empty($pId)){ return NULL;  } 

		    $postIdData = Post::where(['id' => $pId])->first();	
			if(empty($resultPost)){ return NULL;}
			$resultPost = $postIdData->toArray();

		   $post->title = $postIdData->title; 
		   $post->price = $postIdData->price;
		   $post->negotiable = $postIdData->negotiable;
		   $post->contact_name = $postIdData->contact_name;
		   $post->user_id = $postIdData->user_id;
		   $post->country_code = $postIdData->country_code;
		   $post->post_owner_id = $postIdData->user_id;
		   if($post->approve_seller==0)
			{
			$post->seller_product_status = "New Offer";
			$post->buyer_product_status = "Awaiting Response";
			$post->counter_offer = 0;
			}
			elseif($post->approve_seller==1)
			{
			 
			//$post->seller_product_status = 'deal';
			$post->seller_product_status = 'Accepted';
			$post->buyer_product_status = 'Accepted';
			$post->counter_offer = 0;
			}
			else{
			   //$post->seller_product_status = 'Counter Offer'; 
			   $post->seller_product_status = 'Rejected';
			   $post->buyer_product_status = 'Rejected';
			   $post->counter_offer = 1;
			}
		   if(!empty($post->seller_id))
			{
			$user1 = User::findorfail($post->seller_id);			
			$post->seller_name = $user1->username;
			}
			else{$post->seller_name = '';}			
			if(!empty($post->buyer_id))
			{
			$user2 = User::findorfail($post->buyer_id);			
			$post->buyer_name = $user2->username;
			}
			else{$post->buyer_name = '';}
		   $bp1 = Post::where(['id' => $post->buyer_product_1])->first();
		   $bp2 = Post::where(['id' => $post->buyer_product_2])->first();
		   $bp3 = Post::where(['id' => $post->buyer_product_3])->first();
		   $sp1 = Post::where(['id' => $post->seller_product_1])->first();
		   $sp2 = Post::where(['id' => $post->seller_product_2])->first();
		   if($bp1['title']!=''){$post->buyer_product_1_title = $bp1['title'];}else{$post->buyer_product_1_title = '';}
           if($bp1['price']!=''){$post->buyer_product_1_price = $bp1['price'];}else{$post->buyer_product_1_price = '';}
		   if($bp2['title']!=''){$post->buyer_product_2_title = $bp2['title'];}else{$post->buyer_product_2_title = '';}
		   if($bp2['price']!=''){$post->buyer_product_2_price = $bp2['price'];}else{$post->buyer_product_2_price = '';}
		   if($bp3['title']!=''){$post->buyer_product_3_title = $bp3['title'];}else{$post->buyer_product_3_title = '';}
		   if($bp3['price']!=''){$post->buyer_product_3_price = $bp3['price'];}else{$post->buyer_product_3_price = '';}		   
		   if($sp1['title']!=''){$post->seller_product_1_title = $sp1['title'];}else{$post->seller_product_1_title = '';}
		   if($sp1['price']!=''){$post->seller_product_1_price = $sp1['price'];}else{$post->seller_product_1_price = '';}		   
		   if($sp2['title']!=''){$post->seller_product_2_title = $sp2['title'];}else{$post->seller_product_2_title = '';}
		   if($sp2['price']!=''){$post->seller_product_2_price = $sp2['price'];}else{$post->seller_product_2_price = '';}
		   $i++;
		}
	    
	$all_offers=array();$products=array();$price = t('Free');$offer_price = t('Free');$old_offer="";	 
	foreach($data as $offer){
		if(empty($offer->country_code)) {$country_code='KW';}else{$country_code=$offer->country_code;}
	$getcurrencycountry = \DB::table('countries')->join('currencies', 'currencies.code', '=', 'countries.currency_code')
			                     ->select('currencies.*')->where('countries.code', '=', $country_code)->first();
	if ($post->price > 0){$price = \App\Helpers\Number::money_price_latest($offer->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);}
	if ($post->price > 0){$offer_price = \App\Helpers\Number::money_price_latest($offer->offer_price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);}
	 
	        $all_offers['offer_id']=$offerId; 
			
			if($offer_type=="sent"){
				
				$makeanoffers =  DB::table('makeanoffers')->where('makeanoffers.seller_id', $offer->post_owner_id)->where('id','<',$offerId)->first();
                if(!empty($makeanoffers->id)){$old_offer=$makeanoffers->id;}
				$all_offers['offer_status']=$offer->buyer_product_status;
				$all_offers['offer_color']= $this->offer_color($offer->buyer_product_status);
				$all_offers['to_user']=$offer->seller_name;
				$all_offers['wanted_product']=$offer->title;	
			    $all_offers['wanted_product_price']=$price;				
			    $all_offers['my_offer_price']=$offer_price;
				 
			if(!empty($offer->buyer_product_1_title))
			{$products[] = (object) array('title' => $offer->buyer_product_1_title, 'price' => $offer->buyer_product_1_price);}
			if(!empty($offer->buyer_product_2_title))
			{$products[] = (object) array('title' => $offer->buyer_product_2_title, 'price' => $offer->buyer_product_2_price);}
			if(!empty($offer->buyer_product_3_title))
			{$products[] = (object) array('title' => $offer->buyer_product_3_title, 'price' => $offer->buyer_product_3_price);}
				
			}
			else{
				
				$makeanoffers =  DB::table('makeanoffers')->where('makeanoffers.buyer_id', $offer->post_owner_id)->where('id','<',$offerId)->first();
                if(!empty($makeanoffers->id)){$old_offer=$makeanoffers->id;}

				$all_offers['offer_status']=$offer->seller_product_status;
				$all_offers['offer_color']= $this->offer_color($offer->seller_product_status);
				$all_offers['from_user']=$offer->buyer_name;				
				$all_offers['my_product']=$offer->title;	
				$all_offers['my_product_price']=$price;
				$all_offers['recieved_offer_price']=$offer_price;
			 
				if(!empty($offer->buyer_product_1_title))
				{$products[] = (object) array('title' => $offer->buyer_product_1_title, 'price' => $offer->buyer_product_1_price);}
				if(!empty($offer->buyer_product_2_title))
				{$products[] = (object) array('title' => $offer->buyer_product_2_title, 'price' => $offer->buyer_product_2_price);}
				if(!empty($offer->buyer_product_3_title))
				{$products[] = (object) array('title' => $offer->buyer_product_3_title, 'price' => $offer->buyer_product_3_price);}	
			}
			 $all_offers['products']=$products;	
			 $all_offers['created_at']=$offer->created_at;
			}
		return $all_offers;
	}	
	
	
	
	
	public function priceFormatWithCurrency($country_code,$price) 
	{

		$getcurrencycountry = \DB::table('countries')
		->join('currencies', 'currencies.code', '=', 'countries.currency_code')				
		->select('currencies.*')->where('countries.code', '=', $country_code)->first();
		$formatted_price = \App\Helpers\Number::money_price_latest($price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);
	return $formatted_price;
	}
	
	
	public function getOffer(Request $request) 
	{		
		$seller_id = $request->userid;
        $buyer_id = $request->userid;
        $status = 1;

		$makeanoffers =  DB::table('makeanoffers')->where(function ($query) use ($seller_id, $buyer_id, $status)
        {
            $query->where('makeanoffers.buyer_id', $seller_id)->orWhere('makeanoffers.seller_id', $seller_id);
            $query->where('makeanoffers.status', '=', $status);
        })
        ->join('posts', 'makeanoffers.post_id', '=', 'posts.id')
        ->select('makeanoffers.*', 'posts.country_code' , 'posts.user_id', 'posts.category_id', 'posts.post_type_id', 'posts.title', 'posts.description', 'posts.tags', 'posts.price', 'posts.negotiable', 'posts.contact_name', 'posts.email', 'posts.phone', 'posts.phone_hidden', 'posts.address', 'posts.city_id', 'posts.lon', 'posts.lat', 'posts.ip_addr', 'posts.visits', 'posts.email_token', 'posts.phone_token', 'posts.tmp_token', 'posts.verified_email', 'posts.verified_phone', 'posts.reviewed', 'posts.featured', 'posts.archived', 'posts.fb_profile', 'posts.partner',\DB::raw('(SELECT pictures.filename FROM pictures WHERE pictures.post_id = makeanoffers.post_id AND pictures.position = 1 ) AS image'))  
            ->orderByDesc('makeanoffers.id');
        var_dump($makeanoffers);
        die();
		$num = $makeanoffers->count();
		$data = $makeanoffers->get();
		
		$payload = array();
		
		foreach($data as $value)
		{
		    $json['id'] = $value->id;
		    $json['post_id'] = $value->post_id;
		    $json['buyer_product_1'] = $value->buyer_product_1;
		    $json['buyer_product_2'] = $value->buyer_product_2;
		    $json['buyer_product_3'] = $value->buyer_product_3;
		    $json['seller_product_1'] = $value->seller_product_1;
		    $json['seller_product_2'] = $value->seller_product_2;
		    $json['original_price'] = $value->original_price;
		    $json['offer_price'] = $value->offer_price;
		    $json['description_text'] = $value->description_text;
		    $json['buyer_id'] = $value->buyer_id;
		    $json['seller_id'] = $value->seller_id;
		    $json['is_read_admin'] = $value->is_read_admin;
		    $json['is_read_professional'] = $value->is_read_professional;
		    
		    $json['is_read_individual'] = $value->is_read_individual;
		    $json['approve_seller'] = $value->approve_seller;
		    $json['approve_buyer'] = $value->approve_buyer;
		    $json['approve_admin'] = $value->approve_admin;
		    $json['status'] = $value->status;
		    $json['created_at'] = $value->created_at;
		    $json['updated_at'] = $value->updated_at;
		    $json['offer_parent'] = $value->offer_parent;
		    $json['offer_maker_id'] = $value->offer_maker_id;
		    $json['close_offer'] = $value->close_offer;
		    
		    $json['is_read'] = $value->is_read;
		    $json['counter_offer'] = $value->counter_offer;
		    $json['user_id'] = $value->user_id;
		    $json['category_id'] = $value->category_id;
		    $json['post_type_id'] = $value->post_type_id;
		    $json['title'] = $value->title;
		    $json['description'] = $value->description;
		    $json['tags'] = $value->tags;
		    $json['price'] = $value->price;
		    $json['negotiable'] = $value->negotiable;
		    
		    $json['contact_name'] = $value->contact_name;
		    $json['email'] = $value->email;
		    $json['phone'] = $value->phone;
		    $json['phone_hidden'] = $value->phone_hidden;
		    $json['address'] = $value->address;
		    
		    $json['city_id'] = $value->city_id;
		    $json['lon'] = $value->lon;
		    $json['lat'] = $value->lat;
		    $json['ip_addr'] = $value->ip_addr;
		    $json['visits'] = $value->visits;
		    $json['email_token'] = $value->email_token;
		    
		    $json['phone_token'] = $value->phone_token;
		    $json['tmp_token'] = $value->tmp_token;
		    $json['verified_email'] = $value->verified_email;
		    $json['verified_phone'] = $value->verified_phone;
		    $json['reviewed'] = $value->reviewed;
		    $json['featured'] = $value->featured;
		    $json['archived'] = $value->archived;
		    $json['fb_profile'] = $value->fb_profile;
		    $json['partner'] = $value->partner;
		    
		    if(!empty($value->image))
		    {
		        	$postImg = resize($value->image, 'medium');
		    }
		    else
		    {
	        	$postImg = resize(config('larapen.core.picture.default'));
		    }
		    
		    
		    $json['image'] = $postImg;
			
			if(!empty($value->buyer_id))
			{
			$user = User::findorfail($value->buyer_id);
			$json['from'] = $user->username;
			}
			else
			{
			$json['from'] = '';
			}
			
			if(!empty($value->seller_id))
			{
			$user1 = User::findorfail($value->seller_id);			
			$json['to'] = $user1->username;
			}
			else
			{
			$json['to'] = '';
			}
			
			$json['created_at_app'] = date('d F Y H:i',strtotime($value->created_at));
		  var_dump($json);
		 // die();
		  $payload[] =  $json;
		}
		
		
		
		
		return response()->json(['results'=>$payload,'num'=>$num]);
	}
	
	
	
	
	

	public function makeanoffer($id, Request $request)
	{	
		$makeanoffer = Makeanoffer::where(['post_id' => $id , 'status' => 1 , 'offer_parent' => 0])->first();
		if(empty($makeanoffer))
		{
			$data = [];
			$data['makeanoffer'] = $makeanoffer;
			$data['post'] = Post::findOrFail($id);
			$data['picture'] = Picture::where(['post_id' => $data['post']['id'] , 'position' => 1])->first();
			// if(auth()->user()->user_type_id == 2)
			// {

			// }
			// else
			// {

			// }
			
			$data['buyer'] = User::where(['id' => $request->userid])->first();
			$data['seller'] = User::where(['id' => $data['post']['user_id']])->first();
			$seller_id = $data['seller']['id'];
			$buyer_id = $data['buyer']['id'];
			$data['sellerPosts'] = DB::table('posts')->where(function ($query) use ($seller_id, $id)
	        {
	            $query->where('posts.user_id', '=', $seller_id);
	            $query->where('posts.id', '!=', $id);
	            $query->where('posts.reviewed', '=', 1);
	            $query->where('posts.archived', '=', 0);
	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
			->where('pictures.position' , 1)
	        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();

			$data['buyerPosts'] = DB::table('posts')->where(function ($query) use ($buyer_id)
	        {
	            $query->where('posts.user_id', '=', $buyer_id);
	            $query->where('posts.reviewed', '=', 1);
	            $query->where('posts.archived', '=', 0);

	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
	        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();
			
			return $data;
		}
		else
		{
			$data = [];
			$data['makeanoffer'] = Makeanoffer::where(['post_id' => $id , 'status' => 1])->first();
			$data['post'] = Post::findOrFail($id);
			$data['picture'] = Picture::where(['post_id' => $data['post']['id'] , 'position' => 1])->first();
			$data['buyer'] = User::where(['id' => $data['makeanoffer']['buyer_id']])->first();
			$data['seller'] = User::where(['id' => $data['makeanoffer']['seller_id']])->first();
			$seller_id = $data['seller']['id'];
			$buyer_id = $data['buyer']['id'];
			$buyer_product_1 = $data['makeanoffer']['buyer_product_1'];
			$buyer_product_2 = $data['makeanoffer']['buyer_product_2'];
			$buyer_product_3 = $data['makeanoffer']['buyer_product_3'];
			$seller_product_1 = $data['makeanoffer']['seller_product_1'];
			$seller_product_2 = $data['makeanoffer']['seller_product_2'];
			if(!empty($buyer_product_1))
			{
				$data['buyerProduct1'] = DB::table('posts')->where(function ($query) use ($buyer_product_1)
		        {
		            $query->where('posts.id', '=', $buyer_product_1);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
		        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($buyer_product_2))
			{
				$data['buyerProduct2'] = DB::table('posts')->where(function ($query) use ($buyer_product_2)
		        {
		            $query->where('posts.id', '=', $buyer_product_2);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
		        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($buyer_product_3))
			{
				$data['buyerProduct3'] = DB::table('posts')->where(function ($query) use ($buyer_product_3)
		        {
		            $query->where('posts.id', '=', $buyer_product_3);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
		        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($seller_product_1))
			{
				$data['sellerProduct1'] = DB::table('posts')->where(function ($query) use ($seller_product_1)
		        {
		            $query->where('posts.id', '=', $seller_product_1);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
		        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($seller_product_2))
			{
				$data['sellerProduct2'] = DB::table('posts')->where(function ($query) use ($seller_product_2)
		        {
		            $query->where('posts.id', '=', $seller_product_2);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
		        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			$data['sellerPosts'] = DB::table('posts')->where(function ($query) use ($seller_id)
	        {
	            $query->where('posts.user_id', '=', $seller_id);

	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
			->where('pictures.position' , 1)
	        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();
	  		
			$data['buyerPosts'] = DB::table('posts')->where(function ($query) use ($buyer_id)
	        {
	            $query->where('posts.user_id', '=', $buyer_id);

	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
	        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();
			
			return $data;
		}
	}

	public function edit($postId , $offerId, Request $request)
	{
	    
        $getMakeofferCount = \DB::table('makeanoffers')
            ->where('id','=',$offerId)
            ->first();
            
            // if($getMakeofferCount->is_read == '0')
            // {
            //   if($getMakeofferCount->offer_maker_id != $request->user_id)
            //   {
            //       $response_d =  \DB::table('makeanoffers')
            //           ->where('id', $offerId)
            //           ->update(['is_read' => '1']);
            //   }
            // }
            
            
        if(!empty($getMakeofferCount))
        {
           if($getMakeofferCount->is_read == '0')
           {
               if($request->user_id != $getMakeofferCount->offer_maker_id || $getMakeofferCount->approve_seller == 1 || $getMakeofferCount->approve_seller == 2)
               {
                   if($getMakeofferCount->approve_seller == 1)
                   {
                        if($getMakeofferCount->counter_offer == '0')      
                        {
                            if($request->user_id == $getMakeofferCount->buyer_id)
                            {
                                    $response_d =  \DB::table('makeanoffers')
                                      ->where('id', $offerId)
                                      ->update(['is_read' => '1']);
                            }   
                            else
                            {
                                
                            }
                        }
                        else
                        {
                            if($request->user_id == $getMakeofferCount->offer_maker_id)
                            {
                                $response_d =  \DB::table('makeanoffers')
                                  ->where('id', $offerId)
                                  ->update(['is_read' => '1']);
                            }
                        }
                   }
                   else
                   {
                       if($getMakeofferCount->approve_seller == 2)
                       {
                            if($request->user_id != $getMakeofferCount->offer_maker_id)
                            {
                                
                            }
                            else
                            {
                                $response_d =  \DB::table('makeanoffers')
                                  ->where('id', $offerId)
                                  ->update(['is_read' => '1']);
                            }
                       }
                       else
                       {
                           $response_d =  \DB::table('makeanoffers')
                                  ->where('id', $offerId)
                                  ->update(['is_read' => '1']);
                       }
                   }
                }
           }
        }
            
            
            
            
            
            
            
            
            
            
            
            
			$data = [];
			// $data['makeanoffer'] = Makeanoffer::where(['post_id' => $id , 'status' => 1])->first();
			$data['makeanoffer'] = Makeanoffer::findOrFail($offerId);
			$data['post'] = Post::findOrFail($postId);
			$data['picture'] = Picture::where(['post_id' => $data['post']['id'] , 'position' => 1])->first();
			$data['buyer'] = User::where(['id' => $data['makeanoffer']['buyer_id']])->first();
			$data['seller'] = User::where(['id' => $data['makeanoffer']['seller_id']])->first();
			$seller_id = $data['seller']['id'];
			$buyer_id = $data['buyer']['id'];
			$buyer_product_1 = $data['makeanoffer']['buyer_product_1'];
			$buyer_product_2 = $data['makeanoffer']['buyer_product_2'];
			$buyer_product_3 = $data['makeanoffer']['buyer_product_3'];
			$seller_product_1 = $data['makeanoffer']['seller_product_1'];
			$seller_product_2 = $data['makeanoffer']['seller_product_2'];

			if(!empty($buyer_product_1))
			{
				$data['buyerProduct1'] = DB::table('posts')->where(function ($query) use ($buyer_product_1)
		        {
		            $query->where('posts.id', '=', $buyer_product_1);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
		        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			} else {
			    $data['buyerProduct1'] = (object) array();
			}

			if(!empty($buyer_product_2))
			{
				$data['buyerProduct2'] = DB::table('posts')->where(function ($query) use ($buyer_product_2)
		        {
		            $query->where('posts.id', '=', $buyer_product_2);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
		        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			} else {
			    $data['buyerProduct2'] = (object) array();
			}

			if(!empty($buyer_product_3))
			{
				$data['buyerProduct3'] = DB::table('posts')->where(function ($query) use ($buyer_product_3)
		        {
		            $query->where('posts.id', '=', $buyer_product_3);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
		        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			} else {
			    $data['buyerProduct3'] = (object) array();
			}

			if(!empty($seller_product_1))
			{
			   	$data['sellerProduct1'] = DB::table('posts')->where(function ($query) use ($seller_product_1)
		        {
		            $query->where('posts.id', '=', $seller_product_1);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
		        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			} else {
			    $data['sellerProduct1'] = (object) array();
			}

			if(!empty($seller_product_2))
			{
				$data['sellerProduct2'] = DB::table('posts')->where(function ($query) use ($seller_product_2)
		        {
		            $query->where('posts.id', '=', $seller_product_2);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
		        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			} else {
			   $data['sellerProduct2'] = (object) array();
			}

			$data['sellerPosts'] = DB::table('posts')->where(function ($query) use ($seller_id)
	        {
	            $query->where('posts.user_id', '=', $seller_id);
                $query->where('posts.reviewed', '=', 1);
	            $query->where('posts.archived', '=', 0);

	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
			->where('pictures.position' , 1)
	        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();
	  		
			$data['buyerPosts'] = DB::table('posts')->where(function ($query) use ($buyer_id)
	        {
	            $query->where('posts.user_id', '=', $buyer_id);
	            $query->where('posts.reviewed', '=', 1);
	            $query->where('posts.archived', '=', 0);

	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
	        ->select('posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();
			
			
			return response()->json(['results'=>$data]);
			//return view('account.makeanoffers-edit', $data);
		// $data = [];
		// $data['makeanoffers'] = Makeanoffer::findOrFail($id);
		// $data['post'] = Post::findOrFail($data['makeanoffers']['post_id']);
		// $seller_id = $data['makeanoffers']['seller_id'];
  //       $buyer_id = $data['makeanoffers']['buyer_id'];
		
		// $data['sellerPosts'] = DB::table('posts')->where(function ($query) use ($seller_id)
  //       {
  //           $query->where('posts.user_id', '=', $seller_id);

  //       })
		// ->join('pictures', 'posts.id', '=', 'pictures.post_id')
  //       ->select('pictures.filename','pictures.position','pictures.active')->get();
		// $data['buyerPosts'] = DB::table('posts')->where(function ($query) use ($buyer_id)
  //       {
  //           $query->where('posts.user_id', '=', $buyer_id);

  //       })
		// ->join('pictures', 'posts.id', '=', 'pictures.post_id')
  //       ->select('pictures.filename','pictures.position','pictures.active')->get();
		
		// $data['pictures'] = DB::table('pictures')->select('pictures.filename','pictures.position','pictures.active')->where(['post_id' => $data['makeanoffers']['post_id'] , 'position' => 1])->first();

		// $all_post = DB::table('posts')->where(function ($query) use ($seller_id , $buyer_id)
  //       {
  //           if(auth()->user()->user_type_id == 2)
  //           {
  //           	$query->where('posts.user_id', '=', $seller_id);
  //           }
  //           elseif(auth()->user()->user_type_id == 3)
  //           {
  //           	$query->where('posts.user_id', '=', $buyer_id);
  //           }	 
  //       })
 	// 	->orderByDesc('posts.id')->get();

		// $data['all_post'] = $all_post;
		// return view('account.makeanoffers-edit', $data);
	}

	public function store( Request $request)
	{
		
		$postId = $request->input('post_id');
		$offerId = $request->input('makeanoffer_id');
		// $offerPriceSeller = $request->input('offer_price_seller');
		// $offerPriceBuyer = $request->input('offer_price_buyer');
		// $status = $request->input('status');

		$post = Post::unarchived()->findOrFail($postId);
		
		$user = User::findOrFail($request->input('user_id'));
		// $makeanoffer = Makeanoffer::findOrFail($offerId);

        $makeanoffer = new Makeanoffer();

        $makeanoffer->post_id = $post->id;
        if(!empty($offerId))
        {
        	$makeanoffer->offer_parent = $offerId;
        }
        if (!empty($request->input('buyer_product_1')) && $request->input('buyer_product_1') !== $request->input('buyer_product_2') && $request->input('buyer_product_1') !== $request->input('buyer_product_3')) {
        	$makeanoffer->buyer_product_1 = $request->input('buyer_product_1');
        }
        if (!empty($request->input('buyer_product_2')) && $request->input('buyer_product_2') !== $request->input('buyer_product_1') && $request->input('buyer_product_2') !== $request->input('buyer_product_3')) {
        	$makeanoffer->buyer_product_2 = $request->input('buyer_product_2');
        }
        if (!empty($request->input('buyer_product_3')) && $request->input('buyer_product_3') !== $request->input('buyer_product_1') && $request->input('buyer_product_3') !== $request->input('buyer_product_1')) {
        	$makeanoffer->buyer_product_3 = $request->input('buyer_product_3');
        }

        if (!empty($request->input('seller_product_1')) && $request->input('seller_product_1') !== $request->input('seller_product_2')) {
        	$makeanoffer->seller_product_1 = $request->input('seller_product_1');
        }

        if (!empty($request->input('seller_product_2')) && $request->input('seller_product_2') !== $request->input('seller_product_1')) {
        	$makeanoffer->seller_product_2 = $request->input('seller_product_2');
        }
        
        if(!empty($request->input('offer_price_seller')))
        {
        	$makeanoffer->original_price = $request->input('offer_price_seller');
        }
        else
        {
        	$makeanoffer->original_price = $post->price;	
        }
        
        if(!empty($request->input('offer_price_buyer')))
        {
        	$makeanoffer->offer_price = $request->input('offer_price_buyer');
        }
        else{
        	$makeanoffer->offer_price = $request->input('offer_price_buyer');	
        }
        
        $makeanoffer->description_text = 'Start negotiation with buyer';
        // if (auth()->user()->user_type_id == 2) {
        $makeanoffer->buyer_id = $request->input('user_id');
        // } else {
        //     $makeanoffer->buyer_id = $request->user_id;
        // }
        $makeanoffer->seller_id = $post->user_id;
        if ($user->user_type_id == 1) {
            $makeanoffer->is_read_admin = 1;
        } else {
            $makeanoffer->is_read_admin = 0;
        }
        if ($user->user_type_id == 2) {
            $makeanoffer->is_read_professional = 1;
        } else {
            $makeanoffer->is_read_professional = 0;
        }
        if ($user->user_type_id == 3) {
            $makeanoffer->is_read_individual = 1;
        } else {
            $makeanoffer->is_read_individual = 1;
        }
        $makeanoffer->approve_seller = 0;
        $makeanoffer->approve_buyer = 0;
        $makeanoffer->approve_admin = 0;
        $makeanoffer->status = 1;
        $makeanoffer->offer_maker_id = $request->input('user_id');
        $makeanoffer->save();

        
        
         
        $sellername = $post->contact_name;
        $selleremail = $post->email;
        $buyername = $user->user_name;
        $from_email = $user->email;
        
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        $data['sellername'] = $sellername;
        $data['buyername'] = $buyername;
        
        try {
            \Mail::send('emails.post.offer_send', $data, function($message) use ($buyername, $from_email,$selleremail)
            {
                $message->to($selleremail);
                $message->subject('New Offer');
                $message->replyTo($from_email, $buyername);        
            });    
            $msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $post->contact_name]);
            flash($msg)->success();
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }

        // try {
        //     $post->notify(new SellerContacted($post, $message));

        //     $msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $post->contact_name]);
        //     flash($msg)->success();
        // } catch (\Exception $e) {
        //     flash($e->getMessage())->error();
        // }



        // return redirect(config('app.locale') . '/' . $post->uri);

		// echo "<pre>";
		// dd($request);
		// exit;

		// $makeanoffer = Makeanoffer::find($offerId);
		// $makeanoffer->description_text = $offerDescription;
		// $makeanoffer->offer_price = $offerPrice;
		// $makeanoffer->approve_seller = $status;
		// $makeanoffer->is_read_professional = 1;
		// $makeanoffer->update();

		//return redirect('account/makeanoffers/'.$postId.'/edit/'.$makeanoffer->id);
		return response()->json(['results'=>'Success','postId'=>$postId,'makeanofferid'=>$makeanoffer->id]);
	}
	public function storeeditoffer( Request $request)
	{
	    
		
		// exit;
		$postId = $request->input('post-id');
		$offerId = $request->input('makeanoffer-id');
		// $offerPriceSeller = $request->input('offer_price_seller');
		// $offerPriceBuyer = $request->input('offer_price_buyer');
		// $status = $request->input('status');

		$post            = Post::unarchived()->findOrFail($postId);
		$makeanofferdata = Makeanoffer::findOrFail($offerId);
		// echo "<pre>";
		// print_r($makeanofferdata->id);
		// exit;
		

		
		
		
		
		
		
		
		
		
        $makeanoffer = new Makeanoffer();

        $makeanoffer->post_id = $post->id;
        if(!empty($offerId))
        {
        	$makeanoffer->offer_parent = $offerId;
        }
        else
        {
        	$makeanoffer->offer_parent = $makeanofferdata->id;
        }
        // if (!empty($request->input('buyer_product_1')) && $request->input('buyer_product_1') !== $request->input('buyer_product_2') && $request->input('buyer_product_1') !== $request->input('buyer_product_3')) {
        // 	$makeanoffer->buyer_product_1 = $request->input('buyer_product_1');
        // }
        // else
        // {
        	$makeanoffer->buyer_product_1 = !empty($request->input('buyer_product_1'))?$request->input('buyer_product_1'):0;
        // }
        // if (!empty($request->input('buyer_product_2')) && $request->input('buyer_product_2') !== $request->input('buyer_product_1') && $request->input('buyer_product_2') !== $request->input('buyer_product_3')) {
        // 	$makeanoffer->buyer_product_2 = $request->input('buyer_product_2');
        // }
        // else
        // {
        	$makeanoffer->buyer_product_2 = !empty($request->input('buyer_product_2'))?$request->input('buyer_product_2'):0;
        // }
        // if (!empty($request->input('buyer_product_3')) && $request->input('buyer_product_3') !== $request->input('buyer_product_1') && $request->input('buyer_product_3') !== $request->input('buyer_product_2')) {
        // 	$makeanoffer->buyer_product_3 = $request->input('buyer_product_3');
        // }
        // else
        // {
        	$makeanoffer->buyer_product_3 = !empty($request->input('buyer_product_3'))?$request->input('buyer_product_3'):0;
        // }

        // if (!empty($request->input('seller_product_1')) && $request->input('seller_product_1') !== $request->input('seller_product_2')) {
        // 	$makeanoffer->seller_product_1 = $request->input('seller_product_1');
        // }
        // else
        // {
        	$makeanoffer->seller_product_1 = !empty($request->input('seller_product_1'))?$request->input('seller_product_1'):0;
        // }

        // if (!empty($request->input('seller_product_2')) && $request->input('seller_product_2') !== $request->input('seller_product_1')) {
        // 	$makeanoffer->seller_product_2 = $request->input('seller_product_2');
        // }
        // else
        // {
        	$makeanoffer->seller_product_2 = !empty($request->input('seller_product_2'))?$request->input('seller_product_2'):0;
        // }
        if(!empty($request->input('offer_price_seller')))
        {
        	$makeanoffer->original_price = $request->input('offer_price_seller');
        }
        else
        {
        	$makeanoffer->original_price = $makeanofferdata->original_price;
        }
        // $makeanoffer->original_price = $post->price;
        if(!empty($request->input('offer_price_buyer')))
        {
        	$makeanoffer->offer_price = $request->input('offer_price_buyer');
        }
        else{
        	$makeanoffer->offer_price = $makeanofferdata->offer_price;	
        }
        
        $makeanoffer->description_text = 'Start negotiation with buyer';
        
        $makeanoffer->buyer_id = $makeanofferdata->buyer_id;
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
        $makeanoffer->offer_maker_id = $request->user_id;
        $makeanoffer->save();

        // try {
        //     $post->notify(new SellerContacted($post, $message));

        //     $msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $post->contact_name]);
        //     flash($msg)->success();
        // } catch (\Exception $e) {
        //     flash($e->getMessage())->error();
        // }


		$offer_maker_name = User::where(['id' => $makeanofferdata->offer_maker_id])->first();
		
        $sellername = $offer_maker_name->username;
        $selleremail = $offer_maker_name->email;
        $buyername = auth()->user()->username;
        $from_email = auth()->user()->email;
        
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        $data['sellername'] = $sellername;
        $data['buyername'] = $buyername;
        
        try {
            \Mail::send('emails.post.offer_send', $data, function($message) use ($buyername, $from_email,$selleremail)
            {
                $message->to($selleremail);
                $message->subject('New Offer');
                $message->replyTo($from_email, $buyername);        
            });    
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }




        // return redirect(config('app.locale') . '/' . $post->uri);

		// echo "<pre>";
		// dd($request);
		// exit;

		// $makeanoffer = Makeanoffer::find($offerId);
		// $makeanoffer->description_text = $offerDescription;
		// $makeanoffer->offer_price = $offerPrice;
		// $makeanoffer->approve_seller = $status;
		// $makeanoffer->is_read_professional = 1;
		// $makeanoffer->update();

		return redirect('account/makeanoffers/'.$postId.'/edit/'.$makeanoffer->id);
	}
    public function storeeditoffer_api( Request $request)
	{
	    
		
		// exit;
		$postId = $request->input('post-id');
		
		$offerId = $request->input('makeanoffer-id');
		

		$post            = Post::unarchived()->findOrFail($postId);
		$makeanofferdata = Makeanoffer::findOrFail($offerId);
		$userId = $request->input('user_id');
		   
//echo $request->input('user_id');
		$user = User::findOrFail($userId);
		
// 		var_dump($user);
// 		die();
		
		
		
		
		
		
        $makeanoffer = new Makeanoffer();
        
        $makeanoffer->post_id = $post->id;
        if(!empty($offerId))
        {
        	$makeanoffer->offer_parent = $offerId;
        }
        else
        {
        	$makeanoffer->offer_parent = $makeanofferdata->id;
        }
        // if (!empty($request->input('buyer_product_1')) && $request->input('buyer_product_1') !== $request->input('buyer_product_2') && $request->input('buyer_product_1') !== $request->input('buyer_product_3')) {
        // 	$makeanoffer->buyer_product_1 = $request->input('buyer_product_1');
        // }
        // else
        // {
        	$makeanoffer->buyer_product_1 = !empty($request->input('buyer_product_1'))?$request->input('buyer_product_1'):0;
        // }
        // if (!empty($request->input('buyer_product_2')) && $request->input('buyer_product_2') !== $request->input('buyer_product_1') && $request->input('buyer_product_2') !== $request->input('buyer_product_3')) {
        // 	$makeanoffer->buyer_product_2 = $request->input('buyer_product_2');
        // }
        // else
        // {
        	$makeanoffer->buyer_product_2 = !empty($request->input('buyer_product_2'))?$request->input('buyer_product_2'):0;
        // }
        // if (!empty($request->input('buyer_product_3')) && $request->input('buyer_product_3') !== $request->input('buyer_product_1') && $request->input('buyer_product_3') !== $request->input('buyer_product_2')) {
        // 	$makeanoffer->buyer_product_3 = $request->input('buyer_product_3');
        // }
        // else
        // {
        	$makeanoffer->buyer_product_3 = !empty($request->input('buyer_product_3'))?$request->input('buyer_product_3'):0;
        // }

        // if (!empty($request->input('seller_product_1')) && $request->input('seller_product_1') !== $request->input('seller_product_2')) {
        // 	$makeanoffer->seller_product_1 = $request->input('seller_product_1');
        // }
        // else
        // {
        	$makeanoffer->seller_product_1 = !empty($request->input('seller_product_1'))?$request->input('seller_product_1'):0;
        // }

        // if (!empty($request->input('seller_product_2')) && $request->input('seller_product_2') !== $request->input('seller_product_1')) {
        // 	$makeanoffer->seller_product_2 = $request->input('seller_product_2');
        // }
        // else
        // {
        	$makeanoffer->seller_product_2 = !empty($request->input('seller_product_2'))?$request->input('seller_product_2'):0;
        // }
        if(!empty($request->input('offer_price_seller')))
        {
        	$makeanoffer->original_price = $request->input('offer_price_seller');
        }
        else
        {
        	$makeanoffer->original_price = $makeanofferdata->original_price;
        }
        // $makeanoffer->original_price = $post->price;
        if(!empty($request->input('offer_price_buyer')))
        {
        	$makeanoffer->offer_price = $request->input('offer_price_buyer');
        }
        else{
        	$makeanoffer->offer_price = $makeanofferdata->offer_price;	
        }
        
        $makeanoffer->description_text = 'Start negotiation with buyer';
        
        $makeanoffer->buyer_id = $makeanofferdata->buyer_id;
        $makeanoffer->seller_id = $post->user_id;
        
        if ($user->user_type_id == 1) {
            $makeanoffer->is_read_admin = 1;
        } else {
            $makeanoffer->is_read_admin = 0;
        }
        if ($user->user_type_id == 2) {
            $makeanoffer->is_read_professional = 1;
        } else {
            $makeanoffer->is_read_professional = 0;
        }
        if ($user->user_type_id == 3) {
            $makeanoffer->is_read_individual = 1;
        } else {
            $makeanoffer->is_read_individual = 1;
        }
        $makeanoffer->approve_seller = 0;
        $makeanoffer->approve_buyer = 0;
        $makeanoffer->approve_admin = 0;
        $makeanoffer->status = 1;
        $makeanoffer->offer_maker_id = $request->input('user_id');
        $makeanoffer->save();

        // try {
        //     $post->notify(new SellerContacted($post, $message));

        //     $msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $post->contact_name]);
        //     flash($msg)->success();
        // } catch (\Exception $e) {
        //     flash($e->getMessage())->error();
        // }


		$offer_maker_name = User::where(['id' => $makeanofferdata->offer_maker_id])->first();
		
        $sellername = $offer_maker_name->username;
        $selleremail = $offer_maker_name->email;
        $buyername = $user->username;
        $from_email = $user->email;
        
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        $data['sellername'] = $sellername;
        $data['buyername'] = $buyername;
        
        try {
            \Mail::send('emails.post.offer_send', $data, function($message) use ($buyername, $from_email,$selleremail)
            {
                $message->to($selleremail);
                $message->subject('New Offer');
                $message->replyTo($from_email, $buyername);        
            });    
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }
    $seller_id = $makeanofferdata->seller_id;
    $buyer_id = $makeanofferdata->buyer_id;
    if($userId == $seller_id){
        $fcmIdUser = User::findOrFail($buyer_id);
    }
    else{
       $fcmIdUser = User::findOrFail($seller_id); 
    }
    $fcmId = $fcmIdUser->fcm_id;
        error_reporting(-1);
        ini_set('display_errors', 'On');
        $firebase = new Firebase();
        $push = new Push();
        $payload = array(); 
        $data = array();
        $data['postId'] = $postId;
        $data['offerId'] = $makeanoffer->id;
        array_push($payload, $data);                
        // notification title
        $title = $user->first_name.' sent you counter offer!';             
        // notification message
        $message = "Makeanoffer Data"; 
        $type = "makeanoffer";                      
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


        // return redirect(config('app.locale') . '/' . $post->uri);

		// echo "<pre>";
		// dd($request);
		// exit;

		// $makeanoffer = Makeanoffer::find($offerId);
		// $makeanoffer->description_text = $offerDescription;
		// $makeanoffer->offer_price = $offerPrice;
		// $makeanoffer->approve_seller = $status;
		// $makeanoffer->is_read_professional = 1;
		// $makeanoffer->update();
        return response()->json(['status'=>1,'message'=>'Success','results'=>'Success','firebaseresponse'=>$firebaseresponse]);
		//return redirect('account/makeanoffers/'.$postId.'/edit/'.$makeanoffer->id);
	}
	
	public function dealseller($postId , $offerId, Request $request)
	{
	    
        $makeanofferget = Makeanoffer::find($offerId);
		 //$count = $makeanofferget->count();
		
    	$offer_maker_id = $makeanofferget->offer_maker_id;
    	$sellerId = $makeanofferget->seller_id;
    	$buyerId = $makeanofferget->buyer_id;
    	if($sellerId == $offer_maker_id){
    	    $fcm_maker_name = User::where(['id' => $buyerId])->first();
    	}
    	else{
    	   $fcm_maker_name = User::where(['id' => $sellerId])->first(); 
    	}
    	$offer_maker_name = User::where(['id' => $offer_maker_id])->first();

    	$to_name  = $offer_maker_name->username;
    	$to_email = $offer_maker_name->email;
    	
        $from_name = $request->username;
        $from_email = $request->email;
        
        $data['toname'] = $to_name;
        $data['fromname'] = $from_name;
		$post = Post::unarchived()->findOrFail($postId);
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        
	    \Mail::send('emails.post.offer_accept', $data, function($message) use ($to_email,$from_name,$from_email)
        {
            $message->to($to_email);
            $message->subject('Offer Acepted');
            $message->replyTo($from_email, $from_name);        
        });    
	    
	    
	   	$makeanoffer = Makeanoffer::find($offerId);
		$makeanoffer->approve_seller = 1;
		$makeanoffer->is_read = 0;
		$makeanoffer->update();
			$fcmId = $offer_maker_name->fcm_id;
		 error_reporting(-1);
        ini_set('display_errors', 'On');
        $firebase = new Firebase();
        $push = new Push();
        $payload = array(); 
        $data = array();
        
        
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        $data['postId'] = $post->id;
        $data['offerId'] = $makeanoffer->id;
        array_push($payload, $data);                
        // notification title
        $title = $fcm_maker_name->username.' have accepted your offer!';             
        // notification message
        $message = "notdealseller Data"; 
        $type = "notdealseller";                      
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
		//return redirect('account/makeanoffers/'.$postId.'/edit/'.$offerId);
		return response()->json(['results'=>'Success','makeanofferid'=>$offerId,'postId'=>$postId]);
		
	}
	public function notdealseller($postId , $offerId, Request $request)
	{
	    $makeanofferget = Makeanoffer::find($offerId);
	    //print_r($makeanofferget);
    	$offer_maker_id = $makeanofferget->offer_maker_id;
    	$sellerId = $makeanofferget->seller_id;
    	$buyerId = $makeanofferget->buyer_id;
    	if($sellerId == $offer_maker_id){
    	    $fcm_maker_name = User::where(['id' => $buyerId])->first();
    	}
    	else{
    	   $fcm_maker_name = User::where(['id' => $sellerId])->first(); 
    	}
    	$offer_maker_name = User::where(['id' => $offer_maker_id])->first();

    	$to_name  = $offer_maker_name->username;
    	$to_email = $offer_maker_name->email;
    	
        $from_name = $request->username;
        $from_email = $request->email;
        
        $post = Post::unarchived()->findOrFail($postId);
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        
        $data['toname'] = $to_name;
        $data['fromname'] = $from_name;
        
        
        
	    \Mail::send('emails.post.offer_reject', $data, function($message) use ($to_email,$from_name,$from_email)
        {
            $message->to($to_email);
            $message->subject('Offer Rejected');
            $message->replyTo($from_email, $from_name);        
        });    
	    
	    
	    
		$makeanoffer = Makeanoffer::find($offerId);
		$makeanoffer->approve_seller = 2;
		$makeanoffer->is_read = 0;
		$makeanoffer->update();
		$fcmId = $offer_maker_name->fcm_id;
		 error_reporting(-1);
        ini_set('display_errors', 'On');
        $firebase = new Firebase();
        $push = new Push();
        $payload = array(); 
        $data = array();
        
        
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        $data['postId'] = $post->id;
        $data['offerId'] = $makeanoffer->id;
        array_push($payload, $data);                
        // notification title
        $title = $fcm_maker_name->first_name.' has rejected your offer!';             
        // notification message
        $message = "notdealseller Data"; 
        $type = "notdealseller";                      
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
		//return redirect('account/makeanoffers/'.$postId.'/edit/'.$offerId);
		return response()->json(['status'=>1,'message'=>'Success','results'=>$firebaseresponse,'makeanofferid'=>$offerId,'postId'=>$postId]);
	}

	public function dealbuyer($id)
	{
		$makeanoffer = Makeanoffer::find($id);
		$makeanoffer->approve_seller = 1;
		$makeanoffer->is_read = 0;
		$makeanoffer->update();
		//return redirect('account/makeanoffers/'.$id.'/edit');
		return response()->json(['results'=>'Success','makeanofferid'=>$id]);
	}
	public function notdealbuyer($id)
	{
		$makeanoffer = Makeanoffer::find($id);
		$makeanoffer->approve_seller = 0;
		$makeanoffer->is_read = 0;
		$makeanoffer->update();
		//return redirect('account/makeanoffers/'.$id.'/edit');
		return response()->json(['results'=>'Success','makeanofferid'=>$id]);
	}

	public function addmore($id, MakeAnOfferEditRequest $request )
	{
		$makeanoffer = Makeanoffer::find($id);
		$makeanoffer->next_post_id = $request->input('post');
		$makeanoffer->approve_seller = 0;
		$makeanoffer->update();
		return redirect('account/makeanoffers/'.$id.'/edit');
	}

	public function updatemakeanoffer ($id, MakeAnOfferRequest $request)
	{
		$makeanoffer = Makeanoffer::find($id);
		$makeanoffer->offer_price = $request->input('offer_price');
		$makeanoffer->approve_seller = 0;
		$makeanoffer->update();
		return redirect('account/makeanoffers/'.$id.'/edit');
	}

	public function destroy(Request $request)
	{
		$id = $request->input('id');
		$makeanoffer = Makeanoffer::destroy($id);
		//return redirect('account/makeanoffers/');
		return response()->json(['results'=>'Deleted']);
	}
	
	
	public function closeofferapi(Request $request)
	{
	    $id = $request->input('id');
	    $makeanoffer = Makeanoffer::find($id);
	    $makeanoffer->close_offer = 1;
	    $makeanoffer->is_read = 0;
	    $makeanoffer->save();
	    return response()->json(['status'=>1,'message'=>'success','results'=>'Offer successfully closed!']);
	    //return redirect()->back()->with('success','Offer successfully closed!');
	}
	public function closeoffer($id)
	{
	     var_dump($id);
	    die();
	   // $makeanoffer = Makeanoffer::find($id);
	   // $makeanoffer->close_offer = 1;
	   // $makeanoffer->is_read = 0;
	   // $makeanoffer->save();
	   // return redirect()->back()->with('success','Offer successfully closed!');
	}
	
	public function counteroffer($id)
	{
	   // var_dump($id);
	   // die();
        $makeanoffer = Makeanoffer::find($id);
	    $makeanoffer->buyer_product_1 = 0;
        $makeanoffer->buyer_product_2 = 0;
        $makeanoffer->buyer_product_3 = 0;
        $makeanoffer->seller_product_1 = 0;
        $makeanoffer->seller_product_2 = 0;
        $makeanoffer->counter_offer = 1;
        $makeanoffer->is_read = 0;
	    $makeanoffer->save();
	    return redirect()->back()->with('success','Counter offer successfully set!');
	    
	}
	public function counterofferapi(Request $request)
	{
	    $id = $request->input('id');
	 // var_dump($id);
	    //die();
        $makeanoffer = Makeanoffer::find($id);
        if(!empty($makeanoffer)){
    	    $makeanoffer->buyer_product_1 = 0;
            $makeanoffer->buyer_product_2 = 0;
            $makeanoffer->buyer_product_3 = 0;
            $makeanoffer->seller_product_1 = 0;
            $makeanoffer->seller_product_2 = 0;
            $makeanoffer->counter_offer = 1;
            $makeanoffer->is_read = 0;
    	    $makeanoffer->save();
    	    return response()->json(['status'=>1,'message'=>'success','results'=>'Counter offer successfully set!']);
    	    //return redirect()->back()->with('success','Counter offer successfully set!');
        }
        else{
            return response()->json(['status'=>0,'message'=>'failed','results'=>'Counter offer not set!']);
        }
	}
	
	//App api implementation
	
    public function makeanoffersapp(Request $request) {
        
       
        $sellerId = $request->input('receiver_id');
        $postId = $request->input('post_id');

        $post = Post::unarchived()->findOrFail($postId);
		$seller = User::findOrFail($request->input('receiver_id'));
		$user = User::findOrFail($request->input('user_id'));
    
        $makeanoffer = new Makeanoffer();

        $makeanoffer->post_id = $post->id;
        
        if (!empty($request->input('buyer_product_1')) && $request->input('buyer_product_1') !== $request->input('buyer_product_2') && $request->input('buyer_product_1') !== $request->input('buyer_product_3')) {
        	$makeanoffer->buyer_product_1 = $request->input('buyer_product_1');
        }
        if (!empty($request->input('buyer_product_2')) && $request->input('buyer_product_2') !== $request->input('buyer_product_1') && $request->input('buyer_product_2') !== $request->input('buyer_product_3')) {
        	$makeanoffer->buyer_product_2 = $request->input('buyer_product_2');
        }
        if (!empty($request->input('buyer_product_3')) && $request->input('buyer_product_3') !== $request->input('buyer_product_1') && $request->input('buyer_product_3') !== $request->input('buyer_product_1')) {
        	$makeanoffer->buyer_product_3 = $request->input('buyer_product_3');
        }

        if (!empty($request->input('seller_product_1')) && $request->input('seller_product_1') !== $request->input('seller_product_2')) {
        	$makeanoffer->seller_product_1 = $request->input('seller_product_1');
        }

        if (!empty($request->input('seller_product_2')) && $request->input('seller_product_2') !== $request->input('seller_product_1')) {
        	$makeanoffer->seller_product_2 = $request->input('seller_product_2');
        }
        
        if(!empty($request->input('offer_price_seller')))
        {
        	$makeanoffer->original_price = $request->input('offer_price_seller');
        }
        else
        {
        	$makeanoffer->original_price = $post->price;	
        }
        
        if(!empty($request->input('offer_price_buyer')))
        {
        	$makeanoffer->offer_price = $request->input('offer_price_buyer');
        }
        else{
        	$makeanoffer->offer_price = 0;	
        }
        
        $makeanoffer->description_text = $request->input('description_text');
        
        $makeanoffer->buyer_id = $request->input('user_id');
        
        $makeanoffer->seller_id = $sellerId;
        if ($user->user_type_id == 1) {
            $makeanoffer->is_read_admin = 1;
        } else {
            $makeanoffer->is_read_admin = 0;
        }
        if ($user->user_type_id == 2) {
            $makeanoffer->is_read_professional = 1;
        } else {
            $makeanoffer->is_read_professional = 0;
        }
        if ($user->user_type_id == 3) {
            $makeanoffer->is_read_individual = 1;
        } else {
            $makeanoffer->is_read_individual = 1;
        }
        
        $makeanoffer->approve_seller = 0;
        $makeanoffer->approve_buyer = 0;
        $makeanoffer->approve_admin = 0;
        $makeanoffer->status = 1;
        $makeanoffer->offer_maker_id = $request->input('user_id');
        
        $makeanoffer->save();
     
        $fcmId = $seller->fcm_id;
        error_reporting(-1);
        ini_set('display_errors', 'On');
        $firebase = new Firebase();
        $push = new Push();
        $payload = array(); 
        $data = array();
        
        
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        $data['postId'] = $post->id;
        $data['offerId'] = $makeanoffer->id;
        array_push($payload, $data);                
        // notification title
        $title = $user->first_name.' has sent you an offer!';             
        // notification message
        $message = "Makeanoffer Data"; 
        $type = "makeanoffer";                      
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
		
		return response()->json(['results'=>'Success','postId'=>$postId,'makeanofferid'=>$makeanoffer->id, 'firebaseresponse'=>$firebaseresponse]);
    }
    
    
	
	
	
	/**
	 * rechargePoints List
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	
	public function rechargePoints(Request $request)
	{
		
		$translation_lang=$request->translation_lang;

	/*	$user = User::find($request->user_id);
		$user->no_points=1000;
		$user->save();*/


		 
		 $points = \DB::table('points')->where('active','1')
		 //->where('translation_lang',$translation_lang)
		 ->get(); 
		 $w=0;
		 $filterPionts=array();
		 foreach($points as $point){
			 	$filterPionts[$w]['id']=$point->id;		 
				$filterPionts[$w]['price']=$point->price;		 
				$filterPionts[$w]['currency_code']=$point->currency_code;		 
				$filterPionts[$w]['no_photos']=$point->no_photos;		
				$filterPionts[$w]['duration']=$point->duration;	 
				$filterPionts[$w]['no_points']=$point->no_points;	
				$filterPionts[$w]['active']=$point->active;
			    $w++;
			 }
		 $PaymentMethods = PaymentMethod::where('active','1')->get();	
		  $w=0;
		 $filterPaymentMethods=array();
		 foreach($PaymentMethods as $Payment){
			 	$filterPaymentMethods[$w]['id']=$Payment->id;		 
				$filterPaymentMethods[$w]['name']=$Payment->name;		 
				$filterPaymentMethods[$w]['description']=$Payment->description;	
				$filterPaymentMethods[$w]['image']=url('ProfilePictures/')."/".$Payment->m_image;				 
			    $w++;
			 }	 
		return response()->json(['points'=>$filterPionts,'PaymentMethods'=>$filterPaymentMethods]);
		
	 
	}
	
    public function dealsellerapp(Request $request)
	{
		 
		
		$MyPostId=$request->MyPostId;
		$offerId=$request->offerId;
		
		$makeanofferget = Makeanoffer::find($offerId);
    	$offer_maker_id = $makeanofferget->offer_maker_id;
		$offer_seller_id = $makeanofferget->seller_id;
    	
    	$offer_maker_name = User::where(['id' => $offer_maker_id])->first();
		$offer_sender_name = User::where(['id' => $offer_seller_id])->first();

    	$to_name  = $offer_maker_name->username;
		$from_name = $offer_sender_name->username;
		
		
		
        
        $post = Post::unarchived()->findOrFail($MyPostId);
        
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
        $title = 'Offer accepted';             
        // notification message
        $message = "Offer accepted Data"; 
        $type = "Offeraccepted";                      
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
		$makeanoffer->approve_seller = 1;
		$makeanoffer->is_read = 0;
		$makeanoffer->update();
		//return redirect('account/makeanoffers/'.$postId.'/edit/'.$offerId);
		return response()->json(['results'=>'Success','makeanofferid'=>$offerId,'MyPostId'=>$MyPostId, 'firebaseresponse' => $firebaseresponse]);
		
	}
	
	

}