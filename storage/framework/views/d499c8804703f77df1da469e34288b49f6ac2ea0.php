<?php

// Search parameters

$queryString = (Request::getQueryString() ? ('?' . Request::getQueryString()) : '');



// Get the Default Language

$cacheExpiration = (isset($cacheExpiration)) ? $cacheExpiration : config('settings.other.cache_expiration', 60);

$defaultLang = Cache::remember('language.default', $cacheExpiration, function () {

    $defaultLang = \App\Models\Language::where('default', 1)->first();

    return $defaultLang;

});





// Check if the Multi-Countries selection is enabled

$multiCountriesIsEnabled = false;

$multiCountriesLabel = '';

if (config('settings.geo_location.country_flag_activation')) {

    if (!empty(config('country.code'))) {

        if (\App\Models\Country::where('active', 1)->count() > 1) {

            $multiCountriesIsEnabled = true;

            $multiCountriesLabel = 'title="' . t('Select a Country') . '"';

        }

    }

}



// Logo Label

$logoLabel = '';

if (getSegment(1) != trans('routes.countries')) {

    $logoLabel = config('settings.app.name') . ((!empty(config('country.name'))) ? ' ' . config('country.name') : '');

}

?>

<style>

.btn-primary:hover {

  background-color: #ff5555; /* Green */

  color: white;

}

.font_red{
    color: #ff5555;    
}



.col-lg-2-new {

    width: 20%;

}

.pstn_clss_h{	

    margin-top: 4% !important;

	}





	<?php if(empty($nav_style)){?> 

.navbar-default {

     background-color: #f8f8f800;

	 position: relative !important;

} 

.navbar-default .navbar-nav>li>a {

	color:black;

}



	

<?php }else{ ?>

.navbar-default {

     background-color: #f8f8f800;

	 position: absolute !important;

} 

.pstn_clss{

	position: absolute;

	      

	  

	}

<?php }?>





 

.header {

    background-color: #f8f8f800;

}

.copy-info {

    color: #dd5311;

}

.navbar-default .navbar-nav>li>a {

    color: white;

}

.alert {

    

	padding: 4px !important;

	margin-top: 45px !important;

    box-shadow: 0 5px 23px rgb(0 0 0 / 50%) !important;

	width: fit-content;

}



.main-container {

    margin-top: -30px;

}



.dtable {

    display: block;

}

.fix-width{

	width:125%;

	}

.main-logo {height: 205px;} 

  /* Common styles for all devices */
  .main-logo {
               height: 205px;
        }

        /* Styles for desktop */
        @media (min-width: 768px) {
            .main-logo {
                height: 205px; /* Set your desired width for desktop */
            }
        }

        /* Styles for mobile */
        @media (max-width: 767px) {
            .main-logo {
                height: 75px; /* Set your desired width for mobile */
            }
        }






.dtable-cell {

    display: flex;

    vertical-align: middle;

    padding-top: 5%;

	}

	

	 

 /* Smartphones (portrait and landscape) ----------- */

@media  only screen 

and (min-device-width : 320px) 

and (max-device-width : 480px) {

.navbar-default {

     background-color: #f8f8f800;

	 position: relative !important;

}



.navbar-default .navbar-nav>li>a {

    color: black;

}

 

 .main-container {

    margin-top: 0px;

}



.pstn_clss{

	 position: relative !important;

	    

	}

	

}



 



/* Smartphones (portrait) ----------- */

@media  only screen 

and (max-width : 320px) {

.navbar-default {

     background-color: #f8f8f800;

	 position: relative !important;

}



.navbar-default .navbar-nav>li>a {

    color: black;

}



.main-container {

    margin-top: 0px;

}

.pstn_clss{

	    position: auto;



	}

	



	



}



/* iPads (portrait and landscape) ----------- */

@media  only screen 

and (min-device-width : 768px) 

and (max-device-width : 1024px) {

	

	<?php if(empty($nav_style)){?> 

.navbar-default {

     background-color: #f8f8f800;

	 position: relative !important;

} 

.navbar-default .navbar-nav>li>a {

	color:black;

}



<?php }else{ ?>

.navbar-default {

     background-color: #f8f8f800;

	 position: fixed !important;

} 

.pstn_clss{

	position: absolute;

	 

	}

<?php }?>

.navbar-default .navbar-nav>li>a {

    color: black;

}



.main-container {

    margin-top: 0px;

}

.pstn_clss{

	    position: unset;

		 

	}

	

	



}

 



</style>

<div class="header">



  <?php if(!auth()->check()): ?>

  <?php else: ?>

  

  <?php  

