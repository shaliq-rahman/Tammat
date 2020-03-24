{{--
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
--}}
@extends('layouts.master') 
 

<link href="{{ url('datepicker/bootstrap-datetimepicker.css') }}" rel="stylesheet"/>

<?php

$getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();
$username = !empty($getusernamedetail->username)?$getusernamedetail->username:'';                                
                                
?>
    

<?php
// Phone
$phone = TextToImage::make($post->phone, IMAGETYPE_PNG, ['backgroundColor' => 'rgba(0,0,0,0.0)', 'color' => '#FFFFFF']);
$phoneLink = 'tel:' . $post->phone;
$phoneLinkAttr = '';
$addToFavrites = t('Add to Favorites');
if (!auth()->check()) {
	if (config('settings.single.guests_can_contact_seller') != '1') {
		$phone = t('Click to see');
		$addToFavrites = t('Add to Favorites');
		$phoneLink = '#quickLogin';
		$phoneLinkAttr = 'data-toggle="modal"';
	}
}

// Contact Seller URL
$contactSellerURL = '#contactUser';
if (!auth()->check()) {
	if (config('settings.single.guests_can_contact_seller') != '1') {
		$contactSellerURL = '#quickLogin';
	}
}
// Make an Offer 
if (auth()->check()) {
	$contactMakeAnOffer = '#makeAnOffer';
}
else
{
	$contactMakeAnOffer = '#quickLogin';	
}
// Buy Now
if (auth()->check()) {
	$buyNow = '#buyNow';
}
else
{
	$buyNow = '#quickLogin';	
}
// GetitforFree
if (auth()->check()) {
	$GetitforFree = '#GetitforFree';
}
else
{
	$GetitforFree = '#quickLogin';	
}	

?>

@section('content')



<style>
.ChangeSendMessageColor
{
    background-color: #e6e6e6 !important;
    border-color: #adadad;
}

.ChangeSendMessageColor:hover
{
    background-color: #ffffff !important;
 
}

/* Next & previous buttons */ 
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -22px;
  color: white;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  z-index: 150;
  
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

@media only screen and (max-width: 300px) {
  .prev, .next {font-size: 11px}
}
</style>
@if(strtoupper(config('app.locale')) == 'AR')
<style>
.number_ltr {
        direction: ltr!important;
        text-align: right;
}
</style>
@endif
<?php


$nextrecord = \DB::table('posts')
                        ->where('id', '>', $post->id)
                        // ->where('user_id', '=', $post->user_id)
                        ->where('archived', '=', 0)
                        ->where('reviewed', '=', 1)
                        ->where('country_code', '=', $post->country_code)
                        ->first();
                        
$previewrecord = \DB::table('posts')
                        ->where('id', '<', $post->id)
                        ->where('archived', '=', 0)
                        ->where('reviewed', '=', 1)
                        // ->where('user_id', '=', $post->user_id)
                        ->where('country_code', '=', $post->country_code)
                        ->first();
                   
                          
$countnextprerecord = \DB::table('posts')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('country_code', '=', $post->country_code)
                        ->count();
if(isset($post->category->parent->parent_id)){
   if($post->category->parent->parent_id!=0){
$categoryname = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $post->category->parent->parent_id)
                        ->first();
  
if($categoryname->parent_id!=0){
    $catnamedee= \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $categoryname->parent_id)
                        ->first();
}
}
}  

if($countnextprerecord  == '1')        
{
    $nextattr1 = ['slug' => slugify($post->title), 'id' => $post->id];
    $nexturi = trans('routes.v-post', ['slug' => slugify($post->title), 'id' => $post->id]);    
    
     $preattr1 = ['slug' => slugify($post->title), 'id' => $post->id];
    $preuri = trans('routes.v-post', ['slug' => slugify($post->title), 'id' => $post->id]);    
}
else
{
    if(!empty($nextrecord))
    {
        $nextattr1 = ['slug' => slugify($nextrecord->title), 'id' => $nextrecord->id];
        $nexturi = trans('routes.v-post', ['slug' => slugify($nextrecord->title), 'id' => $nextrecord->id]);    
    }
    else
    {
       if(!empty($previewrecord->title) && !empty($previewrecord->id))
        {
           $nextattr1 = ['slug' => slugify($previewrecord->title), 'id' => $previewrecord->id];
            $nexturi = trans('routes.v-post', ['slug' => slugify($previewrecord->title), 'id' => $previewrecord->id]);    
        }
        else
        {
            $nextattr1 = '';
            $nexturi = '';
        }
        
        
       
    }
    
    if(!empty($previewrecord))
    {
        $preattr1 = ['slug' => slugify($previewrecord->title), 'id' => $previewrecord->id];
        $preuri = trans('routes.v-post', ['slug' => slugify($previewrecord->title), 'id' => $previewrecord->id]);    
    }
    else
    {
        if(!empty($nextrecord->title) && !empty($nextrecord->id))
        {
            $preattr1 = ['slug' => slugify($nextrecord->title), 'id' => $nextrecord->id];    
            $preuri = trans('routes.v-post', ['slug' => slugify($nextrecord->title), 'id' => $nextrecord->id]);    
        }
        else
        {
            $preattr1 = '';
            $preuri = '';
        }
        
        
    }
}

