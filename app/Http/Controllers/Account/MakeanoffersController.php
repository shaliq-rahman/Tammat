<?php
/**
 * LaraClassified - Geo Classified Ads Software
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


use Torann\LaravelMetaTags\Facades\MetaTag;
use App\Models\Makeanoffer;
use App\Models\Post;
use App\Models\Picture;
use App\Http\Requests\MakeAnOfferEditRequest;
use App\Http\Requests\MakeAnOfferRequest;
// use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use DB;
use App\Models\User;
use App\Notifications\SellerContacted;
use App\PushNotification\firebase;
use App\PushNotification\push;

class MakeanoffersController extends AccountBaseController
{
	private $perPage = 10;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->perPage = (is_numeric(config('settings.listing.items_per_page'))) ? config('settings.listing.items_per_page') : $this->perPage;
	}
	
	/**
	 * List Transactions
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index() 
	{
		$data = [];
		$data['makeanoffers'] = $this->makeanoffers->paginate($this->perPage);
		
		view()->share('pagePath', 'makeanoffers');
		
		// Meta Tags
		MetaTag::set('title', t('My Offer Maker'));
		MetaTag::set('description', t('My Offers Maker on :app_name', ['app_name' => config('settings.app.name')]));
		return view('account.makeanoffers', $data);
	}

	public function makeanoffer($id)
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
			
			$data['buyer'] = User::where(['id' => auth()->user()->id])->first();
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
			->join('countries', 'countries.code', '=', 'posts.country_code')
			->join('currencies', 'currencies.code', '=', 'countries.currency_code')
			->where('pictures.position' , 1)
	        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();

			$data['buyerPosts'] = DB::table('posts')->where(function ($query) use ($buyer_id)
	        {
	            $query->where('posts.user_id', '=', $buyer_id);
	            $query->where('posts.reviewed', '=', 1);
	            $query->where('posts.archived', '=', 0);

	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
			->join('countries', 'countries.code', '=', 'posts.country_code')
			->join('currencies', 'currencies.code', '=', 'countries.currency_code')
	        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();
			
			return view('account.sendanoffer', $data);
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
				->join('countries', 'countries.code', '=', 'posts.country_code')
			    ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($buyer_product_2))
			{
				$data['buyerProduct2'] = DB::table('posts')->where(function ($query) use ($buyer_product_2)
		        {
		            $query->where('posts.id', '=', $buyer_product_2);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
				->join('countries', 'countries.code', '=', 'posts.country_code')
			    ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($buyer_product_3))
			{
				$data['buyerProduct3'] = DB::table('posts')->where(function ($query) use ($buyer_product_3)
		        {
		            $query->where('posts.id', '=', $buyer_product_3);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
				->join('countries', 'countries.code', '=', 'posts.country_code')
			    ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($seller_product_1))
			{
				$data['sellerProduct1'] = DB::table('posts')->where(function ($query) use ($seller_product_1)
		        {
		            $query->where('posts.id', '=', $seller_product_1);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
				->join('countries', 'countries.code', '=', 'posts.country_code')
			    ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($seller_product_2))
			{
				$data['sellerProduct2'] = DB::table('posts')->where(function ($query) use ($seller_product_2)
		        {
		            $query->where('posts.id', '=', $seller_product_2);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
				->join('countries', 'countries.code', '=', 'posts.country_code')
			    ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			$data['sellerPosts'] = DB::table('posts')->where(function ($query) use ($seller_id)
	        {
	            $query->where('posts.user_id', '=', $seller_id);

	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
			->join('countries', 'countries.code', '=', 'posts.country_code')
		    ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
			->where('pictures.position' , 1)
	        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();
	  		
			$data['buyerPosts'] = DB::table('posts')->where(function ($query) use ($buyer_id)
	        {
	            $query->where('posts.user_id', '=', $buyer_id);

	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
			->join('countries', 'countries.code', '=', 'posts.country_code')
			->join('currencies', 'currencies.code', '=', 'countries.currency_code')
	        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();
			
			return view('account.makeanoffers-edit', $data);
		}
	}

	public function edit($postId , $offerId)
	{
	    
        $getMakeofferCount = \DB::table('makeanoffers')
            ->where('id','=',$offerId)
            ->first();
            
            // if($getMakeofferCount->is_read == '0')
            // {
            //   if($getMakeofferCount->offer_maker_id != auth()->user()->id)
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
               if(auth()->user()->id != $getMakeofferCount->offer_maker_id || $getMakeofferCount->approve_seller == 1 || $getMakeofferCount->approve_seller == 2)
               {
                   if($getMakeofferCount->approve_seller == 1)
                   {
                        if($getMakeofferCount->counter_offer == '0')      
                        {
                            if(auth()->user()->id == $getMakeofferCount->buyer_id)
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
                            if(auth()->user()->id == $getMakeofferCount->offer_maker_id)
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
                            if(auth()->user()->id != $getMakeofferCount->offer_maker_id)
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
				->join('countries', 'countries.code', '=', 'posts.country_code')
				->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($buyer_product_2))
			{
				$data['buyerProduct2'] = DB::table('posts')->where(function ($query) use ($buyer_product_2)
		        {
		            $query->where('posts.id', '=', $buyer_product_2);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
				->join('countries', 'countries.code', '=', 'posts.country_code')
				->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($buyer_product_3))
			{
				$data['buyerProduct3'] = DB::table('posts')->where(function ($query) use ($buyer_product_3)
		        {
		            $query->where('posts.id', '=', $buyer_product_3);
 
		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
				->join('countries', 'countries.code', '=', 'posts.country_code')
				->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($seller_product_1))
			{
			   	$data['sellerProduct1'] = DB::table('posts')->where(function ($query) use ($seller_product_1)
		        {
		            $query->where('posts.id', '=', $seller_product_1);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
				->join('countries', 'countries.code', '=', 'posts.country_code')
				->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			if(!empty($seller_product_2))
			{
				$data['sellerProduct2'] = DB::table('posts')->where(function ($query) use ($seller_product_2)
		        {
		            $query->where('posts.id', '=', $seller_product_2);

		        })
				->join('pictures', 'posts.id', '=', 'pictures.post_id')
				->join('countries', 'countries.code', '=', 'posts.country_code')
				->join('currencies', 'currencies.code', '=', 'countries.currency_code')
		        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->first();
			}

			$data['sellerPosts'] = DB::table('posts')->where(function ($query) use ($seller_id)
	        {
	            $query->where('posts.user_id', '=', $seller_id);
                $query->where('posts.reviewed', '=', 1);
	            $query->where('posts.archived', '=', 0);

	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
			->join('countries', 'countries.code', '=', 'posts.country_code')
			->join('currencies', 'currencies.code', '=', 'countries.currency_code')
			->where('pictures.position' , 1)
	        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();
	  		
			$data['buyerPosts'] = DB::table('posts')->where(function ($query) use ($buyer_id)
	        {
	            $query->where('posts.user_id', '=', $buyer_id);
	            $query->where('posts.reviewed', '=', 1);
	            $query->where('posts.archived', '=', 0);

	        })
			->join('pictures', 'posts.id', '=', 'pictures.post_id')
			->join('countries', 'countries.code', '=', 'posts.country_code')
			->join('currencies', 'currencies.code', '=', 'countries.currency_code')
	        ->select('currencies.html_entity','currencies.in_left','currencies.decimal_places','currencies.decimal_separator','posts.id','posts.title','posts.price','pictures.filename','pictures.position','pictures.active')->get();
			
			return view('account.makeanoffers-edit', $data);
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
	    //dd($request);
		$postId = $request->input('post-id');
		$offerId = $request->input('makeanoffer-id');
		// $offerPriceSeller = $request->input('offer_price_seller');
		// $offerPriceBuyer = $request->input('offer_price_buyer');
		// $status = $request->input('status');

		$post = Post::unarchived()->findOrFail($postId);
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
        	$makeanoffer->original_price = !empty($request->input('offer_price_seller'))?$request->input('offer_price_seller'):0;	
        // 	$makeanoffer->original_price = $post->price;	
        	
        }
        
        if(!empty($request->input('offer_price_buyer')))
        {
        	$makeanoffer->offer_price = $request->input('offer_price_buyer');
        }
        else
        {
        	$makeanoffer->offer_price = $request->input('offer_price_buyer');	
        }
        
        $makeanoffer->description_text = 'Start negotiation with buyer';
        // if (auth()->user()->user_type_id == 2) {
        $makeanoffer->buyer_id = auth()->user()->id;
        // } else {
        //     $makeanoffer->buyer_id = auth()->user()->id;
        // }
        $sellerId = $post->user_id;
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
        $makeanoffer->offer_maker_id = auth()->user()->id;
        $makeanoffer->save();

        
        
         
        $sellername = $post->contact_name;
        $selleremail = $post->email;
        $buyername = auth()->user()->username;
        $from_email = auth()->user()->email;
        
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        $data['sellername'] = $sellername;
        $data['buyername'] = $buyername;
        
        try {
            \Mail::send('emails.post.offer_send', $data, function($message) use ($buyername, $from_email,$selleremail)
            {
                $message->to($selleremail);
                $message->subject(trans('mail.new_offer'));
                $message->replyTo($from_email, $buyername);        
            });    
            $msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $post->contact_name]);
            flash($msg)->success();
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }
        $seller = User::findOrFail($sellerId);
        //print_r($seller);
        //start makeanoffer notification
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
        $title = $buyername.' has sent you an offer!';             
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
        //end makeanoffer notification
        

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

		return redirect(config('app.locale').'/account/makeanoffers/'.$postId.'/edit/'.$makeanoffer->id);
		
		
		
		
	}
	public function storeeditoffer( Request $request)
	{
	    
	   // echo "<pre>";
	   // print_r($_POST);
	   // die;
		
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
        // if(!empty($request->input('offer_price_seller')))
        // {
        
        	$makeanoffer->original_price = !empty($request->input('offer_price_seller'))?$request->input('offer_price_seller'):0;
        	
        // }
        // else
        // {
        // 	$makeanoffer->original_price = $makeanofferdata->original_price;
        // }
        // $makeanoffer->original_price = $post->price;
        // if(!empty($request->input('offer_price_buyer')))
        // {
        
        	$makeanoffer->offer_price = !empty($request->input('offer_price_buyer'))?$request->input('offer_price_buyer'):0;
        	
        // }
        // else{
        // 	$makeanoffer->offer_price = $makeanofferdata->offer_price;	
        // }
        
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
        $makeanoffer->offer_maker_id = auth()->user()->id;
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
                $message->subject(trans('mail.new_offer'));
                $message->replyTo($from_email, $buyername);        
            });    
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }
        $buyer_id = $makeanofferdata->buyer_id;
        $seller_id = $post->user_id;
        $userId = auth()->user()->id;
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
        $title = $buyername.' sent you counter offer!';             
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

		return redirect(config('app.locale').'/account/makeanoffers/'.$postId.'/edit/'.$makeanoffer->id);
	}
// public function storeeditoffer_api( Request $request)
// 	{
// 	   // echo "<pre>";
// 	   // print_r($_POST);
// 	   // die;
		
// 		// exit;
// 		$postId = $request->input('post-id');
// // 		var_dump($postId);
// // 		die();
// 		$offerId = $request->input('makeanoffer-id');
// 		// $offerPriceSeller = $request->input('offer_price_seller');
// 		// $offerPriceBuyer = $request->input('offer_price_buyer');
// 		// $status = $request->input('status');

// 		$post            = Post::unarchived()->findOrFail($postId);
// 		$makeanofferdata = Makeanoffer::findOrFail($offerId);
// 		// echo "<pre>";
// 		// print_r($makeanofferdata->id);
// 		// exit;
		

		
		
		
		
		
		
		
		
		
//         $makeanoffer = new Makeanoffer();

//         $makeanoffer->post_id = $post->id;
//         if(!empty($offerId))
//         {
//         	$makeanoffer->offer_parent = $offerId;
//         }
//         else
//         {
//         	$makeanoffer->offer_parent = $makeanofferdata->id;
//         }
//         // if (!empty($request->input('buyer_product_1')) && $request->input('buyer_product_1') !== $request->input('buyer_product_2') && $request->input('buyer_product_1') !== $request->input('buyer_product_3')) {
//         // 	$makeanoffer->buyer_product_1 = $request->input('buyer_product_1');
//         // }
//         // else
//         // {
//         	$makeanoffer->buyer_product_1 = !empty($request->input('buyer_product_1'))?$request->input('buyer_product_1'):0;
//         // }
//         // if (!empty($request->input('buyer_product_2')) && $request->input('buyer_product_2') !== $request->input('buyer_product_1') && $request->input('buyer_product_2') !== $request->input('buyer_product_3')) {
//         // 	$makeanoffer->buyer_product_2 = $request->input('buyer_product_2');
//         // }
//         // else
//         // {
//         	$makeanoffer->buyer_product_2 = !empty($request->input('buyer_product_2'))?$request->input('buyer_product_2'):0;
//         // }
//         // if (!empty($request->input('buyer_product_3')) && $request->input('buyer_product_3') !== $request->input('buyer_product_1') && $request->input('buyer_product_3') !== $request->input('buyer_product_2')) {
//         // 	$makeanoffer->buyer_product_3 = $request->input('buyer_product_3');
//         // }
//         // else
//         // {
//         	$makeanoffer->buyer_product_3 = !empty($request->input('buyer_product_3'))?$request->input('buyer_product_3'):0;
//         // }

//         // if (!empty($request->input('seller_product_1')) && $request->input('seller_product_1') !== $request->input('seller_product_2')) {
//         // 	$makeanoffer->seller_product_1 = $request->input('seller_product_1');
//         // }
//         // else
//         // {
//         	$makeanoffer->seller_product_1 = !empty($request->input('seller_product_1'))?$request->input('seller_product_1'):0;
//         // }

//         // if (!empty($request->input('seller_product_2')) && $request->input('seller_product_2') !== $request->input('seller_product_1')) {
//         // 	$makeanoffer->seller_product_2 = $request->input('seller_product_2');
//         // }
//         // else
//         // {
//         	$makeanoffer->seller_product_2 = !empty($request->input('seller_product_2'))?$request->input('seller_product_2'):0;
//         // }
//         // if(!empty($request->input('offer_price_seller')))
//         // {
        
//         	$makeanoffer->original_price = !empty($request->input('offer_price_seller'))?$request->input('offer_price_seller'):0;
        	
//         // }
//         // else
//         // {
//         // 	$makeanoffer->original_price = $makeanofferdata->original_price;
//         // }
//         // $makeanoffer->original_price = $post->price;
//         // if(!empty($request->input('offer_price_buyer')))
//         // {
        
//         	$makeanoffer->offer_price = !empty($request->input('offer_price_buyer'))?$request->input('offer_price_buyer'):0;
        	
//         // }
//         // else{
//         // 	$makeanoffer->offer_price = $makeanofferdata->offer_price;	
//         // }
        
//         $makeanoffer->description_text = 'Start negotiation with buyer';
        
//         $makeanoffer->buyer_id = $makeanofferdata->buyer_id;
//         $makeanoffer->seller_id = $post->user_id;
        
//         if (auth()->user()->user_type_id == 1) {
//             $makeanoffer->is_read_admin = 1;
//         } else {
//             $makeanoffer->is_read_admin = 0;
//         }
//         if (auth()->user()->user_type_id == 2) {
//             $makeanoffer->is_read_professional = 1;
//         } else {
//             $makeanoffer->is_read_professional = 0;
//         }
//         if (auth()->user()->user_type_id == 3) {
//             $makeanoffer->is_read_individual = 1;
//         } else {
//             $makeanoffer->is_read_individual = 1;
//         }
//         $makeanoffer->approve_seller = 0;
//         $makeanoffer->approve_buyer = 0;
//         $makeanoffer->approve_admin = 0;
//         $makeanoffer->status = 1;
//         $makeanoffer->offer_maker_id = auth()->user()->id;
//         $makeanoffer->save();

//         // try {
//         //     $post->notify(new SellerContacted($post, $message));

//         //     $msg = t("Your message has sent successfully to :contact_name.", ['contact_name' => $post->contact_name]);
//         //     flash($msg)->success();
//         // } catch (\Exception $e) {
//         //     flash($e->getMessage())->error();
//         // }


// 		$offer_maker_name = User::where(['id' => $makeanofferdata->offer_maker_id])->first();
		
//         $sellername = $offer_maker_name->username;
//         $selleremail = $offer_maker_name->email;
//         $buyername = auth()->user()->username;
//         $from_email = auth()->user()->email;
        
//         $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
//         $data['sellername'] = $sellername;
//         $data['buyername'] = $buyername;
        
//         try {
//             \Mail::send('emails.post.offer_send', $data, function($message) use ($buyername, $from_email,$selleremail)
//             {
//                 $message->to($selleremail);
//                 $message->subject(trans('mail.new_offer'));
//                 $message->replyTo($from_email, $buyername);        
//             });    
//         } catch (\Exception $e) {
//             flash($e->getMessage())->error();
//         }




//         // return redirect(config('app.locale') . '/' . $post->uri);

// 		// echo "<pre>";
// 		// dd($request);
// 		// exit;

// 		// $makeanoffer = Makeanoffer::find($offerId);
// 		// $makeanoffer->description_text = $offerDescription;
// 		// $makeanoffer->offer_price = $offerPrice;
// 		// $makeanoffer->approve_seller = $status;
// 		// $makeanoffer->is_read_professional = 1;
// 		// $makeanoffer->update();

// 		return redirect(config('app.locale').'/account/makeanoffers/'.$postId.'/edit/'.$makeanoffer->id);
// 	}
	public function dealseller($postId , $offerId)
	{
        $makeanofferget = Makeanoffer::find($offerId);
    	$offer_maker_id = $makeanofferget->offer_maker_id;
    	
    	$offer_maker_name = User::where(['id' => $offer_maker_id])->first();

    	$to_name  = $offer_maker_name->username;
    	$to_email = $offer_maker_name->email;
    	
        $from_name = auth()->user()->username;
        $from_email = auth()->user()->email;
        
        $data['toname'] = $to_name;
        $data['fromname'] = $from_name;
		$post = Post::unarchived()->findOrFail($postId);
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        
	    \Mail::send('emails.post.offer_accept', $data, function($message) use ($to_email,$from_name,$from_email)
        {
            $message->to($to_email);
            $message->subject('Offer Accepted');
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
        $data['offerId'] = $offerId;
        array_push($payload, $data);                
        // notification title
        $title = $from_name.' have accepted your offer!';             
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
		return redirect(config('app.locale').'/account/makeanoffers/'.$postId.'/edit/'.$offerId);
	}
	public function notdealseller($postId , $offerId)
	{
	    $makeanofferget = Makeanoffer::find($offerId);
    	$offer_maker_id = $makeanofferget->offer_maker_id;
    	
    	$offer_maker_name = User::where(['id' => $offer_maker_id])->first();

    	$to_name  = $offer_maker_name->username;
    	$to_email = $offer_maker_name->email;
    	
        $from_name = auth()->user()->username;
        $from_email = auth()->user()->email;
        
        $post = Post::unarchived()->findOrFail($postId);
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        
        $data['toname'] = $to_name;
        $data['fromname'] = $from_name;
        
        
        
	    \Mail::send('emails.post.offer_reject', $data, function($message) use ($to_email,$from_name,$from_email)
        {
            $message->to($to_email);
            $message->subject(trans('mail.offer_rejected'));
            $message->replyTo($from_email, $from_name);        
        });    
	    
	    $fcmId = $offer_maker_name->fcm_id;
		 error_reporting(-1);
        ini_set('display_errors', 'On');
        $firebase = new Firebase();
        $push = new Push();
        $payload = array(); 
        $data = array();
        $data['url'] = lurl('/').'/'.slugify($post->title).'/'.$post->id;
        $data['postId'] = $post->id;
        $data['offerId'] = $offerId;
        array_push($payload, $data);                
        // notification title
        $title = $from_name.' has rejected your offer!';             
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
	    
		$makeanoffer = Makeanoffer::find($offerId);
		$makeanoffer->approve_seller = 2;
		$makeanoffer->is_read = 0;
		$makeanoffer->update();
		return redirect(config('app.locale').'/account/makeanoffers/'.$postId.'/edit/'.$offerId);
	}

	public function dealbuyer($id)
	{
		$makeanoffer = Makeanoffer::find($id);
		$makeanoffer->approve_seller = 1;
		$makeanoffer->is_read = 0;
		$makeanoffer->update();
		return redirect(config('app.locale').'/account/makeanoffers/'.$id.'/edit');
	}
	public function notdealbuyer($id)
	{
		$makeanoffer = Makeanoffer::find($id);
		$makeanoffer->approve_seller = 0;
		$makeanoffer->is_read = 0;
		$makeanoffer->update();
		return redirect(config('app.locale').'/account/makeanoffers/'.$id.'/edit');
	}

	public function addmore($id, MakeAnOfferEditRequest $request )
	{
		$makeanoffer = Makeanoffer::find($id);
		$makeanoffer->next_post_id = $request->input('post');
		$makeanoffer->approve_seller = 0;
		$makeanoffer->update();
		return redirect(config('app.locale').'/account/makeanoffers/'.$id.'/edit');
	}

	public function updatemakeanoffer ($id, MakeAnOfferRequest $request)
	{
		$makeanoffer = Makeanoffer::find($id);
		$makeanoffer->offer_price = $request->input('offer_price');
		$makeanoffer->approve_seller = 0;
		$makeanoffer->update();
		return redirect(config('app.locale').'/account/makeanoffers/'.$id.'/edit');
	}

	public function destroy($id)
	{
		$makeanoffer = Makeanoffer::destroy($id);
		return redirect(config('app.locale').'/account/makeanoffers/');
	}
	
	
	public function closeoffer($id)
	{
	    $makeanoffer = Makeanoffer::find($id);
	    $makeanoffer->close_offer = 1;
	    $makeanoffer->is_read = 0;
	    $makeanoffer->save();
	    return redirect()->back()->with('success','Offer successfully closed!');
	}
	
	public function counteroffer($id)
	{
	    
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
        $makeanoffer = Makeanoffer::find($id);
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

}
