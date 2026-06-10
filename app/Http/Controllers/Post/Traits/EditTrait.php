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

namespace App\Http\Controllers\Post\Traits;
use App\Models\Package;

use App\Helpers\Ip;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\City;
use App\Models\Category;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use Torann\LaravelMetaTags\Facades\MetaTag;

trait EditTrait
{
    /**
     * Show the form the create a new ad post.
     *
     * @param $postIdOrToken
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUpdateForm($postIdOrToken)
    {
        $data = [];
        
        // Get Post
        if (getSegment(2) == 'create') {
            if (!session()->has('tmpPostId')) {
                return redirect('posts/create');
            }
            $post = Post::with(['city'])
				->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('id', session('tmpPostId'))
				->where('tmp_token', $postIdOrToken)
				->first();
        } else {
            $post = Post::with(['city'])
				->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('user_id', auth()->user()->id)
				->where('id', $postIdOrToken)
				->first();
				if(!empty($post->city_id)){
						$cityObj = City::find($post->city_id);
						if(!empty($cityObj->name)){$post->city_name = $cityObj->name;}else{$post->city_name = "";}
						
				}
        }
        
        if (empty($post)) {
            abort(404);
        }
        
        view()->share('post', $post);
        
        // Get the Post's Administrative Division
        if (config('country.admin_field_active') == 1 && in_array(config('country.admin_type'), ['1', '2'])) {
            if (!empty($post->city)) {
                $adminType = config('country.admin_type');
                $adminModel = '\App\Models\SubAdmin' . $adminType;
                
                // Get the City's Administrative Division
                $admin = $adminModel::where('code', $post->city->{'subadmin' . $adminType . '_code'})->first();
                if (!empty($admin)) {
                    view()->share('admin', $admin);
                }
            }
        }
        
        // Meta Tags
        MetaTag::set('title', t('Update My Ad'));
        MetaTag::set('description', t('Update My Ad'));
        
        return view('post.edit', $data);
    }
    
    /**
     * Update the Post
     *
     * @param $postIdOrToken
     * @param PostRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postUpdateForm($postIdOrToken, PostRequest $request)
    {
        // Get Post
		

		  
		// dd($request);
        if (getSegment(2) == 'create') {
            if (!session()->has('tmpPostId')) {
                return redirect('posts/create');
            }
            $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('id', session('tmpPostId'))
				->where('tmp_token', $postIdOrToken)
				->first();
        } else {
            $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('user_id', auth()->user()->id)
				->where('id', $postIdOrToken)
				->first();
        }
        
        if (empty($post)) {
            abort(404);
        }
        
        // Get the Post's City
		/* Begin of code made by MonTech Team */
		if($request->input('city_id')){
			$cityArray = explode(',',$request->input('city_id'));
			
// 		$cityObj = City::where('name', '=',$cityArray[0])->first();
			$cityObj = City::where('id', '=',$cityArray[0])->first();
			if (empty($cityObj)) {
				flash(t("Posting Ads was disabled for this time. Please try later. Thank you."))->error();
				return back()->withInput();
			}	
		}
		/* End of code made by MonTech Team */
		
        
        // Conditions to Verify User's Email or Phone
        $emailVerificationRequired = config('settings.mail.email_verification') == 1 && $request->filled('email') && $request->input('email') != $post->email;
        $phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $request->filled('phone') && $request->input('phone') != $post->phone;
        
        // Update Post
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
		$post->main_catogery_id = $m_cat_id;
        
		foreach ($input as $key => $value) {
			$post->{$key} = $value;
		}
		
		/*
		 * Allow admin users to approve the changes,
		 * If the ads approbation option is enable,
		 * And if important data have been changed.
		 */
		if (config('settings.single.posts_review_activation')) {
			if (
				md5($post->title) != md5($request->input('title')) ||
				md5($post->description) != md5($request->input('description'))
			) {
				$post->reviewed = 0;
			}
		}
		
        $post->negotiable = $request->input('negotiable');
		
		if(empty($request->input('from_email'))){$email_hidden=1;}else{$email_hidden=0;}
		if(empty($request->input('from_phone'))){$phone_hidden=1;}else{$phone_hidden=0;}
		