?>
<a href="{{ lurl($preuri, $preattr1) }}" class="prev" style="left:0;position:fixed;color:red;background: white none repeat scroll 0% 0%;padding: 33px 17px;box-shadow: rgba(0, 0, 0, 0.2) 0px 1px 8px 0px;border-top-right-radius: 90px;border-bottom-right-radius: 90px;" >&#10094; </a>
<a href="{{ lurl($nexturi, $nextattr1) }}" class="next" style="position:fixed;color:red;background: white none repeat scroll 0% 0%;padding: 33px 17px;box-shadow: rgba(0, 0, 0, 0.2) 0px 1px 8px 0px;border-bottom-left-radius: 90px;border-top-left-radius: 90px;" >&#10095;</a>

	{!! csrf_field() !!}
	<input type="hidden" id="post_id" value="{{ $post->id }}">
	
	@if (Session::has('flash_notification'))
		@include('common.spacer')
		<?php $paddingTopExists = true; ?>
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					@include('flash::message')
				</div>
			</div>
		</div>
		<?php Session::forget('flash_notification.message'); ?>
	@endif
	
	<div class="main-container">

		
    <?php
     //   $advertising = \DB::table('category_banner')->where('banner_type', 'top')->where('country_code', config('country.icode'))->where('category_id',$post->category->parent->id)->select('*')->first();
    ?>
    @if (!empty($advertising))
    	@include('home.inc.spacer')
    	<div class="container">
    		<div class="container hidden-md hidden-sm hidden-xs ads-parent-responsive">
    			<div class="text-center">
    				{!! $advertising->tracking_code_large !!}
    			</div>
    		</div>
    		<div class="container hidden-lg hidden-xs ads-parent-responsive">
    			<div class="text-center">
    				{!! $advertising->tracking_code_medium !!}
    			</div>
    		</div>
    		<div class="container hidden-sm hidden-md hidden-lg ads-parent-responsive">
    			<div class="text-center">
    				{!! $advertising->tracking_code_small !!}
    			</div>
    		</div>
    	</div>
    @else
        <?php 
    		if (\App\Models\Advertising::where('slug', 'top')->count() > 0){ 
    		?>
    			@include('layouts.inc.advertising.top', ['paddingTopExists' => (isset($paddingTopExists)) ? $paddingTopExists : false])
    		<?php
    			$paddingTopExists = false;
    		}
    	?>
    @endif
	
	

	
	
	
	
	
	
	
		
		
		
		
		
		
		
		
		
		@include('common.spacer')
		


		<div class="container">
		 
			<ol class="breadcrumb pull-left">
				<li><a href="{{ lurl('/') }}"><i class="icon-home fa"></i></a></li>
				<li><a href="{{ lurl('/') }}">{{ config('country.name') }}</a></li>
				<!--@if(isset($_GET['cat']))
				    @if(isset($post->category->parent->parent_id))
				        @if(($post->category->parent->parent_id)!=0)
				            <li><a href="{{ lurl('/') }}/posts/subcats/{{$post->category->parent->parent_id}}"> {{$_GET['cat']}} </a></li>
				        @else
				             <li><a href="{{ lurl('/') }}"> {{$_GET['cat']}} </a></li>
				        @endif
				    @else
				            <li><a href="{{ lurl('/') }}"> {{$_GET['cat']}} </a></li>
				    @endif
				@endif-->
				 @if(isset($catnamedee->name))
			  @if(isset($catnamedee->id)) 
			        @if(($catnamedee->id)!=0)
			<li>
			    
			 
        			  <a href="{{ lurl('/') }}/posts/subcats/{{$catnamedee->id}}">
        			                {{$catnamedee->name}}
        			    </a>
        			  </li>
        			  <li>
			         @else
			            <a href="{{ lurl('/') }}">
			                {{$catnamedee->name}}
			            </a>
			           @endif
			          </li>
			  @endif
			  @else
			   @if(isset($categoryname->name))
			   @if(isset($post->category->parent->parent_id)) 
			        @if(($post->category->parent->parent_id)!=0)
			        <li>
			            
			            <a href="{{ lurl('/') }}/posts/subcats/{{$post->category->parent->parent_id}}">
			                
			                {{ t($categoryname->name) }}
			             </a>
			         </li>
			         @else
			         <li>
			            <a href="{{ lurl('/') }}">
			                {{ t($categoryname->name) }}
			         </a>
			         </li>
			           @endif
			 @endif
			 @endif
			@endif
			 
				@if (!empty($post->category->parent))
					<li>
						<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $post->category->parent->slug]; ?>
						@if(isset($_GET['cat']))
						<a href="{{ lurl(trans('routes.v-search-cat', $attr), $attr) }}?cat={{$_GET['cat']}}">
						 @else
						 <a href="{{ lurl(trans('routes.v-search-cat', $attr), $attr) }}">
						     @endif
							{{ $post->category->parent->name }}
						</a>
					</li>
					@if ($post->category->parent->id != $post->category->id)
					<li>
						<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $post->category->parent->slug, 'subCatSlug' => $post->category->slug]; ?>
						@if(isset($_GET['cat']))
						<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}?cat={{$_GET['cat']}}">
						@else	
						<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}">
						@endif
							{{ $post->category->name }}
						</a>
					</li>
					@endif
				@else
					<li>
						<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $post->category->slug]; ?>
						
						<a href="{{ lurl(trans('routes.v-search-cat', $attr), $attr) }}">
							{{ $post->category->name }}
						</a>
					</li>
				@endif
				<li class="active">{{ str_limit($post->title, 70) }}</li>
			</ol>
			<div class="pull-right backtolist">
                <a href="{{ URL::previous() }}">
                    <i class="fa fa-angle-double-left"></i> {{ t('Back to Results') }}
                </a>
            </div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-sm-9 page-content col-thin-right">
					<div class="inner inner-box ads-details-wrapper">
						<h2 class="enable-long-words">
							<strong>
								<?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>
								@if(isset($_GET['cat']))
								<a href="{{ lurl($post->uri, $attr) }}?cat={{$_GET['cat']}}" title="{{ $post->title }}">
								@else
								<a href="{{ lurl($post->uri, $attr) }}" title="{{ $post->title }}">
								@endif
									{{ $post->title }}
                                </a>
                            </strong>
							<small class="label label-default adlistingtype">@if(isset($post->postType->name)) {{ $post->postType->name }} @endif</small>
							@if ($post->featured==1 and !empty($post->latestPayment))
								@if (isset($post->latestPayment->package) and !empty($post->latestPayment->package))
									<i class="icon-ok-circled tooltipHere" style="color: {{ $post->latestPayment->package->ribbon }};" title="" data-placement="right"
									   data-toggle="tooltip" data-original-title="{{ $post->latestPayment->package->short_name }}"></i>
									   <span class="label label-default adlistingtype" style="background: #16a085;text-transform: uppercase;">{{ $post->latestPayment->package->short_name }}</span>
								@endif
                            @endif
						</h2>
						<span class="info-row">
							<span class="date"><i class=" icon-clock"> </i> {{ $post->created_at_ta }} </span> -&nbsp;
							<span class="category">{{ (!empty($post->category->parent)) ? $post->category->parent->name : $post->category->name }}</span> -&nbsp;
							<span class="item-location"><i class="fa fa-map-marker"></i> {{ $post->city->name }} </span> -&nbsp;
							<span class="category">
								<i class="icon-eye-3"></i>&nbsp;
								{{ \App\Helpers\Number::short($post->visits) }} {{ trans_choice('global.count_views', getPlural($post->visits)) }} -&nbsp; 
							</span>
							<span class="category">
							    {{ t('ID') }} - {{ $post->id }}  
						    </span>
						</span>
						
						<div class="ads-image">
							@if (!in_array($post->category->type, ['non-salable']))
								<h1 class="pricetag">
									@if ($post->price > 0)
										{!! \App\Helpers\Number::money($post->price) !!}
									@else
										<!--{!! \App\Helpers\Number::money(' --') !!}-->
										{{t('Free')}}
									@endif
								</h1>
							@endif
							@if (count($post->pictures) > 0)
								<ul class="bxslider">
									@foreach($post->pictures as $key => $image)
										<li><img src="{{ resize($image->filename, 'big') }}" alt="img"></li>
									@endforeach
								</ul>
								<div class="product-view-thumb-wrapper">
									<ul id="bx-pager" class="product-view-thumb">
									@foreach($post->pictures as $key => $image)
										<li>
											<a class="thumb-item-link" data-slide-index="{{ $key }}" href="">
												<img src="{{ resize($image->filename, 'small') }}" alt="img">
											</a>
										</li>
									@endforeach
									</ul>
								</div>
							@else
								<ul class="bxslider">
									<li><img src="{{ resize(config('larapen.core.picture.default'), 'big') }}" alt="img"></li>
								</ul>
								<div class="product-view-thumb-wrapper">
									<ul id="bx-pager" class="product-view-thumb">
										<li>
											<a class="thumb-item-link" data-slide-index="0" href="">
												<img src="{{ resize(config('larapen.core.picture.default'), 'small') }}" alt="img">
											</a>
										</li>
									</ul>
								</div>
							@endif
						</div>
						<!--ads-image-->
						
						
						@if (isset($reviewsPlugin) and !empty($reviewsPlugin))
							@if (view()->exists('reviews::ratings-single'))
								@include('reviews::ratings-single')
							@endif
						@endif
						

						<div class="ads-details">
							<ul class="nav nav-tabs">
								<li class="active">
									<a href="#tab-details" data-toggle="tab"><h4>{{ t('Ad\'s Details') }}</h4></a>
								</li>
								@if (isset($reviewsPlugin) and !empty($reviewsPlugin))
									<li>
										<a href="#tab-{{ $reviewsPlugin->name }}" data-toggle="tab">
											<h4>
												{{ trans('reviews::messages.Reviews') }}
												@if (isset($rvPost) and !empty($rvPost))
												({{ $rvPost->rating_count }})
												@endif
											</h4>
										</a>
									</li>
								@endif
							</ul>
							
							<!-- Tab panes -->
							<div class="tab-content">
								<div class="tab-pane active" id="tab-details">
									<div class="row" style="padding: 10px;">
										<div class="ads-details-info col-md-12 col-sm-12 col-xs-12 enable-long-words from-wysiwyg">
											<div style="clear: both;"></div>
											<!-- Description -->
											<div class="detail-line-content">
												{!! transformDescription($post->description) !!}
											</div>
											
											<!-- Custom Fields -->
											@include('post.inc.fields-values')
										
											<!-- Tags -->
											<!--@if (!empty($post->tags))-->
											<!--	<?php $tags = explode(',', $post->tags); ?>-->
											<!--	@if (!empty($tags))-->
											<!--	<div style="clear: both;"></div>-->
											<!--	<div class="tags">-->
											<!--		<h4><i class="icon-tag"></i> {{ t('Tags') }}:</h4>-->
											<!--		@foreach($tags as $iTag)-->
											<!--			<?php $attr = ['countryCode' => config('country.icode'), 'tag' => $iTag]; ?>-->
											<!--			<a href="{{ lurl(trans('routes.v-search-tag', $attr), $attr) }}">-->
											<!--				{{ $iTag }}-->
											<!--			</a>-->
											<!--		@endforeach-->
											<!--	</div>-->
											<!--	@endif-->
											<!--@endif-->
											 
											<!-- Actions -->
											{{-- <div class="detail-line-action col-md-12 col-sm-12 text-center">
												<div class="col-md-4 col-sm-4 col-xs-4">
												@if (auth()->check())
													@if (auth()->user()->id == $post->user_id)
														<a href="{{ lurl('posts/' . $post->id . '/edit') }}">
															<i class="fa fa-pencil-square-o tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Edit') }}"></i>
														</a>
													@else
														@if ($post->email != '')
															<a data-toggle="modal" href="{{ $contactSellerURL }}">
																<i class="icon-mail-2 tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Send a message') }}"></i>
															</a>
														@else
															<i class="icon-mail-2" style="color: #dadada"></i>
														@endif
													@endif
												@else
													@if ($post->email != '')
														<a data-toggle="modal" href="{{ $contactSellerURL }}">
															<i class="icon-mail-2 tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Send a message') }}"></i>
														</a>
													@else
														<i class="icon-mail-2" style="color: #dadada"></i>
													@endif
												@endif
												</div>
												<div class="col-md-4 col-sm-4 col-xs-4">
													<a class="make-favorite" id="{{ $post->id }}">
														@if (auth()->check())
															@if (\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $post->id)->count() > 0)
																<i class="fa fa-heart tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Remove favorite') }}"></i>
															@else
																<i class="fa fa-heart" class="tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Save ad') }}"></i>
															@endif
														@else
															<i class="fa fa-heart" class="tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Save ad') }}"></i>
														@endif
													</a>
												</div>
												<div class="col-md-4 col-sm-4 col-xs-4">
													<a href="{{ lurl('posts/' . $post->id . '/report') }}">
														<i class="fa icon-info-circled-alt tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Report abuse') }}"></i>
													</a>
												</div>
											</div> --}}
										</div>
										
										<br>&nbsp;<br>
									</div>
								</div>
								
								@if (isset($reviewsPlugin) and !empty($reviewsPlugin))
									@if (view()->exists('reviews::comments'))
										@include('reviews::comments')
									@endif
								@endif
							</div>
							<!-- /.tab content -->
									
							{{-- <div class="content-footer text-left">
								@if (auth()->check())
									@if (auth()->user()->id == $post->user_id)
										<a class="btn btn-default" href="{{ lurl('posts/' . $post->id . '/edit') }}"><i class="fa fa-pencil-square-o"></i> {{ t('Edit') }}</a>
									@else
										@if ($post->email != '')
											<a class="btn btn-default" data-toggle="modal" href="{{ $contactSellerURL }}"><i class="icon-mail-2"></i> {{ t('Send a message') }} </a>
										@endif
									@endif
								@else
									@if ($post->email != '')
										<a class="btn btn-default" data-toggle="modal" href="{{ $contactSellerURL }}"><i class="icon-mail-2"></i> {{ t('Send a message') }} </a>
									@endif
								@endif
								@if ($post->phone_hidden != 1 and !empty($post->phone))
									<a href="{{ $phoneLink }}" {!! $phoneLinkAttr !!} class="btn btn-primary showphone">
										<i class="icon-phone-1"></i>
										{!! $addToFavrites !!}
									</a>
								@endif
							</div> --}}
						</div>
					</div>
					<!--/.ads-details-wrapper-->
				</div>
				<!--/.page-content-->

				<div class="col-sm-3 page-sidebar-right">
					<aside>
						<div class="panel sidebar-panel panel-contact-seller">
							<div class="panel-heading">{{ t('Contact advertiser') }}</div>
							<div class="panel-content user-info">
								<div class="panel-body text-center">
									<div class="seller-info">
								        @if (isset($post->contact_name) and $post->contact_name != '')
                                            @if (isset($post->user) and !empty($post->user))
                                                <h3 class="no-margin">
                                                    <?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->user->id]; ?>
                                                    <a href="{{ lurl(trans('routes.v-search-user', $attr), $attr) }}">
                                                        {{ $username }}
                                                    </a>
                                                </h3>
                                            @else
                                                <h3 class="no-margin">{{ $username }}</h3>
                                                <!--<h3 class="no-margin">{{ $post->contact_name }}</h3>-->
                                            @endif
                                        @endif
                                        <ul class="list-inline rating" id="rating-ul" data-rating="0" title="Average Rating - 4">
											<li title="1" id="6-1" data-index="1" data-business_id="6" data-rating="0" class="rating" style="cursor: pointer; color: rgb(204, 204, 204); font-size: 16px;">★</li><li title="2" id="6-2" data-index="2" data-business_id="6" data-rating="0" class="rating" style="cursor: pointer; color: rgb(204, 204, 204); font-size: 16px;">★</li><li title="3" id="6-3" data-index="3" data-business_id="6" data-rating="0" class="rating" style="cursor: pointer; color: rgb(204, 204, 204); font-size: 16px;">★</li><li title="4" id="6-4" data-index="4" data-business_id="6" data-rating="0" class="rating" style="cursor: pointer; color: rgb(204, 204, 204); font-size: 16px;">★</li><li title="5" id="6-5" data-index="5" data-business_id="6" data-rating="0" class="rating" style="cursor: pointer; color: rgb(204, 204, 204); font-size: 16px;">★</li>
										</ul>
                                        
                                         <?php
                                        
                                        $checkpaymentpayaccount = \DB::table('payments')->where('post_id', '=', $post->id)->where('active', '=', 1)->count();
                                        if($checkpaymentpayaccount > 0)
                                        {
                                            
                                        ?>
                                        @if(!empty($post->premium_email))
                                        <p>
                                            Email:&nbsp;
                                            <strong>
                                            {{$post->premium_email}}
                                            </strong>
                                        </p>
                                        @endif
                                        @if(!empty($post->premium_phone))
                                        <p>
                                            Phone:&nbsp;
                                            <strong>
                                            {{$post->premium_phone}}
                                            </strong>
                                        </p>
                                        @endif
                                      
                                        <?php }
                                        ?>
                                        
										<p>
											{{ t('Location') }}:&nbsp;
											<strong>
												<?php $attr = ['countryCode' => config('country.icode'), 'city' => slugify($post->city->name), 'id' => $post->city->id]; ?>
												<a href="{!! lurl(trans('routes.v-search-city', $attr), $attr) !!}">
													{{ $post->city->name }}
												</a>
											</strong>
										</p>
										@if ($post->user and !is_null($post->user->created_at_ta))
											<p> {{ t('Joined') }}: <strong>{{ $post->user->created_at_ta }}</strong></p>
										@endif
									</div>
									<div class="user-ads-action">
										@if (auth()->check())
											@if (auth()->user()->id == $post->user_id)
												<a href="{{ lurl('posts/' . $post->id . '/edit') }}" data-toggle="modal" class="btn btn-default btn-block">
													<i class="fa fa-pencil-square-o"></i> {{ t('Update the Details') }}
												</a>
												<a href="{{ lurl('posts/' . $post->id . '/photos') }}" data-toggle="modal" class="btn btn-default btn-block">
													<i class="icon-camera-1"></i> {{ t('Update Photos') }}
												</a>
												<a href="{{ lurl('account/my-posts/'.$post->id.'/deletepost') }}"  class="btn btn-default btn-block">
													<i class="fa fa-trash"></i> {{ t('Delete') }}
												</a>
												@if (isset($countPackages) and isset($countPaymentMethods) and $countPackages > 0 and $countPaymentMethods > 0)
													<a href="{{ lurl('posts/' . $post->id . '/payment') }}" data-toggle="modal" class="btn btn-success btn-block">
														<i class="icon-ok-circled2"></i> {{ t('Make It Premium') }}
													</a>
												@endif
											@else
												@if ($post->email != '')
													<a href="{{ $contactSellerURL }}" data-toggle="modal" class="ChangeSendMessageColor btn btn-default btn-block">
														<i class="icon-mail-2"></i> {{ t('Send a message') }}
													</a>
												@endif
												@if ($post->price > 0)
        												@if (auth()->check())
        												{{-- <a href="{{ $contactMakeAnOffer }}" data-toggle="modal" class="btn btn-success btn-block">
        													<i class="glyphicon glyphicon-hand-left"></i> {{ t('Make an Offer') }}
        												</a> --}}
        												<a href="{{ lurl('account/makeanoffers/' . $post->id ) }}" data-toggle="modal" class="btn btn-success btn-block">
        													<i class="glyphicon glyphicon-hand-left"></i> {{ t('Make an Offer') }}
        												</a>
        												@endif
        												@if (auth()->check())
        													<a href="{{ $buyNow }}" data-toggle="modal" class="btn btn-danger btn-block">
        														<i class="glyphicon glyphicon-shopping-cart"></i> {{ t('Buy Now') }}
        													</a>
        												@endif
										        @else
                                                    	@if ($post->email != '')
            												<a href="{{ $GetitforFree }}" data-toggle="modal" class="btn btn-danger btn-block">
            													<i class="glyphicon glyphicon-shopping-cart"></i> {{ t('Get it for Free') }}
            												</a>
            											@endif
                                                 @endif
											@endif
										@else
											@if ($post->email != '')
												<a href="{{ $contactSellerURL }}" data-toggle="modal" class=" ChangeSendMessageColor btn btn-default btn-block">
													<i class="icon-mail-2"></i> {{ t('Send a message') }}
												</a>
											@endif
										@if ($post->price > 0)
											@if ($post->email != '')
												<a href="{{ $contactMakeAnOffer }}" data-toggle="modal" class="btn btn-success btn-block">
													<i class="glyphicon glyphicon-hand-left"></i> {{ t('Make an Offer') }}
												</a>
											@endif
											@if ($post->email != '')
												<a href="{{ $buyNow }}" data-toggle="modal" class="btn btn-danger btn-block">
												<i class="glyphicon glyphicon-shopping-cart"></i> {{ t('Buy Now') }}
												</a>
											@endif
                                        @else
                                        	@if ($post->email != '')
												<a href="{{ $GetitforFree }}" data-toggle="modal" class="btn btn-danger btn-block">
													<i class="glyphicon glyphicon-shopping-cart"></i> {{ t('Get it for Free') }}
												</a>
											@endif
                                        @endif
                                        
										@endif
										@if (auth()->check())
										    @if (auth()->user()->id != $post->user_id)
        										<a class="make-favorite btn btn-primary btn-block showphone" id="{{ $post->id }}">
        												@if (\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $post->id)->count() > 0)
        													<i class="fa fa-heart tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Remove favorite') }}"></i>
        												@else
        													<i class="fa fa-heart" class="tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Save ad') }}"></i>
        												@endif
        											{!! $addToFavrites !!}
        										</a>
										    @endif
										@else
        									<a class="make-favorite btn btn-primary btn-block showphone" id="{{ $post->id }}">
    										<i class="fa fa-heart" class="tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Save ad') }}"></i>
        										{!! $addToFavrites !!}
        									</a>
										@endif
									</div>
								</div>
							</div>
						</div>
						
						@if (config('settings.single.show_post_on_googlemap'))
							<div class="panel sidebar-panel">
								<div class="panel-heading">{{ t('Location\'s Map') }}</div>
								<div class="panel-content">
									<div class="panel-body text-left" style="padding: 0;">
										<div class="ads-googlemaps">
											<iframe id="googleMaps" width="100%" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src=""></iframe>
										</div>
									</div>
								</div>
							</div>
						@endif
						
						@if (isVerifiedPost($post))
							@include('layouts.inc.social.horizontal')
						@endif
						
						<div class="panel sidebar-panel">
							<div class="panel-heading">{{ t('Safety Tips for Buyers') }}</div>
							<div class="panel-content">
								<div class="panel-body text-left">
									<ul class="list-check">
										<li> {{ t('Meet seller at a public place') }} </li>
										<li> {{ t('Check the item before you buy') }} </li>
										<li> {{ t('Pay only after collecting the item') }} </li>
									</ul>
                                    <?php $tipsLinkAttributes = getUrlPageByType('tips'); ?>
                                    @if (!str_contains($tipsLinkAttributes, 'href="#"') and !str_contains($tipsLinkAttributes, 'href=""'))
									<p>
										<a class="pull-right" {!! $tipsLinkAttributes !!}>
                                            {{ t('Know more') }}
                                            <i class="fa fa-angle-double-right"></i>
                                        </a>
                                    </p>
                                    @endif
								</div>
							</div>
						</div>
						
                        	<?php
                             //   $sidebar_advertising = \DB::table('category_sidebar_banner')->where('country_code', config('country.icode'))->where('category_id',$post->category->parent->id)->select('*')->first();
                                if(!empty($sidebar_advertising))
                                {
                            ?>
            						<div class="panel sidebar-panel">
            						    <img  style="width: 100%;height: 150px;" src="{{ url('banner/'.$sidebar_advertising->tracking_code_large) }}">
            						</div>
						    <?php 
                                }
						    ?>
						
					</aside>
					
					
					
				</div>
			</div>

		</div>
		
		@include('home.inc.featured', ['firstSection' => false])
		
		
		
		
		
		
		<div style="clear:both"></div>

        <div class="container" style="margin-top: 27px;">
            <div class="col-lg-12 content-box layout-section">
                <div class="row row-featured row-featured-category">

                    <div class="col-lg-12 box-title" style="text-align: center;">
                        <div class="inner">
                            <h2>
							<span class="title-3">
							    <span style="font-weight: bold;">{{t('Ads')}}</span> {{t('Posted by')}}

                                @if (isset($post->contact_name) and $post->contact_name != '')
                                    <?php
                                    $name_user = strtolower($username);
                                    $name_user = ucwords($name_user);

                                    ?>

                                    @if (isset($post->user) and !empty($post->user))

                                        <?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->user->id]; ?>
                                        @if(isset($_GET['cat']))
                                        <a href="{{ lurl(trans('routes.v-search-user', $attr), $attr) }}?cat={{$_GET['cat']}}">
										@else	
										<a href="{{ lurl(trans('routes.v-search-user', $attr), $attr) }}">
										@endif
														{{ $name_user }}
													</a>

                                    @else
                                        {{ $username }}
                                    @endif
                                @endif

							</span>

                            </h2>
                        </div>
                    </div>

                    <div style="clear: both"></div>

                    <div class="relative content featured-list-row clearfix">
                        <div class="large-12 columns">

                            <?php
                            $getdetail = \DB::table('posts')
                                ->leftJoin('payments', 'payments.post_id', '=', 'posts.id')
                                ->where('user_id', '=', $post->user_id)
                                ->where('archived', '=', 0)
                                ->where('reviewed', '=', 1)
                                ->where('country_code', '=', $post->country_code)
                                ->orderBy('id', 'desc')
                                ->limit(4)
                                ->get(['posts.*','payments.package_id']);
                            if (!isset($cats)) {
                                $cats = collect([]);
                            } ?>

                            @foreach($getdetail as $value_post)
                                <?php
                                // Get Pack Info
                                $package = null;
                                // if ($value_post->featured == 1) {
                                //     $cacheId = 'package.' . $value_post->package_id . '.' . config('app.locale');
                                //     $package = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($value_post) {
                                //     $package = \App\Models\Package::findTrans($value_post->package_id);
                                //         return $package;
                                //     });
                                // }
                                $pictures = \App\Models\Picture::where('post_id', $value_post->id)->orderBy('position')->orderBy('id');
                                if ($pictures->count() > 0) {
                                    $postImg = resize($pictures->first()->filename, 'medium');
                                } else {
                                    $postImg = resize(config('larapen.core.picture.default'));
                                }
                                $cacheId = 'postType.' . $value_post->post_type_id . '.' . config('app.locale');
                                $postType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($value_post) {
                                    $postType = \App\Models\PostType::findTrans($value_post->post_type_id);
                                    return $postType;
                                });
                                if (empty($postType)) continue;
                                // Get the Post's City
                                $cacheId = config('country.code') . '.city.' . $value_post->city_id;
                                $city = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($value_post) {
                                    $city = \App\Models\City::find($value_post->city_id);
                                    return $city;
                                });
                                if (empty($city)) continue;
                                $value_post->created_at = \Date::parse($value_post->created_at)->timezone(config('timezone.id'));
                                $value_post->created_at = $value_post->created_at->ago();
                                $getcategorydata = \DB::table('categories')
                                    ->select('*')
                                    ->where('id', '=', $value_post->category_id)
                                    ->first();
                                if (!empty($getcategorydata->parent_id)) {
                                    $getcategorydataparent = \DB::table('categories')
                                        ->select('*')
                                        ->where('id', '=', $getcategorydata->parent_id)
                                        ->first();

                                    $liveCatParentId = $getcategorydataparent->id;
                                    $liveCatName = $getcategorydataparent->name;
                                } else {
                                    $liveCatParentId = $getcategorydata->id;
                                    $liveCatName = $getcategorydata->name;
                                }?>
                                <div class="item-list make-grid" style="height: 319px;">
                                    <div class="col-sm-2 no-padding photobox">
                                        <div class="add-image">
                                            <span class="photo-count"><i
                                                        class="fa fa-camera"></i> {{ $pictures->count() }} </span>
                                            <?php $attr1 = ['slug' => slugify($value_post->title), 'id' => $value_post->id];
                                            $uri = trans('routes.v-post', ['slug' => slugify($value_post->title), 'id' => $value_post->id]);
                                            ?>
                                            @if(isset($_GET['cat']))
                                            <a href="{{ lurl($uri, $attr1) }}?cat={{$_GET['cat']}}">
                                            @else
                                            <a href="{{ lurl($uri, $attr1) }}">
                                             @endif   
                                                <img class="thumbnail no-margin" src="{{ $postImg }}" alt="img">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="add-desc-box col-sm-7">
                                        <div class="add-details">
                                            <h5 class="add-title">

                                                <?php
                                                $uri = trans('routes.v-post', ['slug' => slugify($value_post->title), 'id' => $value_post->id]);
                                                $attr11 = ['slug' => slugify($value_post->title), 'id' => $value_post->id]; ?>
                                                @if(isset($_GET['cat']))
                                                <a href="{{ lurl($uri, $attr11) }}?cat={{$_GET['cat']}}">{{ str_limit($value_post->title, 70) }} </a>
                                                @else
                                                <a href="{{ lurl($uri, $attr11) }}">{{ str_limit($value_post->title, 70) }} </a>
                                                @endif
                                            </h5>

      <!--                                      <span class="info-row">-->
      <!--                  <span class="add-type business-ads tooltipHere" data-toggle="tooltip" data-placement="right"-->
      <!--                        title="{{ $postType->name }}">-->
						<!--{{ strtoupper(mb_substr($postType->name, 0, 1)) }}-->
					 <!--   </span>&nbsp; -->
					      <?php $attr_user = ['countryCode' => config('country.icode'), 'id' =>  $post->user_id]; ?>
					    {{-- <div class="date" style="display: inline;"><i class="icon-user"> </i> 
					    @if(isset($_GET['cat']))
					    <a style="font-weight: normal;" href="{{ lurl(trans('routes.v-search-user', $attr_user), $attr_user) }}?cat={{$_GET['cat']}}">{{ $username }}</a>
					    @else
					    <a style="font-weight: normal;" href="{{ lurl(trans('routes.v-search-user', $attr_user), $attr_user) }}">{{ $username }}</a>
            		    @endif
            		    </div> --}}
            		    <div class="date" style="margin-left: 0px;margin-top: -1px;"><i class="icon-clock"> </i> {{ $value_post->created_at }} </div>
                                                {{-- @if (isset($liveCatParentId) and isset($liveCatName))
                                                    <div class="category" style="margin-left: 3px;"> <i
                                                                class="fa fa-list-alt"></i>&nbsp;
							                        <a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except('c'), ['c'=>$liveCatParentId])) !!}"
                                                       class="info-link">{{ $liveCatName }}</a>
						                            </div>
                                                @endif --}}
                                                <div  style="margin-left: 0px;" class="item-location">
													     <img style="margin-right: 1px;" src="{{ url('images/blank.gif') . getPictureVersion() }}"  class="flag flag-<?=strtolower($value_post->country_code)?>" >
						<a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except(['l', 'location']), ['l'=>$value_post->city_id])) !!}"
                           class="info-link">{{ $city->name }}</a> {{ (isset($value_post->distance)) ? '- ' . round(lengthPrecision($value_post->distance), 2) . unitOfLength() : '' }}
					  </div>
                    </span>
                                        </div>
                                    </div>

                                    <div class="col-sm-3 text-right price-box">
                                        <h4 class="item-price">
                                            @if (isset($liveCatType) and !in_array($liveCatType, ['non-salable']))
                                                @if ($value_post->price > 0)
                                                    {!! \App\Helpers\Number::money($value_post->price) !!}
                                                @else
                                                    <!--{!! \App\Helpers\Number::money('--') !!}-->
                                                    {{t('Free')}}
                                                @endif
                                            @else
                                                @if ($value_post->price > 0)
                                                    {!! \App\Helpers\Number::money($value_post->price) !!}
                                                @else
                                                    {{t('Free')}}
                                                @endif
                                            @endif
                                        </h4>
                                        @if (isset($package) and !empty($package))
                                            @if ($package->has_badge == 1)
                                                <a class="btn btn-danger btn-sm make-favorite"><i
                                                            class="fa fa-certificate"></i><span> {{ $package->short_name }} </span></a>
                                                &nbsp;
                                            @endif
                                        @endif
                                        @if (auth()->check())
                                            <a class="btn btn-{{ (\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $value_post->id)->count() > 0) ? 'success' : 'default' }} btn-sm make-favorite"
                                               id="{{ $post->id }}">
                                                <i class="fa fa-heart"></i><span> {{ t('Save') }} </span>
                                            </a>
                                        @else
                                            <a class="btn btn-default btn-sm make-favorite" id="{{ $value_post->id }}"><i
                                                        class="fa fa-heart"></i><span> {{ t('Save') }} </span></a>
                                        @endif
                                    </div>


                                </div>





                            @endforeach


                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		
		
	<?php
    //    $advertising = \DB::table('category_banner')->where('banner_type', 'bottom')->where('country_code', config('country.icode'))->where('category_id',$post->category->parent->id)->select('*')->first();
    ?>
    @if (!empty($advertising))
    	@include('home.inc.spacer')
    	<div class="container">
    		<div class="container hidden-md hidden-sm hidden-xs mb20 ads-parent-responsive">
    			<div class="text-center">
    				{!! $advertising->tracking_code_large !!}
    			</div>
    		</div>
    		<div class="container hidden-lg hidden-xs mb20 ads-parent-responsive">
    			<div class="text-center">
    				{!! $advertising->tracking_code_medium !!}
    			</div>
    		</div>
    		<div class="container hidden-sm hidden-md hidden-lg ads-parent-responsive">
    			<div class="text-center">
    				{!! $advertising->tracking_code_small !!}
    			</div>
    		</div>
	    </div>
    @else
        @include('layouts.inc.advertising.bottom', ['firstSection' => false])
    @endif
		
		
		
		
		@if (isVerifiedPost($post))
			@include('layouts.inc.tools.facebook-comments', ['firstSection' => false])
		@endif
		
	</div>