$net_free_posts=0;

  $free_points = \DB::table('payments')

  ->leftjoin('packages','payments.package_id','=','packages.id')

                         ->where('user_id', '=', auth()->user()->id)

						 ->where('packages.price', '=', 0)

						 ->first();

						 //echo "xxx".$free_points->no_points;

						 if(!empty($free_points->no_points)){

						  $count_free_posts = \DB::table('payments')

						  ->where('user_id', '=', auth()->user()->id)

						 ->where('package_id', '=', $free_points->package_id)

						 ->count();

						 //echo "zzzz".$count_free_posts;

						 if(!empty($count_free_posts)){

						 $net_free_posts=$free_points->no_points-$count_free_posts;

						 }

						 }

						 

			//	 echo "zzzz".$net_free_posts.auth()->user()->no_points;		 

						 ?>

  <?php endif; ?>

    <nav class="navbar navbar-site navbar-default" role="navigation"   style="border-top:10px solid #2a2a2a00;;border-bottom: 1px;">

        <div class="container" style="width: 100%;margin: 0px;">

            <div class="nav navbar-nav ">



                

                

                <!--

                <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">

                    <span class="sr-only">Toggle navigation</span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>

                </button>

                -->



                

                <?php if(getSegment(1) != trans('routes.countries')): ?>

                    <?php if(isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled): ?>

                        <?php if(!empty(config('country.icode'))): ?>

                            <?php if(file_exists(public_path().'/images/flags/24/'.config('country.icode').'.png')): ?>

                                <button  class="flag-menu country-flag visible-xs btn btn-default hidden"

                                        href="#selectCountry" data-toggle="modal" style="margin-bottom:0px">

                                    <img src="<?php echo e(url('images/flags/24/'.config('country.icode').'.png') . getPictureVersion()); ?>"

                                         style="float: left;">

                                    <span class="caret hidden-xs"></span>

                                </button>

                            <?php endif; ?>

                        <?php endif; ?>

                    <?php endif; ?>

                <?php endif; ?>



                
                

                <a href="<?php echo e(lurl('/')); ?>" class=" nav navbar-nav navbar-brand logo logo-title  hidden-lg mobile-logo-link">

                    <img  src="<?php echo e(\Storage::url(config('settings.app.logo')) . getPictureVersion()); ?>"

                         alt="<?php echo e(strtolower(config('settings.app.name'))); ?>" class="tooltipHere main-logo mobile-logo" title=""

                         data-placement="bottom"

                         data-toggle="tooltip"

                         data-original-title="<?php echo isset($logoLabel) ? $logoLabel : ''; ?>"  />



                         

                </a>

                <?php if(!auth()->check()): ?>

                <?php else: ?>

                    <ul class="hidden-lg hidden-md user-details-header"  style="border: 1px solid #e4e4df;background-color: #fbfbfb;">

                    <li class="dropdown" style="margin: 10px;">

                            <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">

                                <i class="icon-user fa hidden-sm"></i>

                                <span><?php echo e(auth()->user()->username); ?></span>

                               

                                

                                <span class="badge badge-important count-conversations-with-new-messages-offer">0</span>

                                <i class="icon-down-open-big fa hidden-sm"></i>

                            </a>

                            <ul class="dropdown-menu user-menu">

                                <!-- <li class="active">-->

                                 

                               <li style="border-bottom: 1px solid #cccccc;">

                                    <a href="<?php echo e(lurl('recharge_points')); ?>">

                                        <i class="icon-user"></i>

                                        <!--<?php echo e(t('Personal Home')); ?>-->

                                        <span class="font_red"> <?php echo e(t('Profile')); ?></span>

                                    </a>

                                </li>

                                

                                <li>
                                    <a href="<?php echo e(lurl('account/recharge_points')); ?>">
                                        <i class="icon-money"></i>
                                        <!--<?php echo e(t('Personal Home')); ?>-->
                                         <span><?php echo e(t('Balance')); ?> 
                                            (<span style="color:#ff5555"><?php echo e(auth()->user()->no_points); ?></span>) 
                                        </span>
                                    </a>
                                </li>

                                <li><a href="<?php echo e(lurl('account/transactions')); ?>"><i

                                    class="icon-ok"></i> 
                                    <span class="font_red">
                                         <?php echo e(t('Transactions')); ?>

                                    <span></a>
                                </li>

                                   

                                                <li >

                                                    <?php $attr = ['countryCode' => config('country.icode'), 'id' => auth()->user()->id]; ?>

                                                    <a href="<?php echo e(lurl(trans('routes.v-search-user', $attr), $attr)); ?>">

                                                     <i class="icon-star fa hidden-sm"></i> <span class="font_red">MyPage</span>

                                                    </a>

                                                </li>

                        

                                   

                        

                        

                                

                                <li style="border-bottom: 1px solid #cccccc;">

                                    <a href="<?php echo e(lurl('account/my-posts')); ?>">

                                        <i class="icon-th-thumb"></i> <span class="font_red"><?php echo e(t('My ads')); ?> </span>

                                    </a>

                                </li>

                                <!--<li>-->

                                <!--    <a href="<?php echo e(lurl('account/favourite')); ?>">-->

                                <!--        <i class="icon-heart"></i> <?php echo e(t('Favourite ads')); ?> -->

                                <!--    </a>-->

                                <!--</li>-->

                                

                                <!--<li><a href="<?php echo e(lurl('account/saved-search')); ?>"><i-->

                                <!--                class="icon-star-circled"></i> <?php echo e(t('Saved searches')); ?> </a></li>-->

                                                

                                <!--<li><a href="<?php echo e(lurl('account/pending-approval')); ?>"><i-->

                                <!--                class="icon-hourglass"></i> <?php echo e(t('Pending approval')); ?> </a></li>-->

                                <!--<li><a href="<?php echo e(lurl('account/archived')); ?>"><i-->

                                <!--                class="icon-folder-close"></i> <?php echo e(t('Archived ads')); ?></a></li>-->

                                <li style="border-bottom: 1px solid #cccccc;">

                                    <a href="<?php echo e(lurl('account/conversations')); ?>">

                                        <i class="icon-mail-1"></i> 
                                        <span class="font_red"> <?php echo e(t('Messages')); ?> </span>

                                        <!--<?php echo e(t('Conversations')); ?>-->

                                        <span class="badge badge-important count-conversations-with-new-messages">0</span>

                                    </a>

                                </li>

                                

                                <li style="border-bottom: 1px solid #cccccc;">

                                    <a href="<?php echo e(lurl('account/makeanoffers')); ?>"> 

                                        <i class="glyphicon glyphicon-hand-left" style="margin-left: 4px;"></i> 

                                        <span class="font_red"><?php echo e(t('Offers')); ?></span>

                                        <span class="badge badge-important count-conversations-with-new-offer">

                                            0

                                        </span>

                                    </a>

                                </li>

                                 <li><a  href="<?php echo e(lurl(trans('routes.logout'))); ?>" >

                                    <i class="glyphicon glyphicon-off hidden-sm"></i> 
                                    <span class="font_red"><?php echo e(t('Log Out')); ?> </span>

                                </a></li>

                            </ul>

                    </li>

                </ul>

                <?php endif; ?>                

            </div>

            

            

            

            

            

            <div class="navbar-collapse collapse in" style="margin-top: 0px;margin-bottom: 14px;border-color: #f8f8f8; margin-left: 0%;display: contents;">

                <ul class="nav navbar-nav navbar-left ddd">

              
                   

                    <!--<li class="dropdown currency-menu">-->

                    <!--    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">-->

                    <!--        <?php if(!empty(Session::get('currency'))): ?>-->

                    <!--            <?php echo e(Session::get('currency')); ?>-->

                    <!--        <?php else: ?>-->

                    <!--            Select Currency-->

                    <!--        <?php endif; ?>-->

                    <!--        <span class="caret hidden-sm"> </span>-->

                    <!--    </button>-->

                    <!--    <ul class="dropdown-menu" role="menu">-->

                    <!--        <?php if(getCurrency()): ?>-->

                    <!--            <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>-->

                    <!--                <li>-->

                    <!--                    <a href="javascript:;" onclick="selectCurrency('<?php echo e($key); ?>')">-->

                    <!--                        <span class="lang-name"><?php echo e($key); ?></span>-->

                    <!--                    </a>-->

                    <!--                </li>-->

                    <!--            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>-->

                    <!--        <?php endif; ?>-->

                    <!--    </ul>-->

                    <!--</li>-->

                    

                    

                    

                    <?php if(getSegment(1) != trans('routes.countries')): ?>

                        <?php if(isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled): ?>

                            <?php if(!empty(config('country.icode'))): ?>

                                <?php if(file_exists(public_path().'/images/flags/24/'.config('country.icode').'.png')): ?>



                                    <button class="flag-menu country-flag visible-s btn btn-default hidden"

                                            href="#selectCountry" data-toggle="modal">

                                        <img src="<?php echo e(url('images/flags/24/'.config('country.icode').'.png') . getPictureVersion()); ?>"

                                             style="float: left;">

                                        <span class="caret hidden-xs"></span>

                                    </button>

                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endif; ?>

                    <?php endif; ?>

                    

                    <?php if(getSegment(1) != trans('routes.countries')): ?>

                        <?php if(config('settings.geo_location.country_flag_activation')): ?>

                            <?php if(!empty(config('country.icode'))): ?>

                                <?php if(file_exists(public_path().'/images/flags/32/'.config('country.icode').'.png')): ?>

                                    <li class="flag-menu country-flag tooltipHere hidden-xs" data-toggle="tooltip"

                                        data-placement="<?php echo e((config('lang.direction') == 'rtl') ? 'bottom' : 'right'); ?>" <?php echo $multiCountriesLabel; ?>>

                                        <?php if(isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled): ?>

                                            <a href="#selectCountry" data-toggle="modal">

                                                <img class="flag-icon"

                                                     src="<?php echo e(url('images/flags/32/'.config('country.icode').'.png') . getPictureVersion()); ?>"

                                                     style="float: left;">

                                                <span class="caret hidden-sm"></span>

                                            </a>

                                        <?php else: ?>

                                            <a style="cursor: default;">

                                                <img class="flag-icon"

                                                     src="<?php echo e(url('images/flags/32/'.config('country.icode').'.png') . getPictureVersion()); ?>"

                                                     style="float: left;">

                                            </a>

                                        <?php endif; ?>

                                    </li>

                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endif; ?>

                    <?php endif; ?>


 





                    <?php if(count(LaravelLocalization::getSupportedLocales()) > 1): ?>


                     <!-- Language selector -->

                     <li class="dropdown lang-menu ">

                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                        
                        style="background: #ff000000;
                        border: none;
                        font-size: 15px;color: #ff5555;
                       " >

                            <?php if(strtoupper(config('app.locale')) == 'EN'): ?>

                            English

                            <?php elseif(strtoupper(config('app.locale')) == 'FR'): ?>

                             Français 

                            <?php elseif(strtoupper(config('app.locale')) == 'ES'): ?>

                             Español 

                            <?php elseif(strtoupper(config('app.locale')) == 'DE'): ?>

                             German 

                            <?php elseif(strtoupper(config('app.locale')) == 'PT'): ?>

                              Português 

                            <?php elseif(strtoupper(config('app.locale')) == 'TR'): ?>

                               Türkçe

                            <?php elseif(strtoupper(config('app.locale')) == 'SQ'): ?>

                                Shqip  

                            <?php elseif(strtoupper(config('app.locale')) == 'RU'): ?>

                                Русский 

                            <?php elseif(strtoupper(config('app.locale')) == 'AR'): ?>

                                العربية

                            <?php else: ?>

                            <?php echo e(strtoupper(config('app.locale'))); ?>


                            <?php endif; ?>

                            <span class="caret hidden-sm"> </span>

                        </button>

                        <ul class="dropdown-menu" role="menu">

                            <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <?php if(strtolower($localeCode) != strtolower(config('app.locale'))): ?>

                                    <?php

                                    // Controller Parameters

                                    $attr = [];

                                    $attr['countryCode'] = config('country.icode');

                                    if (isset($uriPathCatSlug)) {

                                        $attr['catSlug'] = $uriPathCatSlug;

                                        if (isset($uriPathSubCatSlug)) {

                                            $attr['subCatSlug'] = $uriPathSubCatSlug;

                                        }

                                    }

                                    if (isset($uriPathCityName) && isset($uriPathCityId)) {

                                        $attr['city'] = $uriPathCityName;

                                        $attr['id'] = $uriPathCityId;

                                    }

                                    if (isset($uriPathUserId)) {

                                        $attr['id'] = $uriPathUserId;

                                        if (isset($uriPathUsername)) {

                                            $attr['username'] = $uriPathUsername;

                                        }

                                    }

                                    if (isset($uriPathUsername)) {

                                        if (isset($uriPathUserId)) {

                                            $attr['id'] = $uriPathUserId;

                                        }

                                        $attr['username'] = $uriPathUsername;

                                    }

                                    if (isset($uriPathTag)) {

                                        $attr['tag'] = $uriPathTag;

                                    }

                                    if (isset($uriPathPageSlug)) {

                                        $attr['slug'] = $uriPathPageSlug;

                                    }



                                    // Default

                                    $link = LaravelLocalization::getLocalizedURL($localeCode, null, [], true);

                                    //var_dump('$link',$attr, $localeCode);

                                    // $link = lurl(null, $attr, $localeCode);

                                    // $localeCode = strtolower($localeCode);

                                    // if($localeCode == 'en'){

                                    // 	$link = "https://www.dealnotdeal.com/en";	

                                    // // 	$link = lurl(null, $attr, 'en');

                                    // }

                                    // else

                                    // {

                                    //     $link = url('/'.$localeCode);	

                                    // }

                                    

                                    ?>

                                    <li>

                                        <a href="<?php echo e($link); ?>" tabindex="-1" rel="alternate"

                                           hreflang="<?php echo e($localeCode); ?>">

                                            <span class="lang-name" style="color: #ff5555;"> <?php echo e($properties['native']); ?> </span>

                                        </a>

                                    </li>

                                <?php endif; ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </ul>

                    </li>

                <?php endif; ?>



                </ul>





                
