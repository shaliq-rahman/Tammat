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



use App\Http\Requests\PackageRequest;

use App\Models\Scopes\StrictActiveScope;

use Illuminate\Support\Facades\Session;

use App\Helpers\Payment as PaymentHelper;

use App\Http\Controllers\Post\Traits\PaymentTrait;

use App\Models\Currency;



use App\Helpers\Ip;

use App\Http\Controllers\Auth\Traits\VerificationTrait;

use App\Http\Controllers\Post\Traits\CustomFieldTrait;

use App\Http\Requests\PostRequest;

use App\Http\Requests\UpdatePostRequest;

use App\Models\Post;

use App\Models\PostType;

use App\Models\Category;

use App\Models\Package;

use App\Models\Payment;

use App\Models\City;

use App\Models\Scopes\VerifiedScope;

use App\Models\User;

use App\Http\Controllers\FrontController;

use App\Models\Scopes\ReviewedScope;

use App\Mail\PostNotification;

use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Mail;

use Torann\LaravelMetaTags\Facades\MetaTag;

use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;

use App\Helpers\Localization\Country as CountryLocalization;

use App\Http\Controllers\Post\Traits\EditTrait;

use Illuminate\Http\Request;

use DB;



class CreateController extends FrontController

{

    use PaymentTrait;

	use EditTrait, VerificationTrait, CustomFieldTrait;

	

	public $data;

    public $request;

    public $msg = [];

    public $uri = [];

    public $packages;

    public $paymentMethods;

	

	/**

	 * CreateController constructor.

	 */

	public function __construct()

	{

		parent::__construct();

		

		// Check if guests can post Ads

		if (config('settings.single.guests_can_post_ads') != '1') {

			$this->middleware('auth')->only(['getForm', 'postForm']);

		}

		

		// From Laravel 5.3.4 or above

		$this->middleware(function ($request, $next) {

			$this->commonQueries();

			

			return $next($request);

		});

	}

	

	

	

	

	

	

	public function getCategoryFieldsApp(Request $request)

	{

	    

		$catNestedIds = $request->catNestedIds;

		$postId = $request->postId;

		$languageCode = $request->languageCode;

		

	    $fields = $this->getCategoryFieldsBufferApp($catNestedIds, $postId, $languageCode);

	    //return $fields;

	    return response()->json(['fields'=>$fields]);

	}

	

	

	

	

	/**

	 * Common Queries

	 */

	public function commonQueries()

	{



 		// Messages

        if (getSegment(2) == 'create') {

            $this->msg['post']['success'] = t("Your ad has been created.");

        } else {

            $this->msg['post']['success'] = t("Your ad has been updated.");

        }

        $this->msg['checkout']['success'] = t("We have received your payment.");

        $this->msg['checkout']['cancel'] = t("We have not received your payment. Payment cancelled.");

        $this->msg['checkout']['error'] = t("We have not received your payment. An error occurred.");



        // Set URLs

        if (getSegment(2) == 'create') {

        	$this->uri['previousUrl'] = config('app.locale') . '/posts/#entryId/edit';

            // $this->uri['previousUrl'] = config('app.locale') . '/posts/create/#entryToken/payment';

            $this->uri['nextUrl'] = config('app.locale') . '/posts/create/#entryToken/photos';

            $this->uri['paymentCancelUrl'] = url(config('app.locale') . '/posts/create/#entryToken/payment/cancel');

            $this->uri['paymentReturnUrl'] = url(config('app.locale') . '/posts/create/#entryToken/payment/success');

        } else {

            $this->uri['previousUrl'] = config('app.locale') . '/posts/#entryId/payment';

            $this->uri['nextUrl'] = config('app.locale') . '/' . trans('routes.v-post', ['slug' => '#title', 'id' => '#entryId']);

            $this->uri['paymentCancelUrl'] = url(config('app.locale') . '/posts/#entryId/payment/cancel');

            $this->uri['paymentReturnUrl'] = url(config('app.locale') . '/posts/#entryId/payment/success');

        }



   // Payment Helper init.

        PaymentHelper::$country = collect(config('country'));

        PaymentHelper::$lang = collect(config('lang'));

        PaymentHelper::$msg = $this->msg;

        PaymentHelper::$uri = $this->uri;



        $this->packages = Package::where('translation_lang',config('app.locale'))->where('currency_code','USD')->orderBy('lft')->get();

        

        

        if (!empty(Session::get('currency'))) {

            $CurrencyObj = Currency::where('code',Session::get('currency'))->first();

            foreach ($this->packages as $p){

                $p->price = $p->price;

                // $p->price = getCurrencyAmount(Session::get('currency'),$p->price);

                $p->currency = $CurrencyObj;

            }

        }

        view()->share('packages', $this->packages);

        view()->share('countPackages', $this->packages->count());



        // Keep the Post's creation message

        // session()->keep(['message']);

        if (getSegment(2) == 'create') {

            if (session()->has('tmpPostId')) {

                session()->flash('message', t('Your ad has been created.'));

            }

        }











		// References

		$data = [];

		

		// Get Countries

		$data['countries'] = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());

		view()->share('countries', $data['countries']);

		

		// Get Categories

		$cacheId = 'categories.parentId.0.with.children' . config('app.locale');

		$data['categories'] = Cache::remember($cacheId, $this->cacheExpiration, function () {

			$categories = Category::trans()->where('parent_id', 0)->with([

				'children' => function ($query) {

					$query->trans();

				},

			])->orderBy('lft')->get();

			return $categories;

		});

		view()->share('categories', $data['categories']);

		

		// Get Post Types

		$cacheId = 'postTypes.all.' . config('app.locale');

		$data['postTypes'] = Cache::remember($cacheId, $this->cacheExpiration, function () {

			$postTypes = PostType::trans()->orderBy('lft')->get();

			return $postTypes;

		});

		view()->share('postTypes', $data['postTypes']);

		

		// Count Packages

        // $data['countPackages'] = Package::trans()->applyCurrency()->count();

        

        

        

        $data['countPackages'] = Package::where('translation_lang','en')->where('currency_code','USD')->where('active','1')->count();

		view()->share('countPackages', $data['countPackages']);

		