@endsection

@section('modal_message')
	@if (auth()->check() or config('settings.single.guests_can_contact_seller')=='1')
		@include('post.inc.compose-message')
		@include('post.inc.buymessage')
		@include('post.inc.getitforfree') 
	@endif
@endsection

@section('make_an_offer')
	@if (auth()->check() or config('settings.single.guests_can_contact_seller')=='1')
		@include('post.inc.make-an-offer')
	@endif
@endsection

@section('after_styles')
	<!-- bxSlider CSS file -->
	@if (config('lang.direction') == 'rtl')
		<link href="{{ url('assets/plugins/bxslider/jquery.bxslider.rtl.css') }}" rel="stylesheet"/>
	@else
		<link href="{{ url('assets/plugins/bxslider/jquery.bxslider.css') }}" rel="stylesheet"/>
	@endif
@endsection

@section('after_scripts')
    @if (config('services.googlemaps.key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemaps.key') }}" type="text/javascript"></script>
    @endif

<script src="{{ url('datepicker/moment-with-locales.js') }}"></script>
<script src="{{ url('datepicker/bootstrap-datetimepicker.js') }}"></script>

<script>
$(document).ready(function() {

	load_business_data();
	
	function load_business_data(){
		$.ajax({
			url:"/ratings/get/{{$post->id}}",
			method:"GET",
			success:function(data){
				remove_background(6);
				for (var count = 1; count <= data; count++) {
					$("#6-" + count).css("color", "#ffcc00");
				}
				$('.rating').attr("data-rating", data);
			},
			error: function(){
				alert("There is some problem in System");
			},
		});
	}
	$(document).on("click", "li.rating", function() {
		var index = $(this).data("index");
		var business_id = $(this).data("business_id");
		var rater = {!!isset(auth()->user()->id) ?auth()->user()->id: 0!!};
		if (!rater){
			return alert('Please login first to rate Item');
		}
		$.ajax({
		url: "/ratings/set",
		method: "POST",
		data: { rated: {{$post->user_id}}, rater: rater, post: {{$post->id}}, rate: index },
		success: function(data) {
			alert(data);
			load_business_data();
			console.log(data);
		},
		error: function(){
			alert("There is some problem in System");
		},
		});
	});

	$(document).on("mouseenter", "li.rating", function() {
		var index = $(this).data("index");
		var business_id = $(this).data("business_id");
		remove_background(business_id);
		for (var count = 1; count <= index; count++) {
		$("#" + business_id + "-" + count).css("color", "#ffcc00");
		}
	});

	function remove_background(business_id) {
		for (var count = 1; count <= 5; count++) {
		$("#" + business_id + "-" + count).css("color", "#ccc");
		}
	}

	$(document).on("mouseleave", "li.rating", function() {
		var index = $(this).data("index");
		var business_id = $(this).data("business_id");
		var rating = $(this).data("rating");
		remove_background(business_id);
		for (var count = 1; count <= rating; count++) {
		$("#" + business_id + "-" + count).css("color", "#ffcc00");
		}
	});

});
</script>

	<!-- bxSlider Javascript file -->
	<script src="{{ url('assets/plugins/bxslider/jquery.bxslider.min.js') }}"></script>
     <script type="text/javascript">
            $(function () {
                	 $('.datetimepicker').datetimepicker({
                         useCurrent: false
                     });
                
            });
        </script>
        
        
	<script>
		/* Favorites Translation */
        var lang = {
            labelSavePostSave: "{!! t('Save ad') !!}",
            labelSavePostRemove: "{!! t('Remove favorite') !!}",
            loginToSavePost: "{!! t('Please log in to save the Ads.') !!}",
            loginToSaveSearch: "{!! t('Please log in to save your search.') !!}",
            confirmationSavePost: "{!! t('Post saved in favorites successfully !') !!}",
            confirmationRemoveSavePost: "{!! t('Post deleted from favorites successfully !') !!}",
            confirmationSaveSearch: "{!! t('Search saved successfully !') !!}",
            confirmationRemoveSaveSearch: "{!! t('Search deleted successfully !') !!}"
        };
		
		$(document).ready(function () {
			/* Slider */
			var $mainImgSlider = $('.bxslider').bxSlider({
				speed: 1000,
				pagerCustom: '#bx-pager',
				adaptiveHeight: true
			});
			
			/* Initiates responsive slide */
			var settings = function () {
				var mobileSettings = {
					slideWidth: 80,
					minSlides: 2,
					maxSlides: 5,
					slideMargin: 5,
					adaptiveHeight: true,
					pager: false
				};
				var pcSettings = {
					slideWidth: 100,
					minSlides: 3,
					maxSlides: 6,
					pager: false,
					slideMargin: 10,
					adaptiveHeight: true
				};
				return ($(window).width() < 768) ? mobileSettings : pcSettings;
			};
			
			var thumbSlider;
			
			function tourLandingScript() {
				thumbSlider.reloadSlider(settings());
			}
			
			thumbSlider = $('.product-view-thumb').bxSlider(settings());
			$(window).resize(tourLandingScript);
			
			
			@if (config('settings.single.show_post_on_googlemap'))
				/* Google Maps */
				getGoogleMaps(
				'{{ config('services.googlemaps.key') }}',
				'{{ (isset($post->city) and !empty($post->city)) ? addslashes($post->city->name) . ',' . config('country.name') : config('country.name') }}',
				'{{ config('app.locale') }}'
				);
			@endif
            
			/* Keep the current tab active with Twitter Bootstrap after a page reload */
            /* For bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line */
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                /* save the latest tab; use cookies if you like 'em better: */
                localStorage.setItem('lastTab', $(this).attr('href'));
            });
            /* Go to the latest tab, if it exists: */
            var lastTab = localStorage.getItem('lastTab');
            if (lastTab) {
                $('[href="' + lastTab + '"]').tab('show');
            }
		})
	</script>
@endsection