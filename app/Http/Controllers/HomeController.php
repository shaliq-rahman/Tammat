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



namespace App\Http\Controllers;







use Illuminate\Http\Request;

use Illuminate\Support\Facades\Password;

use App\Http\Controllers\Post\Traits\CustomFieldTrait;

use Illuminate\Pagination\LengthAwarePaginator;

use App\Helpers\Arr;

use App\Helpers\Search;

use App\Helpers\DBTool;

use App\Models\Post;

use App\Models\Category;

use App\Models\HomeSection;

use App\Models\SubAdmin1;

use App\Models\City;

use App\Models\User;

use App\Models\Newsletter;

use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Session;

use Torann\LaravelMetaTags\Facades\MetaTag;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;

use App\Helpers\Localization\Country as CountryLocalization;

use LaravelLocalization;



class HomeController extends FrontController

{

    use CustomFieldTrait;

   //  use SendsPasswordResetEmails {

   //     sendResetLinkEmail as public traitSendResetLinkEmail;

   // }

   

    /**

     * HomeController constructor.

     */

    public function __construct()

    {

        parent::__construct();



        // Check Country URL for SEO

        $countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());

        view()->share('countries', $countries);

    }





    

    public function getSearch_app(Request $request){

        $paymentJoin = '';

        $sponsoredCondition = '';

        $sponsoredOrder = '';

        $productData = $request->productData;

		$user_id = $request->user_id;

		$category = $request->category;

		$minprice = $request->minprice;

		$maxprice = $request->maxprice;

		$distance = $request->distance;

		$country = strtoupper($request->country);

		$language = $request->language;

        $lang = $request->language;

        $sortBy = $request->sortBy;

        $sallerType = $request->sallerType;

        $allAds = $request->allAds;

        if($allAds=="ads with images"){

            $allAds = "ads with images";

        }

        else{

           $allAds = ''; 

        }

        if($sortBy=="LtoH"){

            $order = "ASC";

        }

        else if($sortBy=="HtoL"){

             $order = "DESC";

        }

        else{

          $order = "";  

        }

        if($sallerType=="Individual"){

            $sallerType = 'and post_type_id=1';

        }

        else if($sallerType=="Shop"){

           $sallerType = 'and post_type_id=2'; 

        }

        else{

         $sallerType = ""; 

        }

        

        /*$sql = 'SELECT * from posts where price between '.$minprice.' and '.$maxprice.' and category_id in (select id from categories where parent_id='.$category.' and active=1) and country_code="'.$country.'" and archived!=1 ';*/

	 if($productData == "latest"){

           $sql = 'SELECT * FROM posts WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and country_code="'.$country.'" and archived!=1'; 

        }

        else if($productData == "all"){

            $sql = 'SELECT * FROM posts WHERE country_code="'.$country.'" and archived!=1'; 

        }

        else if($productData == "popular"){

          $sql = 'SELECT * FROM posts WHERE country_code="'.$country.'" and archived!=1 ORDER BY visits DESC';  

        }

        else if($productData == "susponser"){

            $sql = 'SELECT * FROM posts WHERE premium_email!="" and country_code="'.$country.'" and archived!=1 ORDER BY visits DESC';

        }

        $bindings = [

            //'countryCode' => $country,

        ];

		

		//echo $sql;



        //$cacheId = $country_code . '.home.getPosts.' . $type;

        $posts = DB::select($sql);



        //return $posts;

		$i=0;

		foreach($posts as $key => $post){

		    

		                        

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

                            

					                $post->currency = $get_currency;

						                

						              

									  

									  $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $post->contact_name = $getusernamedetail->username;     

		    

		    

		    

		        	                    /*$package = '';

										if ($post->featured == 1) {

											$package = \App\Models\Package::findTransApp($post->py_package_id,$lang);

										}

										

										

										if(!empty($post->py_package_id))

										{

								        $post->py_package_id = $post->py_package_id;

								        }

										else

										{

										$post->py_package_id = 'No Value';

										}

										

										

										if(!empty($package))

										{

								        $post->package = $package;

								        }

										else

										{

										$post->package = 'No Value';

										}*/

								        

								        

										

										$postType = \App\Models\PostType::findTransApp($post->post_type_id, $lang);

											//return $postType;

										$post->postType = $postType;

										

										

							

										// Get Post's Pictures

										$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

										if ($pictures->count() > 0) {

											$postImg = resize($pictures->first()->filename, 'medium');

										} else {

										    

											$postImg = resize(config('larapen.core.picture.default'));

										}

										$post->postImg = $postImg;

										

										$city = \App\Models\City::find($post->city_id);

										$post->cityy = $city;

										

										

										

										$post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));

										$post->created_at = $post->created_at->ago();

										

										$liveCat = \App\Models\Category::findTransApp($post->category_id, $lang);

											//return $liveCat;

										$post->liveCat = $liveCat;

										

										// Check parent

										if (empty($liveCat->parent_id)) {

											$liveCatParentId = $liveCat->id;

											$liveCatType = $liveCat->type;

										} else {

											$liveCatParentId = $liveCat->parent_id;

											

											$liveParentCat = \App\Models\Category::findTransApp($liveCat->parent_id, $language);

											//echo $liveParentCat;

											

										if(isset($language))

										{

										$lang1 = $language;

										}

										else

										{

										$lang1 = 'en';

										}	

											$bindings = [

            'translation_lang' => $language,

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

										

										

										

										

										

										

											

                                			

										

										//$post->paymentpre = $results;	

										

										

										

										

										

										

							            $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $username = $getusernamedetail->username;  

										$user_created_at = $getusernamedetail->created_at;      

										$post->liveCatName = $liveCatName;

										$post->username = $username;

										//$post->user_created_at = $user_created_at;

										

										$post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));

										$post->user_created_at = $post->user_created_at->ago();

										

										

										

										if (!empty($user_id))

													{

													$scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post->id)->count();

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

		

		

		// Append the Posts 'uri' attribute

       /* $posts = collect($posts)->map(function ($post) {

            

            return t($post);

        })->toArray();

		*/

		

// 		print_r($posts);

// 		if(empty($posts[$key][''])) {

//     //this means value does not exist or is FALSE

// }

		//return $posts;

		return response()->json(['status'=>1,'message'=>'success','results'=>$posts]);

		

		

    }





	public function countries_app()

    {

     



        \App::setLocale(request()->get('language') ?: 'en');

	     $countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());

	 

	   // $i=0;

		foreach($countries as $country){		

		//$code = strtolower($country->first());

		// $country->flag = url('images/flags/32/'.$code.'.png') . getPictureVersion();

		//$country->$country->first()->flag="https://tmmat.com/images/flags/32/".$code.".png";

		//$i++;

		 $country['icode']=url('images/flags/32/'.$country['icode'].'.png') . getPictureVersion();

		 

		} 

		

		 

				

		$results = array();

		foreach($countries as $index=>$key) 

		{

		  

		 array_push($results, $key);

		}

		

		

		

	return response()->json(['results'=>$results]);

	}

	

	

	

		public function switchlang($lang)

    {

  // so we can redir back to where the user was

  $url   = url()->previous();

    $url_explode = explode("/",$url);

  $url_explode2 = explode("/",$url);

  $url_explode[3] = $lang;

  $redir = implode('/',$url_explode);

$change = $url_explode2[3];

$last = str_replace($change, $lang, $redir);

dd($last);

  return redirect($last);

	}

	

	

	

	public function getCat_app(Request $request)

    {

		$bindings = [

            'translation_lang' => $request->translation_lang,

        ];

		

		$sql = 'SELECT * FROM categories WHERE parent_id=0 and active=1 AND translation_lang="'.$request->translation_lang.'"  order by lft ';

        $categories = DB::select(DB::raw($sql), $bindings);

        $cats = DB::select(DB::raw($sql), $bindings);

		$res = array();

        $sArr = array();

        $subfinal = array();

        

        $sm = 0;

        $total = 0;

        // foreach ($cats->groupBy('parent_id')->get(0) as $iCat){

        //   echo $countCatPosts->get($iCat->tid)->total;

        // }

		

         

            $total_product=0;

            foreach($categories as $key => $category){

                

               // $total_product=$this->GetProductCountByCatid($category->translation_of,$request->countryCode);

               $total_product=$this->GetProductCountByCatid_new($category->translation_of,$request->countryCode);

                $category->total_product=$total_product;

               //$category->total_product='';





		    $mSum = 0; 

		   // echo "main:". $category->id;

		   	if($category->picture!="")

    		{

    		    $picture =  \Storage::url($category->picture) . getPictureVersion();

    		}

    		else

    		{

    		    $picture = 'https://www.tmmat.com/storage/app/default/categories/fa-folder-skin-blue.png';

    		}

//     		if(isset($countSubCatPosts->get($category->id)->total)){

// 		    echo $total +=  $countSubCatPosts->get($category->id)->total;

// 		}

		//$productSql = 'select * from posts where category_id = "'.$category->translation_of.'" AND country_code ="'.$request->country_code.'"';

       // $products = DB::select(DB::raw($productSql));

       // $mSum += count($products);

		$sql = 'select * from categories where parent_id = "'.$category->translation_of.'" and translation_lang="'.$request->translation_lang.'"  order by lft ';

        $subCategories = DB::select(DB::raw($sql), $bindings);

        

       /* foreach($subCategories as $subC){

            // echo "subC:". $subC->id.'<br>';

            $productSql1 = 'select * from posts where category_id = "'.$subC->translation_of.'" AND country_code="'.$request->country_code.'"';

        $products1 = DB::select(DB::raw($productSql1));

        //print_r($products);

        $mSum += count($products1);

       		$sql2 = 'select * from categories where parent_id = "'.$subC->translation_of.'" and translation_lang="'.$request->translation_lang.'"  order by lft ';

            $subCategories2 = DB::select(DB::raw($sql2), $bindings);

            if(!empty($subCategories2)){

                foreach($subCategories2 as $subC){

                     $productSql2 = 'select * from posts where category_id = "'.$subC->translation_of.'" and country_code="'.$request->country_code.'"';

                $products2 = DB::select(DB::raw($productSql2));

                //print_r($products);

               $mSum += count($products2);

                }

            }

        }*/

        //$mSum+=$sm;

       // print_r($subCategories);

if($category->translation_lang==""){

    $translation_lang = "";

}

else{

    $translation_lang = $category->translation_lang;

}

if($category->translation_of==""){

    $translation_of = "";

}

else{

    $translation_of = $category->translation_of;

}

if($category->name==""){

    $name = "";

}

else{

    $name = $category->name;

}

if($category->description==""){

    $description = "";

}

else{

    $description = $category->description;

}

if($category->icon_class==""){

    $icon_class = "";

}

else{

    $icon_class = $category->icon_class;

}

if($category->type==""){

    $type = "";

}

else{

    $type = $category->type;

}

 unset($subCategories['description']);



 $res[] = array(

		       'id'=>$category->id,

		       'translation_lang'=>$category->translation_lang,

		       'translation_of'=>$category->translation_of,

		       'parent_id'=>$category->parent_id,

		       'name'=>$category->name,

		       'slug'=>$category->slug,

		       'description'=>$description,

		       'picture'=>$picture,

		       'icon_class'=>$icon_class,

		       'lft'=>$category->lft,

		       'rgt'=>$category->rgt,

		       'depth'=>$category->depth,

		       'type'=>$type,

		       'active'=>$category->active,

		      // 'totalProduct'=>$mSum,

              'totalProduct'=>$category->total_product,

		       'subCat'=>$subCategories

		       );

	



		}



		return response()->json(['status'=>1,'message'=>'success','results'=>$res]);

    }









	

	public function GetMainCat_app(Request $request)

    {

		$bindings = [

            'translation_lang' => $request->translation_lang,

        ];

		

		$sql = 'SELECT * FROM categories WHERE parent_id=0 and active=1 AND translation_lang="'.$request->translation_lang.'"  order by lft ';

        $categories = DB::select(DB::raw($sql), $bindings);

        $cats = DB::select(DB::raw($sql), $bindings);

		$res = array();

        $sArr = array();

        $subfinal = array();

        

        $sm = 0;

        $total = 0;

		$total_product=0;

		foreach($categories as $key => $category){

			

			if($category->picture!="")

    		{

    		    $picture =  \Storage::url($category->picture) . getPictureVersion();

    		}

    		else

    		{

    		    $picture = 'https://www.tmmat.com/storage/app/default/categories/fa-folder-skin-blue.png';

    		}

     	 

		

        

      

if($category->translation_lang==""){$translation_lang = "";}else{$translation_lang = $category->translation_lang;}

if($category->translation_of==""){$translation_of = "";}else{$translation_of = $category->translation_of;}

if($category->name==""){$name = "";}else{$name = $category->name;}

if($category->description==""){$description = "";}else{$description = $category->description;}

if($category->icon_class==""){$icon_class = "";}else{$icon_class = $category->icon_class;}

if($category->type==""){$type = "";}else{$type = $category->type;}

 



 $res[] = array(

		       'id'=>$category->id,

		       //'translation_lang'=>$category->translation_lang,

		       'translation_of'=>$category->translation_of,

		      //'parent_id'=>$category->parent_id,

		       'name'=>$category->name,

		      //'slug'=>$category->slug,

			   'picture'=>$picture,

		      // 'description'=>$description,		       

		      // 'icon_class'=>$icon_class,

		      // 'lft'=>$category->lft,

		      // 'rgt'=>$category->rgt,

		       //'depth'=>$category->depth,

		       //'type'=>$type,

		       //'active'=>$category->active 

		     

              

		       );

	



		}



		return response()->json(['results'=>$res]);

    }