<?php /*
                <a href="{{ lurl('/') }}" class=" nav navbar-nav navbar-brand logo logo-title  visible-lg"

                   style="position: absolute; left: 44%;padding-top: 0px;">

                    <img src="{{ \Storage::url(config('settings.app.logo')) . getPictureVersion() }}"

                         alt="{{ strtolower(config('settings.app.name')) }}" class="tooltipHere main-logo" title=""

                         data-placement="bottom"

                         data-toggle="tooltip"

                         data-original-title="{!! isset($logoLabel) ? $logoLabel : '' !!}" style="display:none"/>

                         

                          <img src="/images/tammat.png"

                         alt="{{ strtolower(config('settings.app.name')) }}" class="tooltipHere main-logo" title=""

                         data-placement="bottom"

                         data-toggle="tooltip"

                         data-original-title="{!! isset($logoLabel) ? $logoLabel : '' !!}"/>

                         

                </a>
*/?>




                <ul class="nav navbar-nav navbar-right" style="display: inline-block; ">

                   
                    <li class="dropdown hidden-sm hidden-xs"> 
                        <a href="<?php echo e(lurl('/')); ?>">
                            <i class="fa fa-home" style="font-size: 20px;"></i>
                        </a>
                    </li>


                    <?php if(!auth()->check()): ?>

                       

                        <li style="display: inline-block ; width:120px">

                            <?php if(config('settings.security.login_open_in_modal')): ?>

                                <a href="#quickLogin" data-toggle="modal" style="font-size: 15px;"  class="btn btn-primery"><i class="icon-user fa" style="font-size: 15px;color: #ff5555;"></i> <?php echo e(t('Log In')); ?> 

                                </a>

                            <?php else: ?>

                                <a style="display: inline-block ; width:50px" href="<?php echo e(lurl(trans('routes.login'))); ?>"><i

                                            class="icon-user fa" ></i> <?php echo e(t('Log In')); ?></a>

                            <?php endif; ?>

                        </li>

                        <li style="display: inline-block;  width:120px; margin-left:-20px; margin-right:-10px; color:#ff5555">

                        <a href="<?php echo e(lurl(trans('routes.register'))); ?>" style="font-size: 15px;" class="btn btn-primery"><i class="icon-user-add fa" style="font-size: 15px;color: #ff5555;"></i> <?php echo e(t('Register')); ?></a></li>

                        <li class="postadd ddd">

                            <?php if(config('settings.single.guests_can_post_ads') != '1'): ?>

                                <a class="btn btn-primary"  style="color: white !important;background-color: #ff5555;" href="#quickLogin"

                                   data-toggle="modal">

                                    <i class="fa fa-plus-circle"></i> <?php echo e(t('Add Listing')); ?>


                                </a>

                            <?php else: ?>

                                <a class="btn btn-primary" style="color: white !important;background-color: #ff5555;"

                                   href="<?php echo e(lurl('posts/create_step1')); ?>">

                                    <i class="fa fa-plus-circle"></i> <?php echo e(t('Add Listing')); ?>


                                </a>

                            <?php endif; ?>

                        </li>

                    <?php else: ?>

                        <li style="display: inline-block ; width:96px">

                            <?php if(app('impersonate')->isImpersonating()): ?>

                                <a href="<?php echo e(route('impersonate.leave')); ?>">

                                    <i class="icon-logout hidden-sm"></i> <?php echo e(t('Leave')); ?>


                                </a>

                            <?php else: ?>

                                <a  href="<?php echo e(lurl(trans('routes.logout'))); ?>" style="display:none">

                                    <i class="glyphicon glyphicon-off hidden-sm"></i> <?php echo e(t('Log Out')); ?>


                                </a>

                            <?php endif; ?>

                        </li>

                        

                        

                        

                        

                        

                        

                        <!-- <div class="hidden-sm hidden-xs"> -->

                        <li class="dropdown hidden-sm hidden-xs" style="background-color: #ff5555;border-radius: 6px;">

                            <a href="#" class="dropdown-toggle btn btn-primary" data-toggle="dropdown" style="color:white !important;">

                                <i class="icon-user fa hidden-sm"></i>

                                <span><?php echo e(auth()->user()->username); ?></span>

                                <span class="badge badge-important count-conversations-with-new-messages-offer"></span>

                                <i class="icon-down-open-big fa hidden-sm"></i>

                            </a>

                            <ul class="dropdown-menu user-menu">

                               <!-- <li class="active">-->

                                

                               <li>

                                    <a href="<?php echo e(lurl('account')); ?>">

                                        <i class="icon-user"></i>

                                        <!--<?php echo e(t('Personal Home')); ?>-->

                                        <span class="font_red"> <?php echo e(t('Profile')); ?></span>

                                    </a>

                                </li>

                                

                                 <li >

                                    <a href="<?php echo e(lurl('account/recharge_points')); ?>">
                                        <i class="icon-money"></i>
                                        <!--<?php echo e(t('Personal Home')); ?>-->  
                                        <span class="font_red"><?php echo e(t('Balance')); ?> 
                                            (<span style="color:#ff5555"><?php echo e(auth()->user()->no_points); ?></span>) 
                                        </span>
                                    </a>

                                </li>
                                <li><a href="<?php echo e(lurl('account/transactions')); ?>"><i

                                    class="icon-ok"></i> 
                                    <span class="font_red">
                                         <?php echo e(t('Transactions')); ?>

                                    <span></a>
                                </li>
                                

                                                  <li >

                                                    <?php $attr = ['countryCode' => config('country.icode'), 'id' => auth()->user()->id]; ?>

                                                    <a href="<?php echo e(lurl(trans('routes.v-search-user', $attr), $attr)); ?>">

                                                      <i class="icon-star fa hidden-sm"></i> 
                                                      <span class="font_red">MyPage</span>

                                                    </a>

                                                </li>

                         

                                

                                <li>
                                    <a href="<?php echo e(lurl('account/my-posts')); ?>">
                                                 <i class="icon-th-thumb"></i>
                                                 <span class="font_red"><?php echo e(t('My ads')); ?></span>    
                                     </a>
                                </li>

                                <!--<li><a href="<?php echo e(lurl('account/favourite')); ?>"><i-->

                                <!--                class="icon-heart"></i> <?php echo e(t('Favourite ads')); ?> </a></li>-->

                                

                                <!--<li><a href="<?php echo e(lurl('account/saved-search')); ?>"><i-->

                                <!--                class="icon-star-circled"></i> <?php echo e(t('Saved searches')); ?> </a></li>-->

                                                

                                <!--<li><a href="<?php echo e(lurl('account/pending-approval')); ?>"><i-->

                                <!--                class="icon-hourglass"></i> <?php echo e(t('Pending approval')); ?> </a></li>-->

                                <!--<li><a href="<?php echo e(lurl('account/archived')); ?>"><i-->

                                <!--                class="icon-folder-close"></i> <?php echo e(t('Archived ads')); ?></a></li>-->

                                <li>

                                    <a href="<?php echo e(lurl('account/conversations')); ?>">

                                        <i class="icon-mail-1"></i> <span class="font_red"> <?php echo e(t('Messages')); ?> </span>

                                        <!--<?php echo e(t('Conversations')); ?>-->

                                        <span class="badge badge-important count-conversations-with-new-messages"></span>

                                    </a>

                                </li>

                               

                                <li><a href="<?php echo e(lurl('account/makeanoffers')); ?>"> 
                                    <i class="glyphicon glyphicon-hand-left" style="margin-left: 4px;"></i> 
                                    <span class="font_red"><?php echo e(t('Offers')); ?></span>

                                                <span class="badge badge-important count-conversations-with-new-offer"></span>

                                        </a></li>

                                         <li><a  href="<?php echo e(lurl(trans('routes.logout'))); ?>" >

                                    <i class="glyphicon glyphicon-off hidden-sm"></i> 
                                    <span class="font_red"><?php echo e(t('Log Out')); ?></span>

                                </a></li>

                            </ul>

                        </li> 

                    <!-- </div> -->

                        <li class="postadd ddd">

                            <a class="btn btn-primary"   style="color: white !important;background-color: #ff5555;"

                               href="<?php echo e(lurl('posts/create_step1')); ?>">

                                <i class="fa fa-plus-circle"></i> <?php echo e(t('Add Listing')); ?>


                            </a>

                        </li>

                        

                               

                    <?php endif; ?>


                   


                </ul>

            </div>

            

            

            

            <div class="qedamamobile" style="margin-top: -25px;" >

                

              <hr style="border: 1px solid #ff000000;">  

                

                

                

                

                

               

                

                  <ul class="nav navbar-nav" style="float: left; display: inline-block;">

                  <?php if(count(LaravelLocalization::getSupportedLocales()) > 1): ?>

                    <!-- Language selector -->  

                    <?php endif; ?> 

                    <!--<li class="dropdown currency-menu">-->

                    <!--    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">-->

                    <!--        <?php if(!empty(Session::get('currency'))): ?>-->

                    <!--            <?php echo e(Session::get('currency')); ?>-->

                    <!--        <?php else: ?>-->

                    <!--            Select Currency-->

                    <!--        <?php endif; ?>-->

                    <!--        <span class="caret hidden-sm"> </span>-->

                    <!--    </button>-->

                    <!--    <ul class="dropdown-menu" role="menu">-->

                    <!--        <?php if(getCurrency()): ?>-->

                    <!--            <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>-->

                    <!--                <li>-->

                    <!--                    <a href="javascript:;" onclick="selectCurrency('<?php echo e($key); ?>')">-->

                    <!--                        <span class="lang-name"><?php echo e($key); ?></span>-->

                    <!--                    </a>-->

                    <!--                </li>-->

                    <!--            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>-->

                    <!--        <?php endif; ?>-->

                    <!--    </ul>-->

                    <!--</li>-->

                    

                    

                    

                    <?php if(getSegment(1) != trans('routes.countries')): ?>

                        <?php if(isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled): ?>

                            <?php if(!empty(config('country.icode'))): ?>

                                <?php if(file_exists(public_path().'/images/flags/24/'.config('country.icode').'.png')): ?>



                                    <button class="flag-menu country-flag visible-s btn btn-default hidden"

                                            href="#selectCountry" data-toggle="modal">

                                        <img src="<?php echo e(url('images/flags/24/'.config('country.icode').'.png') . getPictureVersion()); ?>"

                                             style="float: left;">

                                        <span class="caret hidden-xs"></span>

                                    </button>

                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endif; ?>

                    <?php endif; ?>

                    

                    <?php if(getSegment(1) != trans('routes.countries')): ?>

                        <?php if(config('settings.geo_location.country_flag_activation')): ?>

                            <?php if(!empty(config('country.icode'))): ?>

                                <?php if(file_exists(public_path().'/images/flags/32/'.config('country.icode').'.png')): ?>

                                    <li class="flag-menu country-flag tooltipHere hidden-xs" data-toggle="tooltip"

                                        data-placement="<?php echo e((config('lang.direction') == 'rtl') ? 'bottom' : 'right'); ?>" <?php echo $multiCountriesLabel; ?>>

                                        <?php if(isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled): ?>

                                            <a href="#selectCountry" data-toggle="modal">

                                                <img class="flag-icon"

                                                     src="<?php echo e(url('images/flags/32/'.config('country.icode').'.png') . getPictureVersion()); ?>"

                                                     style="float: left;">

                                                <span class="caret hidden-sm"></span>

                                            </a>

                                        <?php else: ?>

                                            <a style="cursor: default;">

                                                <img class="flag-icon"

                                                     src="<?php echo e(url('images/flags/32/'.config('country.icode').'.png') . getPictureVersion()); ?>"

                                                     style="float: left;">

                                            </a>

                                        <?php endif; ?>

                                    </li>

                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endif; ?>

                    <?php endif; ?>

                </ul>

                

                

                

                

                

                <ul class="nav navbar-nav ">

                      

                    <?php if(!auth()->check()): ?>

                       

                      <li class="dropdown lang-menu" style="display: inline-block;">

                            <button class="btn btn-default dropdown-toggle" 
                            style="background: #ff000000;
                            border: none;
                            font-size: 15px;
                           "  type="button" data-toggle="dropdown">

                                <?php if(strtoupper(config('app.locale')) == 'EN'): ?>

                                English

                                <?php elseif(strtoupper(config('app.locale')) == 'FR'): ?>

                                 Français 

                                <?php elseif(strtoupper(config('app.locale')) == 'ES'): ?>

                                 Español 

                                <?php elseif(strtoupper(config('app.locale')) == 'DE'): ?>

                                 German 

                                <?php elseif(strtoupper(config('app.locale')) == 'PT'): ?>

                                  Português 

                                <?php elseif(strtoupper(config('app.locale')) == 'TR'): ?>

                                   Türkçe

                                <?php elseif(strtoupper(config('app.locale')) == 'SQ'): ?>

                                    Shqip  

                                <?php elseif(strtoupper(config('app.locale')) == 'RU'): ?>

                                    Русский 

                                <?php elseif(strtoupper(config('app.locale')) == 'AR'): ?>

                                    العربية

                                <?php else: ?>

                                <?php echo e(strtoupper(config('app.locale'))); ?>


                                <?php endif; ?>

                                <span class="caret hidden-sm"> </span>

                            </button>

                            <ul class="dropdown-menu" role="menu">

                                <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <?php if(strtolower($localeCode) != strtolower(config('app.locale'))): ?>

                                        <?php

                                        // Controller Parameters

                                        $attr = [];

                                        $attr['countryCode'] = config('country.icode');

                                        if (isset($uriPathCatSlug)) {

                                            $attr['catSlug'] = $uriPathCatSlug;

                                            if (isset($uriPathSubCatSlug)) {

                                                $attr['subCatSlug'] = $uriPathSubCatSlug;

                                            }

                                        }

                                        if (isset($uriPathCityName) && isset($uriPathCityId)) {

                                            $attr['city'] = $uriPathCityName;

                                            $attr['id'] = $uriPathCityId;

                                        }

                                        if (isset($uriPathUserId)) {

                                            $attr['id'] = $uriPathUserId;

                                            if (isset($uriPathUsername)) {

                                                $attr['username'] = $uriPathUsername;

                                            }

                                        }

                                        if (isset($uriPathUsername)) {

                                            if (isset($uriPathUserId)) {

                                                $attr['id'] = $uriPathUserId;

                                            }

                                            $attr['username'] = $uriPathUsername;

                                        }

                                        if (isset($uriPathTag)) {

                                            $attr['tag'] = $uriPathTag;

                                        }

                                        if (isset($uriPathPageSlug)) {

                                            $attr['slug'] = $uriPathPageSlug;

                                        }



                                        // Default

                                        $link = LaravelLocalization::getLocalizedURL($localeCode, null, [], true);

                                        // var_dump('$link',$attr, $localeCode);

                                        // $link = lurl(null, $attr, $localeCode);

                                        // $localeCode = strtolower($localeCode);

                                        // if($localeCode == 'en'){

                                        // 	$link = "https://www.dealnotdeal.com/en";	

                                        // // 	$link = lurl(null, $attr, 'en');

                                        // }

                                        // else

                                        // {

                                        //     $link = url('/'.$localeCode);	

                                        // }

                                        

                                        ?>

                                        <li>

                                            <a href="<?php echo e($link); ?>" tabindex="-1" rel="alternate"

                                               hreflang="<?php echo e($localeCode); ?>">

                                                <span class="lang-name"> <?php echo e($properties['native']); ?> </span>

                                            </a>

                                        </li>

                                    <?php endif; ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </ul>

                        </li>

                     

                     

                        <li class="postadd" style="display: inline-block; ">

                            <?php if(config('settings.single.guests_can_post_ads') != '1'): ?>

                                <a class="btn btn-default dropdown-toggle btn-add-listing" href="#quickLogin"

                                   data-toggle="modal">

                                    <i class="fa fa-plus-circle"></i> <?php echo e(t('Add Listing')); ?>


                                </a>

                            <?php else: ?>

                                <a class="btn btn-default dropdown-toggle btn-add-listing"

                                   href="<?php echo e(lurl('posts/create_step1')); ?>">

                                    <i class="fa fa-plus-circle"></i> <?php echo e(t('Add Listing')); ?>


                                </a>

                            <?php endif; ?>

                        </li>

                    <?php else: ?>

                    

                     <li class="dropdown lang-menu" style="display: inline-block;">

                            <button class="btn btn-default dropdown-toggle" 
                            style="background: #ff000000;
                            border: none;
                            font-size: 15px;padding: 10.45px 15px;
                           " type="button" data-toggle="dropdown">

                                <?php if(strtoupper(config('app.locale')) == 'EN'): ?>

                                English

                                <?php elseif(strtoupper(config('app.locale')) == 'FR'): ?>

                                 Français 

                                <?php elseif(strtoupper(config('app.locale')) == 'ES'): ?>

                                 Español 

                                <?php elseif(strtoupper(config('app.locale')) == 'DE'): ?>

                                 German 

                                <?php elseif(strtoupper(config('app.locale')) == 'PT'): ?>

                                  Português 

                                <?php elseif(strtoupper(config('app.locale')) == 'TR'): ?>

                                   Türkçe

                                <?php elseif(strtoupper(config('app.locale')) == 'SQ'): ?>

                                    Shqip  

                                <?php elseif(strtoupper(config('app.locale')) == 'RU'): ?>

                                    Русский 

                                <?php elseif(strtoupper(config('app.locale')) == 'AR'): ?>

                                    العربية

                                <?php else: ?>

                                <?php echo e(strtoupper(config('app.locale'))); ?>


                                <?php endif; ?>

                                <span class="caret hidden-sm"> </span>

                            </button>

                            <ul class="dropdown-menu" role="menu">

                                <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <?php if(strtolower($localeCode) != strtolower(config('app.locale'))): ?>

                                        <?php

                                        // Controller Parameters

                                        $attr = [];

                                        $attr['countryCode'] = config('country.icode');

                                        if (isset($uriPathCatSlug)) {

                                            $attr['catSlug'] = $uriPathCatSlug;

                                            if (isset($uriPathSubCatSlug)) {

                                                $attr['subCatSlug'] = $uriPathSubCatSlug;

                                            }

                                        }

                                        if (isset($uriPathCityName) && isset($uriPathCityId)) {

                                            $attr['city'] = $uriPathCityName;

                                            $attr['id'] = $uriPathCityId;

                                        }

                                        if (isset($uriPathUserId)) {

                                            $attr['id'] = $uriPathUserId;

                                            if (isset($uriPathUsername)) {

                                                $attr['username'] = $uriPathUsername;

                                            }

                                        }

                                        if (isset($uriPathUsername)) {

                                            if (isset($uriPathUserId)) {

                                                $attr['id'] = $uriPathUserId;

                                            }

                                            $attr['username'] = $uriPathUsername;

                                        }

                                        if (isset($uriPathTag)) {

                                            $attr['tag'] = $uriPathTag;

                                        }

                                        if (isset($uriPathPageSlug)) {

                                            $attr['slug'] = $uriPathPageSlug;

                                        }



                                        // Default

                                        $link = LaravelLocalization::getLocalizedURL($localeCode, null, [], true);

                                        //var_dump('$link',$attr, $localeCode);

                                        // $link = lurl(null, $attr, $localeCode);

                                        // $localeCode = strtolower($localeCode);

                                        // if($localeCode == 'en'){

                                        	// $link = "https://www.dealnotdeal.com/en";

                                        // 	$link = lurl(null, $attr, 'en');

                                        // }

                                        // else

                                        // {

                                            // $link = url('/'.$localeCode);	

                                        // }

                                        

                                        ?>

                                        <li>

                                            <a href="<?php echo e($link); ?>" tabindex="-1" rel="alternate"

                                               hreflang="<?php echo e($localeCode); ?>">

                                                <span class="lang-name"> <?php echo e($properties['native']); ?> </span>

                                            </a>

                                        </li>

                                    <?php endif; ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </ul>

                        </li>

                       

                       

                       

                        <li class="postadd" style="display: inline-block;">

                            <a class="btn btn-default dropdown-toggle btn-add-listing"

                               href="<?php echo e(lurl('posts/create_step1')); ?>">

                                <i class="fa fa-plus-circle"></i> <?php echo e(t('Add Listing')); ?>


                            </a>

                        </li>

                        

                        

                    <?php endif; ?>





                </ul>

                

                

                

                

                

            </div>

            

            

            

            

            

        </div>

    </nav>

</div>



<script>

    function selectCurrency(currency) {

        if (currency) {

            // if(currency == 'USD'){ var s_url = '/en?d=US'}

            // if(currency == 'EUR'){ var s_url = '/en?d=US'}

            // if(currency == 'GBP'){ var s_url = '/en?d=US'}

            $(".currency-menu").find('.btn').html(currency + ' <span class="caret hidden-sm"></span>');



            $.ajax({

                headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},

                url: "<?php echo e(url('/setCurrency')); ?>/" + currency,

                type: "GET",

                success: function (response) {



                    if (response.status == 'success') {

                        // console.log(window.location.href);

                        // var pathname = window.location.pathname; // Returns path only

                        window.location.reload();

                    }

                },



            });

        }

    }

</script>