		// Count Payment Methods

		$data['countPaymentMethods'] = $this->countPaymentMethods;

		

		// Save common's data

		$this->data = $data;

	}

	

	/**

	 * New Post's Form.

	 *

	 * @param null $tmpToken

	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View

	 */

	public function getForm($tmpToken = null)

	{

	    

		 $data = [];

		// Check possible Update

		if (!empty($tmpToken)) {

			session()->keep(['message']);

			

			return $this->getUpdateForm($tmpToken);

		}











		

		// Meta Tags

		MetaTag::set('title', getMetaTag('title', 'create'));

		MetaTag::set('description', strip_tags(getMetaTag('description', 'create')));

		MetaTag::set('keywords', getMetaTag('keywords', 'create'));

		

		// Create

		return view('post.create',$data);

	}

	

	

	

		public function getForm2($tmpToken = null)

	{

	    

	    $s_arr = array();

	    

	    Session::put('cats', $s_arr);



	    

	    $listOfCats = array();

	    

	    session(['cats' => $listOfCats]);

	    

	    

		 $data = [];

		// Check possible Update

		if (!empty($tmpToken)) {

			session()->keep(['message']);

			

			return $this->getUpdateForm($tmpToken);

		}











		

		// Meta Tags

		MetaTag::set('title', getMetaTag('title', 'create'));

		MetaTag::set('description', strip_tags(getMetaTag('description', 'create')));

		MetaTag::set('keywords', getMetaTag('keywords', 'create'));

		

		// Create

		return view('post.create_step1',$data);

	}

	

	

		public function getForm3($id)

	{

	   

		 $data = [];

		 

		 

		 

		  $s_arr = session('cats');

		  

		  array_push($s_arr,$id);



	    

	      Session::put('cats', $s_arr);

		 

	        

	        $catts = DB::table('categories')->where('parent_id', $id)

	        

	        ->where('translation_lang', config('app.locale'))

	        ->orderBy('rgt', 'asc')

	        ->get();

	       // $catt = DB::table('categories')->where('id', $id)->first();

            $catt = DB::table('categories')->where('translation_of', $id)->where('translation_lang', config('app.locale'))->first();

	        

	        $data['catss'] = $catts;

	        $data['catt'] = $catt;

	







		

		// Meta Tags

		MetaTag::set('title', getMetaTag('title', 'create'));

		MetaTag::set('description', strip_tags(getMetaTag('description', 'create')));

		MetaTag::set('keywords', getMetaTag('keywords', 'create'));

		

		// Create

		return view('post.create_step2',$data);

	}

	

	

		public function getForm6($id)

	{

	     

		 $data = [];

	        

	        $catts = DB::table('categories')->where('parent_id', $id)

	        

	        ->where('translation_lang', config('app.locale'))

	        ->orderBy('rgt', 'asc')

	        ->get();

	        $catt = DB::table('categories')->where('translation_of', $id)->where('translation_lang', config('app.locale'))->first();

	        $data['catss'] = $catts;

	        $data['catt'] = $catt;

		// Meta Tags

		MetaTag::set('title', getMetaTag('title', 'create'));

		MetaTag::set('description', strip_tags(getMetaTag('description', 'create')));

		MetaTag::set('keywords', getMetaTag('keywords', 'create'));

		

		// Create

		return view('post.subcats',$data);

	}

	

	

	

	

		public function getForm4($id)

	{

	  

	  

	//  echo "xxxxxxx";



//$userRoles = DB::table('cities_pickup13-8')->groupBy('country_code', 'name', 'asciiname', 'latitude', 'longitude')->get();



//$userRolesId = array_column($userRoles ->toArray(), 'id');

//DB::table('cities_pickup13-8')->whereNotIn('id', $userRolesId )->delete();





 /*$posts_q = DB::table('posts_pickup13-8')->get();

 $post_fix=array();

 foreach($posts_q as $pst){

	 

	$cty_q = DB::table('cities_pickup13-8')->where('name',$pst->city_name)->orwhere('asciiname',$pst->city_name)->first(); 

	if(!empty($cty_q->id)){

	  	 $query_update =  \DB::table('posts_pickup13-8')

           ->where('id', $pst->id)

           ->update(['city_id' => $cty_q->id]); 

	}else{$post_fix[]=$pst->id;}

	 

	 }

print_r($post_fix);

	*/

		 $data = [];

		 

		 $data['catid'] = $id;

		 $data['catid'] = $id;

		 

		    $cat1=0;

			$cat2=0;

			$cat3=0;

			$cat4=0;

			

			

	        

	     $p_id = DB::table('categories')->where('id', $id)

	        

			//abdelhay hash this command becouse make problem when i try to change langaug in same page 

	      //  ->where('translation_lang', config('app.locale'))

	        

	        ->first();

			

			if($p_id->parent_id > 0){

				

				 $p_id2 = DB::table('categories')->where('id', $p_id->parent_id)->first();	        

			    if($p_id2->parent_id > 0){

					

					 $p_id3 = DB::table('categories')->where('id', $p_id2->parent_id)->first();	     

					

					     if($p_id3->parent_id > 0){

							 $cat1=$p_id->parent_id;$cat2=$p_id2->parent_id;$cat3=$p_id3->parent_id;$cat4=$id;

							 }else{

						 

						  $cat1=$p_id->parent_id;$cat2=$p_id2->parent_id;$cat3=$id;$cat4=0;

						 }

						 

					}else{

					 

					$cat1=$p_id->parent_id;$cat2=$id;$cat3=0;$cat4=0;

					}

	        

				

				}else{$cat1=$id;$cat2=0;$cat3=0;$cat4=0;}

			

			

		//	echo "a".$cat1."b".$cat2.'c'.$cat3.'d'.$cat4; 

			

			$data['cat1']=$cat1;

			$data['cat2']=$cat2;

			$data['cat3']=$cat3;

			$data['cat4']=$cat4;

			

	 	//dd($p_id);

		//print_r($p_id);

		

        $data['p_idd'] = $p_id->parent_id;

		$data['categoryid']=$id;

        $arr= session('cats');

		//dd($arr);

        if(isset($arr[0])){

        $data['p_idd'] = $arr[0];

        }

        if(isset($arr[1])){

        $data['categoryid'] = $arr[1];

        }

		// Meta Tags

		MetaTag::set('title', getMetaTag('title', 'create'));

		MetaTag::set('description', strip_tags(getMetaTag('description', 'create')));

		MetaTag::set('keywords', getMetaTag('keywords', 'create'));





     	$user_info = User::select('city')->where('id', auth()->user()->id)->first();

		if(!empty($user_info->city)){$data['user_city']=$user_info->city;}else{$data['user_city']='';}

		

		// Create

		return view('post.create_step3',$data);

	}

	

	

	

	/**

	 * Store a new Post.

	 *

	 * @param null $tmpToken

	 * @param PostRequest $request

	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

	 */

	 

	public function postForm($tmpToken = null, PostRequest $request)

	{

	 

	 

	 //dd($request);

	 

		// Check possible Update

		if (!empty($tmpToken)) {

			session()->keep(['message']);

			

			return $this->postUpdateForm($tmpToken, $request);

		}

		

		

		

		

		$country_code = strtoupper(config('country.code'));

		$cityname = $request->input('city_name');

		 
					   

			   $city_query = \DB::table('posts')->select('lat','lon','city_id')->where('city_name', 'like', '%'.$cityname.'%')->first();

			   $lat = !empty($city_query->lat)?$city_query->lat:0;

			   $lng = !empty($city_query->lon)?$city_query->lon:0;

			   $locationid =!empty($city_query->city_id)?$city_query->city_id:0;

			   $city = City::find($locationid);

			   if(!empty($lat)&&!empty($lng)&&!empty($locationid)&&empty($city))

			   {

				 // dd($city); 

				   $subadmin1_code_query = \DB::table('subadmin1')

				   ->where('country_code','=',$country_code)->select('code')->first();

				   $subadmin1_code = !empty($subadmin1_code_query->code)?$subadmin1_code_query->code:'';

				   $subadmin2_code_query = \DB::table('subadmin2')

										   ->where('country_code','=',$country_code)

										   ->where('subadmin1_code','=',$subadmin1_code)

										   ->select('code')

										   ->first();

			        $subadmin2_code = !empty($subadmin2_code_query->code)?$subadmin2_code_query->code:'';  

					$timezon_query = \DB::table('time_zones')

									->where('country_code','=',$country_code)

								    ->select('time_zone_id')

								    ->first();

           

        $timezon = !empty($timezon_query->time_zone_id)?$timezon_query->time_zone_id:'';    

		

		   $city_data = \DB::insert('insert into cities (country_code, name, asciiname,latitude,longitude,subadmin1_code,subadmin2_code,active,time_zone,created_at,updated_at) 

       values ("'.$country_code.'", "'.$cityname.'", "'.$cityname.'", "'.$lat.'", "'.$lng.'", "'.$subadmin1_code.'", "'.$subadmin2_code.'", 1, "'.$timezon.'", "'.date('Y-m-d H:i:s').'", "'.date('Y-m-d H:i:s').'")');

	   

	    $locationid = \DB::getPdo()->lastInsertId();

	   

				   

				   

				   }

			   

		       

		

			 //  dd($city);

			   

			   if($lat == 0 && $lng == 0){

				   

		      $city_query = \DB::table('cities')->select('latitude','longitude','id')->where('name', 'like', '%'.$cityname.'%')->first();

			  $lat = !empty($city_query->latitude)?$city_query->latitude:0;

			  $lng = !empty($city_query->longitude)?$city_query->longitude:0;

		      if(!empty($city_query->id)){$locationid = $city_query->id;}

	 

	 if($lat == 0 && $lng == 0){		 

		 

       $getlocation = $this->getlocationcity($cityname);

        //dd($getlocation);

          $lat = !empty($getlocation['lat'])?$getlocation['lat']:0;

		  $lng = !empty($getlocation['lng'])?$getlocation['lng']:0;

		  

				

		

		$subadmin1_code_query = \DB::table('subadmin1')

		    ->where('country_code','=',$country_code)

           ->select('code')

           ->first();

           

           

        

           

		$subadmin1_code = !empty($subadmin1_code_query->code)?$subadmin1_code_query->code:'';

		

		$subadmin2_code_query = \DB::table('subadmin2')

		   ->where('country_code','=',$country_code)

		   ->where('subadmin1_code','=',$subadmin1_code)

		   ->select('code')

           ->first();

           

          

           

        $subadmin2_code = !empty($subadmin2_code_query->code)?$subadmin2_code_query->code:'';           

           

     	$timezon_query = \DB::table('time_zones')

		    ->where('country_code','=',$country_code)

           ->select('time_zone_id')

           ->first();

           

        $timezon = !empty($timezon_query->time_zone_id)?$timezon_query->time_zone_id:'';    

		

		   $city_data = \DB::insert('insert into cities (country_code, name, asciiname,latitude,longitude,subadmin1_code,subadmin2_code,active,time_zone,created_at,updated_at) 

       values ("'.$country_code.'", "'.request()->get('location').'", "'.request()->get('location').'", "'.$lat.'", "'.$lng.'", "'.$subadmin1_code.'", "'.$subadmin2_code.'", 1, "'.$timezon.'", "'.date('Y-m-d H:i:s').'", "'.date('Y-m-d H:i:s').'")');

	   

	    $locationid = \DB::getPdo()->lastInsertId();

	   

	 }

	 

	 }

	 

      

        $city_id = $locationid;

    

	

    	// Get the Post's City

		$city = City::find($city_id);

		if (empty($city)) {

			flash(t("Posting Ads was disabled for this time. Please try later. Thank you."))->error();

			

			return back()->withInput();

		}

		

		

		 

		

		// Conditions to Verify User's Email or Phone

		if (auth()->check()) {

			$emailVerificationRequired = config('settings.mail.email_verification') == 1 && $request->filled('email') && $request->input('email') != auth()->user()->email;

			$phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $request->filled('phone') && $request->input('phone') != auth()->user()->phone;

		} else {

			$emailVerificationRequired = config('settings.mail.email_verification') == 1 && $request->filled('email');

			$phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $request->filled('phone');

		}

		

	/*

	abdelhay ( 11-8-2021)i made this code to update all posts table main category id

	$posts =  DB::table('posts')->get();

		foreach($posts as $pst){

			



			

		//level4

		$catsss = Category::where('id', $pst->category_id)->first();

		if(($catsss->parent_id)==0){$m_cat_id=$pst->category_id;}

		else{

		//level3

			$catsss1 = Category::where('id', $catsss->parent_id)->first();

		if(($catsss1->parent_id)==0){$m_cat_id=$catsss1->id;}

		else{

        //level2

			$catsss2 = Category::where('id', $catsss1->parent_id)->first();

			if(($catsss2->parent_id)==0){$m_cat_id=$catsss2->id;}

			else{

				//level1

			$catsss3 = Category::where('id', $catsss2->parent_id)->first();

			if(($catsss3->parent_id)==0){$m_cat_id=$catsss3->id;}

			else{

				//level0

				$catsss4 = Category::where('id', $catsss3->parent_id)->first();

			if(($catsss4->parent_id)==0){$m_cat_id=$catsss4->id;}

			else{

				$m_cat_id=0;

			}

			}

			}



		}



		}





			$query_update = DB::table('posts')

            ->where('id', $pst->id)

            ->update(['main_catogery_id' => $m_cat_id]);

		}



	*/	

		

		// New Post

		$post = new Post();

		$input = $request->only($post->getFillable());





		//level4

		$catsss = Category::where('id', $input['category_id'])->first();

		if(($catsss->parent_id)==0){$m_cat_id=$input['category_id'];}

		else{

		//level3

			$catsss1 = Category::where('id', $catsss->parent_id)->first();

		if(($catsss1->parent_id)==0){$m_cat_id=$catsss1->id;}

		else{

        //level2

			$catsss2 = Category::where('id', $catsss1->parent_id)->first();

			if(($catsss2->parent_id)==0){$m_cat_id=$catsss2->id;}

			else{

				//level1

			$catsss3 = Category::where('id', $catsss2->parent_id)->first();

			if(($catsss3->parent_id)==0){$m_cat_id=$catsss3->id;}

			else{

				//level0

				$catsss4 = Category::where('id', $catsss3->parent_id)->first();

			if(($catsss4->parent_id)==0){$m_cat_id=$catsss4->id;}

			else{

				$m_cat_id=0;

			}

			}

			}

		}



		}



		

		foreach ($input as $key => $value) {

		    $value1 = !empty($value)?$value:'';

			$post->{$key} = $value1;

		}

		

		$post->main_catogery_id = $m_cat_id;

		

		$post->country_code = config('country.code');

		$post->user_id = (auth()->check()) ? auth()->user()->id : 0;

	//	$post->negotiable = $request->input('negotiable');

		$post->phone = !empty($request->input('phone'))?$request->input('phone'):'';

		$post->city_id = !empty($city_id)?$city_id:'';

		$post->lat = !empty($city->latitude)?$city->latitude:'';

		$post->lon = !empty($city->longitude)?$city->longitude:'';

		$post->ip_addr = Ip::get();

		$post->tmp_token = md5(microtime() . mt_rand(100000, 999999));

		$post->verified_email = 1;

		$post->verified_phone = 1;

		if(empty($request->input('from_email'))){$email_hidden=1;}else{$email_hidden=0;}

		if(empty($request->input('from_phone'))){$phone_hidden=1;}else{$phone_hidden=0;}

		$post->email_hidden = $email_hidden;

		$post->phone_hidden = $phone_hidden;

		

		// Email verification key generation

//		if ($emailVerificationRequired) {

//			$post->email_token = md5(microtime() . mt_rand());

//			$post->verified_email = 0;

//		}

		

		// Mobile activation key generation

//		if ($phoneVerificationRequired) {

//			$post->phone_token = mt_rand(100000, 999999);

//			$post->verified_phone = 0;

//		}



		// Save

		$post->save();

		$postOwner = User::find(auth()->user()->id);

		

			

		

	//	Mail::send(new PostNotification($post, $postOwner));



		

		// Save ad Id in session (for next steps)

		session(['tmpPostId' => $post->id]);

		

		// Custom Fields

		//dd($request);

		 

		$this->createPostFieldsValues($post, $request);

		

		// The Post's creation message

		if (getSegment(2) == 'create') {

			session()->flash('message', t('Your ad has been created.'));

		}

		

		// Get Next URL

		$nextStepUrl = config('app.locale') . '/posts/create/' . $post->tmp_token . '/payment';

		

		// Send Admin Notification Email

		if (config('settings.mail.admin_email_notification') == 1) {

			try {

				// Get all admin users

				$admins = User::where('is_admin', 1)->get();

				if ($admins->count() > 0) {

					foreach ($admins as $admin) {
                    // this code for send email for all admins in system 
					//	Mail::send(new PostNotification($post, $admin));

					}

				}

			} catch (\Exception $e) {

				flash($e->getMessage())->error();

			}

		}

		

		// Send Email Verification message

		if ($emailVerificationRequired || 1==1) {

			// Save the Next URL before verification

			session(['itemNextUrl' => $nextStepUrl]);

			

			// Send

			$this->sendVerificationEmail($post);

			

			// Show the Re-send link

			$this->showReSendVerificationEmailLink($post, 'post');

		}

		

		// Send Phone Verification message

		if ($phoneVerificationRequired) {

			// Save the Next URL before verification

			session(['itemNextUrl' => $nextStepUrl]);

			

			// Send

			$this->sendVerificationSms($post);

			

			// Show the Re-send link

			$this->showReSendVerificationSmsLink($post, 'post');

			

			// Go to Phone Number verification

			$nextStepUrl = config('app.locale') . '/verify/post/phone/';

		}







	    if (getSegment(2) == 'create') {

            if (!session()->has('tmpPostId')) {

                return redirect('posts/create');

            }

            $post1 = Post::with(['latestPayment'])

                ->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])

                ->where('id', session('tmpPostId'))

                ->where('tmp_token', $post->tmp_token)

                ->first();

        } else {

            $post1 = Post::with(['latestPayment'])

                ->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])

                ->where('user_id', auth()->user()->id)

                ->where('id', $post->tmp_token)

                ->first();

        }



     	 $query_update =  \DB::table('posts')

           ->where('id', $post1->id)

           ->update(['premium_email' => $request->input('from_email'),'premium_phone' => $request->input('from_phone')]);

        



           // Check if the selected Package has been already paid for this Post

        $alreadyPaidPackage = false;

        if (!empty($post->latestPayment)) {

            if ($post->latestPayment->package_id == $request->input('package_id')) {

                $alreadyPaidPackage = true;

            }

        }



        // Check if Payment is required

        $package = Package::find($request->input('package_id'));

        if (!empty($package)) {

			

			$package->price = 0;

			 $info_user = DB::table('users')

            ->where('id', auth()->user()->id)->first();

	       

	       

	       $query_update = DB::table('users')

            ->where('id', auth()->user()->id)

			->update(['no_points' => ($info_user->no_points-$package->no_points)]);

			

			

			

			// start function update user package table

			//check if this post id is already exist in package  

		 

		

			 if($package->price == 0)

	    {

	       $payment= new Payment;

	       $payment->post_id = $post->tmp_token;

	       $payment->package_id = $request->input('package_id');

	       $payment->transaction_id = 0;

	       $payment->payment_method_id = 0;

	       $payment->active = 1;

		   $payment->user_id = (auth()->check()) ? auth()->user()->id : 0;

	       $payment->save();

		}

			

			// end function update user package table 

			

			

            if ($package->price > 0 && $request->filled('payment_method_id') && !$alreadyPaidPackage) {

                if($request->payment_method_id == 2)

                {

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL,"https://www.hesabe.com/authpost");

                    curl_setopt($ch, CURLOPT_POST, 1);

                    curl_setopt($ch, CURLOPT_POSTFIELDS,

                                "MerchantCode=8861618&Amount=0.600&SuccessUrl=".url(config('app.locale').'/post/hesabe-success')."&FailureUrl=".url(config('app.locale').'/post/hesabe-cancel')."&Variable1=$post->id&Variable2=$post->tmp_token&Variable3=$request->package_id");

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $server_output = curl_exec($ch);

                    curl_close ($ch);

                    $json_decode  = json_decode($server_output, true);

                    $token = $json_decode['data']['token'];

                    $paymenturl = $json_decode['data']['paymenturl'].$token;

                    header("Location:$paymenturl");

                    exit();

                    

                }

                else

                {

                    // Send the Payment

                    return $this->sendPayment($request, $post1);

                }

            }

			

			

        }

		

		

		

		

		



        // IF NO PAYMENT IS MADE (CONTINUE)



        // Get the next URL

        if (getSegment(2) == 'create') {

            // $request->session()->flash('message', t('Your ad has been created.'));

            $nextStepUrl = config('app.locale') . '/posts/create/' . $post->tmp_token . '/photos';

        } else {

        	

            flash(t("Your ad has been updated."))->success();

            $nextStepUrl = config('app.locale') . '/posts/'.$request->input('post_id').'/photos';

            // $nextStepUrl = config('app.locale') . '/' . $post->uri . '?preview=1';

        }

        



		

		// Redirection

		return redirect($nextStepUrl);

	}

	

	

	

	

	public function HesabeSuccess(Request $request)

	{

	    if($request->Status == 1)

	    {

	       

	       $query_update = DB::table('posts')

            ->where('id', $request->Variable1)

            //->update(['reviewed' => 1,'featured' => 1]);

			->update(['featured' => 1]);

	       

	       $payment= new Payment;

	       $payment->post_id = $request->Variable1;

	       $payment->package_id = $request->Variable3;

		   $payment->user_id = (auth()->check()) ? auth()->user()->id : 0;

	       $payment->transaction_id = !empty($request->PaymentId)?$request->PaymentId:'';

	       $payment->payment_method_id = 2;

	       $payment->active = 1;

	       $payment->save();



	        

           flash(t("We have received your payment."))->success();

	       //if(!empty($request->Variable2))

	       //{

	       //  return redirect(config('app.locale').'/posts/create/'.$request->Variable2.'/photos');    

	       //}

	       //else

	       //{

	            return redirect(config('app.locale').'/posts/'.$request->Variable1.'/photos');    

	       //}

            

	    }

	    else

	    {

	       flash(t("We have not received your payment. Payment cancelled."))->error();

           return redirect(config('app.locale').'/posts/'.$request->Variable1.'/edit/?error=paymentCancelled'); 

	    }

	}

	

	

	

	

	

	

	

	

	public function postFormApp($tmpToken = null, request $request)

	{

		

		 $alreadyPaidPackage=false;

		 $getpoint = \DB::table('points')->where('id', '=', $request->point_id)->first(); 

		// dd($getpoint);

		

		  if ($getpoint->price > 0 && $request->filled('payment_method_id') && !$alreadyPaidPackage) {

                if($request->payment_method_id == 2)

                { $kd_price=0.30*$getpoint->price;

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL,"https://www.hesabe.com/authpost");

                    curl_setopt($ch, CURLOPT_POST, 1);

                    curl_setopt($ch, CURLOPT_POSTFIELDS,

                                "MerchantCode=8861618&Amount=".$kd_price."&SuccessUrl=".url(config('app.locale').'api/point/hesabe-success')."&FailureUrl=".url(config('app.locale').'api/point/hesabe-cancel')."&Variable1=$getpoint->id&Variable2=$getpoint->no_points&Variable3=$request->package_id");

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $server_output = curl_exec($ch);

                    curl_close ($ch);

                    $json_decode  = json_decode($server_output, true);

                    $token = $json_decode['data']['token'];

                    $paymenturl = $json_decode['data']['paymenturl'].$token;

					

                   //  header("Location:$paymenturl");

                   // exit();

				   return response()->json(['paymenturl'=>$paymenturl]);

                    

                }

                else

                {

                    // Send the Payment

                   return $this->sendPaymentNew($request, $getpoint);	

                }

            }

		

		//return view('account.recharge_points', $data);



		 return response()->json(['results'=>$data]);

	}

	 

 

	

	

	public function HesabeSuccess_app(Request $request)

	{

	    if($request->Status == 1)

	    {

	       

	       $query_update = DB::table('posts')

            ->where('id', $request->post_id)

          //  ->update(['reviewed' => 1,'featured' => 1]);

	        ->update(['featured' => 1]);

	       $payment= new Payment;

	       $payment->post_id = $request->post_id;

	       $payment->package_id = $request->package_id;

	       $payment->transaction_id = !empty($request->PaymentId)?$request->PaymentId:'';

	       $payment->payment_method_id = 2;

	       $payment->active = 1;

	       $payment->save();



			

		   $post = DB::table('posts')->where(['id'=>$request->post_id])->get();

		   

		  

           $post[0]->package_id = $request->input('package_id');



        // Check if Payment is required

        //$package = Package::find($request->input('package_id'));	

	        

           //flash(t("We have received your payment."))->success();

	       //if(!empty($request->Variable2))

	       //{

	       //  return redirect(config('app.locale').'/posts/create/'.$request->Variable2.'/photos');    

	       //}

	       //else

	       //{

	           //return redirect(config('app.locale').'/posts/'.$request->Variable1.'/photos');    

	       //}

		   return response()->json(['results'=>"We have received your payment",'data'=>$post]);

            

	    }

	    else

	    {

	       //flash(t("We have not received your payment. Payment cancelled."))->error();

           //return redirect(config('app.locale').'/posts/'.$request->Variable1.'/edit/?error=paymentCancelled');

		   return response()->json(['results'=>"We have not received your payment. Payment cancelled."]); 

	    }

	}

	

	public function HesabeCancel(Request $request)

	{   

        flash(t("We have not received your payment. Payment cancelled."))->error();

        return redirect(config('app.locale').'/posts/'.$request->Variable1.'/edit/?error=paymentCancelled');

	}

	

	public function HesabeCancel_app(Request $request)

	{   

        return response()->json(['results'=>"We have not received your payment. Payment cancelled."]);

	}

	

	

	

	

	

	public function postForm_app($tmpToken = null, PostRequest $request)

	{

		 

		//return ($request);

		$category_id=$request->input('category_id');

		$post_type_id=$request->input('post_type_id');

		$parent_id=$request->input('parent_id');		 

		$cityname = $request->input('city_name');

		$customFields = $request->input('customFields');   

	    $customArr =  json_decode($customFields, true);

		$country_code = $request->input('country_code');		

		$status = $request->input('status');

		

		if(!empty($category_id)){$parent_id=$category_id;}		

		if(empty($post_type_id)){$post_type_id=2;}

		

		

	    $getlocation = $this->getlocationcity($cityname);

	   

		

		$lat = !empty($getlocation['lat'])?$getlocation['lat']:0;

	    $lng = !empty($getlocation['lng'])?$getlocation['lng']:0;

		

		$subadmin1_code_query = \DB::table('subadmin1')

		    ->where('country_code','=',$country_code)

           ->select('code')

           ->first();

		$subadmin1_code = !empty($subadmin1_code_query->code)?$subadmin1_code_query->code:'';

		$subadmin2_code_query = \DB::table('subadmin2')

		   ->where('country_code','=',$country_code)

		   ->where('subadmin1_code','=',$subadmin1_code)

		   ->select('code')

           ->first();

        $subadmin2_code = !empty($subadmin2_code_query->code)?$subadmin2_code_query->code:'';           

     	$timezon_query = \DB::table('time_zones')

		    ->where('country_code','=',$country_code)

           ->select('time_zone_id')

           ->first();

        $timezon = !empty($timezon_query->time_zone_id)?$timezon_query->time_zone_id:'';           

        $city_data = \DB::insert('insert into cities (country_code, name, asciiname,latitude,longitude,subadmin1_code,subadmin2_code,active,time_zone,created_at,updated_at) 

       values ("'.$country_code.'", "'.$cityname.'", "'.$cityname.'", "'.$lat.'", "'.$lng.'", "'.$subadmin1_code.'", "'.$subadmin2_code.'", 1, "'.$timezon.'", "'.date('Y-m-d H:i:s').'", "'.date('Y-m-d H:i:s').'")');

       

        $city_id = \DB::getPdo()->lastInsertId();

    

    	// Get the Post's City

		$city = City::find($city_id);

		if (empty($city)) {

			//flash(t("Posting Ads was disabled for this time. Please try later. Thank you."))->error();

			return response()->json(['results'=>"Posting Ads was disabled for this time. Please try later. Thank you."]);

			//return back()->withInput();

		}

		

		// Conditions to Verify User's Email or Phone

		

		

		if ($request->input('user_id')) {

			$emailVerificationRequired = config('settings.mail.email_verification') == 1 && $request->filled('email') && $request->input('email') != $request->input('email');

			$phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $request->filled('phone') && $request->input('phone') != $request->input('phone');

		} else {

			$emailVerificationRequired = config('settings.mail.email_verification') == 1 && $request->filled('email');

			$phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $request->filled('phone');

		}

		

		// New Post

		$post = new Post();

		$input = $request->only($post->getFillable());





		//level4

		$catsss = Category::where('id', $input['category_id'])->first();

		if(($catsss->parent_id)==0){$m_cat_id=$input['category_id'];}

		else{

		//level3

			$catsss1 = Category::where('id', $catsss->parent_id)->first();

		if(($catsss1->parent_id)==0){$m_cat_id=$catsss1->id;}

		else{

        //level2

			$catsss2 = Category::where('id', $catsss1->parent_id)->first();

			if(($catsss2->parent_id)==0){$m_cat_id=$catsss2->id;}

			else{

				//level1

			$catsss3 = Category::where('id', $catsss2->parent_id)->first();

			if(($catsss3->parent_id)==0){$m_cat_id=$catsss3->id;}

			else{

				//level0

				$catsss4 = Category::where('id', $catsss3->parent_id)->first();

			if(($catsss4->parent_id)==0){$m_cat_id=$catsss4->id;}

			else{

				$m_cat_id=0;

			}

			}

			}

		}



		}



		

		foreach ($input as $key => $value) {

		    $value1 = !empty($value)?$value:'';

			$post->{$key} = $value1;

		}

		

		$post->main_catogery_id = $m_cat_id;

		$post->country_code = $request->input('country_code');

 	    $post->user_id = $request->input('user_id');

      //$post->negotiable = $request->input('negotiable');

	    $post->phone = $request->input('phone');

		$post->premium_email = $request->input('email');

		$post->premium_phone = $request->input('phone');

		  

		

		$getusernamedetail = \DB::table('users')->where('id', '=', $request->input('user_id'))->first();

	    $post->contact_name = $getusernamedetail->username;

		$post->post_type_id = $getusernamedetail->user_type_id;

	    $post->city_id = $city_id;

		$post->lat = $city->latitude;

		$post->lon = $city->longitude;

		$post->ip_addr = Ip::get();

		$post->tmp_token = md5(microtime() . mt_rand(100000, 999999));

		$post->verified_email = 1;

		$post->verified_phone = 1;		

		// Email verification key generation

		if ($emailVerificationRequired) {

			$post->email_token = md5(microtime() . mt_rand());

			$post->verified_email = 0;

		}		

		// Mobile activation key generation

		if ($phoneVerificationRequired) {

			$post->phone_token = mt_rand(100000, 999999);

			$post->verified_phone = 0;

		}

		if(empty($request->input('from_email'))){$email_hidden=1;}else{$email_hidden=0;}

		if(empty($request->input('from_phone'))){$phone_hidden=1;}else{$phone_hidden=0;}



		$post->email_hidden = $request->input('email_hidden');

		$post->phone_hidden = $request->input('phone_hidden');

		//print_r($post);

		//die();

		// Save

		$post->save();

	

		//return response()->json(['results'=>"Your ad has been created.",'data'=>$post]);

		// Save ad Id in session (for next steps)

		//session(['tmpPostId' => $post->id]);

		

		// Custom Fields

		

		//return $request;

		$this->createPostFieldsValues($post, $request);

	 	 



	if(!empty($customArr)){

	foreach($customArr as $custom){

		if(!empty($custom['fieldId'])){

			$fieldId = $custom['fieldId'];

			//fieldId,optionId,value

			$optionId = $custom['optionId'];

			$value = $custom['value'];

			$values = array('post_id' => $post->id,'field_id' => $fieldId,'option_id'=>$optionId,'value'=>$value);

			DB::table('post_values')->insert($values);

		}

		}

		}

	  	

		

	  $images = array();

	    

           // Check if the selected Package has been already paid for this Post

        $alreadyPaidPackage = false;

        if (!empty($post->latestPayment)) {

            if ($post->latestPayment->package_id == $request->input('package_id')) {

                $alreadyPaidPackage = true;

            }

        }

		  

	    // Check if Payment is required

        $package = Package::find($request->input('package_id'));

        if (!empty($package)) {

			

			$package->price = 0;

			 $info_user = DB::table('users')

            ->where('id', $request->input('user_id'))->first();

	       

	       

	       $query_update = DB::table('users')

            ->where('id', $request->input('user_id'))

			->update(['no_points' => ($info_user->no_points-$package->no_points)]);

			 

			// start function update user package table

			//check if this post id is already exist in package  

		 

		

			 if($package->price == 0)

	    {

	       $payment= new Payment;

	       $payment->post_id = $post->tmp_token;

	       $payment->package_id = $request->input('package_id');

	       $payment->transaction_id = 0;

	       $payment->payment_method_id = 0;

	       $payment->active = 1;

		   $payment->user_id = ($request->input('user_id')) ? $request->input('user_id') : 0;

	       $payment->save();

		}

			

			// end function update user package table 

			

			

            if ($package->price > 0 && $request->filled('payment_method_id') && !$alreadyPaidPackage) {

                if($request->payment_method_id == 2)

                {

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL,"https://www.hesabe.com/authpost");

                    curl_setopt($ch, CURLOPT_POST, 1);

                    curl_setopt($ch, CURLOPT_POSTFIELDS,

                                "MerchantCode=8861618&Amount=0.600&SuccessUrl=".url(config('app.locale').'/post/hesabe-success')."&FailureUrl=".url(config('app.locale').'/post/hesabe-cancel')."&Variable1=$post->id&Variable2=$post->tmp_token&Variable3=$request->package_id");

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $server_output = curl_exec($ch);

                    curl_close ($ch);

                    $json_decode  = json_decode($server_output, true);

                    $token = $json_decode['data']['token'];

                    $paymenturl = $json_decode['data']['paymenturl'].$token;

                    header("Location:$paymenturl");

                    exit();

                    

                }

                else

                {

                    // Send the Payment

                    return $this->sendPayment($request, $post1);

                }

            }

			

			

        }

		

	   



 

		

	return response()->json(['status'=> 1,'message'=>'success','results'=>"Your ad has been created.",'data'=>$post, 'images' => $images]);

	

	

	

	

	}

	





	

	 

	public function updateDetails(Request $request)

	{

		//$userdata = User::where('id', '=', $request->userid)->first();

		$userdata = DB::table('users')

			->select('*')

			->where('id', $request->userid)

			->first();

		// Check if these fields has changed

		//print_r($userdata);

		if($userdata === null)

		{

		return response()->json(['results'=>'User is not existing','id'=>$request->userid]);

		}

		else

		{

		 echo $userdata->email;

		$emailChanged = ($request->email != $userdata->email);

		$phoneChanged = ($request->phone != $userdata->phone);

		$usernameChanged = ($request->username != $userdata->username);

		

		// Validation Rules

		$rules = [

			'gender_id'    => 'required|not_in:0',

			'user_type_id' => 'required|not_in:0',

			'first_name' => 'required',

            'last_name' => 'required',

			'username'     => 'valid_username|allowed_username|between:3,100',

			'phone'        => 'required|max:20',

			'email'        => 'required|email|whitelist_email|whitelist_domain',

			'city' => 'required',

			//'name'         => 'required|max:100',

			

        ];

		

				

		if ($phoneChanged) {

			$rules['phone'] = 'unique:users,phone|' . $rules['phone'];

		}

		

		// Email

		if ($emailChanged) {

			$rules['email'] = 'unique:users,email|' . $rules['email'];

		}

		

		// Username

		if ($usernameChanged) {

			$rules['username'] = 'required|unique:users,username|' . $rules['username'];

		}

		

		if($this->input('zipcode')){

			$rules['zipcode'] = 'numeric';

		}

			

		

		

		return response()->json(['results'=>$rules]); 

		

	

	

	

	

		// Check if these fields has changed

		$emailChanged = $request->email;

		$phoneChanged = $request->phone;

		$usernameChanged = $request->username;

		

		// Conditions to Verify User's Email or Phone

		$emailVerificationRequired = config('settings.mail.email_verification') == 1 && $emailChanged;

		$phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $phoneChanged;

		

		// Get User

		$user = User::withoutGlobalScopes([VerifiedScope::class])->find($request->userid);

		

		// Update User

		$input = $request->only($user->getFillable());

		foreach ($input as $key => $value) {

			if (in_array($key, ['email', 'phone', 'username']) && empty($value)) {

				continue;

			}

			$user->{$key} = $value;

		}

		

		$user->phone_hidden = $request->phone_hidden;

		$user->country_code = $request->country_code;

		// Save

		$user->save();

		

		return response()->json(['results'=>'Profile has been updated successfully']);

	

	}

	}

	



	

	

	public function UpdatPostForm_app(UpdatePostRequest $request)

	{

		 

		 //return ($request);

		$post_id=$request->input('post_id');

		$user_id=$request->input('user_id');

		$title=$request->input('title');		 

		$description = $request->input('description');

		$price = $request->input('price');  

		$email = $request->input('email'); 

		$phone = $request->input('phone'); 

		$city_name = $request->input('city_name'); 

		$country_code = $request->input('country_code');  



	  

	    $res = Post::where('id',$post_id)->where('user_id',$user_id)->update([

			'title'   => $title, 

			'description'   => $description, 

			'price'   => $price, 

			'email'   => $email, 

			'phone'   => $phone, 

			'city_name'   => $city_name,      

			'country_code'   => $country_code,                 

		]);	 

	 

		

	    // Check if Payment is required

        if(!empty($request->input('package_id'))){

		$package = Package::find($request->input('package_id'));

        if (!empty($package)) {

			

			$package->price = 0;

			 $info_user = DB::table('users')

            ->where('id', $request->input('user_id'))->first();

	       

	       

	       $query_update = DB::table('users')

            ->where('id', $request->input('user_id'))

			->update(['no_points' => ($info_user->no_points-$package->no_points)]);

			

			

			

			// start function update user package table

			//check if this post id is already exist in package  

		 

		

			 if($package->price == 0)

	    {

	       $payment= new Payment;

	       $payment->post_id = $post->tmp_token;

	       $payment->package_id = $request->input('package_id');

	       $payment->transaction_id = 0;

	       $payment->payment_method_id = 0;

	       $payment->active = 1;

		   $payment->user_id = ($request->input('user_id')) ? $request->input('user_id') : 0;

	       $payment->save();

		}

			

			// end function update user package table 

			

			

            if ($package->price > 0 && $request->filled('payment_method_id') && !$alreadyPaidPackage) {

                if($request->payment_method_id == 2)

                {

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL,"https://www.hesabe.com/authpost");

                    curl_setopt($ch, CURLOPT_POST, 1);

                    curl_setopt($ch, CURLOPT_POSTFIELDS,

                                "MerchantCode=8861618&Amount=0.600&SuccessUrl=".url(config('app.locale').'/post/hesabe-success')."&FailureUrl=".url(config('app.locale').'/post/hesabe-cancel')."&Variable1=$post->id&Variable2=$post->tmp_token&Variable3=$request->package_id");

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $server_output = curl_exec($ch);

                    curl_close ($ch);

                    $json_decode  = json_decode($server_output, true);

                    $token = $json_decode['data']['token'];

                    $paymenturl = $json_decode['data']['paymenturl'].$token;

                    header("Location:$paymenturl");

                    exit();

                    

                }

                else

                {

                    // Send the Payment

                    return $this->sendPayment($request, $post1);

                }

            }

			

			

        }

		

	}

		 return response()->json(['results'=>"Your ad has been updated.",'data'=>$res]);

  

   

	

	}

	

	/**

	 * Confirmation

	 *

	 * @param $tmpToken

	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View

	 */

	public function finish($tmpToken)

	{

		// Keep Success Message for the page refreshing

		session()->keep(['message']);

		if (!session()->has('message')) {

			return redirect(config('app.locale') . '/');

		}

		

		// Clear the steps wizard

		if (session()->has('tmpPostId')) {

			// Get the Post

			$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->where('id', session('tmpPostId'))->where('tmp_token', $tmpToken)->first();

			if (empty($post)) {

				abort(404);

			}

			

			// Apply finish actions

			$post->tmp_token = null;

			$post->save();

			session()->forget('tmpPostId');

		}

		

		// Redirect to the Post,

		// - If User is logged

		// - Or if Email and Phone verification option is not activated

		if (auth()->check() || (config('settings.mail.email_verification') != 1 && config('settings.sms.phone_verification') != 1)) {

			if (!empty($post)) {

				flash(session('message'))->success();

				

				return redirect(config('app.locale') . '/' . $post->uri . '?preview=1');

			}

		}

		

		// Meta Tags

		MetaTag::set('title', session('message'));

		MetaTag::set('description', session('message'));

		

		return view('post.finish');

	}

	

 public	function  getlocationcity($string)

	{

	    $string = str_replace(" ", "+", urlencode($string));

        $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$string."&key=AIzaSyD3HKnsvpSAYaoQQ-wIeqDBTjb69hJ-vMw";

    

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $details_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = json_decode(curl_exec($ch), true);

    

        if ($response['status'] != 'OK') {

            return null;

        }

    

        $geometry = $response['results'][0]['geometry'];

     

        $array = array(

            'lat' => $geometry['location']['lat'],

            'lng' => $geometry['location']['lng'],

        );

    

        return $array;

	}

	

	

	

}