public function getSubCategoriesNew_app(Request $request)

    {

		$bindings = [

            'translation_lang' => $request->translation_lang,

			'translation_of' => $request->translation_of,

        ];

		

		$sql = 'SELECT * FROM categories WHERE parent_id="'.$request->translation_of.'" and active=1 AND translation_lang="'.$request->translation_lang.'"  order by lft ';

        $categories = DB::select(DB::raw($sql), $bindings);

        $cats = DB::select(DB::raw($sql), $bindings);

		$res = array();

        $sArr = array();

        $subfinal = array();

        

        $sm = 0;

        $total = 0;

		$total_product=0;

		foreach($categories as $key => $category){

			

			

			$child=$this->check_cat_child($category->translation_of,$category->translation_lang);

			

			if($category->picture!="")

    		{

    		    $picture =  \Storage::url($category->picture) . getPictureVersion();

    		}

    		else

    		{

    		    $picture = 'https://www.tmmat.com/storage/app/default/categories/fa-folder-skin-blue.png';

    		}

     	 

		

        

      

if($category->translation_lang==""){$translation_lang = "";}else{$translation_lang = $category->translation_lang;}

if($category->translation_of==""){$translation_of = "";}else{$translation_of = $category->translation_of;}

if($category->name==""){$name = "";}else{$name = $category->name;}

if($category->description==""){$description = "";}else{$description = $category->description;}

if($category->icon_class==""){$icon_class = "";}else{$icon_class = $category->icon_class;}

if($category->type==""){$type = "";}else{$type = $category->type;}

 



 $res[] = array(

		       'id'=>$category->id,

		       //'translation_lang'=>$category->translation_lang,

		       'translation_of'=>$category->translation_of,

		      //'parent_id'=>$category->parent_id,

		       'name'=>$category->name,

		      //'slug'=>$category->slug,

			   'picture'=>$picture,

			    'child'=>$child,

		      // 'description'=>$description,		       

		      // 'icon_class'=>$icon_class,

		      // 'lft'=>$category->lft,

		      // 'rgt'=>$category->rgt,

		       //'depth'=>$category->depth,

		       //'type'=>$type,

		       //'active'=>$category->active 

		     

              

		       );

	



		}



		return response()->json(['results'=>$res]);

    }







   public function check_cat_child($cat_id,$lang)

    {

		$bindings = ['cat_id' => $cat_id,'lang' => $lang];		

		$sql = 'SELECT * FROM categories WHERE parent_id="'.$cat_id.'" and active=1 AND translation_lang="'.$lang.'"  order by lft ';

        $categories = DB::select(DB::raw($sql), $bindings);		

	    $cat_names=array();

		foreach($categories as $category){

			$cat_names[]=$category->name;

			}			

			

       return $cat_names;

    }











    public function cat_Product_count_app(Request $request)

    {



        $subCatId=$request->category_id;

        if(!empty($subCatId)){

            $searchCat = Category::find($subCatId);

            $mainID = Category::where('id', $searchCat->translation_of)->first();

            $IDsList = $this->getAllSubs($mainID->id, $searchCat->translation_lang);

            $catId = '(category_id='.$mainID->id;

            foreach($IDsList as $key){

                $catId .= ' or category_id='.$key;

            }

        //    $catId = 'category_id='.$subCatId.' and';

           $catId .= ')';

        }

        else{

           $catId = ' category_id = "'.$request->category_id.'" '; 

        }



        $productSql = 'select * from posts where '.$catId.'

        and country_code="'.$request->countryCode.'" and archived!=1';

        $products = DB::select(DB::raw($productSql)); 



        $total_products= count($products);

        return response()->json(['status'=>1,'message'=>'success','total_products'=>$total_products]);

    }



    public function GetProductCountByCatid($category_id,$countryCode)

    {



        $subCatId=$category_id;

        if(!empty($subCatId)){

            $searchCat = Category::find($subCatId);

            $mainID = Category::where('id', $searchCat->translation_of)->first();

            $IDsList = $this->getAllSubs($mainID->id, $searchCat->translation_lang);

            $catId = '(category_id='.$mainID->id;

            foreach($IDsList as $key){

                $catId .= ' or category_id='.$key;

            }

        //    $catId = 'category_id='.$subCatId.' and';

           $catId .= ')';

        }

        else{

           $catId = ' category_id = "'.$category_id.'" '; 

        }



        $productSql = 'select * from posts where '.$catId.'

        and country_code="'.$countryCode.'" and archived!=1';

        $products = DB::select(DB::raw($productSql)); 

        $total_products= count($products);

        return $total_products;

    }







    public function GetProductCountByCatid_new($category_id,$countryCode)

    {



         

        $productSql = 'select * from posts where main_catogery_id="'.$category_id.'"

        and country_code="'.$countryCode.'" and archived!=1';

        $products = DB::select(DB::raw($productSql)); 

        $total_products= count($products);

        return $total_products;

    }









    function filter_callback($element) {

        if (isset($element->category_id) && $element->category_id == '1') {

          return TRUE;

        }

        return FALSE;

      }





    public function nextSubcatProduct_app(Request $request)

    {

        $data = array();

        // $categories = Category::where('translation_lang', $request->translation_lang)->where('parent_id', $request->category_id)->orderBy('lft')->get();

		$bindings = [

            'translation_lang' => $request->translation_lang,

        ];

		$sql = 'select * from categories where parent_id = "'.$request->category_id.'"  and translation_lang="'.strtolower($request->translation_lang).'"  order by lft ';

        $categories = DB::select(DB::raw($sql), $bindings);



       

        $subCatId=$request->category_id;

        if(!empty($subCatId)){

            $searchCat = Category::find($subCatId);

            $mainID = Category::where('id', $searchCat->translation_of)->first();

            $IDsList = $this->getAllSubs($mainID->id, $searchCat->translation_lang);

            $catId = '(category_id='.$mainID->id;

            foreach($IDsList as $key){

                $catId .= ' or category_id='.$key;

            }

        //    $catId = 'category_id='.$subCatId.' and';

           $catId .= ')';

        }

        else{

           $catId = ' category_id = "'.$request->category_id.'" '; 

        }



       // $productSql = 'select * from posts where category_id = "'.$request->category_id.'" 

       // and country_code="'.$request->countryCode.'" and archived!=1';

        

       

        $productSql = 'select * from posts where '.$catId.'

        and country_code="'.$request->countryCode.'" and archived!=1';

        

        $products = DB::select(DB::raw($productSql)); 

        //print_r($products);

        $total_products=count($products);

        foreach($products as $key => $value){

            $pId = $value->id;

            $imgSql = 'select * from pictures where post_id = "'.$pId.'"';

            $img = DB::select(DB::raw($imgSql)); 

            $des = $value->description;

            $des = strip_tags($des);

             $getcurrencycountry = \DB::table('countries')

                                            ->join('currencies', 'currencies.code', '=', 'countries.currency_code')

                                            ->select('currencies.*')

                                            ->where('countries.code', '=', $value->country_code)

                                            ->first();

                                            //print_r($getcurrencycountry);

            $data[] = array(

                'id' =>  $pId,

                'country_code' =>  $value->country_code,

                'user_id' =>  $value->user_id,

                'category_id' =>  $value->category_id,

                'post_type_id' =>  $value->post_type_id,

                'title' =>  $value->title,

                'description' => $des,

                'tags' =>  $value->tags,

                'price' =>  $getcurrencycountry->font_code2000.' '.$value->price,

                'negotiable' =>  $value->negotiable,

                'contact_name' =>  $value->contact_name,

                'email' =>  $value->email,

                'phone' =>  $value->phone,

                'address' =>  $value->address,

                'city_id' =>  $value->city_id,

                'city_name' =>  $value->city_name,

                'lon' =>  $value->lon,

                'lat' =>  $value->lat,

                'created_at' =>  $value->created_at,

                'total_products' =>  $total_products,

                'picture' =>  $img

            );

        }

       // print_r($product);  



      





       $total_product=0;

        foreach($categories as $key => $category):

           // $arr = array_filter($data, 'filter_callback');

           // $category->total_product =  count($arr);

            $total_product=$this->GetProductCountByCatid($category->translation_of,$request->countryCode);

            $category->total_product=$total_product;



    		if($category->picture!="")

    		{

    		    $category->picture =  \Storage::url($category->picture) . getPictureVersion();

    		}

    		else

    		{

    		    $category->picture = 'https://www.tmmat.com/storage/app/default/categories/fa-folder-skin-blue.png';

    		}

		endforeach;

        

        

      

      

         



        $final = array_values($categories);

       

	

	   // if(!empty($final)){

	   //    $product = array(); 

	   // }

		

		return response()->json(['status'=>1,'message'=>'success','subCat'=>$final,'total_products' =>  $total_products,'product'=>$data]);

    }





    public function getprofile(Request $request)

	{

		$userdata=array();

		return response()->json(['results'=>$userdata]);

	}





    public function getMyPosts_new(Request $request)

    {

        $myPosts = Post::where('userid', $request->user_id)

            ->currentCountry()

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



	 public function getdetails_new(Request $request)

	{

		

		//$userdata = User::find($request->userid);

			$userdata = DB::table('users')

			->select('*')

			->where('id', $request->userid)

			->first();

		if(empty($userdata)){

		    return response()->json(['results'=>'Invalid User']);

		}

		else{

		$userdata = DB::table('users')->where('id','=',$request->userid)->get();

		foreach($userdata as $u){





            $userdata[0]->phone_hidden=$u->phone_hidden?$u->phone_hidden:0;

            $userdata[0]->email_hidden=$u->email_hidden?$u->email_hidden:0;

            $userdata[0]->profile_image_hidden=$u->profile_image_hidden?$u->profile_image_hidden:0;  

		    $user_type_id = $u->user_type_id;







		         if($user_type_id==1){

		             $userdata[0]->user_type = "Individual";

		         }

		         elseif($user_type_id==2){

		             $userdata[0]->user_type = "Shop";

		         }

		         else{

		           $userdata[0]->user_type = "";  

		         }

		   $country_code = $u->country_code;  

		    $country = DB::table('countries')->where('code','=',$country_code)->get();

		    //print_r($country);

		    foreach($country as $c){

		         $cname = $c->name;

		        // $userdata['country'] = $cname;

		        if(!empty($cname)){

		            $userdata[0]->country = $cname;

		        }

		        else{

		            $userdata[0]->country = "";

		        }

		    }

		  

		}

		

        

		$userdata[0]->filename = 'https://secure.gravatar.com/avatar/4a8459b084c7ad587603ed458392690d.jpg';

		return response()->json(['results'=>$userdata]);

		}

	}

		

     

    

	public function language_app()

    {

	$language = LaravelLocalization::getSupportedLocales();

	

	$results = array();

		foreach($language as $index=>$key) 

		{

		 array_push($results, $key);

		}

	

	return response()->json(['results'=>$results]);

	}

    /**

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View

     */

    public function index()

    {

        $data = [];

         $data['nav_style']="position: fixed !important;"; 

        // Get all homepage sections

        $data['sections'] = Cache::remember('homeSections', $this->cacheExpiration, function () {

            $sections = HomeSection::orderBy('lft')->get();



            return $sections;

        });



        if ($data['sections']->count() > 0) {

            foreach ($data['sections'] as $section) {

                // Check if method exists

                if (!method_exists($this, $section->method)) {

                    continue;

                }



                // Call the method

                try {

                    if (isset($section->options)) {

                        $this->{$section->method}($section->options);

                    } else {

                        $this->{$section->method}();

                    }

                } catch (\Exception $e) {

                    flash($e->getMessage())->error();

                    continue;

                }

            }

        }



        // Get SEO

        $this->setSeo();



        // Check and load Reviews plugin

        $reviewsPlugin = load_installed_plugin('reviews');

        view()->share('reviewsPlugin', $reviewsPlugin);

        // echo "<pre>";

        // print_r($data);

        // exit;

        return view('home.index', $data);

    }



    /**

     * Get search form (Always in Top)

     *

     * @param array $options

     */

    protected function getSearchForm($options = [])

    {

        view()->share('searchFormOptions', $options);

    }



    /**

     * Get locations & SVG map

     *

     * @param array $options

     */

    protected function getLocations($options = [])

    {

        // Get the default Max. Items

        $maxItems = 14;

        if (isset($options['max_items'])) {

            $maxItems = (int)$options['max_items'];

        }



        // Get the Default Cache delay expiration

        $cacheExpiration = $this->getCacheExpirationTime($options);



        // Modal - States Collection

        $cacheId = config('country.code') . '.home.getLocations.modalAdmins';

        $modalAdmins = Cache::remember($cacheId, $cacheExpiration, function () {

            $modalAdmins = SubAdmin1::currentCountry()->orderBy('name')->get(['code', 'name'])->keyBy('code');



            return $modalAdmins;

        });

        view()->share('modalAdmins', $modalAdmins);



        // Get cities

        $cacheId = config('country.code') . 'home.getLocations.cities';

        $cities = Cache::remember($cacheId, $cacheExpiration, function () use ($maxItems) {

            $cities = City::currentCountry()->take($maxItems)->orderBy('population', 'DESC')->orderBy('name')->get();



            return $cities;

        });

        $cities = collect($cities)->push(Arr::toObject([

            'id' => 999999999,

            'name' => t('More cities') . ' &raquo;',

            'subadmin1_code' => 0,

        ]));



        // Get cities number of columns

        $nbCol = 4;

        if (file_exists(config('larapen.core.maps.path') . strtolower(config('country.code')) . '.svg')) {

            if (isset($options['show_map']) and $options['show_map'] == '1') {

                $nbCol = 3;

            }

        }



        // Chunk

        $cols = round($cities->count() / $nbCol, 0); // PHP_ROUND_HALF_EVEN

        $cols = ($cols > 0) ? $cols : 1; // Fix array_chunk with 0

        $cities = $cities->chunk($cols);



        view()->share('cities', $cities);

        view()->share('citiesOptions', $options);

    }

	

	

	protected function getLocations_app()

    {

			$cities = City::currentCountry()->orderBy('population', 'DESC')->orderBy('name')->get();

			return response()->json(['results'=>$cities]);

    }



    /**

     * Get sponsored posts

     *

     * @param array $options

     */

    protected function getSponsoredPosts($options = [])

    {

        // Get the default Max. Items

        $maxItems = 20;

        if (isset($options['max_items'])) {

            $maxItems = (int)$options['max_items'];

        }



        // Get the default orderBy value

        $orderBy = 'random';

        if (isset($options['order_by'])) {

            $orderBy = $options['order_by'];

        }



        // Get the default Cache delay expiration

        $cacheExpiration = $this->getCacheExpirationTime($options);



        $sponsored = null;



        // Get featured posts

        $posts = $this->getPosts($maxItems, 'sponsored', $cacheExpiration);



        if (!empty($posts)) {

            if ($orderBy == 'random') {

                $posts = Arr::shuffle($posts);

            }

            $attr = ['countryCode' => config('country.icode')];

            $sponsored = [

                'title' => t('Home - Sponsored Ads'),

                'link' => lurl(trans('routes.v-search', $attr), $attr),

                'posts' => $posts,

            ];

            $sponsored = Arr::toObject($sponsored);

        }



        view()->share('featured', $sponsored);

        view()->share('featuredOptions', $options);

    }

	

	protected function getSponsoredPosts_app(Request $request)

    {

        // Get the default Max. Items

        $maxItems = 20;

        



        // Get the default orderBy value

        $orderBy = 'random';



        $sponsored = null;

		$lang = $request->lang;

        // Get featured posts

        $posts = $this->getPosts1($request->user_id,$request->country_code,$lang, $maxItems, 'sponsored');



        if (!empty($posts)) {

            if ($orderBy == 'random') {

                $posts = Arr::shuffle($posts);

            }

        }

		$final = array_values($posts);

        return response()->json(['results'=>$final]);

    }



    /**

     * Get latest posts

     *

     * @param array $options

     */

    protected function getLatestPosts($options = [])

    {

        // Get the default Max. Items

        $maxItems = 12;

        if (isset($options['max_items'])) {

            $maxItems = (int)$options['max_items'];

        }



        // Get the default orderBy value

        $orderBy = 'date';

        if (isset($options['order_by'])) {

            $orderBy = $options['order_by'];

        }



        // Get the Default Cache delay expiration

        $cacheExpiration = $this->getCacheExpirationTime($options);



        // Get latest posts

        $posts = $this->getPosts($maxItems, 'latest', $cacheExpiration);

        

        $posts_popular = $this->getPostsPopular($maxItems, 'latest', $cacheExpiration);

        

        



        if (!empty($posts)) {

            if ($orderBy == 'random') {

                $posts = Arr::shuffle($posts);

            }

        }



        view()->share('posts', $posts);

        view()->share('posts_popular', $posts_popular);

        view()->share('latestOptions', $options);

    }





protected function getLatestPostsHomepage(Request $request)

    {

        // Get the default Max. Items

        $maxItems = 12;  

		// Get the default orderBy value

        $orderBy = 'date';

		$country_code = $request->country_code;

		$lang = $request->lang;

		$cat_id = $request->cat_id;

		$user_id = $request->user_id;

		$local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 'ar';

		// set laravel localization

		app()->setLocale($local);

		//$max = 12;

		

		

		

		$bindings = [

            'translation_lang' => $request->lang,

        ];

		

		$sql = 'select * from categories where parent_id="0" and active="1" and translation_lang="'.$request->lang.'" order by lft ';



        $categories = DB::select(DB::raw($sql), $bindings);

		$cat_posts=array();

		//translation_of

		$x=0;

		foreach($categories as $cat){

			$cat_posts[$x]['cat_id']=$cat->translation_of;

			$cat_posts[$x]['cat_name']=$cat->name;

			$cat_posts[$x]['cat_slug']=$cat->slug;

			$cat_posts[$x]['cat_picture']=$cat->picture;			

			$cat_posts[$x]['cat_active']=$cat->active;

			$cat_id=$cat->translation_of;

			$AllPosts = @$this->getPosts1_by_cat_id($cat_id,$country_code, $lang, $maxItems, 'latest');

            if (!empty($AllPosts)) {

            if ($orderBy == 'random') {

                $AllPosts = Arr::shuffle($AllPosts);

                }

            }

		

		   $AllPosts = array_values($AllPosts);

		   $filterAllPosts=array();

		   $w=0;

		   foreach($AllPosts as $post){

			  $filterAllPosts[$w]['id']=$post->id;

			  $filterAllPosts[$w]['title']=$post->title; 

			  $filterAllPosts[$w]['price']=$post->price; 

			  $filterAllPosts[$w]['formmated_price']=$post->currency;

			  $filterAllPosts[$w]['city_name']=$post->city_name; 

			  $filterAllPosts[$w]['created_at']=$post->created_at; 			   

			  $filterAllPosts[$w]['postImg']=$post->postImg; 

			  $filterAllPosts[$w]['featured']=$post->featured;

			  if(empty($user_id)){

			  $filterAllPosts[$w]['favourite']="0";

			  }else{

			   $filterAllPosts[$w]['favourite']= @$this->check_fav_post($post->id,$user_id);

			   }

			  

			  

			  $w++;

		   }

		      //$cat_posts[$x]['AllPosts']=$AllPosts;

			 $cat_posts[$x]['AllPosts']=$filterAllPosts;

			

			$x++;

			}

		return response()->json([

		'categories'=>$cat_posts]);

		

		//return response()->json(['AllPosts'=>$AllPosts]);

         

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



protected function getLatestPosts_appByCategory(Request $request)

    {

        // Get the default Max. Items

        $maxItems = 12;  

		// Get the default orderBy value

        $orderBy = 'date';

		$country_code = $request->country_code;

		$lang = $request->lang;

		$cat_id = $request->cat_id;

		$user_id = $request->user_id;

		$local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 'ar';

		// set laravel localization

		app()->setLocale($local);

		//$max = 12;

		$AllPosts = @$this->getPosts1_by_cat_id($cat_id,$country_code, $lang, $maxItems, 'latest');

        if (!empty($AllPosts)) {

            if ($orderBy == 'random') {

                $AllPosts = Arr::shuffle($AllPosts);

            }

        }

		$AllPosts = array_values($AllPosts);	 

		 

		

		

		   $AllPosts = array_values($AllPosts);

		   $filterAllPosts=array();

		   $w=0;

		   foreach($AllPosts as $post){

			  $filterAllPosts[$w]['id']=$post->id;

			  $filterAllPosts[$w]['title']=$post->title; 

			  $filterAllPosts[$w]['price']=$post->price; 

			  $filterAllPosts[$w]['formmated_price']=$post->currency;

			  $filterAllPosts[$w]['city_name']=$post->city_name; 

			  $filterAllPosts[$w]['created_at']=$post->created_at; 			   

			  $filterAllPosts[$w]['postImg']=$post->postImg; 

			  $filterAllPosts[$w]['featured']=$post->featured;

			  if(!empty($user_id)){

			  $filterAllPosts[$w]['favourite']= @$this->check_fav_post($post->id,$user_id);

			  }else{

			  $filterAllPosts[$w]['favourite']="0";

			  }

			  

			  

			  $w++;

		   }

		       

		return response()->json(['AllPosts'=>$filterAllPosts]);

		

		

		

         

    }







    

protected function getActivePostsByUser(Request $request)

{

    

    // Get the default orderBy value

    $orderBy = 'date';

    $country_code = $request->country_code;

    $lang = $request->lang;   

    $user_id = $request->user_id;

     

    $AllPosts =\App\Models\Post::where('user_id',$user_id)

   // ->where('country_code',$country_code)

     ->where('reviewed',1)

     ->where('archived',0)

    ->get();     

    

    

       $filterAllPosts=array();

       $w=0;

       foreach($AllPosts as $post){



        if ($post->price > 0){

            $getcurrencycountry = \DB::table('countries')->join('currencies', 'currencies.code', '=', 'countries.currency_code')

                                         ->select('currencies.*')->where('countries.code', '=', $post->country_code)->first();

            

            $price = \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator);}

            

              

          $filterAllPosts[$w]['id']=$post->id;

          $filterAllPosts[$w]['title']=$post->title;           

          $filterAllPosts[$w]['price']=$price;           

          $w++;

       }           

    return response()->json(['AllPosts'=>$filterAllPosts]);     

}













	protected function getLatestPosts_app(Request $request)

    {

        // Get the default Max. Items

        $maxItems = 12;

        



        // Get the default orderBy value

        $orderBy = 'date';

        



        $country_code = $request->country_code;

		$lang = $request->lang;





		$local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 'ar';



		// set laravel localization

		app()->setLocale($local);



        //$max = 12;

        // Get latest posts

        $posts_latest = @$this->getPosts1($request->user_id,$country_code, $lang, $maxItems, 'latest');



        if (!empty($posts_latest)) {

            if ($orderBy == 'random') {

                $posts_latest = Arr::shuffle($posts_latest);

            }

        }

        

        

         // Get the default Max. Items

        $maxItems = 10000;

        // Get the default orderBy value

        $orderBy = 'date';

        $country_code = $request->country_code;

		$lang = $request->lang;

		$sponsored = null;

		

        // Get latest posts

       $all = @$this->getPosts1($request->user_id,$country_code,$lang, $maxItems, 'latest');

        $allProductCount = count($all);

        if (!empty($all)) {

            if ($orderBy == 'random') {

                $all = Arr::shuffle($all);

            }

        }

        

       

         // Get latest posts

        // $maxItems = 16;

         $max = 12;

        $latest = @$this->getPosts1($request->user_id,$country_code, $lang, $max, 'latest');

		if (!empty($latest)) {

            if ($orderBy == 'random') {

                $latest = Arr::shuffle($latest);

            }

        }

        $latestProductCount = count($latest);

        // Get sponsor

      //  $maxItems = 16;

        $posts = @$this->getPosts1($request->user_id,$request->country_code,$lang, $maxItems, 'sponsored');

        

        if (!empty($posts)) {

            if ($orderBy == 'random') {

                $posts = Arr::shuffle($posts);

            }

        }

		$sponsored = array_values($posts);

		$sponsoredProductCount = count($sponsored);

		//popular

        // Get the default orderBy value

        $visits = 'visits';

        $options = [];

        $options['visits'] = $visits;

		$options['max_items'] = $maxItems;



		$cacheExpiration = $this->getCacheExpirationTime($options);

        // Get Popular posts

        $postsPopular = @$this->getPosts1_popular($request->user_id,$request->country_code,$request->lang, $maxItems, 'latest',$cacheExpiration);

		$popular = array_values($postsPopular);

		$popularProductCount = count($popular);

		return response()->json(['allProductCount'=>$allProductCount,'latestProductCount'=>$latestProductCount,'sponsoredProductCount'=>$sponsoredProductCount,'popularProductCount'=>$popularProductCount,'results'=>$posts_latest]);

         

    }

    

    

    

    

	

	protected function getAllPosts_app(Request $request)

    {

        if(empty($request->country_code)){$request->country_code='kw';}

        if(empty($request->lang)){$request->lang='en';}         

        if(empty($request->user_id)){$request->lang=1;}         

		 

		 

		// Get the default Max. Items

        $maxItems = 1000;

        

       

        // Get the default orderBy value

        $orderBy = 'date';

        



        $country_code = $request->country_code;

		$lang = $request->lang;

        // Get latest posts

        $posts = @$this->getPosts1($request->user_id,$country_code,$lang, $maxItems, 'latest');



		





        if (!empty($posts)) {

            if ($orderBy == 'random') {

                $posts = Arr::shuffle($posts);

            }

        }

		return response()->json(['results'=>$posts]);

         

    }





    /**

     * Get list of categories

     *

     * @param array $options

     */



    protected function getPopularPosts($options = [])

    {

        // Get the default Max. Items

        $maxItems = 12;

        if (isset($options['max_items'])) {

            $maxItems = (int)$options['max_items'];

        }



        // Get the default orderBy value

        $visits = 'visits';

        if (isset($options['visits'])) {

            $visits = $options['visits'];

        }



        // Get the Default Cache delay expiration

        $cacheExpiration = $this->getCacheExpirationTime($options);



        // Get latest posts

        $postsPopular = $this->getPosts($maxItems, 'latest', $cacheExpiration);



        if (!empty($postsPopular)) {

            if ($visits > 10) {

                $postsPopular = Arr::shuffle($postsPopular);

            }

        }

         /*echo "<pre>";

        print_r($postsPopular);

         exit;*/

        view()->share('postsPopular', $postsPopular);

        view()->share('latestOptions', $options);

    }

	

	

	protected function getPopularPosts_post(Request $request)

    {

        

        

        // Get the default Max. Items

        $maxItems = 12;

        // Get the default orderBy value

        $visits = 'visits';

        $options = [];

        $options['visits'] = $visits;

		$options['max_items'] = $maxItems;



		$cacheExpiration = $this->getCacheExpirationTime($options);

        // Get latest posts

        $postsPopular = $this->getPosts1_popular($request->user_id,$request->country_code,$request->lang, $maxItems, 'latest',$cacheExpiration);

        

  



        /*if (!empty($postsPopular)) {

            if ($visits > 10) {

                $postsPopular = Arr::shuffle($postsPopular);

            }

        }*/

		

		

// 		foreach($postsPopular as $key => $post):

// 		if($post->visits < 25) {

// 		unset($postsPopular[$key]);

// 		} 

// 		endforeach;



        // echo "<pre>";

        // print_r($postsPopular);

        // exit;

        

        

        

        

		$final = array_values($postsPopular);

        return response()->json(['results'=>$final]);

    }

    

    /* Home API start*/

    protected function home1_app(Request $request)

    {

        // Get the default Max. Items

        $maxItems = 1000;

        // Get the default orderBy value

        $orderBy = 'date';

        $country_code = $request->country_code;

		$lang = $request->lang;

		$sponsored = null;

        // Get latest posts

        $all = $this->getPosts1($request->user_id,$country_code,$lang, $maxItems, 'latest');

        if (!empty($all)) {

            if ($orderBy == 'random') {

                $all = Arr::shuffle($all);

            }

        }

      

		return response()->json(['status'=>1,'message'=>'success','All'=>$all]);

         

    }

    /* Home API end*/

  

     /* Home API start*/

    protected function home_app(Request $request)

    {

        // Get the default Max. Items

        $maxItems = 10000;

        // Get the default orderBy value

        $orderBy = 'date';

        $country_code = $request->country_code;

        $lat1 = $request->lat;

        $lon1 = $request->lon;

        if($lat1==''){

            $lat1 = 0;

        }

        if($lon1==''){

           $lon1 = 0; 

        }

        // if(isset($request->lat)){

        //   $lat1 = $request->lat;  

        // }

        // else{

        //   $lat1 = 0;  

        // }

        //  if(isset($request->lon)){

        //   $lon1 = $request->lon;

        // }

        // else{

        //   $lon1 = 0;  

        // }

		  

		$lang = $request->lang;

		$sponsored = null;

        // Get latest posts

        if($lat1==0 && $lon1==0){

          $all = @$this->getPosts1($request->user_id,$country_code,$lang, $maxItems, 'latest',$cacheExpiration = 0);  

        }

        else{

        $all[] = @$this->getPostsHome1($request->user_id,$country_code,$lang, $maxItems, 'latest',$cacheExpiration = 0,$lat1,$lon1);

        }

        $allProductCount = count($all);

        // if($allProductCount==1){

        //     $allProductCount=0;

        // }

        if (!empty($all)) {

            if ($orderBy == 'random') {

                $all[] = Arr::shuffle($all);

            }

            if(empty($all[0])){

                $all = '';

                $allProductCount = 0;

            }

        }

        else{

            $all = "";

            

        }

         // Get latest posts

         $max = 12;

          if($lat1==0 && $lon1==0){

            $latest = @$this->getPosts1($request->user_id,$country_code, $lang, $max, 'latest',$cacheExpiration = 0);  

          }

          else{

            $latest[] = @$this->getPostsHome1($request->user_id,$country_code, $lang, $max, 'latest',$cacheExpiration = 0,$lat1,$lon1);  

          }

           $latestProductCount = count($latest);

		if (!empty($latest)) {

            if ($orderBy == 'random') {

                $latest[] = Arr::shuffle($latest);

            }

            if(empty($latest[0])){

                $latest = '';

                $latestProductCount = 0;

            }

        }

        else{

            $latest = "";

        }

//          if(!empty($latest)){

// 		$latest = array_values($latest);

//         }

//         else{

//           $latest = '';  

//         }

       

        // if($latestProductCount == 1){

        //     $latestProductCount = 0;

        // }

        // Get sponsor

      //  $maxItems = 16;

      if($lat1==0 && $lon1==0){

          $posts = @$this->getPosts1($request->user_id,$request->country_code,$lang, $maxItems, 'sponsored',$cacheExpiration = 0);

      }

      else{

        $posts[] = @$this->getPostsHome1($request->user_id,$request->country_code,$lang, $maxItems, 'sponsored',$cacheExpiration = 0,$lat1,$lon1);

      }

        if (!empty($posts)) {

            if ($orderBy == 'random') {

                $posts = Arr::shuffle($posts);

            }

        }

        if(!empty($posts)){

		$sponsored = array_values($posts);

		$sponsoredProductCount = count($sponsored);

		if(empty($posts[0])){

		    $sponsored = "";

		    $sponsoredProductCount = 0;

		}

        }

        else{

          $sponsored = '';  

          $sponsoredProductCount = 0;

        }

		

		//popular

        // Get the default orderBy value

        $visits = 'visits';

        $options = [];

        $options['visits'] = $visits;

		$options['max_items'] = $maxItems;



		$cacheExpiration = $this->getCacheExpirationTime($options);

        // Get Popular posts

        if($lat1==0 & $lon1==0){

            $postsPopular = @$this->getPosts1_popular($request->user_id,$request->country_code,$request->lang, $maxItems, 'latest',$cacheExpiration);

        }

        else{

        $postsPopular1 = @$this->getPostsHome1_popular($request->user_id,$request->country_code,$request->lang, $maxItems, 'latest',$cacheExpiration,$lat1,$lon1);

         if(!empty($postsPopular1)){

             $popular[] = $postsPopular1;

		//$popular = array_values($postsPopular);

	//	$popular = is_array($postsPopular)? array_values($postsPopular): array();

		$popularProductCount = count($popular);

        }

        else{

          $popular = '';  

          $popularProductCount = 0;

        }

        }

         if(!empty($postsPopular)){

             $popular = $postsPopular;

		//$popular = array_values($postsPopular);

	//	$popular = is_array($postsPopular)? array_values($postsPopular): array();

		$popularProductCount = count($popular);

        }

        else{

          $popular = '';  

          $popularProductCount = 0;

        }

		//$popular = array_values($postsPopular);

		

		return response()->json(['status'=>1,'message'=>'success','allProductCount'=>$allProductCount,'latestProductCount'=>$latestProductCount,'sponsoredProductCount'=>$sponsoredProductCount,'popularProductCount'=>$popularProductCount,'All'=>$all,'Latest'=>$latest,'sponsored'=>$sponsored,'popular'=>$popular]);

         

    }

    /* Home API end*/  

    /* Start Category Field API */

    

    protected function categoryField_app(Request $request)

    {

        $catId = $request->catId;

        $subcatId = $request->subcatId;

		$lang = $request->lang;

        $bindings = [

            'translation_lang' => $lang,

        ];

		

		$sql = 'select * from category_field where (category_id="'.$catId.'") OR (category_id="'.$subcatId.'")';

        $categories = DB::select(DB::raw($sql), $bindings);

        //print_r($categories);

        foreach($categories as $categoryData){

            $fieldId = $categoryData->field_id;

            $sqlField = 'select * from fields where id="'.$fieldId.'"';

            $fieldDetails = DB::select(DB::raw($sqlField), $bindings);

            //print_r($fieldDetail);

            foreach($fieldDetails as $fieldDetail){

                $fieldName =  $fieldDetail->name;

                $fieldType = $fieldDetail->type;

                $sqlFieldOption = 'select id,translation_of,value from fields_options WHERE field_id="'.$fieldId.'" AND translation_lang="'.$lang.'"';

                $fieldOptionDetails = DB::select(DB::raw($sqlFieldOption), $bindings);

                

                //print_r($fieldOptionDetails);

                //echo $fieldOptionDetails['id'];

                if(!empty($fieldOptionDetails)){

                $data[] = array(

                    "fieldId" => $fieldId,

                    "fieldName" => $fieldName,

                    "fieldType" => $fieldType,

                    "fieldOption" => $fieldOptionDetails

                );

                }

            }

        }

		return response()->json(['status'=>1,'message'=>'success','results'=>$data]);

    }

    /* End Category Field API*/

    /**

     * Get list of categories

     *

     * @param array $options

     */

    protected function getCategories($options = [])

    {

        // Get the default Max. Items

        $maxItems = 12;

        if (isset($options['max_items'])) {

            $maxItems = (int)$options['max_items'];

        }



        // Get the Default Cache delay expiration

        $cacheExpiration = $this->getCacheExpirationTime($options);



        $cacheId = 'categories.parents.' . config('app.locale') . '.take.' . $maxItems;



        if (isset($options['type_of_display']) && in_array($options['type_of_display'], ['cc_normal_list', 'cc_normal_list_s'])) {



            $categories = Cache::remember($cacheId, $cacheExpiration, function () {

                $categories = Category::trans()->orderBy('lft')->get();



                return $categories;

            });

            $categories = collect($categories)->keyBy('translation_of');

            $categories = $subCategories = $categories->groupBy('parent_id');



            if ($categories->has(0)) {

                $cols = round($categories->get(0)->count() / 4, 0, PHP_ROUND_HALF_EVEN);

                $cols = ($cols > 0) ? $cols : 1;

                $categories = $categories->get(0)->chunk($cols);

                $subCategories = $subCategories->forget(0);

            } else {

                $categories = collect([]);

                $subCategories = collect([]);

            }



            $categories = $categories->take($maxItems);



            view()->share('categories', $categories);

            view()->share('subCategories', $subCategories);



        } else {



            $categories = Cache::remember($cacheId, $cacheExpiration, function () use ($maxItems) {

                $categories = Category::trans()->where('parent_id', 0)->take($maxItems)->orderBy('lft')->get();



                return $categories;

            });



            if (isset($options['type_of_display']) && $options['type_of_display'] == 'c_picture_icon') {

                $categories = collect($categories)->keyBy('id');

            } else {

                $cols = round($categories->count() / 3, 0); // PHP_ROUND_HALF_EVEN

                $cols = ($cols > 0) ? $cols : 1; // Fix array_chunk with 0

                $categories = $categories->chunk($cols);

            }



            view()->share('categories', $categories);



        }



        view()->share('categoriesOptions', $options);

    }

	

	

	protected function getCategories_app(Request $request)

    {

        //$categories = Category::trans()->where('parent_id', 0)->orderBy('lft')->get();

		

		$bindings = [

            'translation_lang' => $request->translation_lang,

        ];

		

		$sql = 'select * from categories where parent_id="0" and translation_lang="'.$request->translation_lang.'" order by lft ';



        $categories = DB::select(DB::raw($sql), $bindings);

		

		

		

		

		//print_r($categories1);

		

		foreach($categories as $key => $category):

		if($category->picture!="")

		{

	        	$category->picture =  \Storage::url($category->picture) . getPictureVersion();

		}

		else

		{

	        	$category->picture = 'https://www.tmmat.com/storage/app/default/categories/fa-folder-skin-blue.png';

		}

		$bindings1 = [

            'translation_lang' => 'en',

        ];

		

		//$sql1 = 'select * from categories where id="'.$category->translation_of.'" and translation_lang="en" order by lft ';

		

		

		$categories1 = DB::select('select * from categories where id = :id', ['id' => $category->translation_of]);



        //$categories1 = DB::select(DB::raw($sql1), $bindings1);

		

		$category->slug = $categories1[0]->slug;	

		

		endforeach;

        // echo "<pre>";

        // print_r($postsPopular);

        // exit;

		$final = array_values($categories);

        return response()->json(['results'=>$final]);

    }

    

    

    	

	public function getSubcategory_app(Request $request)

    {

        

        

        // $categories = Category::where('translation_lang', $request->translation_lang)->where('parent_id', $request->category_id)->orderBy('lft')->get();

		

		$bindings = [

            'translation_lang' => $request->translation_lang,

        ];

		

		$sql = 'select * from categories where parent_id = "'.$request->category_id.'"  and translation_lang="'.strtolower($request->translation_lang).'" order by lft ';



        $categories = DB::select(DB::raw($sql), $bindings);

		

		//id

		



        $total_product=0;

        foreach($categories as $key => $category):

            

            $total_product=$this->GetProductCountByCatid($category->translation_of,$request->countryCode);

            $category->total_product=$total_product;





		 

    		if($category->picture!="")

    		{

    		    $category->picture =  \Storage::url($category->picture) . getPictureVersion();

    		}

    		else

    		{

    		    $category->picture = 'https://www.tmmat.com/storage/app/default/categories/fa-folder-skin-blue.png';

    		}

		endforeach;

        

		$final = array_values($categories);

		

		return response()->json(['results'=>$final]);

    }

    

    

    

    

    

    



    /**

     * Get mini stats data

     */

    protected function getStats()

    {

        // Count posts

        $countPosts = Post::currentCountry()->unarchived()->count();



        // Count cities

        $countCities = City::currentCountry()->count();



        // Count users

        $countUsers = User::count();



        // Share vars

        view()->share('countPosts', $countPosts);

        view()->share('countCities', $countCities);

        view()->share('countUsers', $countUsers);

    }



    /**

     * Set SEO information

     */

    protected function setSeo()

    {

        $title = getMetaTag('title', 'home');

        $description = getMetaTag('description', 'home');

        $keywords = getMetaTag('keywords', 'home');



        // Meta Tags

        MetaTag::set('title', $title);

        MetaTag::set('description', strip_tags($description));

        MetaTag::set('keywords', $keywords);



        // Open Graph

        $this->og->title($title)->description($description);

        view()->share('og', $this->og);

    }



    /**

     * @param int $limit

     * @param string $type (latest OR sponsored)

     * @param int $cacheExpiration

     * @return mixed

     */

    private function getPosts($limit = 20, $type = 'latest', $cacheExpiration = 0)

    {

        $paymentJoin = '';

        $sponsoredCondition = '';

        $sponsoredOrder = '';

        if ($type == 'sponsored') {

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

            $sponsoredCondition = ' AND a.featured = 1';

            $sponsoredOrder = 'p.lft DESC, ';

        } else {

            // $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'LEFT JOIN (SELECT MAX(id) max_id, post_id FROM ' . DBTool::table('payments') . ' WHERE active=1 GROUP BY post_id) mpy ON mpy.post_id = a.id AND a.featured=1' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.id=mpy.max_id' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

        }

        $reviewedCondition = '';

        if (config('settings.single.posts_review_activation')) {

            $reviewedCondition = ' AND a.reviewed = 1';

        }

        $sql = 'SELECT DISTINCT a.*, py.package_id as py_package_id' . '

                FROM ' . DBTool::table('posts') . ' as a

                INNER JOIN ' . DBTool::table('categories') . ' as c ON c.id=a.category_id AND c.active=1

                ' . $paymentJoin . '

                WHERE a.country_code = :countryCode

                	AND (a.verified_email=1 AND a.verified_phone=1)

                	AND a.archived!=1 ' . $reviewedCondition . $sponsoredCondition . '

                GROUP BY a.id 

                ORDER BY ' . $sponsoredOrder . 'a.created_at DESC

                LIMIT 0,' . (int)$limit;

        $bindings = [

            'countryCode' => config('country.code'),

        ];



        $cacheId = config('country.code') . '.home.getPosts.' . $type;

        $posts = Cache::remember($cacheId, $cacheExpiration, function () use ($sql, $bindings) {

            $posts = DB::select(DB::raw($sql), $bindings);



            return $posts;

        });



        // Append the Posts 'uri' attribute

        $posts = collect($posts)->map(function ($post) {

            $post->title = mb_ucfirst($post->title);

            $post->uri = trans('routes.v-post', ['slug' => slugify($post->title), 'id' => $post->id]);



            return $post;

        })->toArray();

 

        return $posts;

    }

	

	

	private function getPostsPopular($limit = 20, $type = 'latest', $cacheExpiration = 0)

    {

        $paymentJoin = '';

        $sponsoredCondition = '';

        $sponsoredOrder = '';

        if ($type == 'sponsored') {

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

            $sponsoredCondition = ' AND a.featured = 1';

            $sponsoredOrder = 'p.lft DESC, ';

        } else {

            // $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'LEFT JOIN (SELECT MAX(id) max_id, post_id FROM ' . DBTool::table('payments') . ' WHERE active=1 GROUP BY post_id) mpy ON mpy.post_id = a.id AND a.featured=1' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.id=mpy.max_id' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

        }

        $reviewedCondition = '';

        if (config('settings.single.posts_review_activation')) {

            $reviewedCondition = ' AND a.reviewed = 1';

        }

        $sql = 'SELECT DISTINCT a.*, py.package_id as py_package_id' . '

                FROM ' . DBTool::table('posts') . ' as a

                INNER JOIN ' . DBTool::table('categories') . ' as c ON c.id=a.category_id AND c.active=1

                ' . $paymentJoin . '

                WHERE a.country_code = :countryCode

                	AND (a.verified_email=1 AND a.verified_phone=1)

                	AND a.visits > 25 AND  a.archived!=1 ' . $reviewedCondition . $sponsoredCondition . '

                GROUP BY a.id 

                ORDER BY a.visits DESC, a.created_at DESC

                LIMIT 0,' . (int)$limit;

        $bindings = [

            'countryCode' => config('country.code'),

        ];



        $cacheId = config('country.code') . '.home.getPosts.' . $type;

        $posts = Cache::remember($cacheId, $cacheExpiration, function () use ($sql, $bindings) {

            $posts = DB::select(DB::raw($sql), $bindings);



            return $posts;

        });



        // Append the Posts 'uri' attribute

        $posts = collect($posts)->map(function ($post) {

            $post->title = mb_ucfirst($post->title);

            $post->uri = trans('routes.v-post', ['slug' => slugify($post->title), 'id' => $post->id]);



            return $post;

        })->toArray();



        return $posts;

    }

	

	private function getPosts1_popular($user_id,$country_code,$lang, $limit = 20, $type = 'latest', $cacheExpiration = 0)

    {

        $paymentJoin = '';

        $sponsoredCondition = '';

        $sponsoredOrder = '';

        if ($type == 'sponsored') {

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

            $sponsoredCondition = ' AND a.featured = 1';

            $sponsoredOrder = 'p.lft DESC, ';

        } else {

            // $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'LEFT JOIN (SELECT MAX(id) max_id, post_id FROM ' . DBTool::table('payments') . ' WHERE active=1 GROUP BY post_id) mpy ON mpy.post_id = a.id AND a.featured=1' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.id=mpy.max_id' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

        }

        $reviewedCondition = '';

        if (config('settings.single.posts_review_activation')) {

            $reviewedCondition = ' AND a.reviewed = 1';

        }

        $sql = 'SELECT DISTINCT a.*, py.package_id as py_package_id' . '

                FROM ' . DBTool::table('posts') . ' as a

                INNER JOIN ' . DBTool::table('categories') . ' as c ON c.id=a.category_id AND c.active=1

                ' . $paymentJoin . '

                WHERE a.country_code = :countryCode

                	AND (a.verified_email=1 AND a.verified_phone=1)

                	AND a.visits > 25 AND a.archived!=1 ' . $reviewedCondition . $sponsoredCondition . '

                	  GROUP BY a.id 

                ORDER BY a.visits DESC, a.created_at DESC

                LIMIT 0,' . (int)$limit;

                

        $bindings = [

            'countryCode' => $country_code,

        ];



        $cacheId = $country_code . '.home.getPosts.' . $type;

        $posts = DB::select(DB::raw($sql), $bindings);



        //return $posts;

		

		

		

		

		

		$i=0;

		foreach($posts as $key => $post){

		    

		    

		                        

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

                            

						                $post->currency = $get_currency;

								    

														

														

		                                

	                                	$package = '';

										if ($post->featured == 1) {

											$package = \App\Models\Package::findTransApp($post->py_package_id,$lang);

										}

								        //$post->package = $package;

		                                   

										   

										if(!empty($post->py_package_id))

										{

								        $post->py_package_id = $post->py_package_id;

								        }

										else

										{

										$post->py_package_id = 'No Value';

										}

										   

										    

										if(!empty($package))

										{

								        $post->package = $package;

								        }

										else

										{

										$post->package = 'No Value';

										}	

										

											

											

																					

											

										

										$postType = \App\Models\PostType::findTransApp($post->post_type_id,$lang);

											//return $postType;

										$post->postType = $postType;

										

										

										

										$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $post->contact_name = $getusernamedetail->username;  

										

							

							

							

										// Get Post's Pictures

										$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

										if ($pictures->count() > 0) {

											$postImg = resize($pictures->first()->filename, 'medium');

										} else {

											$postImg = resize(config('larapen.core.picture.default'));

										}

										$post->postImg = $postImg;

										

										$city = \App\Models\City::find($post->city_id);

										$post->cityy = $city;

										

										

										

										$post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));

										$post->created_at = $post->created_at->ago();

										

										$liveCat = \App\Models\Category::findTrans($post->category_id);

											//return $liveCat;

										$post->liveCat = $liveCat;

										

										// Check parent

										if (empty($liveCat->parent_id)) {

											$liveCatParentId = $liveCat->id;

											$liveCatType = $liveCat->type;

										} else {

											$liveCatParentId = $liveCat->parent_id;

											

											$liveParentCat = \App\Models\Category::findTrans($liveCat->parent_id);

											

											

											

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

											

											

											

											

											//$post->liveParentCat = $liveParentCat;

											$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';

										}

										

										// Check translation

										$liveCatName = $liveCat->name;

										

										

										

										

										

										

										

										

										

										

											

                                		$catNestedIds = (object)[

                                            'parentId' => $liveCatParentId,

                                            'id' => $post->category_id,

                                        ];

                                

                                        $customFields = $this->getPostFieldsValues_app($catNestedIds, $post->id);

                                						

										

										$results = array();

										foreach($customFields as $field)

										{

										

										

										

										

										if (in_array($field->type, ['checkbox_multiple'])) 

										{

										$dvals = $field->default;

										$results1 = array();

										foreach($dvals as $index=>$key) 

										{

										 array_push($results1, $key);

										}

										

																				

										$field->default = $results1;										

										}

										

										

										/*if (in_array($field->type, ['radio', 'select'])) {

											if (is_numeric($field->default)) {

												$option = \App\Models\FieldOption::findTrans($field->default);

												if (!empty($option)) {

													$field->default = $option->value;

												}

											}

										}

										if (in_array($field->type, ['checkbox'])) {

											$field->default = ($field->default == 1) ? t('Yes') : t('No');

										}

										

										

										if ($field->type == 'file')

										{

										}

										else

										{

										if (!is_array($field->default))

										{

										//array_push($results, $valueItem->value);

										

										}

										else

										{

										

										if (count($field->default) > 0)

										{

										foreach($field->default as $valueItem)

										{

										array_push($results,$valueItem->value);

										}

										}

										else

										{

										

										}

										}

										}*/

										}

											$customArr = array();

										$data = array();

									//	print_r($customFields);

										$json  = json_encode($customFields);

                                       $customFields = json_decode($json, true);

                                       //print_r($customFields);

                                       foreach($customFields as $key => $value){

                                           $customArr[] = $value;

                                       }

										$post->customFields = $customArr;	

										

										

										

										

							            $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $username = $getusernamedetail->username;  

										$user_created_at = $getusernamedetail->created_at;       

										$post->liveCatName = $liveCatName;

										$post->username = $username;

										

										$post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));

										$post->user_created_at = $post->user_created_at->ago();

										

										

													if (!empty($user_id))

													{

													$scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post->id)->count();

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

		

		

		return $posts;

		

		

		

		

    }

	

	

	private function getPostsHome1_popular($user_id,$country_code,$lang, $limit = 20, $type = 'latest', $cacheExpiration = 0,$lat1,$lon1)

    {

        $paymentJoin = '';

        $sponsoredCondition = '';

        $sponsoredOrder = '';

        if ($type == 'sponsored') {

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

            $sponsoredCondition = ' AND a.featured = 1';

            $sponsoredOrder = 'p.lft DESC, ';

        } else {

            // $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'LEFT JOIN (SELECT MAX(id) max_id, post_id FROM ' . DBTool::table('payments') . ' WHERE active=1 GROUP BY post_id) mpy ON mpy.post_id = a.id AND a.featured=1' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.id=mpy.max_id' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

        }

        $reviewedCondition = '';

        if (config('settings.single.posts_review_activation')) {

            $reviewedCondition = ' AND a.reviewed = 1';

        }

        $sql = 'SELECT DISTINCT a.*, py.package_id as py_package_id' . '

                FROM ' . DBTool::table('posts') . ' as a

                INNER JOIN ' . DBTool::table('categories') . ' as c ON c.id=a.category_id AND c.active=1

                ' . $paymentJoin . '

                WHERE a.country_code = :countryCode

                	AND (a.verified_email=1 AND a.verified_phone=1)

                	AND a.visits > 25 AND a.archived!=1 ' . $reviewedCondition . $sponsoredCondition . '

                	  GROUP BY a.id 

                ORDER BY a.visits DESC, a.created_at DESC

                LIMIT 0,' . (int)$limit;

                

        $bindings = [

            'countryCode' => $country_code,

        ];



        $cacheId = $country_code . '.home.getPosts.' . $type;

        $posts = DB::select(DB::raw($sql), $bindings);



        //return $posts;

		

		

		

		

		

		$i=0;

		foreach($posts as $key => $post){

		    $unit = 'K';

		 $lat2 = $post->lat;

		 $lon2 = $post->lon;

		 $theta = $lon1 - $lon2; 

        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 

        $dist = acos($dist); 

        $dist = rad2deg($dist); 

        $miles = $dist * 60 * 1.1515;

        $unit = strtoupper($unit);

      $distance = round($miles * 1.609344);

		  

		      if($distance<=10){                  

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

                            

						                $post->currency = $get_currency;

								    

														

														

		                                

	                                	$package = '';

										if ($post->featured == 1) {

											$package = \App\Models\Package::findTransApp($post->py_package_id,$lang);

										}

								        //$post->package = $package;

		                                   

										   

										if(!empty($post->py_package_id))

										{

								        $post->py_package_id = $post->py_package_id;

								        }

										else

										{

										$post->py_package_id = 'No Value';

										}

										   

										    

										if(!empty($package))

										{

								        $post->package = $package;

								        }

										else

										{

										$post->package = 'No Value';

										}	

										

											

								

																					

											

										

										$postType = \App\Models\PostType::findTransApp($post->post_type_id,$lang);

											//return $postType;

										$post->postType = $postType;

										

										

										

										$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $post->contact_name = $getusernamedetail->username;  

										

							

							

							

										// Get Post's Pictures

										$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

										if ($pictures->count() > 0) {

											$postImg = resize($pictures->first()->filename, 'medium');

										} else {

											$postImg = resize(config('larapen.core.picture.default'));

										}

										$post->postImg = $postImg;

										

										$city = \App\Models\City::find($post->city_id);

										$post->cityy = $city;

										

										

										

										$post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));



										$post->created_at = $post->created_at->ago();

										

										$liveCat = \App\Models\Category::findTrans($post->category_id);

											//return $liveCat;

										$post->liveCat = $liveCat;

										

										// Check parent

										if (empty($liveCat->parent_id)) {

											$liveCatParentId = $liveCat->id;

											$liveCatType = $liveCat->type;

										} else {

											$liveCatParentId = $liveCat->parent_id;

											

											$liveParentCat = \App\Models\Category::findTrans($liveCat->parent_id);

											

											

											

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

											//$post->liveParentCat = $liveParentCat;

											$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';

										}

										

										// Check translation

										$liveCatName = $liveCat->name;

                                		$catNestedIds = (object)[

                                            'parentId' => $liveCatParentId,

                                            'id' => $post->category_id,

                                        ];

                                

                                        $customFields = $this->getPostFieldsValues_app($catNestedIds, $post->id);

                                						

										

										$results = array();

										foreach($customFields as $field)

										{

										if (in_array($field->type, ['checkbox_multiple'])) 

										{

										$dvals = $field->default;

										$results1 = array();

										foreach($dvals as $index=>$key) 

										{

										 array_push($results1, $key);

										}

										

																				

										$field->default = $results1;										

										}

										

										

									

										}

											$customArr = array();

										$data = array();

									//	print_r($customFields);

										$json  = json_encode($customFields);

                                       $customFields = json_decode($json, true);

                                       //print_r($customFields);

                                       foreach($customFields as $key => $value){

                                           $customArr[] = $value;

                                       }

										$post->customFields = $customArr;	

										

										

										

										

							            $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $username = $getusernamedetail->username;  

										$user_created_at = $getusernamedetail->created_at;       

										$post->liveCatName = $liveCatName;

										$post->username = $username;

										

										$post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));

										$post->user_created_at = $post->user_created_at->ago();

										

										

													if (!empty($user_id))

													{

													$scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post->id)->count();

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

										return $post;

										//echo count($post);

										//print_r($post);

								}

							

		

		

		

		

		}

		

		

    }

	

    private function getPosts1_by_cat_id($cat_id,$country_code, $lang, $limit = 20, $type = 'latest', $cacheExpiration = 0)

    {

		//echo 'trans='.t('hour');

        $paymentJoin = '';

        $sponsoredCondition = '';

        $sponsoredOrder = '';

        

		

		$ids=array();

	

				

				

					$childIds = \App\Models\Category::where('parent_id', $cat_id)->pluck('id');

					$subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)->pluck('id');

					$superid=$cat_id;

					$postsID = \App\Models\Post::select('posts.id')->where('country_code',$country_code)

					->where(function ($query) use ($subchildIds,$childIds,$superid) {

						$query->whereIn('category_id',$childIds)

						->orWhereIn('category_id',$subchildIds)

						->orWhere('category_id',$superid);

						})->where('reviewed',1)->where('archived',0)->get();

				

				foreach($postsID as $post_id)

				{

				$ids[]=$post_id->id;

				}

				if(empty($ids)){

					

					$ids="-1,-2";

					}else{

				$ids=implode(',',$ids);}





				 //print_r($postsID);

				

        $reviewedCondition = '';

        if (config('settings.single.posts_review_activation')) {

            $reviewedCondition = ' AND a.reviewed = 1';

        }

        $sql = 'SELECT DISTINCT a.* 

                FROM ' . DBTool::table('posts') . ' as a               

				WHERE a.id IN (' . $ids . ')					 

                GROUP BY a.id 

                ORDER BY ' . $sponsoredOrder . 'a.created_at DESC

                LIMIT 0,' . (int)$limit;

        $bindings = [

            'countryCode' => $country_code,

        ];



        $cacheId = $country_code . '.home.getPosts.' . $type;

        $posts = DB::select(DB::raw($sql), $bindings);



        //return $posts;

		$i=0;

		foreach($posts as $key => $post){

		    

		                        

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

									$post->currency = $get_currency;						 

									 

									 

									if ($post->featured == 1) {

											$package = \App\Models\Package::findTransApp($post->py_package_id,$lang);

										}

										

										

										if(!empty($post->py_package_id))

										{

								        $post->py_package_id = $post->py_package_id;

								        }

										else

										{

										$post->py_package_id = 'No Value';

										}

										

										 

								        

										 

										

										

							

										// Get Post's Pictures

										$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

										if ($pictures->count() > 0) {

											$postImg = resize($pictures->first()->filename, 'medium');

										} else {

											$postImg = resize(config('larapen.core.picture.default'));

										}

										$post->postImg = $postImg;

										

										$city = \App\Models\City::find($post->city_id);

										$post->cityy = $city;

										

										

										

										$post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));

										$post->created_at = $post->created_at->ago();										

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

										

										 

										

										

										if (!empty($user_id))

													{

													$scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post->id)->count();

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



		 

		

		

		return $posts;

		

		

		

		

    }

	

	private function getPosts1($user_id,$country_code, $lang, $limit = 20, $type = 'latest', $cacheExpiration = 0)

    {

		//echo 'trans='.t('hour');

        $paymentJoin = '';

        $sponsoredCondition = '';

        $sponsoredOrder = '';

        if ($type == 'sponsored') {

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

            $sponsoredCondition = ' AND a.featured = 1';

            $sponsoredOrder = 'p.lft DESC, ';

        } else {

            // $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'LEFT JOIN (SELECT MAX(id) max_id, post_id FROM ' . DBTool::table('payments') . ' WHERE active=1 GROUP BY post_id) mpy ON mpy.post_id = a.id AND a.featured=1' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.id=mpy.max_id' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

        }

        $reviewedCondition = '';

        if (config('settings.single.posts_review_activation')) {

            $reviewedCondition = ' AND a.reviewed = 1';

        }

        $sql = 'SELECT DISTINCT a.*, py.package_id as py_package_id' . '

                FROM ' . DBTool::table('posts') . ' as a

                INNER JOIN ' . DBTool::table('categories') . ' as c ON c.id=a.category_id AND c.active=1

                ' . $paymentJoin . '

                WHERE a.country_code = :countryCode

                	AND (a.verified_email=1 AND a.verified_phone=1)

                	AND a.archived!=1 ' . $reviewedCondition . $sponsoredCondition . '

                GROUP BY a.id 

                ORDER BY ' . $sponsoredOrder . 'a.created_at DESC

                LIMIT 0,' . (int)$limit;

        $bindings = [

            'countryCode' => $country_code,

        ];



        $cacheId = $country_code . '.home.getPosts.' . $type;

        $posts = DB::select(DB::raw($sql), $bindings);



        //return $posts;

		

		

		

		

		

		$i=0;

		foreach($posts as $key => $post){

		    

		                        

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

                            

					                $post->currency = $get_currency;

						                

						              

									  

									  $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $post->contact_name = $getusernamedetail->username;     

		    

		    

		    

		        	                    $package = '';

										if ($post->featured == 1) {

											$package = \App\Models\Package::findTransApp($post->py_package_id,$lang);

										}

										

										

										if(!empty($post->py_package_id))

										{

								        $post->py_package_id = $post->py_package_id;

								        }

										else

										{

										$post->py_package_id = 'No Value';

										}

										

										

										if(!empty($package))

										{

								        $post->package = $package;

								        }

										else

										{

										$post->package = 'No Value';

										}

								        

								        

										

										$postType = \App\Models\PostType::findTransApp($post->post_type_id, $lang);

											//return $postType;

										$post->postType = $postType;

										

										

							

										// Get Post's Pictures

										$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

										if ($pictures->count() > 0) {

											$postImg = resize($pictures->first()->filename, 'medium');

										} else {

											$postImg = resize(config('larapen.core.picture.default'));

										}

										$post->postImg = $postImg;

										

										$city = \App\Models\City::find($post->city_id);

										$post->cityy = $city;

										

										

										

										$post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));

										$post->created_at = $post->created_at->ago();

										

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

										

										

										

										

										

										

											

                                		$catNestedIds = (object)[

                                            'parentId' => $liveCatParentId,

                                            'id' => $post->category_id,

                                        ];

                                

                                        $customFields = $this->getPostFieldsValues_app($catNestedIds, $post->id);

                                						

										

										$results = array();

										foreach($customFields as $field)

										{

										

										

										

										

								// 		if (in_array($field->type, ['checkbox_multiple'])) 

								// 		{

								// 		$dvals = $field->default;

								// 		$results1 = array();

								// 		foreach($dvals as $index=>$key) 

								// 		{

								// 		 array_push($results1, $key);

								// 		}

										

																				

								// 		$field->default = $results1;										

								// 		}

										

										

										/*if (in_array($field->type, ['radio', 'select'])) {

											if (is_numeric($field->default)) {

												$option = \App\Models\FieldOption::findTrans($field->default);

												if (!empty($option)) {

													$field->default = $option->value;

												}

											}

										}

										if (in_array($field->type, ['checkbox'])) {

											$field->default = ($field->default == 1) ? t('Yes') : t('No');

										}

										

										

										if ($field->type == 'file')

										{

										}

										else

										{

										if (!is_array($field->default))

										{

										//array_push($results, $valueItem->value);

										

										}

										else

										{

										

										if (count($field->default) > 0)

										{

										foreach($field->default as $valueItem)

										{

										array_push($results,$valueItem->value);

										}

										}

										else

										{

										

										}

										}

										}*/

										}

										$customArr = array();

										$data = array();

									//	print_r($customFields);

										$json  = json_encode($customFields);

                                       $customFields = json_decode($json, true);

                                       //print_r($customFields);

                                       foreach($customFields as $key => $value){

                                           $customArr[] = $value;

                                       }

                                      

                                       //print_r($customArr);

										$post->customFields = $customArr;				

										

										//$post->paymentpre = $results;	

										

										

										

										

										

										

							            $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $username = $getusernamedetail->username;  

										$user_created_at = $getusernamedetail->created_at;      

										$post->liveCatName = $liveCatName;

										$post->username = $username;

										//$post->user_created_at = $user_created_at;

										

										$post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));

										$post->user_created_at = $post->user_created_at->ago();

										

										

										

										if (!empty($user_id))

													{

													$scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post->id)->count();

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



		

		// Append the Posts 'uri' attribute

       /* $posts = collect($posts)->map(function ($post) {

            

            return t($post);

        })->toArray();

		*/

		

		

		return $posts;

		

		

		

		

    }

	

	private function getPostsHome1($user_id,$country_code, $lang, $limit = 20, $type = 'latest', $cacheExpiration = 0,$lat1,$lon1)

    {

		//echo 'trans='.t('hour');

        $paymentJoin = '';

        $sponsoredCondition = '';

        $sponsoredOrder = '';

        if ($type == 'sponsored') {

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'INNER JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

            $sponsoredCondition = ' AND a.featured = 1';

            $sponsoredOrder = 'p.lft DESC, ';

        } else {

            // $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.post_id=a.id AND py.active=1' . "\n";

            $paymentJoin .= 'LEFT JOIN (SELECT MAX(id) max_id, post_id FROM ' . DBTool::table('payments') . ' WHERE active=1 GROUP BY post_id) mpy ON mpy.post_id = a.id AND a.featured=1' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('payments') . ' as py ON py.id=mpy.max_id' . "\n";

            $paymentJoin .= 'LEFT JOIN ' . DBTool::table('packages') . ' as p ON p.id=py.package_id' . "\n";

        }

        $reviewedCondition = '';

        if (config('settings.single.posts_review_activation')) {

            $reviewedCondition = ' AND a.reviewed = 1';

        }

        $sql = 'SELECT DISTINCT a.*, py.package_id as py_package_id' . '

                FROM ' . DBTool::table('posts') . ' as a

                INNER JOIN ' . DBTool::table('categories') . ' as c ON c.id=a.category_id AND c.active=1

                ' . $paymentJoin . '

                WHERE a.country_code = :countryCode

                	AND (a.verified_email=1 AND a.verified_phone=1)

                	AND a.archived!=1 ' . $reviewedCondition . $sponsoredCondition . '

                GROUP BY a.id 

                ORDER BY ' . $sponsoredOrder . 'a.created_at DESC

                LIMIT 0,' . (int)$limit;

        $bindings = [

            'countryCode' => $country_code,

        ];



        $cacheId = $country_code . '.home.getPosts.' . $type;

        $posts = DB::select(DB::raw($sql), $bindings);



        //return $posts;

		

		

		

		

		

		$i=0;

		foreach($posts as $key => $post){

		   $unit = 'K';

		 $lat2 = $post->lat;

		 $lon2 = $post->lon;

		 $theta = $lon1 - $lon2; 

        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 

        $dist = acos($dist); 

        $dist = rad2deg($dist); 

        $miles = $dist * 60 * 1.1515;

        $unit = strtoupper($unit);

     $distance = round($miles * 1.609344); 

        if($distance<=10){

          //echo "fgld"; 

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



    $post->currency = $get_currency;

        

      

	  

	  $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

        $post->contact_name = $getusernamedetail->username;     







        $package = '';

		if ($post->featured == 1) {

			$package = \App\Models\Package::findTransApp($post->py_package_id,$lang);

		}

		

		

		if(!empty($post->py_package_id))

		{

        $post->py_package_id = $post->py_package_id;

        }

		else

		{

		$post->py_package_id = 'No Value';

		}

		

		

		if(!empty($package))

		{

        $post->package = $package;

        }

		else

		{

		$post->package = 'No Value';

		}

        

        

		

		$postType = \App\Models\PostType::findTransApp($post->post_type_id, $lang);

			//return $postType;

		$post->postType = $postType;

		

		



		// Get Post's Pictures

		$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

		if ($pictures->count() > 0) {

			$postImg = resize($pictures->first()->filename, 'medium');

		} else {

			$postImg = resize(config('larapen.core.picture.default'));

		}

		$post->postImg = $postImg;

		

		$city = \App\Models\City::find($post->city_id);

		$post->cityy = $city;

		

		

		

		$post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));

		$post->created_at = $post->created_at->ago();

		

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

										

										

										

										

										

										

											

                                		$catNestedIds = (object)[

                                            'parentId' => $liveCatParentId,

                                            'id' => $post->category_id,

                                        ];

                                

                                        $customFields = $this->getPostFieldsValues_app($catNestedIds, $post->id);

                                						

										

										$results = array();

										foreach($customFields as $field)

										{

										

										

										

										

								// 		if (in_array($field->type, ['checkbox_multiple'])) 

								// 		{

								// 		$dvals = $field->default;

								// 		$results1 = array();

								// 		foreach($dvals as $index=>$key) 

								// 		{

								// 		 array_push($results1, $key);

								// 		}

										

																				

								// 		$field->default = $results1;										

								// 		}

										

										

										/*if (in_array($field->type, ['radio', 'select'])) {

											if (is_numeric($field->default)) {

												$option = \App\Models\FieldOption::findTrans($field->default);

												if (!empty($option)) {

													$field->default = $option->value;

												}

											}

										}

										if (in_array($field->type, ['checkbox'])) {

											$field->default = ($field->default == 1) ? t('Yes') : t('No');

										}

										

										

										if ($field->type == 'file')

										{

										}

										else

										{

										if (!is_array($field->default))

										{

										//array_push($results, $valueItem->value);

										

										}

										else

										{

										

										if (count($field->default) > 0)

										{

										foreach($field->default as $valueItem)

										{

										array_push($results,$valueItem->value);

										}

										}

										else

										{

										

										}

										}

										}*/

										}

										$customArr = array();

										$data = array();

									//	print_r($customFields);

										$json  = json_encode($customFields);

                                       $customFields = json_decode($json, true);

                                       //print_r($customFields);

                                       foreach($customFields as $key => $value){

                                           $customArr[] = $value;

                                       }

                                      

                                       //print_r($customArr);

										$post->customFields = $customArr;				

										

										//$post->paymentpre = $results;	

										

										

										

										

										

										

							            $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $username = $getusernamedetail->username;  

										$user_created_at = $getusernamedetail->created_at;      

										$post->liveCatName = $liveCatName;

										$post->username = $username;

										//$post->user_created_at = $user_created_at;

										

										$post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));

										$post->user_created_at = $post->user_created_at->ago();

										

										

										

										if (!empty($user_id))

													{

													$scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post->id)->count();

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

									return $post;

						}

						

		// Append the Posts 'uri' attribute

       /* $posts = collect($posts)->map(function ($post) {

            

            return t($post);

        })->toArray();

		*/

		

		

		

		}

		

		

		

    }





    public function getlocationcity($string)

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

    

    



    

	public function customsearch_app_pickup(Request $request)

    {

        \App::setLocale(request()->get('language') ?: 'en');

		//echo 'trans='.t('hour');

        $paymentJoin = '';

        $sponsoredCondition = '';

        $sponsoredOrder = '';

		$user_id = $request->user_id;

		$category = $request->category;

		$minprice = $request->minprice ?: 0;

		$maxprice = $request->maxprice;

		$distance = $request->distance;

		$subCatId = $request->subCatId;

		$country = strtoupper($request->country);

		$language = $request->language;

        $lang = $request->language;

        $sortBy = $request->sortBy;

        $sallerType = $request->sallerType;

        $allAds = $request->allAds;

        $condition = $request->condition;

        $paymentPreference = $request->paymentPreference;

        $deliveryCost = $request->deliveryCost;

        $customFilteredData = $request->customFilteredData;

        // $customFilteredData = str_replace("\"","",$customFilteredData);

        // $customFilteredData = json_decode($customFilteredData,true);

        // print_r($customFilteredData);

        // die();

        // print_r($customFilteredData);

        // foreach($customFilteredData as $customFiltered){

        //   print_r($customFiltered);

        // }

        if(!empty($condition) && !empty($paymentPreference) && !empty($deliveryCost)){

            $field_values = 'post_values.option_id IN ('.$condition.','.$paymentPreference.','.$deliveryCost.') and';

        }

        else{

            $field_values = '';

        }

        if(!empty($customFilteredData)){

            $field_values_custom = 'post_values.option_id IN ('.$customFilteredData.') and';

                }

        else{

            $field_values_custom='';

        }

        if(!empty($subCatId)){

            $searchCat = Category::find($subCatId);

            $mainID = Category::where('id', $searchCat->translation_of)->first();

            $IDsList = $this->getAllSubs($mainID->id, $searchCat->translation_lang);

            $catId = '(category_id='.$mainID->id;

            foreach($IDsList as $key){

                $catId .= ' or category_id='.$key;

            }

        //    $catId = 'category_id='.$subCatId.' and';

           $catId .= ') and';

        }

        else{

           $catId = ''; 

        }

        if($allAds=="ads with images"){

            $allAds = "ads with images";

        }

        else{

           $allAds = ''; 

        }

        if($sortBy=="LtoH"){

            $order = "ASC";

        }

        else if($sortBy=="HtoL"){

             $order = "DESC";

        }

        else{

          $order = "";  

        }

        if($sallerType=="Individual"){

            $sallerType = 'posts.post_type_id=1 and';

        }

        else if($sallerType=="Shop"){

           $sallerType = 'posts.post_type_id=2 and'; 

        }

        else{

         $sallerType = ""; 

        }

        if(!empty($minprice) || !empty($maxprice)){

            $price_query = 'posts.price between '.$minprice.' and '.$maxprice.' and';

        }

        else{

           $price_query = ''; 

        }





        $getcity = \DB::table('users')->where('id', '=', $user_id)->first();

       //print_r($getcity);

       // $getcity[0]['city'];



        if(empty($getcity[0]->city)){

            

             $getdetail = \DB::table('countries')



    ->select('latitude','longitude','capital')



    //->where('code','=', config('country.icode'))

    ->where('code','=', $country)



    ->first();



  $capital = !empty($getdetail->capital)?$getdetail->capital:''; 

  $locationid = !empty($getdetail->id)?$getdetail->id:'';

  $lat = !empty($getdetail->latitude)?$getdetail->latitude:0;

  $lng = !empty($getdetail->longitude)?$getdetail->longitude:0; 

        }else{  

        $city_query = \DB::table('posts')->select('lat','lon')->where('city_name', 'like', '%'.$getcity->city.'%')->first();

        $lat = !empty($city_query->lat)?$city_query->lat:0;

        $lng = !empty($city_query->lon)?$city_query->lon:0;

        

        if($lat == 0 && $lng == 0){

            

       $city_query = \DB::table('cities')->select('latitude','longitude','id')->where('name', 'like', '%'.$getcity->city.'%')->first();

       $lat = !empty($city_query->latitude)?$city_query->latitude:0;

       $lng = !empty($city_query->longitude)?$city_query->longitude:0;

       if(!empty($city_query->id)){$locationid = $city_query->id;}



if($lat == 0 && $lng == 0){ 

$getlocation = $this->getlocationcity($getcity->city); 

   $lat = !empty($getlocation['lat'])?$getlocation['lat']:0;

   $lng = !empty($getlocation['lng'])?$getlocation['lng']:0;   

 $country_code = strtoupper(config('country.code'));	

 

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

        }

if((isset($distance) && !empty($distance))){}else{$distance =300;}	

if($distance ==10){$Latitudinal_Degrees = 0.090909091;$Longitudinal_Degrees = 0.117647059;}

if($distance ==15){$Latitudinal_Degrees = 0.1764705882;$Longitudinal_Degrees = 0.1363636364;}

elseif($distance ==75){$Latitudinal_Degrees = 0.681818182;$Longitudinal_Degrees = 0.882352941;} 

elseif($distance ==150){$Latitudinal_Degrees = 1.363636364;$Longitudinal_Degrees = 1.764705882;} 

elseif($distance ==300){$Latitudinal_Degrees = 3.52941176;$Longitudinal_Degrees = 2.72727273;} 

elseif($distance ==500){$Latitudinal_Degrees = 4.545454545;$Longitudinal_Degrees = 5.882352941;} 

elseif($distance ==750){$Latitudinal_Degrees = 8.8235294118;$Longitudinal_Degrees = 6.8181818182;} 

elseif($distance ==1000){$Latitudinal_Degrees = 9.090909091;$Longitudinal_Degrees = 11.76470588;} 

elseif($distance ==50000){$Latitudinal_Degrees = 454.54545455;$Longitudinal_Degrees = 588.235294;} 

$Max_Latitudinal =  (float)$lat  +  (float)$Latitudinal_Degrees;

$Min_Latitudinal =  (float)$lat  -  (float)$Latitudinal_Degrees;

$Max_Longitudinal = (float)$lng + (float)$Longitudinal_Degrees;

$Min_Longitudinal = (float)$lng - (float)$Longitudinal_Degrees;

$Min_Latitudinal=str_replace(",",".",$Min_Latitudinal);

$Max_Latitudinal=str_replace(",",".",$Max_Latitudinal);

$Min_Longitudinal=str_replace(",",".",$Min_Longitudinal);

$Max_Longitudinal=str_replace(",",".",$Max_Longitudinal);

 $cities_query = DB::select('select distinct `city_name` from `posts` where `lat` between '.$Min_Latitudinal.' and '.$Max_Latitudinal.' 

 and `lon` between '.$Min_Longitudinal.' and '.$Max_Longitudinal.'');

$cities=array();	

$cities[]=request()->get('location');

//print_r($cities_query);

if(!empty($cities_query)){

foreach($cities_query as $city_nm){

$cities[]=$city_nm->city_name;

}

}

      

    $sql = 'SELECT

posts.id ,posts.country_code,posts.user_id,posts.category_id,posts.post_type_id,posts.title,posts.description,posts.tags,posts.price,posts.negotiable,posts.contact_name,posts.email,posts.phone,posts.phone_hidden,posts.address,posts.city_id,posts.city_name,posts.lon,posts.lat,posts.ip_addr,posts.visits,posts.email_token,posts.phone_token,posts.tmp_token,posts.verified_email,posts.verified_phone,posts.reviewed,posts.featured,posts.archived,posts.fb_profile,posts.partner,posts.premium_email,posts.premium_phone,posts.created_at,posts.updated_at,posts.deleted_at,pictures.id as pictureId,pictures.post_id,pictures.filename,pictures.position,pictures.active,pictures.created_at,pictures.updated_at,post_values.id as postvalueId,post_values.post_id,post_values.field_id,post_values.option_id,post_values.value,post_types.id as postTypeId,post_types.name FROM posts

INNER JOIN pictures ON posts.id = pictures.post_id

INNER JOIN post_values ON  posts.id = post_values.post_id

INNER JOIN post_types ON posts.post_type_id = post_types.id

WHERE '.$catId.' '.$field_values_custom.' '.$field_values.' '.$price_query.' '.$sallerType.' posts.country_code="'.$country.'" 

and posts.archived!=1 ';  



if(!empty($cities))

{  

 

    $sql .= 'and posts.city_name IN ';  

    $IDsListsx = $cities;

    $IDsListx = '( "1"';

    foreach($IDsListsx as $key){

        $IDsListx .= ',"'.$key.'"';

    }

    $IDsListx .= ')';

    $sql .= $IDsListx;

   

  }	

  $sql .= ' ORDER BY posts.price '.$order.'';





       //$sql = 'SELECT * from posts where '.$price_query.' '.$sallerType.' country_code="'.$country.'" and archived!=1 '.$sallerType.' ORDER BY price '.$order.''; 

        /*$sql = 'SELECT * from posts where price between '.$minprice.' and '.$maxprice.' and category_id in (select id from categories where parent_id='.$category.' and active=1) and country_code="'.$country.'" and archived!=1 ';*/

// 		if(!empty($minprice) && !empty($maxprice))

// 		{

		   

// 		    $price_query = 'price between '.$minprice.' and '.$maxprice.' and';

// 		    $sql = 'SELECT * from posts where price between '.$minprice.' and '.$maxprice.' and country_code="'.$country.'" and archived!=1 '.$sallerType.' ORDER BY price '.$order.'';



// 		    }

// 		else if(!empty($minprice))

// 		{

		    

// 		    $price_query = 'price>='.$minprice.' and';

// 		$sql = 'SELECT * from posts where price>='.$minprice.' and country_code="'.$country.'" and archived!=1 '.$sallerType.' ORDER BY price '.$order.'';

// 		}

// 		else if(!empty($maxprice))

// 		{

		  

// 		    $price_query = 'price>='.$maxprice.' and';

// 		$sql = 'SELECT * from posts where price <= '.$maxprice.' and country_code="'.$country.'" and archived!=1 '.$sallerType.' ORDER BY price '.$order.'';

// 		}

// 		else if(!empty($allAds)){

		    

// 		    $sql = 'SELECT posts.*, pictures.* FROM posts LEFT JOIN pictures ON posts.id = pictures.post_id WHERE pictures.post_id = posts.id';

// 		}

// 		else if(!empty($condition)){

		    

// 		    $sql = 'SELECT posts.*, post_values.* FROM posts LEFT JOIN post_values ON posts.id = post_values.post_id WHERE post_values.post_id = posts.id '.$price_query.' AND option_id="'.$condition.'"';

// 		}

// 		else if(!empty($deliveryCost)){

		    

// 		    $sql = 'SELECT posts.*, post_values.* FROM posts LEFT JOIN post_values ON posts.id = post_values.post_id WHERE post_values.post_id = posts.id '.$price_query.' AND option_id="'.$deliveryCost.'"';

// 		}

// 		else if(!empty($paymentPreference)){

		    

// 		    $sql = 'SELECT posts.*, post_values.* FROM posts LEFT JOIN post_values ON posts.id = post_values.post_id WHERE post_values.post_id = posts.id '.$price_query.' AND option_id="'.$paymentPreference.'"';

// 		}

// 		else

// 		{

// 		$sql = 'SELECT * from posts where country_code="'.$country.'" and archived!=1 ';

// 		}

        $bindings = [

            //'countryCode' => $country,

        ];

		



        //$cacheId = $country_code . '.home.getPosts.' . $type;

    // $posts = array();

//$array = DB::table('demotable')->select('*')->get();

        $posts = DB::select($sql); //->paginate(1);

        

        //$posts = array_values(array_unique($posts));;

//echo count($posts);

      // print_r($posts);

       

		$ids = array_column($posts, 'id');

		$ids = array_unique($ids);

		

		$posts = array_filter($posts, function ($key, $value) use ($ids) {

    return in_array($value, array_keys($ids));

}, ARRAY_FILTER_USE_BOTH);



        $i=0;

        

        if (isset($request->page)){

            $collect = collect($posts);

            $posts = new LengthAwarePaginator(

                $collect->forPage($request->page, 10),

                $collect->count(),

                10,

                $request->page

            );

            $posts = $posts->toArray()['data'];

        }

        

		foreach($posts as $key => $post){

		    //print_r($post);

		   

		                        

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

                            

					                $post->currency = $get_currency;

						                

						              

									  

									  $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $post->contact_name = $getusernamedetail->username;     

		    

		    

		    

		        	                    /*$package = '';

										if ($post->featured == 1) {

											$package = \App\Models\Package::findTransApp($post->py_package_id,$lang);

										}

										

										

										if(!empty($post->py_package_id))

										{

								        $post->py_package_id = $post->py_package_id;

								        }

										else

										{

										$post->py_package_id = 'No Value';

										}

										

										

										if(!empty($package))

										{

								        $post->package = $package;

								        }

										else

										{

										$post->package = 'No Value';

										}*/

								        

								        

										

										$postType = \App\Models\PostType::findTransApp($post->post_type_id, $lang);

											//return $postType;

										$post->postType = $postType;

										

										

							

										// Get Post's Pictures

										$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

										if ($pictures->count() > 0) {

											$postImg = resize($pictures->first()->filename, 'medium');

										} else {

										    

											$postImg = resize(config('larapen.core.picture.default'));

										}

										$post->postImg = $postImg;

										

										$city = \App\Models\City::find($post->city_id);

										$post->cityy = $city;

										

										

										

										$post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));

										$post->created_at = $post->created_at->ago();

										

										$liveCat = \App\Models\Category::findTransApp($post->category_id, $lang);

											//return $liveCat;

										$post->liveCat = $liveCat;

										

										// Check parent

										if (empty($liveCat->parent_id)) {

											$liveCatParentId = $liveCat->id;

											$liveCatType = $liveCat->type;

										} else {

											$liveCatParentId = $liveCat->parent_id;

											

											$liveParentCat = \App\Models\Category::findTransApp($liveCat->parent_id, $language);

											//echo $liveParentCat;

											

										if(isset($language))

										{

										$lang1 = $language;

										}

										else

										{

										$lang1 = 'en';

										}	

											$bindings = [

            'translation_lang' => $language,

        ];



        if (!isset($liveParentCat->tid)) continue;



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

										

                                        //$post->paymentpre = $results;

                                        $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                        $username = $getusernamedetail->username;  

										$user_created_at = $getusernamedetail->created_at;      

										$post->liveCatName = $liveCatName;

										$post->username = $username;

                                        //$post->user_created_at = $user_created_at;

                                        $post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));

                                        $post->user_created_at = $post->user_created_at->ago();

                                        

                                        if (!empty($user_id)){

                                             $scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post->id)->count();

                                                        if($scount>0){$post->saved = 'Yes';}

                                                        else{$post->saved = 'No';}

                                            }else{$post->saved = 'No'; }

                                            

                                        $i++;

						}

		return response()->json(['status'=>1,'message'=>'success','results'=>array_values($posts)]);

	}











   //  user_id category  minprice maxprice distance subCatId  country

   // sortBy  sallerType  allAds  condition  paymentPreference  deliveryCost  customFilteredData









   public function customsearch_new_app(Request $request)

   {



       //in view all of catogries page paramters must be remove to work good

       \App::setLocale(request()->get('language') ?: 'en');

       //echo 'trans='.t('hour');

       $paymentJoin = '';

       $sponsoredCondition = '';

       $sponsoredOrder = '';

       //$user_id = $request->user_id;

       $user_id = $request->user_id;

       $category = $request->category;

       $minprice = $request->minprice ?: 0;

       $maxprice = $request->maxprice;

       $distance = $request->distance;

       $subCatId = $request->subCatId;

       $country = strtoupper($request->country);

       $language = $request->language;

       $lang = $request->language;

       $sortBy = $request->sortBy;

       $sallerType = $request->sallerType;

       $allAds = $request->allAds;

       $condition = $request->condition;

       $paymentPreference = $request->paymentPreference;

       $deliveryCost = $request->deliveryCost;

       $customFilteredData = $request->customFilteredData;

       if(empty($request->page)){$request->page=1;}

       else{$request->page=$request->page+1;}      

       $catIdarray=array();

       $field_values_custom_array=array();

       $field_values_array=array();

       if(!empty($condition) && !empty($paymentPreference) && !empty($deliveryCost)){

           $field_values = 'post_values.option_id IN ('.$condition.','.$paymentPreference.','.$deliveryCost.') and';

           $field_values_array[]=$condition;

           $field_values_array[]=$paymentPreference;

           $field_values_array[]=$deliveryCost;

       }

       else{ 

           $field_values = '';

       }

       if(!empty($customFilteredData)){

           $field_values_custom = 'post_values.option_id IN ('.$customFilteredData.') and';

       }

       else{

           $field_values_custom='';

       } 

       if(!empty($subCatId)){

           $searchCat = Category::find($subCatId);

           $mainID = Category::where('id', $searchCat->translation_of)->first();

           $IDsList = $this->getAllSubs($mainID->id, $searchCat->translation_lang);

           $catId = '(posts.category_id='.$mainID->id;

           $catIdarray[]=$mainID->id;

           foreach($IDsList as $key){

               $catId .= ' or posts.category_id='.$key;

               $catIdarray[]=$key;

           }

           $catId .= ') and';

       }

       else{

          $catId = ''; 

       }

       if($allAds=="ads with images"){

           $allAds = "ads with images";

       }

       else{

          $allAds = ''; 

       }

       if($sortBy=="LtoH"){

           $order = "ASC";

           $order_fld ="price";

       }

       else if($sortBy=="HtoL"){

            $order = "DESC";

            $order_fld ="price";

       }

       else if($sortBy=="OtoN"){

           $order = "ASC";

           $order_fld ="created_at";

      }

      else if($sortBy=="NtoO"){

       $order = "DESC";

       $order_fld ="created_at";

        }

       else{

           $order = "DESC";

           $order_fld ="created_at";

       }

       if($sallerType=="Individual"){

           $sallerType = 'posts.post_type_id=1 and';

       }

       else if($sallerType=="Shop"){

          $sallerType = 'posts.post_type_id=2 and'; 

       }

       else{

        $sallerType = ""; 

       }

       if(!empty($minprice) || !empty($maxprice)){

           $price_query = 'posts.price between '.$minprice.' and '.$maxprice.' and';

       }

       else{

          $price_query = ''; 

       }

       $getcity = \DB::table('users')->where('id', '=', $user_id)->first();

       if(empty($getcity[0]->city)){

           $getdetail = \DB::table('countries')

           ->select('latitude','longitude','capital')

           ->where('code','=', $country)->first();

           $capital = !empty($getdetail->capital)?$getdetail->capital:'';

           $locationid = !empty($getdetail->id)?$getdetail->id:'';

           $lat = !empty($getdetail->latitude)?$getdetail->latitude:0;

           $lng = !empty($getdetail->longitude)?$getdetail->longitude:0;

        }else{

            $city_query = \DB::table('posts')->select('lat','lon')->where('city_name', 'like', '%'.$getcity->city.'%')->first();

            $lat = !empty($city_query->lat)?$city_query->lat:0;

           $lng = !empty($city_query->lon)?$city_query->lon:0;

    if($lat == 0 && $lng == 0){

      $city_query = \DB::table('cities')->select('latitude','longitude','id')->where('name', 'like', '%'.$getcity->city.'%')->first();

      $lat = !empty($city_query->latitude)?$city_query->latitude:0;

      $lng = !empty($city_query->longitude)?$city_query->longitude:0;

      if(!empty($city_query->id)){$locationid = $city_query->id;}

if($lat == 0 && $lng == 0){

    

   $getlocation = $this->getlocationcity($getcity->city);

   $lat = !empty($getlocation['lat'])?$getlocation['lat']:0;

   $lng = !empty($getlocation['lng'])?$getlocation['lng']:0;

   $country_code = strtoupper(config('country.code'));



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



       }





       // Pre-Search values  

if((isset($distance) && !empty($distance))){}else{$distance =50000;}	





if($distance ==10){$Latitudinal_Degrees = 0.090909091;$Longitudinal_Degrees = 0.117647059;}

if($distance ==15){$Latitudinal_Degrees = 0.1764705882;$Longitudinal_Degrees = 0.1363636364;}

elseif($distance ==75){$Latitudinal_Degrees = 0.681818182;$Longitudinal_Degrees = 0.882352941;} 

elseif($distance ==150){$Latitudinal_Degrees = 1.363636364;$Longitudinal_Degrees = 1.764705882;} 

elseif($distance ==300){$Latitudinal_Degrees = 3.52941176;$Longitudinal_Degrees = 2.72727273;} 

elseif($distance ==500){$Latitudinal_Degrees = 4.545454545;$Longitudinal_Degrees = 5.882352941;} 

elseif($distance ==750){$Latitudinal_Degrees = 8.8235294118;$Longitudinal_Degrees = 6.8181818182;} 

elseif($distance ==1000){$Latitudinal_Degrees = 9.090909091;$Longitudinal_Degrees = 11.76470588;} 

elseif($distance ==50000){$Latitudinal_Degrees = 454.54545455;$Longitudinal_Degrees = 588.235294;} 







$Max_Latitudinal =  (float)$lat  +  (float)$Latitudinal_Degrees;

$Min_Latitudinal =  (float)$lat  -  (float)$Latitudinal_Degrees;

$Max_Longitudinal = (float)$lng + (float)$Longitudinal_Degrees;

$Min_Longitudinal = (float)$lng - (float)$Longitudinal_Degrees;





$Min_Latitudinal=str_replace(",",".",$Min_Latitudinal);

$Max_Latitudinal=str_replace(",",".",$Max_Latitudinal);

$Min_Longitudinal=str_replace(",",".",$Min_Longitudinal);

$Max_Longitudinal=str_replace(",",".",$Max_Longitudinal);



$cities_query = DB::select('select distinct `city_name` from `posts` where `lat` between '.$Min_Latitudinal.' and '.$Max_Latitudinal.' 

and `lon` between '.$Min_Longitudinal.' and '.$Max_Longitudinal.'');





$cities=array();	

$cities[]=request()->get('location');

//print_r($cities_query);

if(!empty($cities_query)){

foreach($cities_query as $city_nm){

$cities[]=$city_nm->city_name;



}



}

 



   $sql=\DB::table('posts')



   ->leftJoin('pictures', function ($join) {

    $join->on('pictures.id', '=', DB::raw('(SELECT id FROM pictures WHERE pictures.post_id = posts.id LIMIT 1)'));

})



   //->leftJoin('pictures', 'pictures.post_id', '=', 'posts.id')

   ->leftJoin('post_values', function ($join) {

    $join->on('post_values.id', '=', DB::raw('(SELECT id FROM post_values WHERE post_values.post_id = posts.id LIMIT 1)'));

})



   //->leftJoin('post_values', 'post_values.post_id', '=', 'posts.id')     

   //->leftJoin('post_types', 'post_types.id', '=', 'posts.post_type_id')



   ->leftJoin('post_types', function ($join) {

    $join->on('post_types.id', '=', DB::raw('(SELECT id FROM post_types WHERE post_types.id = posts.post_type_id LIMIT 1)'));

})



   ->select('posts.id','posts.country_code','posts.user_id','posts.premium','posts.category_id','posts.post_type_id','posts.title','posts.description',

   'posts.tags','posts.price','posts.negotiable','posts.contact_name','posts.email','posts.phone','posts.phone_hidden','posts.address'

   ,'posts.city_id','posts.city_name','posts.lon','posts.lat','posts.ip_addr','posts.visits','posts.email_token','posts.phone_token','posts.tmp_token',

   'posts.verified_email','posts.verified_phone','posts.reviewed','posts.featured','posts.archived','posts.fb_profile','posts.partner',

   'posts.premium_email','posts.premium_phone','posts.created_at','posts.updated_at','posts.deleted_at','pictures.id as pictureId',

   'pictures.post_id','pictures.filename','pictures.position','pictures.active','pictures.created_at','pictures.updated_at',

   'post_values.id as postvalueId','post_values.post_id','post_values.field_id','post_values.option_id','post_values.value',

   'post_types.id as postTypeId','post_types.name');

   //->where('user_id', '=', $post->user_id)

   if(!empty($catIdarray)){ 

   $sql->whereIn('posts.category_id',$catIdarray);//done 

   }

   if(!empty($customFilteredData)){ 

   $sql->whereIn('post_values.option_id',$customFilteredData);//done  

   }

   if(!empty($field_values_array)){ 

   $sql->whereIn('post_values.option_id',$field_values_array);//done  

   } 

   if(!empty($minprice) || !empty($maxprice)){     

       $sql->whereBetween('posts.price', [$minprice, $maxprice]);     

   }

   if($sallerType=="Individual"){    

       $sql->where('posts.post_type_id', '=', 1);

   }

   else if($sallerType=="Shop"){   

      $sql->where('posts.post_type_id', '=', 2);

   } 

   $cities_array=array();

    

   

   $sql->where('archived', '=', 0)

   ->where('reviewed', '=', 1)

   //->where('country_code', '=',$country)

   ->where('country_code', '=','KW')

   ->orderBy('id', 'desc') 

    ->limit(10);



   $posts=$sql->get();

    //$posts=$posts->toArray();



   /*

  //$posts = (array)$posts;

   $ids = array_column($posts, 'id');

   $ids = array_unique($ids);

   $posts = array_filter($posts, function ($key, $value) use ($ids) {

    return in_array($value, array_keys($ids));

}, ARRAY_FILTER_USE_BOTH); 

   //dd($sql);*/

   //return $posts;

//dd($posts);





 





   $i=0;

   if (isset($request->page) ){

           

          $collect = collect($posts)->sortByDesc('posts.id');

          //  print_r($collect);

           $posts = new LengthAwarePaginator(

                $collect->forPage($request->page, 10),

               $collect->count(),                

               10,

               $request->page

           );

    

   $posts = $posts->toArray()['data'];

/* */

       }



    



       

       foreach($posts as $key => $post){

                               

        

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

                           

                                   $post->currency = $get_currency;

                                       

                                     

                                     

                                     $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                       $post->contact_name = $getusernamedetail->username;     

           

           

           

                                       /*$package = '';

                                       if ($post->featured == 1) {

                                           $package = \App\Models\Package::findTransApp($post->py_package_id,$lang);

                                       }

                                       

                                       

                                       if(!empty($post->py_package_id))

                                       {

                                       $post->py_package_id = $post->py_package_id;

                                       }

                                       else

                                       {

                                       $post->py_package_id = 'No Value';

                                       }

                                       

                                       

                                       if(!empty($package))

                                       {

                                       $post->package = $package;

                                       }

                                       else

                                       {

                                       $post->package = 'No Value';

                                       }*/

                                       

                                       

                                       

                                       $postType = \App\Models\PostType::findTransApp($post->post_type_id, $lang);

                                           //return $postType;

                                       $post->postType = $postType;

                                       

                                       

                           

                                       // Get Post's Pictures

                                       $pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

                                       if ($pictures->count() > 0) {

                                           $postImg = resize($pictures->first()->filename, 'medium');

                                       } else {

                                           

                                           $postImg = resize(config('larapen.core.picture.default'));

                                       }

                                       $post->postImg = $postImg;

                                       

                                       $city = \App\Models\City::find($post->city_id);

                                       $post->cityy = $city;

                                       

                                       

                                       

                                       $post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));

                                       $post->created_at = $post->created_at->ago();

                                       

                                       $liveCat = \App\Models\Category::findTransApp($post->category_id, $lang);

                                           //return $liveCat;

                                       $post->liveCat = $liveCat;

                                       

                                       // Check parent

                                       if (empty($liveCat->parent_id)) {

                                           $liveCatParentId = $liveCat->id;

                                           $liveCatType = $liveCat->type;

                                       } else {

                                           $liveCatParentId = $liveCat->parent_id;

                                           

                                           $liveParentCat = \App\Models\Category::findTransApp($liveCat->parent_id, $language);

                                           //echo $liveParentCat;

                                           

                                       if(isset($language))

                                       {

                                       $lang1 = $language;

                                       }

                                       else

                                       {

                                       $lang1 = 'en';

                                       }	

                                           $bindings = [

           'translation_lang' => $language,

       ];



       if (!isset($liveParentCat->tid)) continue;



       $sql = 'select * from categories where translation_of="'.$liveParentCat->tid.'" and translation_lang="'.$lang.'" ';



       $categories = DB::select($sql);

                      //  print_r($categories);

                       //echo 'id='.$liveParentCat->parent_id;		

                       //echo 'name='.$categories[0]->name;			

                                           $liveParentCat->name = $categories[0]->name;			

                                           

                                           $post->liveParentCat = $liveParentCat;

                                           

                                           

                                           $liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';

                                       }

                                       

                                       // Check translation

                                       $liveCatName = $liveCat->name;

                                       //$post->paymentpre = $results;	

                                       

                                       

                                       

                                       

                                       

                                       

                                       $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                       $username = $getusernamedetail->username;  

                                       $user_created_at = $getusernamedetail->created_at;      

                                       $post->liveCatName = $liveCatName;

                                       $post->username = $username;

                                       //$post->user_created_at = $user_created_at;

                                       

                                       $post->user_created_at = \Date::parse($user_created_at)->timezone(config('timezone.id'));

                                       $post->user_created_at = $post->user_created_at->ago();

                                       

                                       

                                       

                                       if (!empty($user_id))

                                                   {

                                                   $scount = \App\Models\SavedPost::where('user_id', $user_id)->where('post_id', $post->id)->count();

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

       

       

      

 //$postArray = json_decode(json_encode($posts),true);



 //return $postArray[0]->current_page;       

 

      //return response()->json(['status'=>1,'message'=>'success','results'=>array_values($posts)]);

        return response()->json(['status'=>1,'message'=>'success','results'=>$posts]);

       

       

       

   

   

   }











    public function customsearch_app(Request $request)

    {



        //in view all of catogries page paramters must be remove to work good

        \App::setLocale(request()->get('language') ?: 'en');

		//echo 'trans='.t('hour');

        $paymentJoin = '';

        $sponsoredCondition = '';

        $sponsoredOrder = '';

		//$user_id = $request->user_id;

        $user_id = $request->user_id;

		$category = $request->category;

		$minprice = $request->minprice ?: 0;

		$maxprice = $request->maxprice;

        $serachKey = $request->serachKey;

		$distance = $request->distance;

		 $subCatId = $request->subCatId;

         if($subCatId=='4188'){

           // $subCatId = '';

         }

        

		$country = strtoupper($request->country);

		$language = $request->language;

        $lang = $request->language;

        $sortBy = $request->sortBy;

        $sallerType = $request->sallerType;

        $allAds = $request->allAds;

        $condition = $request->condition;

        $paymentPreference = $request->paymentPreference;

        $deliveryCost = $request->deliveryCost;

        $customFilteredData = $request->customFilteredData;

        if(empty($request->page)){$request->page=1;}

        else{$request->page=$request->page+1;}

        // $customFilteredData = str_replace("\"","",$customFilteredData);

        // $customFilteredData = json_decode($customFilteredData,true);

        // print_r($customFilteredData);

        // die();

        // print_r($customFilteredData);

        // foreach($customFilteredData as $customFiltered){

        //   print_r($customFiltered);

        // }

        if(!empty($condition) && !empty($paymentPreference) && !empty($deliveryCost)){

            $field_values = 'post_values.option_id IN ('.$condition.','.$paymentPreference.','.$deliveryCost.') and';

        }

        else{ 

            $field_values = '';

        }

        if(!empty($customFilteredData)){

            $field_values_custom = 'post_values.option_id IN ('.$customFilteredData.') and';

        }

        else{

            $field_values_custom='';

        }

        if(!empty($subCatId)){

            $searchCat = Category::find($subCatId);

            $mainID = Category::where('id', $searchCat->translation_of)->first();

            $IDsList = $this->getAllSubs($mainID->id, $searchCat->translation_lang);

            $catId = '(posts.category_id='.$mainID->id;

            foreach($IDsList as $key){

                $catId .= ' or posts.category_id='.$key;

            }

        //    $catId = 'category_id='.$subCatId.' and';

           $catId .= ') and';

        }

        else{

           $catId = ''; 

        }

        if($allAds=="ads with images"){

            $allAds = "ads with images";

        }

        else{

           $allAds = ''; 

        }

        if($sortBy=="LtoH"){

            $order = "ASC";

            $order_fld ="price";

        }

        else if($sortBy=="HtoL"){

             $order = "DESC";

             $order_fld ="price";

        }

        else if($sortBy=="OtoN"){

            $order = "ASC";

            $order_fld ="created_at";

       }

       else if($sortBy=="NtoO"){

        $order = "DESC";

        $order_fld ="created_at";

         }

        else{

            $order = "DESC";

            $order_fld ="created_at";

        }

        if($sallerType=="Individual"){

            $sallerType = 'posts.post_type_id=1 and';

        }

        else if($sallerType=="Shop"){

           $sallerType = 'posts.post_type_id=2 and'; 

        }

        else{

         $sallerType = ""; 

        }

        if(!empty($minprice) || !empty($maxprice)){

            $price_query = 'posts.price between '.$minprice.' and '.$maxprice.' and';

        }

        else{

           $price_query = ''; 

        }

      

        if(!empty($serachKey) || !empty($serachKey)){

            $serachKey_query = "posts.title like  '%".$serachKey."%' and";

        }

        else{

           $serachKey_query = ''; 

        }



        $getcity = \DB::table('users')->where('id', '=', $user_id)->first();

       //print_r($getcity);

       // $getcity[0]['city'];



        if(empty($getcity[0]->city)){

            

             $getdetail = \DB::table('countries')



    ->select('latitude','longitude','capital')



    //->where('code','=', config('country.icode'))

    ->where('code','=', $country)



    ->first();



  $capital = !empty($getdetail->capital)?$getdetail->capital:'';



 $locationid = !empty($getdetail->id)?$getdetail->id:'';

 

   $lat = !empty($getdetail->latitude)?$getdetail->latitude:0;

   $lng = !empty($getdetail->longitude)?$getdetail->longitude:0;

 

        }else{

                

                

        $city_query = \DB::table('posts')->select('lat','lon')->where('city_name', 'like', '%'.$getcity->city.'%')->first();

        $lat = !empty($city_query->lat)?$city_query->lat:0;

        $lng = !empty($city_query->lon)?$city_query->lon:0;

        

        if($lat == 0 && $lng == 0){

            

       $city_query = \DB::table('cities')->select('latitude','longitude','id')->where('name', 'like', '%'.$getcity->city.'%')->first();

       $lat = !empty($city_query->latitude)?$city_query->latitude:0;

       $lng = !empty($city_query->longitude)?$city_query->longitude:0;

       if(!empty($city_query->id)){$locationid = $city_query->id;}



if($lat == 0 && $lng == 0){

  

  

$getlocation = $this->getlocationcity($getcity->city);

 

   $lat = !empty($getlocation['lat'])?$getlocation['lat']:0;

   $lng = !empty($getlocation['lng'])?$getlocation['lng']:0;

   

   $country_code = strtoupper(config('country.code'));		

 

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



        }





		// Pre-Search values  

if((isset($distance) && !empty($distance))){}else{$distance =50000;}	





if($distance ==10){$Latitudinal_Degrees = 0.090909091;$Longitudinal_Degrees = 0.117647059;}

if($distance ==15){$Latitudinal_Degrees = 0.1764705882;$Longitudinal_Degrees = 0.1363636364;}

elseif($distance ==75){$Latitudinal_Degrees = 0.681818182;$Longitudinal_Degrees = 0.882352941;} 

elseif($distance ==150){$Latitudinal_Degrees = 1.363636364;$Longitudinal_Degrees = 1.764705882;} 

elseif($distance ==300){$Latitudinal_Degrees = 3.52941176;$Longitudinal_Degrees = 2.72727273;} 

elseif($distance ==500){$Latitudinal_Degrees = 4.545454545;$Longitudinal_Degrees = 5.882352941;} 

elseif($distance ==750){$Latitudinal_Degrees = 8.8235294118;$Longitudinal_Degrees = 6.8181818182;} 

elseif($distance ==1000){$Latitudinal_Degrees = 9.090909091;$Longitudinal_Degrees = 11.76470588;} 

elseif($distance ==50000){$Latitudinal_Degrees = 454.54545455;$Longitudinal_Degrees = 588.235294;} 

 





$Max_Latitudinal =  (float)$lat  +  (float)$Latitudinal_Degrees;

$Min_Latitudinal =  (float)$lat  -  (float)$Latitudinal_Degrees;

$Max_Longitudinal = (float)$lng + (float)$Longitudinal_Degrees;

$Min_Longitudinal = (float)$lng - (float)$Longitudinal_Degrees;





$Min_Latitudinal=str_replace(",",".",$Min_Latitudinal);

$Max_Latitudinal=str_replace(",",".",$Max_Latitudinal);

$Min_Longitudinal=str_replace(",",".",$Min_Longitudinal);

$Max_Longitudinal=str_replace(",",".",$Max_Longitudinal);



 $cities_query = DB::select('select distinct `city_name` from `posts` where `lat` between '.$Min_Latitudinal.' and '.$Max_Latitudinal.' 

 and `lon` between '.$Min_Longitudinal.' and '.$Max_Longitudinal.'');

 



$cities=array();	

$cities[]=request()->get('location');

//print_r($cities_query);

if(!empty($cities_query)){

foreach($cities_query as $city_nm){

$cities[]=$city_nm->city_name;



}



}

      

 $sql = 'SELECT

posts.id ,posts.country_code,posts.user_id,posts.premium,posts.category_id,posts.post_type_id,posts.title,posts.description,posts.tags,posts.price,posts.negotiable,posts.contact_name,posts.email,posts.phone,posts.phone_hidden,posts.address,posts.city_id,posts.city_name,posts.lon,posts.lat,posts.ip_addr,posts.visits,posts.email_token,posts.phone_token,posts.tmp_token,posts.verified_email,posts.verified_phone,posts.reviewed,posts.featured,posts.archived,posts.fb_profile,posts.partner,posts.premium_email,posts.premium_phone,posts.created_at,posts.updated_at,posts.deleted_at,pictures.id as pictureId,pictures.post_id,pictures.filename,pictures.position,pictures.active,pictures.created_at,pictures.updated_at,post_values.id as postvalueId,post_values.post_id,post_values.field_id,post_values.option_id,post_values.value,post_types.id as postTypeId,post_types.name FROM posts

left JOIN pictures ON posts.id = pictures.post_id

left JOIN post_values ON  posts.id = post_values.post_id

left JOIN post_types ON posts.post_type_id = post_types.id

WHERE  '.$catId.' '.$field_values_custom.' '.$field_values.' '.$price_query.' '.$serachKey_query.' '.$sallerType.' posts.country_code="'.$country.'" 

and posts.archived!=1 ';  



 

 

if(!empty($cities))

{  

 

    $sql .= 'and posts.city_name IN ';  

//$cities="[".implode(",",$cities)."]";

 

    $IDsListsx = $cities;

    $IDsListx = '( "1"';

    foreach($IDsListsx as $key){

        $IDsListx .= ',"'.$key.'"';

    }

    $IDsListx .= ')';

    $sql .= $IDsListx;

   

  }	

   

   



  //  abdelhay 23-9-2021 $sql .= ' ORDER BY  posts.id desc , posts.'.$order_fld.' '.$order.'';

  $sql .= ' ORDER BY  posts.id desc ';





      

        $bindings = [

            //'countryCode' => $country,

        ];

        $posts = DB::select($sql);

        $ids = array_column($posts, 'id');

		$ids = array_unique($ids);

		

		 $posts = array_filter($posts, function ($key, $value) use ($ids) {

    return in_array($value, array_keys($ids));

}, ARRAY_FILTER_USE_BOTH); 



        $i=0;

        

        if (isset($request->page)){

           

$items =  $posts; 

if($request->page==1){

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

    }else{      

        $currentPage=$request->page;

    }



$perPage = 100;

$currentItems = array_slice($items, $perPage * ($currentPage - 1), $perPage);



$posts = new LengthAwarePaginator($currentItems, count($items), $perPage, $currentPage);







            

            $posts = $posts->toArray()['data'];

        }

        

		//foreach($posts as $key => $post){}



        $filterAllPosts=array();$w=0;

		foreach($posts as $post){

			

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

			  $filterAllPosts[$w]['price']=$post->price; 

			  $filterAllPosts[$w]['formmated_price']=$post->currency;

			  $filterAllPosts[$w]['city_name']=$post->city_name;

              $middle = strtotime($post->created_at);             // returns bool(false)

              $new_date = date('Y-m-d', $middle);           

			  $filterAllPosts[$w]['created_at']=(object) [  'date' => $new_date];

			  $filterAllPosts[$w]['postImg']=$post->postImg; 			 

			  $w++;

		   }

			

      

		$data = $filterAllPosts;

		

		

	 



		return response()->json(['status'=>1,'message'=>'success','results'=>array_values($data)]);

		

		

		

    

	

	}



    /**

     * @param array $options

     * @return int

     */

    private function getCacheExpirationTime($options = [])

    {

        // Get the default Cache Expiration Time

        $cacheExpiration = 0;

        if (isset($options['cache_expiration'])) {

            $cacheExpiration = (int)$options['cache_expiration'];

        }

		//print_r($cacheExpiration);

        return $cacheExpiration;

    }



    public function saveNewsLetterEmail(Request $request)

    {

        $newsletteremail = $request->newsLetterVal;



        if (!empty($newsletteremail)) {

            $newsletter = new Newsletter();

            $newsdata = Newsletter::where('news_letter_email', $newsletteremail)->first();

            if (empty($newsdata)) {

                $newsletter->news_letter_email = $newsletteremail;

                $newsletter->save();

                $arr = array('msg' => "Successfully Registered.");

            } else {

                $arr = array('msg' => "You have already registered for Newsletter");

            }



        } else {

            $arr = array('msg' => "Please Enter Email");

        }



        return \Response::json($arr);

    }



    public function setCurrency($currency)

    {

        if ($currency) {

            Session::put('currency', $currency);

            $arr = array('status' => 'success', 'msg' => "Currency set successfully.", 'result' => array());

        } else {

            $arr = array('status' => 'success', 'msg' => "Currency set successfully.", 'result' => array());

        }

        return \Response::json($arr);

    }

    

    

    

    public function getPostImage(Request $request)

    {

        if(!empty($request->post_id))

        {

            $getimage = DB::table('pictures')

                   ->select('filename')

                   ->where('post_id',$request->post_id)

                   ->where('position',1)

                   ->first();

                   

                   

            if(!empty($getimage->filename))

		    {

		        	$postImg = resize($getimage->filename, 'medium');

		    }

		    else

		    {

	        	$postImg = resize(config('larapen.core.picture.default'));

		    }

		    

		    $arr['success'] = 1; 

		    $arr['image'] = $postImg;

		    

      

        }

        else

        {

            $arr['success'] = 0; 

		    $arr['message'] = 'Parameter Missing !';

        }

        

       return \Response::json($arr); 

        

        

    }

    

    

    

    public function getLocationSearch(Request $request)

    {   

            if(!empty($request->search))

            {

                header("Cache-Control: private, max-age=86400");

                header("Expires: ".gmdate('r', time()+86400));

                $query = $request->search;

                $countrycode= $request->countrycode;

                $apikey = config('services.googlemaps.key');

                

                

                

                // $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?key='.$apikey.'&types=geocode&sensor=true&radius=12000&components=country:'.$countrycode.'&input='.urlencode($query);

                

                $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?key='.$apikey.'&libraries=places&types=(cities)&components=country:'.$countrycode.'&input='.urlencode($query);

                

                

                

                

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $data1 = curl_exec($ch);

                curl_close($ch);

                $details = json_decode($data1, true);

                header("Content-Type: application/json");

                $arr = array();

                foreach($details['predictions'] as $key=>$row) {

                    $arr[] = $row['description'];

                }

                $json['status'] = 'success';

                $json['msg'] = 'Location found!';

                $json['result'] = $arr;

                

            }

            else

            {

                $json['status'] = 'error';

                $json['msg'] = 'Parameter missing!';

                $json['result'] = array();

            }

            echo json_encode($json);    

    }

    

     public function forgotPassword_app(Request $request)

    {   

        $email = $query = $request->email;

        $user = \DB::table('users')->where('email', '=', $email)->first();

        if(!empty($user)){

            $user = User::where('email', request()->input('email'))->first();

            if(!empty($user)){

            $token = Password::getRepository()->create($user);

            $msg = "Clicking the Reset Password button, copy and paste the URL below into your web browser: https://www.tmmat.com/password/reset/".$token;

            $from = "admin@tmmat.com";

            $headers  = "From: $from\r\n";

            $headers .= "Content-type: text/html\r\n";

            if(mail($email,"Tammat Forgot Password",$msg,$headers)){

                $json['status'] = 1;

                $json['message'] = 'We have e-mailed your password reset link!';

                echo json_encode($json);   

            }

            }

            else{

                $json['status'] = 0;

            $json['message'] = 'Email not verified';

            echo json_encode($json); 

            }

        }

        else{

            $json['status'] = 0;

            $json['message'] = 'Invalid Email';

            echo json_encode($json); 

        }

    } 

    

    

    

    

    

    function similarads()

	{

		$maxItems = 20;

       



        // Get the default orderBy value

        $orderBy = 'random';

        

$options = array();

        // Get the default Cache delay expiration

        $cacheExpiration = $this->getCacheExpirationTime($options);



        $sponsored = null;

		// Get featured posts

        $posts = $this->getPosts($maxItems, 'sponsored', $cacheExpiration);



        if (!empty($posts)) {

            if ($orderBy == 'random') {

                $posts = Arr::shuffle($posts);

            }

            $attr = ['countryCode' => config('country.icode')];

            $sponsored = [

                'title' => t('Home - Sponsored Ads'),

                'link' => lurl(trans('routes.v-search', $attr), $attr),

                'posts' => $posts,

            ];

            $sponsored = Arr::toObject($sponsored);

        }

		$featured = $sponsored;

		

		if (isset($featured) and !empty($featured) and !empty($featured->posts))

		{

		foreach($featured->posts as $key => $post):

								if (empty($countries) or !$countries->has($post->country_code)) continue;

			

								// Picture setting

								$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

								if ($pictures->count() > 0) {

									$postImg = resize($pictures->first()->filename, 'medium');

								} else {

									$postImg = resize(config('larapen.core.picture.default'));

								}

			

								// Category

								$cacheId = 'category.' . $post->category_id . '.' . config('app.locale');

								$liveCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

									$liveCat = \App\Models\Category::find($post->category_id);

									return $liveCat;

								});

			

								// Check parent

								if (empty($liveCat->parent_id)) {

									$liveCatType = $liveCat->type;

								} else {

									$cacheId = 'category.' . $liveCat->parent_id . '.' . config('app.locale');

									$liveParentCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($liveCat) {

										$liveParentCat = \App\Models\Category::find($liveCat->parent_id);

										return $liveParentCat;

									});

									$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';

								}

								

								endforeach;



	}

	return response()->json(['results'=>$sponsored]);

	

	}

    

    

    

    public function autocomplete(){

        return DB::table('posts')

        ->where('title', 'like', '%'.request()->q.'%')

        ->orWhere('id', 'like', '%'.request()->q.'%')

        ->orWhere('contact_name', 'like', '%'.request()->q.'%')

        ->orWhere('city_name', 'like', '%'.request()->q.'%')

        ->select(['title'])->get()->pluck('title');

        return 'Working...';

    }

    

    

    public function getAllSubs($id, $lang){

        $IDsss = array();

        $catsss = Category::where('parent_id', $id)->where('translation_lang', $lang)->get();

        foreach ($catsss as $cat){

            $IDsss[] = $cat->id;

            $catChildren = Category::where('parent_id', $cat->id)->where('translation_lang', $lang)->get();

            if (count($catChildren)){

                $IDsss = array_merge($IDsss, $this->getAllSubs($cat->id, $lang));

            }

        }

        return $IDsss;

    }

    

    

    

    



}

