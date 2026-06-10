<?php

   if (!isset($cacheExpiration)) {

       $cacheExpiration = (int)config('settings.other.cache_expiration');

   }

   if (config('settings.listing.display_mode') == '.compact-view') {

   	$colDescBox = 'col-sm-9';

   	$colPriceBox = 'col-sm-3';

   } else {

   	$colDescBox = 'col-sm-7';

   	$colPriceBox = 'col-sm-3';

   }

   ?>
 
<?php if(isset($posts) and count($posts) > 0): ?>

<?php echo $__env->make('home.inc.spacer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div class="container">

   <div class="row">

      <div class="col-sm-12 page-content col-thin-left">



        

        
         <div class="category-list">

            <div class="col-lg-12 box-title no-border" style="text-align: center;background-color: #ff5555c7 ; border-radius: 40px;margin:7px;height: 70px;">

              
   
              <h1 class="title-3" style="color: #fff;padding-top: 20px;"> <p style="font-weight: bold;"><?php echo e(t('Latest Ads')); ?></p></h1>
   
               
                    
   
           </div>

            <div class="tab-content">

                

               <div class="tab-pane col-lg-12 content-box layout-section" id="PremiumAds">

                  <div class="row row-featured row-featured-category" style="border-radius: 40px">

                     <div class="col-lg-12 box-title no-border">

                        <div class="inner">

                           <h2>

                              <span class="title-3">

                                 <!--<?php echo t('Home - Latest Ads'); ?>-->

                                 &nbsp;

                              </span>

                              <?php $attr = ['countryCode' => config('country.icode')]; ?>

                              <a href="<?php echo e(lurl(trans('routes.v-search', $attr), $attr)); ?>" class="sell-your-item">

                              <?php echo e(t('View all')); ?> <i class="icon-th-list"></i>

                              </a>

                           </h2>

                        </div>

                     </div>

                     <div class="adds-wrapper noSideBar">

                        <?php

                        if (isset($featured) and !empty($featured) and !empty($featured->posts)){

                           foreach($featured->posts as $key => $post):

                           if (empty($countries) or !$countries->has($post->country_code)) continue;

                           

                           

                                    $getusernamedetail = @\DB::table('users')->where('id', '=', $post->user_id)->first();

                                                         $username = $getusernamedetail->username;      

                           

                           // Get Pack Info

                           $package = null;

                           if ($post->featured == 1) {

                           	$cacheId = 'package.' . $post->py_package_id . '.' . config('app.locale');

                           	$package = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           		$package = \App\Models\Package::findTrans($post->py_package_id);

                           		return $package;

                           	});

                           }

                           

                           // Get PostType Info

                           $cacheId = 'postType.' . $post->post_type_id . '.' . config('app.locale');

                           $postType = @\Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           	$postType = @\App\Models\PostType::findTrans($post->post_type_id);

                           	return $postType;

                           });

                           if (empty($postType)) continue;

                           

                           // Get Post's Pictures

                           $pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

                           if ($pictures->count() > 0) {

                           	$postImg = @resize($pictures->first()->filename, 'medium');

                           } else {

                           	$postImg = @resize(config('larapen.core.picture.default'));

                           }

                           

                           // Get the Post's City

                           $cacheId = config('country.code') . '.city.' . $post->city_id;

                           $city = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           	$city = \App\Models\City::find($post->city_id);

                           	return $city;

                           });

                           if (empty($city)) continue;

                           

                           // Convert the created_at date to Carbon object

                           $post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));

                           $post->created_at = $post->created_at->ago();

                           

                           // Category

                           $cacheId = 'category.' . $post->category_id . '.' . config('app.locale');

                           $liveCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           	$liveCat = \App\Models\Category::findTrans($post->category_id);

                           	return $liveCat;

                           });

                           

                           // Check parent

                           if (empty($liveCat->parent_id)) {

                           	$liveCatParentId = $liveCat->id;

                           	$liveCatType = $liveCat->type;

                           } else {

                           	$liveCatParentId = $liveCat->parent_id;

                           	

                           	$cacheId = 'category.' . $liveCat->parent_id . '.' . config('app.locale');

                           	$liveParentCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($liveCat) {

                           		$liveParentCat = \App\Models\Category::findTrans($liveCat->parent_id);

                           		return $liveParentCat;

                           	}); 

                           	$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';

                           }

                           

                           // Check translation

                           $liveCatName = $liveCat->name;

                           $lang_Cat = \App\Models\Category::findTrans($post->category_id);

                           $lCat = $lang_Cat->name;

                           ?>

                        <div class="item-list">

                           <?php if(isset($package) and !empty($package)): ?>

                           <?php if($package->ribbon != ''): ?>

                           <div class="cornerRibbons <?php echo e($package->ribbon); ?>"><a href="#"> <?php echo e($package->name); ?></a></div>

                           <?php endif; ?>

                           <?php endif; ?>

                           <div class="col-sm-2 no-padding photobox">

                              <div class="add-image">

                                 <span class="photo-count"><i class="fa fa-camera"></i> <?php echo e($pictures->count()); ?> </span>

                                 <?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>

                                 <a href="<?php echo e(lurl($post->uri, $attr)); ?>">

                                 <img class="thumbnail no-margin" src="<?php echo e($postImg); ?>" alt="img">

                                 </a>

                              </div>

                           </div>

                           <div class="<?php echo e($colDescBox); ?> add-desc-box">

                              <div class="add-details">

                                 <h5 class="add-title">

                                    <?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>

                                    <a href="<?php echo e(lurl($post->uri, $attr)); ?>"><?php echo e(str_limit($post->title, 70)); ?> </a>

                                 </h5>

                                 <span class="info-row">

                                    <!--<span class="add-type business-ads tooltipHere" data-toggle="tooltip" data-placement="right" title="<?php echo e($postType->name); ?>">-->

                                    <!--	<?php echo e(strtoupper(mb_substr($postType->name, 0, 1))); ?> -->

                                    <!--</span>&nbsp;-->

                                    <?php $attr_user = ['countryCode' => config('country.icode'), 'id' => $post->user_id]; ?>													

                                    

                                    <div class="date" style="margin-left: 0px;margin-top: -1px;"><i class="icon-clock"> </i> <?php echo e($post->created_at); ?> </div>

                                    

                                    <div  style="margin-left: 0px;" class="item-location">

                                       <img style="margin-right: 1px;" src="<?php echo e(url('images/blank.gif') . getPictureVersion()); ?>"  class="flag flag-<?=strtolower($post->country_code)?>" >

                                       <a href="<?php echo qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except(['l', 'location']), ['l'=>$post->city_id])); ?>" class="info-link"><?php echo e($city->name); ?></a> <?php echo e((isset($post->distance)) ? '- ' . round(lengthPrecision($post->distance), 2) . unitOfLength() : ''); ?>


                                    </div>

                                 </span>

                              </div>

                              <?php if(isset($reviewsPlugin) and !empty($reviewsPlugin)): ?>

                              <?php if(view()->exists('reviews::ratings-list')): ?>

                              <?php echo $__env->make('reviews::ratings-list', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                              <?php endif; ?>

                              <?php endif; ?>

                           </div>

                           <div class="<?php echo e($colPriceBox); ?> text-right price-box">

                              <h4 class="item-price">

                                 <?php if(isset($liveCatType) and !in_array($liveCatType, ['non-salable'])): ?>

                                 <?php if($post->price > 0): ?>

                                 <?php echo \App\Helpers\Number::money($post->price); ?>


                                 <?php else: ?>

                                 <?php echo e(t('Free')); ?>


                                 <?php endif; ?>

                                 <?php else: ?>

                                 <?php if($post->price > 0): ?>

                                 <?php echo \App\Helpers\Number::money($post->price); ?>


                                 <?php else: ?>

                                 <?php echo e(t('Free')); ?>


                                 <?php endif; ?>

                                 <?php endif; ?>

                              </h4>

                              <?php if(auth()->check()): ?>

                              <a class="btn btn-<?php echo e((\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $post->id)->count() > 0) ? 'success' : 'default'); ?> btn-sm make-favorite"

                                 id="<?php echo e($post->id); ?>">

                              <i class="fa fa-heart"></i><span> <?php echo e(t('Save')); ?> </span>

                              </a>

                              <?php else: ?>

                              <a class="btn btn-default btn-sm make-favorite" id="<?php echo e($post->id); ?>"><i class="fa fa-heart"></i><span> <?php echo e(t('Save')); ?> </span></a>

                              <?php endif; ?>

                           </div>

                        </div>

                        <?php endforeach; 

                        }

                        ?>

                        <div style="clear: both"></div>

                        <?php if(isset($latestOptions) and isset($latestOptions['show_show_more_btn']) and $latestOptions['show_show_more_btn'] == '1'): ?>

                        <div class="mb20" style="text-align: center;">

                           <?php $attr = ['countryCode' => config('country.icode')]; ?>

                           <a href="<?php echo e(lurl(trans('routes.v-search', $attr), $attr)); ?>" class="btn btn-default mt10">

                           <i class="fa fa-arrow-circle-right"></i> <?php echo e(t('View all')); ?>


                           </a>

                        </div>

                        <?php endif; ?>

                     </div>

                  </div>

               </div>

               

               

               

               

               

               

               

               

               

               

               

               

               

               

               

               

               

               

               

               

               <div class="tab-pane active col-lg-12 content-box layout-section" id="latest">

                  <div class="row row-featured row-featured-category">

                     <div class="col-lg-12 box-title no-border">

                        <div class="inner">

                           <h2 style="display:none">

                              <span class="title-3">

                                 <!--<?php echo t('Home - Latest Ads'); ?>-->

                                 &nbsp;

                              </span>

                              <?php $attr = ['countryCode' => config('country.icode')]; ?>

                              <a href="<?php echo e(lurl(trans('routes.v-search', $attr), $attr)); ?>" class="sell-your-item">

                              <?php echo e(t('View all')); ?> <i class="icon-th-list"></i>

                              </a>

                           </h2>

                        </div>

                     </div>

                     <div class="adds-wrapper noSideBar">

                     

                    <?php  $i=0; 

					foreach($posts as $key => $post){

						

						// echo $i++.$post->title."<br />";

						}?>

                        <?php

                           foreach($posts as $key => $post):

                         

						   

						   if (empty($countries) or !$countries->has($post->country_code)) continue;

                           

                           

                                    $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                                         $username = !empty($getusernamedetail->username)?$getusernamedetail->username:'';      

                           

                           // Get Pack Info

                           $package = null;

                           if ($post->featured == 1) {

                           	$cacheId = 'package.' . $post->py_package_id . '.' . config('app.locale');

                           	$package = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           		$package = \App\Models\Package::findTrans($post->py_package_id);

                           		return $package;

                           	});

                           }

                           

                           // Get PostType Info

                           $cacheId = 'postType.' . $post->post_type_id . '.' . config('app.locale');

                           $postType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           	$postType = \App\Models\PostType::findTrans($post->post_type_id);

                           	return $postType;

                           });

                           if (empty($postType)) continue;



                          

                           

                           // Get Post's Pictures

                           $pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

                           if ($pictures->count() > 0) {

                           	$postImg = resize($pictures->first()->filename, 'medium');

                           } else {

                           	$postImg = resize(config('larapen.core.picture.default'));

                           }

                           

                           // Get the Post's City

                           $cacheId = config('country.code') . '.city.' . $post->city_id;

                           $city = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           	$city = \App\Models\City::find($post->city_id);

                           	return $city;

                           });

                          // if (empty($city)) continue;



                         

                           

                           // Convert the created_at date to Carbon object

                           $post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));

                           $post->created_at = $post->created_at->ago();

                           

                           // Category

                           $cacheId = 'category.' . $post->category_id . '.' . config('app.locale');

                           $liveCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           	$liveCat = \App\Models\Category::findTrans($post->category_id);

                           	return $liveCat;

                           });

                           

                           // Check parent

                           if (empty($liveCat->parent_id)) {

                           	$liveCatParentId = $liveCat->id;

                           	$liveCatType = $liveCat->type;

                           } else {

                           	$liveCatParentId = $liveCat->parent_id;

                           	

                           	$cacheId = 'category.' . $liveCat->parent_id . '.' . config('app.locale');

                           	$liveParentCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($liveCat) {

                           		$liveParentCat = \App\Models\Category::findTrans($liveCat->parent_id);

                           		return $liveParentCat;

                           	}); 

                           	$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';

                           }

                           

                           // Check translation

                           $liveCatName = $liveCat->name;

                           $lang_Cat = \App\Models\Category::findTrans($post->category_id);

                           $lCat = $lang_Cat->name;

                           ?>

                           

                           

                        <div class="item-list">

                         

                           <?php if(isset($package) and !empty($package)): ?>

                           <?php if($package->ribbon != ''): ?>

                           <div class="cornerRibbons <?php echo e($package->ribbon); ?>"><a href="#"> <?php echo e($package->name); ?></a></div>

                           <?php endif; ?>

                           <?php endif; ?>

                           <div class="col-sm-2 no-padding photobox">

                              <div class="add-image">

                                 <span class="photo-count"><i class="fa fa-camera"></i> <?php echo e($pictures->count()); ?> </span>

                                 <?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>

                                 <a href="<?php echo e(lurl($post->uri, $attr)); ?>">

                                 <img class="thumbnail no-margin" src="<?php echo e($postImg); ?>" alt="img">

                                 </a>

                              </div>

                           </div>

                           <div class="<?php echo e($colDescBox); ?> add-desc-box">

                              <div class="add-details">

                                 <h5 class="add-title">

                                    <?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>

                                    <a href="<?php echo e(lurl($post->uri, $attr)); ?>"><?php echo e(str_limit($post->title, 70)); ?> </a>

                                 </h5>

                                 <span class="info-row">

                                    <!--<span class="add-type business-ads tooltipHere" data-toggle="tooltip" data-placement="right" title="<?php echo e($postType->name); ?>">-->

                                    <!--	<?php echo e(strtoupper(mb_substr($postType->name, 0, 1))); ?> -->

                                    <!--</span>&nbsp;-->

                                    <?php $attr_user = ['countryCode' => config('country.icode'), 'id' => $post->user_id]; ?>													

                                    

                                    <div class="date" style="margin-left: 0px;margin-top: -1px;"><i class="icon-clock"> </i> <?php echo e($post->created_at); ?> </div>

                                    

                                    <div  style="margin-left: 0px;" class="item-location">

                                       <img style="margin-right: 1px;" src="<?php echo e(url('images/blank.gif') . getPictureVersion()); ?>"  class="flag flag-<?=strtolower($post->country_code)?>" >

                                       <a href="<?php echo qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except(['l', 'location']), ['l'=>$post->city_id])); ?>" class="info-link"><?php echo e($post->city_name); ?></a> <?php echo e((isset($post->distance)) ? '- ' . round(lengthPrecision($post->distance), 2) . unitOfLength() : ''); ?>


                                    </div>

                                 </span>

                              </div>

                              <?php if(isset($reviewsPlugin) and !empty($reviewsPlugin)): ?>

                              <?php if(view()->exists('reviews::ratings-list')): ?>

                              <?php echo $__env->make('reviews::ratings-list', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                              <?php endif; ?>

                              <?php endif; ?>

                           </div>

                           <div class="<?php echo e($colPriceBox); ?> text-right price-box">

                              <h4 class="item-price">

                                 <?php if(isset($liveCatType) and !in_array($liveCatType, ['non-salable'])): ?>

                                 <?php if($post->price > 0): ?>

                                  <?php echo \App\Helpers\Number::money($post->price); ?> 

                                 <?php else: ?>

                                 <?php echo e(t('Free')); ?>


                                 <?php endif; ?>

                                 <?php else: ?>

                                 <?php if($post->price > 0): ?>

                                  <?php echo \App\Helpers\Number::money($post->price); ?>


                                 <?php else: ?>

                                 <?php echo e(t('Free')); ?>


                                 <?php endif; ?>

                                 <?php endif; ?>

                              </h4>

                              <?php if(isset($package) and !empty($package)): ?>

                              <?php if($package->has_badge == 1): ?>

                              <a class="btn btn-danger btn-sm make-favorite"><i class="fa fa-certificate"></i><span> <?php echo e($package->short_name); ?> </span></a>&nbsp;

                              <?php endif; ?>

                              <?php endif; ?>

                              <?php if(auth()->check()): ?>

                              <a class="btn btn-<?php echo e((\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $post->id)->count() > 0) ? 'success' : 'default'); ?> btn-sm make-favorite"

                                 id="<?php echo e($post->id); ?>">

                              <i class="fa fa-heart"></i><span> <?php echo e(t('Save')); ?> </span>

                              </a>

                              <?php else: ?>

                              <a class="btn btn-default btn-sm make-favorite" id="<?php echo e($post->id); ?>"><i class="fa fa-heart"></i><span> <?php echo e(t('Save')); ?> </span></a>

                              <?php endif; ?>

                           </div>

                        </div>

                        <?php endforeach; ?>

                        <div style="clear: both"></div>

                        <?php if(isset($latestOptions) and isset($latestOptions['show_show_more_btn']) and $latestOptions['show_show_more_btn'] == '1'): ?>

                        <div class="mb20" style="text-align: center;">

                           <?php $attr = ['countryCode' => config('country.icode')]; ?>

                           <a href="<?php echo e(lurl(trans('routes.v-search', $attr), $attr)); ?>" class="btn btn-default mt10">

                           <i class="fa fa-arrow-circle-right"></i> <?php echo e(t('View all')); ?>


                           </a>

                        </div>

                        <?php endif; ?>

                     </div>

                  </div>

               </div>

            

            

            

            

            

            

            

            

            

            

            

            

            

            

            

            

            

            

            

               <div class="tab-pane col-lg-12 content-box layout-section" id="popular">

                  <div class="row row-featured row-featured-category">

                     <div class="col-lg-12 box-title no-border">

                        <div class="inner">

                           <h2>

                              <span class="title-3">

                                 <!--Popular <span style="font-weight: bold;">Ads</span>-->

                                 &nbsp;

                              </span>

                              <?php $attr = ['countryCode' => config('country.icode')]; ?>

                              <a href="<?php echo e(lurl(trans('routes.v-search', $attr), $attr)); ?>" class="sell-your-item">

                              <?php echo e(t('View all')); ?> <i class="icon-th-list"></i>

                              </a>

                           </h2>

                        </div>

                     </div>

                     <div class="adds-wrapper noSideBar">

                        <?php

                           foreach($posts_popular as $key => $post):

                           	if (empty($countries) or !$countries->has($post->country_code)) continue;

                           

                           	// Get Pack Info

                           	$package = null;

                           	if ($post->featured == 1) {

                           		$cacheId = 'package.' . $post->py_package_id . '.' . config('app.locale');

                           		$package = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           			$package = \App\Models\Package::findTrans($post->py_package_id);

                           			return $package;

                           		});

                           	}

                           

                           	// Get PostType Info

                           	$cacheId = 'postType.' . $post->post_type_id . '.' . config('app.locale');

                           	$postType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           		$postType = \App\Models\PostType::findTrans($post->post_type_id);

                           		return $postType;

                           	});

                           	if (empty($postType)) continue;

                           

                           	// Get Post's Pictures

                           	$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');

                           	if ($pictures->count() > 0) {

                           		$postImg = resize($pictures->first()->filename, 'medium');

                           	} else {

                           		$postImg = resize(config('larapen.core.picture.default'));

                           	}

                           

                           	// Get the Post's City

                           	$cacheId = config('country.code') . '.city.' . $post->city_id;

                           	$city = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           		$city = \App\Models\City::find($post->city_id);

                           		return $city;

                           	});

                           	if (empty($city)) continue;

                           	

                           	// Convert the created_at date to Carbon object

                           	$post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));

                           	$post->created_at = $post->created_at->ago();

                           	

                           	// Category

                           	$cacheId = 'category.' . $post->category_id . '.' . config('app.locale');

                           	$liveCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {

                           		$liveCat = \App\Models\Category::findTrans($post->category_id);

                           		return $liveCat;

                           	});

                           	

                           	// Check parent

                           	if (empty($liveCat->parent_id)) {

                           		$liveCatParentId = $liveCat->id;

                           		$liveCatType = $liveCat->type;

                           	} else {

                           		$liveCatParentId = $liveCat->parent_id;

                           		

                           		$cacheId = 'category.' . $liveCat->parent_id . '.' . config('app.locale');

                           		$liveParentCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($liveCat) {

                           			$liveParentCat = \App\Models\Category::findTrans($liveCat->parent_id);

                           			return $liveParentCat;

                           		});

                           		$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';

                           	}

                           	

                           	// Check translation

                           	$liveCatName = $liveCat->name;

                           	

                                     $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();

                                                          $username = $getusernamedetail->username;      

                           	

                           	?>

                        <div class="item-list">

                           <?php if(isset($package) and !empty($package)): ?>

                           <?php if($package->ribbon != ''): ?>

                           <div class="cornerRibbons <?php echo e($package->ribbon); ?>"><a href="#"> <?php echo e($package->short_name); ?></a></div>

                           <?php endif; ?>

                           <?php endif; ?>

                           <div class="col-sm-2 no-padding photobox">

                              <div class="add-image">

                                 <span class="photo-count"><i class="fa fa-camera"></i> <?php echo e($pictures->count()); ?> </span>

                                 <?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>

                                 <a href="<?php echo e(lurl($post->uri, $attr)); ?>">

                                 <img class="thumbnail no-margin" src="<?php echo e($postImg); ?>" alt="img">

                                 </a>

                              </div>

                           </div>

                           <div class="<?php echo e($colDescBox); ?> add-desc-box">

                              <div class="add-details">

                                 <h5 class="add-title">

                                    <?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>

                                    <a href="<?php echo e(lurl($post->uri, $attr)); ?>"><?php echo e(str_limit($post->title, 70)); ?> </a>

                                 </h5>

                                 <span class="info-row">

                                    <!--<span class="add-type business-ads tooltipHere" data-toggle="tooltip" data-placement="right" title="<?php echo e($postType->name); ?>">-->

                                    <!--	<?php echo e(strtoupper(mb_substr($postType->name, 0, 1))); ?>-->

                                    <!--</span>&nbsp;-->

                                    <?php $attr_user = ['countryCode' => config('country.icode'), 'id' => $post->user_id]; ?>	

                                    

                                    <div class="date" style="margin-left: 0px;margin-top: -1px;"><i class="icon-clock"> </i> <?php echo e($post->created_at); ?> </div>

                                    

                                    <div  style="margin-left: 0px;" class="item-location">

                                       <img style="margin-right: 1px;" src="<?php echo e(url('images/blank.gif') . getPictureVersion()); ?>"  class="flag flag-<?=strtolower($post->country_code)?>" >

                                       <a href="<?php echo qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except(['l', 'location']), ['l'=>$post->city_id])); ?>" class="info-link"><?php echo e($city->name); ?></a> <?php echo e((isset($post->distance)) ? '- ' . round(lengthPrecision($post->distance), 2) . unitOfLength() : ''); ?>


                                    </div>

                                 </span>

                              </div>

                              <?php if(isset($reviewsPlugin) and !empty($reviewsPlugin)): ?>

                              <?php if(view()->exists('reviews::ratings-list')): ?>

                              <?php echo $__env->make('reviews::ratings-list', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                              <?php endif; ?>

                              <?php endif; ?>

                           </div>

                           <div class="<?php echo e($colPriceBox); ?> text-right price-box">

                              <h4 class="item-price">

                                 <?php if(isset($liveCatType) and !in_array($liveCatType, ['non-salable'])): ?>

                                 <?php if($post->price > 0): ?>

                                 <?php echo \App\Helpers\Number::money($post->price); ?>


                                 <?php else: ?>

                                 <?php echo e(t('Free')); ?>


                                 <?php endif; ?>

                                 <?php else: ?>

                                 <?php if($post->price > 0): ?>

                                 <?php echo \App\Helpers\Number::money($post->price); ?>


                                 <?php else: ?>

                                 <?php echo e(t('Free')); ?>


                                 <?php endif; ?>

                                 <?php endif; ?>

                              </h4>

                              <?php if(isset($package) and !empty($package)): ?>

                              <?php if($package->has_badge == 1): ?>

                              <a class="btn btn-danger btn-sm make-favorite"><i class="fa fa-certificate"></i><span> <?php echo e($package->short_name); ?> </span></a>&nbsp;

                              <?php endif; ?>

                              <?php endif; ?>

                              <?php if(auth()->check()): ?>

                              <a class="btn btn-<?php echo e((\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $post->id)->count() > 0) ? 'success' : 'default'); ?> btn-sm make-favorite"

                                 id="<?php echo e($post->id); ?>">

                              <i class="fa fa-heart"></i><span> <?php echo e(t('Save')); ?> </span>

                              </a>

                              <?php else: ?>

                              <a class="btn btn-default btn-sm make-favorite" id="<?php echo e($post->id); ?>"><i class="fa fa-heart"></i><span> <?php echo e(t('Save')); ?> </span></a>

                              <?php endif; ?>

                           </div>

                        </div>

                        <?php 

                           endforeach; ?>

                        <div style="clear: both"></div>

                        <?php if(isset($latestOptions) and isset($latestOptions['show_show_more_btn']) and $latestOptions['show_show_more_btn'] == '1'): ?>

                        <div class="mb20" style="text-align: center;">

                           <?php $attr = ['countryCode' => config('country.icode')]; ?>

                           <a href="<?php echo e(lurl(trans('routes.v-search', $attr), $attr)); ?>" class="btn btn-default mt10">

                           <i class="fa fa-arrow-circle-right"></i> <?php echo e(t('View all')); ?>


                           </a>

                        </div>

                        <?php endif; ?>

                     </div>

                  </div>

               </div>

               <!-- <div class="tab-pane col-lg-12 content-box layout-section" id="favorite">

                  </div> -->

            </div>

         </div>

      </div>

   </div>

</div>

<div style="clear:both"></div>

<div class="container" style="text-align: center;margin-top: 15px;">

   <?php $attr = ['countryCode' => config('country.icode')]; ?>

   <a href="<?php echo e(lurl(trans('routes.v-search', $attr), $attr)); ?>" class="btn btn-primary" >

   <?php echo e(t('View all')); ?> <i class="icon-th-list"></i>

   </a>

</div>

<div style="clear:both"></div>

<?php endif; ?>

<?php $__env->startSection('after_scripts'); ?>

##parent-placeholder-3bf5331b3a09ea3f1b5e16018984d82e8dc96b5f##

<script>

   /* Default view (See in /js/script.js) */

   <?php if(isset($posts) and count($posts) > 0): ?>

   	<?php if(config('settings.listing.display_mode') == '.grid-view'): ?>

   		gridView('.grid-view');

   	<?php elseif(config('settings.listing.display_mode') == '.list-view'): ?>

   		listView('.list-view');

   	<?php elseif(config('settings.listing.display_mode') == '.compact-view'): ?>

   		compactView('.compact-view');

   	<?php else: ?>

   		gridView('.grid-view');

   	<?php endif; ?>

   <?php else: ?>

   	listView('.list-view');

   <?php endif; ?>

   /* Save the Search page display mode */

   var listingDisplayMode = readCookie('listing_display_mode');

   if (!listingDisplayMode) {

   	createCookie('listing_display_mode', '<?php echo e(config('settings.listing.display_mode', '.grid-view')); ?>', 7);

   }

   

   /* Favorites Translation */

   var lang = {

   	labelSavePostSave: "<?php echo t('Save ad'); ?>",

   	labelSavePostRemove: "<?php echo t('Remove favorite'); ?>",

   	loginToSavePost: "<?php echo t('Please log in to save the Ads.'); ?>",

   	loginToSaveSearch: "<?php echo t('Please log in to save your search.'); ?>",

   	confirmationSavePost: "<?php echo t('Post saved in favorites successfully !'); ?>",

   	confirmationRemoveSavePost: "<?php echo t('Post deleted from favorites successfully !'); ?>",

   	confirmationSaveSearch: "<?php echo t('Search saved successfully !'); ?>",

   	confirmationRemoveSaveSearch: "<?php echo t('Search deleted successfully !'); ?>"

   };

     

</script>

<?php $__env->stopSection(); ?>