		$post->email_hidden = $email_hidden;
		$post->phone_hidden = $phone_hidden;
		
		
		//$post->phone_hidden = $request->input('phone_hidden');
		/* Begin of code made by MonTech Team */
		$post->city_id = $cityObj->id;
		$post->lat = $cityObj->latitude;
		$post->lon = $cityObj->longitude;
		/* End of code made by MonTech Team */
        $post->ip_addr = Ip::get();
        
        // Email verification key generation
        if ($emailVerificationRequired) {
            $post->email_token = md5(microtime() . mt_rand());
            $post->verified_email = 0;
        }
        
        // Phone verification key generation
        if ($phoneVerificationRequired) {
            $post->phone_token = mt_rand(100000, 999999);
            $post->verified_phone = 0;
        }
        
        // Save
        $post->save();
    
        // Custom Fields
        $this->createPostFieldsValues($post, $request);
        
        // Get Next URL
        $creationPath = (getSegment(2) == 'create') ? 'create/' : '';
		$nextStepUrl = config('app.locale') . '/posts/' . $creationPath . $postIdOrToken . '/photos';
        
        // Send Email Verification message
        if ($emailVerificationRequired) {
            $this->sendVerificationEmail($post);
            $this->showReSendVerificationEmailLink($post, 'post');
        }
        
        // Send Phone Verification message
        if ($phoneVerificationRequired) {
            // Save the Next URL before verification
            session(['itemNextUrl' => $nextStepUrl]);
            
            $this->sendVerificationSms($post);
            $this->showReSendVerificationSmsLink($post, 'post');
            
            // Go to Phone Number verification
            $nextStepUrl = config('app.locale') . '/verify/post/phone/';
        }



		
		   
		   
    $query_update =  \DB::table('posts')
           ->where('id', $post->id)
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
            if ($package->price > 0 && $request->filled('payment_method_id') && !$alreadyPaidPackage) {
                if($request->payment_method_id == 2)
                {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,"https://www.hesabe.com/authpost");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                                "MerchantCode=8861618&Amount=0.350&SuccessUrl=".url(config('app.locale').'/post/hesabe-success')."&FailureUrl=".url(config('app.locale').'/post/hesabe-cancel')."&Variable1=$post->id&Variable3=$request->package_id");
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
                    return $this->sendPayment($request, $post);    
                }
                
            }
        }

        // IF NO PAYMENT IS MADE (CONTINUE)

        flash(t("Your ad has been updated."))->success();
        $nextStepUrl = config('app.locale') . '/posts/'.$post->id.'/photos';
            // $nextStepUrl = config('app.locale') . '/' . $post->uri . '?preview=1';
        
		
		//abdelhay make catogry id all time  english one 
		 $org_cat = \DB::table('categories')
		    ->where('id','=',$request->input('category_id'))
           ->select('translation_of')
           ->first();
		  $category_id= $org_cat->translation_of;

          $query_update =  \DB::table('posts')
           ->where('id', $post->id)
           ->update(['category_id' => $category_id]);
		   
        // Redirection
        return redirect($nextStepUrl);
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
	
	public function postUpdateForm_app($postIdOrToken, PostRequest $request)
    {
		
		if(empty($request->userid)){$request->userid=$request->user_id;}
		
		
		 $getlocation = $this->getlocationcity($request->input('city_name'));
	   
		$lat = !empty($getlocation['lat'])?$getlocation['lat']:0;
	    $lng = !empty($getlocation['lng'])?$getlocation['lng']:0;
		
		
        // Get Post
        if (getSegment(2) == 'create') {
            if (!session()->has('tmpPostId')) {
                return redirect('posts/create');
            }
            $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('id', session('tmpPostId'))
				->where('tmp_token', $postIdOrToken)
				->first();
        } else {
            $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('user_id', $request->userid)
				->where('id', $postIdOrToken)
				->first();
        }
		
		//dd($post);
        
        if (empty($post)) {
            abort(404);
        } 
        
		$country_code='KW';
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
       values ("'.$country_code.'", "'.$request->input('city_name').'", "'.$request->input('city_name').'", "'.$lat.'", "'.$lng.'", "'.$subadmin1_code.'", "'.$subadmin2_code.'", 1, "'.$timezon.'", "'.date('Y-m-d H:i:s').'", "'.date('Y-m-d H:i:s').'")');
       
        $city_id = \DB::getPdo()->lastInsertId();
    
    	// Get the Post's City
		$cityObj = City::find($city_id);
		if (empty($cityObj)) {
			//flash(t("Posting Ads was disabled for this time. Please try later. Thank you."))->error();
			return response()->json(['results'=>"Posting Ads was disabled for this time. Please try later. Thank you."]);
			//return back()->withInput();
		}
		
		
		
        // Get the Post's City
		/* Begin of code made by MonTech Team */
	/*	if($request->input('city_id')){
			$cityArray = explode(',',$request->input('city_id'));
			
// 		$cityObj = City::where('name', '=',$cityArray[0])->first();
			$cityObj = City::where('id', '=',$cityArray[0])->first();
			if (empty($cityObj)) {
				//flash(t("Posting Ads was disabled for this time. Please try later. Thank you."))->error();
				//return back()->withInput();
				return response()->json(['results'=>'Posting Ads was disabled for this time. Please try later. Thank you.']);
			}	
		}*/
		/* End of code made by MonTech Team */
		
        
        // Conditions to Verify User's Email or Phone
        $emailVerificationRequired = config('settings.mail.email_verification') == 1 && $request->filled('email') && $request->input('email') != $post->email;
	    $phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $request->filled('phone') && $request->input('phone') != $post->phone;
	    //$emailVerificationRequired =0;
        //$phoneVerificationRequired =0;
        
        // Update Post
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
		$post->main_catogery_id = $m_cat_id;

		foreach ($input as $key => $value) {
			$post->{$key} = $value;
		}
		
		/*
		 * Allow admin users to approve the changes,
		 * If the ads approbation option is enable,
		 * And if important data have been changed.
		 */
		if (config('settings.single.posts_review_activation')) {
			if (
				md5($post->title) != md5($request->input('title')) ||
				md5($post->description) != md5($request->input('description'))
			) {
				$post->reviewed = 0;
			}
		}
		
        $post->negotiable = $request->input('negotiable');
		$post->phone_hidden = $request->input('phone_hidden');
		/* Begin of code made by MonTech Team */
		$post->city_id = $cityObj->id;
		$post->lat = $cityObj->latitude;
		$post->lon = $cityObj->longitude;
		/* End of code made by MonTech Team */
        $post->ip_addr = Ip::get();
        
        // Email verification key generation
        if ($emailVerificationRequired) {
            $post->email_token = md5(microtime() . mt_rand());
            $post->verified_email = 0;
        }
        
        // Phone verification key generation
        if ($phoneVerificationRequired) {
            $post->phone_token = mt_rand(100000, 999999);
            $post->verified_phone = 0;
        }
        
        // Save
        $post->save();
    
        // Custom Fields
        $this->createPostFieldsValues($post, $request);
        
        // Get Next URL
        $creationPath = (getSegment(2) == 'create') ? 'create/' : '';
		flash(t("Your ad has been updated."))->success();
		$nextStepUrl = config('app.locale') . '/posts/' . $creationPath . $postIdOrToken . '/photos';
        
        // Send Email Verification message
        if ($emailVerificationRequired) {
            $this->sendVerificationEmail($post);
            $this->showReSendVerificationEmailLink($post, 'post');
        }
        
        // Send Phone Verification message
        if ($phoneVerificationRequired) {
            // Save the Next URL before verification
            session(['itemNextUrl' => $nextStepUrl]);
            
            $this->sendVerificationSms($post);
            $this->showReSendVerificationSmsLink($post, 'post');
            
            // Go to Phone Number verification
            $nextStepUrl = config('app.locale') . '/verify/post/phone/';
        }
        
        // Redirection
        //return redirect($nextStepUrl);
		return response()->json(['results'=>'Post Data has been updated']);
    }
	
	
	
}
