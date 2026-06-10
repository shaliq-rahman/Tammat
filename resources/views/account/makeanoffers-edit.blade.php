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
	<?php
	if (auth()->check()) {
		$addmore = '#addmore';
	}
	else
	{
		$contactMakeAnOffer = '#quickLogin';	
	}

	if (auth()->check()) {
		$updateoffer = '#updateoffer';
	}
	else
	{
		$contactMakeAnOffer = '#quickLogin';	
	}
	?>
	@extends('layouts.master')
	@section('content')
	<div class="main-container" style="margin-top: 85px;">
		<div class="container">
			<div class="row">
				<div class="col-md-12 page-content">
					<div class="inner-box category-content">
						<h2 class="title-2">
							<strong> <i class="icon-docs"></i> {{ t('Update Offer') }}</strong> -&nbsp;
							<?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>
							<a href="{{ lurl($post->uri, $attr) }}" class="tooltipHere" title="" data-placement="top"
								data-toggle="tooltip"
								data-original-title="{!! $post->title !!}">
								{!! str_limit($post->title, 45) !!}
							</a>
						</h2>
						<form class="form-horizontal" id="postForm" method="POST" action="{{ url((config('app.locale')!='en'?config('app.locale'):'en').'/account/makeanoffers/storeeditoffer')}}" enctype="multipart/form-data">
							<input type="hidden" name="post-id" value="{{ $post->id }}" />
							<input type="hidden" name="makeanoffer-id" value="{{ $makeanoffer->id }}" />
							<?php

							if (!empty($picture->filename)) {
								$postImg = resize($picture->filename, 'medium');
							} else {
								$postImg = resize(config('larapen.core.picture.default'));
							}
							?>

							<div class="row brefcase-row">
								<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 nopadding">
									<div class="col-md-5 col-lg-5 col-sm-5 col-xs-12 nopadding">
									<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
										<div class="col-md-6 col-lg-6 col-sm-6 col-xs-4 pull-left username-position">
											<h1 class="buyername-header" style="color: #3c80af !important;">@if(isset($buyer)) {{ $buyer->username }} @else <br> @endif</h1>
										</div>
									</div>
									<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
										<div class="col-md-6 col-lg-6 col-sm-6 col-xs-4 buyer-face pull-left">
										</div>
										<div class="col-md-6 col-lg-6 col-sm-6 col-xs-12 brefcase-grip brefcase-grip-top pull-left">
										</div>
									</div>
										<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 brefcase buyer">
											<div class="row">
												<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 nopadding">
													<div id="container5" class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-up nopadding">
														@if(!empty($buyerProduct3))
														<input class="buyer-product-selected" type="hidden" name="buyer_product_3" value="{{ $buyerProduct3->id }}" />
														<div>
															@if(auth()->user()->id != $makeanoffer->offer_maker_id || $makeanoffer->approve_seller == 1 || $makeanoffer->approve_seller == 2)
															@if($makeanoffer->approve_seller == 2)
															@if(auth()->user()->id != $makeanoffer->offer_maker_id) 
															    @if($makeanoffer->counter_offer != 0)
															<button style="color: white; background-color: #ed1c24; border-radius: 50px; padding-left: 5px; padding-right: 5px;" type="button" class="close remove-product removeprouctcontainner5" >×</button>
															    @endif
															@endif 
															@endif
															@endif

															<div class="col-md-12 col-lg-12 col-sm-12 drag">
																<?php

																if (!empty($buyerProduct3->filename)) {
																	$postImgbuyerProduct3 = resize($buyerProduct3->filename, 'medium');
																} else {
																	$postImgbuyerProduct3 = resize(config('larapen.core.picture.default'));
																}
																?>
																<a class="thumbnail" target="_blank" href="{{ lurl('/').'/'.slugify($buyerProduct3->title).'/'.$buyerProduct3->id }}"><img alt="" src="{{$postImgbuyerProduct3}}"></a>
																<div class="col-md-12 col-lg-12 col-sm-12 product-name">
																	<p class="product-name-data"><span class="tooltipHere" data-placement="top" data-toggle="tooltip" data-original-title="{!! $buyerProduct3->title !!}">{!! $buyerProduct3->title !!}</span><br> <span>
																		@if ($buyerProduct3->price > 0)
																		{!! \App\Helpers\Number::money_price_latest($buyerProduct3->price,$buyerProduct3->html_entity,$buyerProduct3->in_left,$buyerProduct3->decimal_places,$buyerProduct3->decimal_separator) !!}

																		<!--{!! \App\Helpers\Number::money($buyerProduct3->price) !!}-->
																		@else
																		<!--{!! \App\Helpers\Number::money(' --') !!}-->
																		{{t('Free')}}
																		@endif
																	</span>
																</p> 
															</div>
														</div>
													</div>
													@else
													<input class="buyer-product-selected" type="hidden" name="buyer_product_3" value="" />
													@endif
												</div>
												
													@if($makeanoffer->counter_offer == '1')     
    													<div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-up-last nopadding">
                                                	@else
                                                	    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-up-last nopadding" style="<?=($makeanoffer->offer_price == '0.0' || $makeanoffer->offer_price == '0')?'display:none;':''?>">
                                                	@endif

													<div class="col-md-12 col-lg-12 col-sm-12">
														<div class="col-md-12 col-lg-12 col-sm-12 cash-heading">
															{{-- <label>Cash</label> --}}
														</div>
													</div>
													<div class="col-md-12 col-lg-12 col-sm-12" style="padding-left:0px;">
														<div class="input-group offer-price offer-price-left">
															 
																<input   id="offer_price_buyer" name="offer_price_buyer" type="text" placeholder="Offer Price" maxlength="60" class="form-control offer-price" value="{{ $makeanoffer->offer_price }}"   
                                                                @if(($makeanoffer->counter_offer == '0') ||($makeanoffer->close_offer == 1)) 
                                                                  disabled
                                                                 @endif
                                                                >
																 
																<span class="input-group-addon">
																	{!! substr(\App\Helpers\Number::money(''), 0 , -1) !!}
																</span>
															</div>
														</div>
													</div>	
													
													
												</div>
											</div>
											<div class="row">
												<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 nopadding">
													<div id="container1" class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-down nopadding">
														@if(!empty($buyerProduct1))
														<input class="buyer-product-selected" type="hidden" name="buyer_product_1" value="{{ $buyerProduct1->id }}" />
														<div>
															@if(auth()->user()->id != $makeanoffer->offer_maker_id || $makeanoffer->approve_seller == 1 || $makeanoffer->approve_seller == 2)
															@if($makeanoffer->approve_seller == 2)
															@if(auth()->user()->id != $makeanoffer->offer_maker_id)
															    @if($makeanoffer->counter_offer != 0)
															        <button style="color: white; background-color: #ed1c24; border-radius: 50px; padding-left: 5px; padding-right: 5px;" type="button" class="close remove-product removeprouctcontainner1" >×</button>
															    @endif								
															@endif								
															@endif								
															@endif								
															<div class="col-md-12 col-lg-12 col-sm-12 drag">
																<?php

																if (!empty($buyerProduct1->filename)) {
																	$postImgbuyerProduct1 = resize($buyerProduct1->filename, 'medium');
																} else {
																	$postImgbuyerProduct1 = resize(config('larapen.core.picture.default'));
																}
																?>
																<a class="thumbnail" target="_blank" href="{{ lurl('/').'/'.slugify($buyerProduct1->title).'/'.$buyerProduct1->id }}"><img alt="" src="{{$postImgbuyerProduct1}}"></a>
																<div class="col-md-12 col-lg-12 col-sm-12 product-name">
																	<p class="product-name-data"><span class="tooltipHere" data-placement="top" data-toggle="tooltip" data-original-title="{!! $buyerProduct1->title !!}">{!! $buyerProduct1->title !!}</span><br> <span>
																		@if ($buyerProduct1->price > 0)
																		{!! \App\Helpers\Number::money_price_latest($buyerProduct1->price,$buyerProduct1->html_entity,$buyerProduct1->in_left,$buyerProduct1->decimal_places,$buyerProduct1->decimal_separator) !!}
																		<!--{!! \App\Helpers\Number::money($buyerProduct1->price) !!}-->
																		@else
																		<!--{!! \App\Helpers\Number::money(' --') !!}-->
																		{{t('Free')}}
																		@endif
																	</span>
																</p> 
															</div>
														</div>
													</div>
													@else
													<input class="buyer-product-selected" type="hidden" name="buyer_product_1" value="" />
													@endif
												</div>
												<div id="container2" class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-down-last nopadding">

													@if(!empty($buyerProduct2))
													<input class="buyer-product-selected" type="hidden" name="buyer_product_2" value="{{ $buyerProduct2->id }}" />
													<div>
														@if(auth()->user()->id != $makeanoffer->offer_maker_id || $makeanoffer->approve_seller == 1 || $makeanoffer->approve_seller == 2)
														@if($makeanoffer->approve_seller == 2)
														@if(auth()->user()->id != $makeanoffer->offer_maker_id) 
														    @if($makeanoffer->counter_offer != 0)
														<button style="color: white; background-color: #ed1c24; border-radius: 50px; padding-left: 5px; padding-right: 5px;" type="button" class="close remove-product removeprouctcontainner2" >×</button>
														    @endif
														@endif
														@endif
														@endif										        			
														<div class="col-md-12 col-lg-12 col-sm-12 drag">
															<?php

															if (!empty($buyerProduct2->filename)) {
																$postImgbuyerProduct2 = resize($buyerProduct2->filename, 'medium');
															} else {
																$postImgbuyerProduct2 = resize(config('larapen.core.picture.default'));
															}
															?>
															<a class="thumbnail" target="_blank" href="{{ lurl('/').'/'.slugify($buyerProduct2->title).'/'.$buyerProduct2->id }}"><img alt="" src="{{$postImgbuyerProduct2}}"></a>
															<div class="col-md-12 col-lg-12 col-sm-12 product-name">
																<p class="product-name-data"><span class="tooltipHere" data-placement="top" data-toggle="tooltip" data-original-title="{!! $buyerProduct2->title !!}">{!! $buyerProduct2->title !!}</span><br> <span>
																	@if ($buyerProduct2->price > 0)
																	{!! \App\Helpers\Number::money_price_latest($buyerProduct2->price,$buyerProduct2->html_entity,$buyerProduct2->in_left,$buyerProduct2->decimal_places,$buyerProduct2->decimal_separator) !!}
																	<!--{!! \App\Helpers\Number::money($buyerProduct2->price) !!}-->
																	@else
																	<!--{!! \App\Helpers\Number::money(' --') !!}-->
																	{{t('Free')}}
																	@endif
																</span>
															</p> 
														</div>
													</div>
												</div>
												@else
												<input class="buyer-product-selected" type="hidden" name="buyer_product_2" value="" />
												@endif
											</div>	
										</div>	
									</div>	
								</div>	
								<!-- <div class="container">
									<div class='row'> -->
									
									

										@if(auth()->user()->id != $makeanoffer->offer_maker_id || $makeanoffer->approve_seller == 1 || $makeanoffer->approve_seller == 2)
										@if($makeanoffer->approve_seller == 2)
										@if(auth()->user()->id != $makeanoffer->offer_maker_id) 
											<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 product-carousel">
											<div class="carousel slide media-carousel" id="media1">
												<div class="carousel-inner buyer-carousel">
													<?php  $j = 0 ;?>
													@foreach($buyerPosts->unique('id') as $buyerPost)
													<?php


													if (!empty($picture->filename)) {
														$postproductImg = resize($buyerPost->filename, 'medium');
													} else {
														$postproductImg = resize(config('larapen.core.picture.default'));
													}


													$displaynonebuyer = '';
													if(!empty($buyerProduct3->id) && $buyerProduct3->id == $buyerPost->id)
													{
														$displaynonebuyer = 'display:none';
													}
													else if(!empty($buyerProduct1->id) && $buyerProduct1->id == $buyerPost->id)
													{
														$displaynonebuyer = 'display:none';
													}
													else if(!empty($buyerProduct2->id) && $buyerProduct2->id == $buyerPost->id)
													{
														$displaynonebuyer = 'display:none';
													}

													?>

													<div id="drag<?=$j ?>" class="col-md-4 col-lg-4 col-sm-4 col-xs-4 col-md-4 drag buyer-drag-product buyerproducthide{{$buyerPost->id}}" style="{{$displaynonebuyer}}">
														<a class="thumbnail" href="{{ lurl('/').'/'.slugify($buyerPost->title).'/'.$buyerPost->id }}" target="_blank"><img alt="" src="{{ $postproductImg }}"></a>
														<input class="buyer-product" type="hidden" name="buyer_product" value="{{ $buyerPost->id }}" />
														<div class="col-md-12 col-lg-12 col-sm-12 product-name">
															<p class="product-name-data">
																<span>
																	{!! $buyerPost->title !!}
																</span>
																<br>
																<span>
																	@if ($buyerPost->price > 0)
																	{!! \App\Helpers\Number::money_price_latest($buyerPost->price,$buyerPost->html_entity,$buyerPost->in_left,$buyerPost->decimal_places,$buyerPost->decimal_separator) !!}
																	<!--{!! \App\Helpers\Number::money($buyerPost->price) !!}-->
																	@else
																	<!--{!! \App\Helpers\Number::money(' --') !!}-->
																	{{t('Free')}}
																	@endif
																</span>
															</p> 
														</div>	
													</div>

													<?php  $j++;?>
													@endforeach
												</div>
												@if($buyerPosts->unique('id')->count() > 3)
												<a data-slide="prev" href="#media1" class="left carousel-control">‹</a>
												<a data-slide="next" href="#media1" class="right carousel-control">›</a>
												@endif    
											</div>                          
										</div>
										<div class="col-md-12 product-model-button hide">
											<div class="row">
												<div class="col-md-12 col-lg-12 col-sm-12">
													<button class="btn btn-primary col-md-12 col-lg-12 col-sm-12">Add Products</button>
												</div>	
											</div>	
										</div>    
										@endif
										@endif
										@endif


									<!-- </div></div> -->
								</div>
								<div class="col-md-2 col-lg-2 col-sm-2 col-xs-12">
									<div class="col-md-12" style="padding-left: 10px;padding-right: 10px;text-align: center;">
										<div class="row">
											<div class="form-group">
												@if($makeanoffer->close_offer == 1)
												<div class="col-md-12 col-lg-12 col-sm-12 not-deal-icon center"></div>
												<div class="col-md-12 col-lg-12 col-sm-12" style="cursor: auto;margin-top: 10px;background-color: #d9534f;border-color: #d9534f;color: #fff;text-align: center;padding: 6px;">{{ t('Closed') }}</div>
												@else
												@if(auth()->user()->id != $makeanoffer->offer_maker_id || $makeanoffer->approve_seller == 1 || $makeanoffer->approve_seller == 2)
												@if($makeanoffer->approve_seller == 1)
												<div class="col-md-12 col-lg-12 col-sm-12">
													<div class="col-md-12 col-lg-12 col-sm-12 deal-icon center"></div>	
												</div>
												<div  class="col-md-12 col-lg-12 col-sm-12">
													@if($makeanoffer->counter_offer == '0')      
													@if(auth()->user()->id == $makeanoffer->buyer_id)
													<div class="col-md-12 col-lg-12 col-sm-12 blink_me_offer" style="cursor: auto;margin-top: 10px;color: green;text-align: center;padding: 6px;">{{ t('Accepted') }}</div>
													<a href="#proceedDelivery" style="margin-top: 50px;" data-toggle="modal" class="btn btn-primary btn-block">
													{{ t('Send Delivery Info') }}</a>
													@else
													<div class="col-md-12 col-lg-12 col-sm-12 blink_me_offer" style="cursor: auto;margin-top: 10px;color: green;text-align: center;padding: 6px;">{{ t('Deal') }}</div>
													@endif
													@else
													@if(auth()->user()->id == $makeanoffer->offer_maker_id)
													<div class="col-md-12 col-lg-12 col-sm-12 blink_me_offer" style="cursor: auto;margin-top: 10px;color: green;text-align: center;padding: 6px;">{{ t('Deal') }}</div>
													@else
													<div class="col-md-12 col-lg-12 col-sm-12 blink_me_offer" style="cursor: auto;margin-top: 10px;color: green;text-align: center;padding: 6px;">{{ t('Accepted') }}</div>
													<a href="#proceedDelivery" style="margin-top: 50px;" data-toggle="modal" class="btn btn-primary btn-block">
													{{ t('Send Delivery Info') }}</a>
													@endif
													@endif

												</div>	
												@else
												<div class="col-md-12 col-lg-12 col-sm-12">
													@if($makeanoffer->approve_seller == 2)
													@if(auth()->user()->id != $makeanoffer->offer_maker_id)

													@if($makeanoffer->counter_offer == '0')        												    			    
													<div class="col-md-12 col-lg-12 col-sm-12 not-deal-icon center"></div>
													<div class="col-md-12 col-lg-12 col-sm-12 blink_me_offer" style="cursor: auto;margin-top: 10px;color: red;text-align: center;padding: 6px;">{{ t('Not Deal') }}</div>
												    <div>
													<a href="{{ url('makeanoffers/counteroffer/'.$makeanoffer->id) }}" style="margin-top: 14px;position: relative;z-index: 1000;" class="btn btn-success col-md-12 col-lg-12 col-sm-12">{{ t('Counter-Offer') }}</a>	
													</div>
													@else
													<button  style="margin-top: 130px;position: relative;z-index: 1000;"  type="submit" class="btn btn-success col-md-12 col-lg-12 col-sm-12 Send-New-Offer-css">{{ t('Send New Offer') }}</button>	
													@endif
													<div>
													<a href="{{url('account/closeoffer/'.$makeanoffer->id)}}"  style="margin-top: 14px;position: relative;z-index: 1000;" class="btn btn-primary col-md-12 col-lg-12 col-sm-12">{{ t('Close Offer') }}</a>	
													</div>
													@else
													<div class="col-md-12 col-lg-12 col-sm-12 not-deal-icon center"></div>
													<div class="col-md-12 col-lg-12 col-sm-12 blink_me_offer" style="cursor: auto;margin-top: 10px;color: red;text-align: center;padding: 6px;">{{ t('Rejected') }}</div>
													<!--<button style="margin-top: 14px;" class="btn btn-default col-md-12 col-lg-12 col-sm-12" disabled>Awaiting Response</button>-->
													<!--<button  style="margin-top: 14px;"  type="submit" class="btn btn-success col-md-12 col-lg-12 col-sm-12">SEND NEW OFFER</button>	-->
													@endif
													@else
													<div style="padding: 0px;margin-top: 77px;">
														<img src="{{ url('images/deal.png') }}" style="height: 70px;margin-bottom: 20px;">
														<img src="{{ url('images/notdeal.png') }}" style="height: 70px;margin-bottom: 20px;">
														<br />
														<a href="{{lurl('account/makeanoffers/'.$makeanoffer->post_id.'/dealseller/'. $makeanoffer->id)}}" style="display: inline;z-index: 1000;position: relative;" class="btn btn-success acceptoffer">{{ t('Accept') }}</a>
														<a href="{{lurl('account/makeanoffers/'.$makeanoffer->post_id.'/notdealseller/'. $makeanoffer->id)}}" style="display: inline;z-index: 1000;position: relative;" class="btn btn-danger rejectoffer">{{ t('Reject') }}</a>
													</div>
													@endif
												</div>
												@endif
												@else
												<div class="col-md-12 col-lg-12 col-sm-12 offer-div" style="padding:0px;margin-top: 150px;">
													<button class="btn btn-default col-md-12 col-lg-12 col-sm-12" style="white-space: normal;" disabled>{{ t('Awaiting Response') }}</button>
												</div>
												@endif	
												@endif
												<div class="col-md-12 col-lg-12 col-sm-12" style="padding:0px;">
													<div class="offer-arrow"></div>
												</div>
											</div>
										</div>	
									</div>





									<!--   @if(auth()->user()->user_type_id == 2)-->
									<!--<div class="col-md-12" style="padding: 0px;">-->
										<!--   	<div class="row">-->
											<!--   		@if(auth()->user()->id != $makeanoffer->offer_maker_id || $makeanoffer->approve_seller == 1 || $makeanoffer->approve_seller == 2)-->
											<!--   			@if($makeanoffer->approve_seller == 1)-->
											<!-- 		                	<div class="col-md-12 col-lg-12 col-sm-12">-->
												<!--   						<div class="col-md-12 col-lg-12 col-sm-12 deal-icon center">-->
													<!--   						</div>	-->
													<!--   					</div>-->
													<!--   					<div  class="col-md-12 col-lg-12 col-sm-12">-->
														<!--   					    <div class="col-md-12 col-lg-12 col-sm-12" style="cursor: auto;margin-top: 10px;background-color: #2ecc71;border-color: #2ecc71;color: #fff;text-align: center;padding: 6px;">ACCEPTED!</div>-->

														<!--   						<a href="#proceedDelivery" style="margin-top: 50px;" data-toggle="modal" class="btn btn-danger btn-block">-->
															<!--						 {{ t('Send Delivery Info') }}</a>-->
															<!--   					</div>	-->

															<!--<div class="col-md-12 col-lg-12 col-sm-12" style="padding: 0px;">-->
																<!--  						{{-- @if($makeanoffer->offer_maker_id == auth()->user()->id)-->
																<!--<a href="#buyNow" data-toggle="modal" class="btn btn-danger btn-block">-->
																	<!--<i class="glyphicon glyphicon-shopping-cart"></i> {{ t('Buy Now') }}</a>	-->
																	<!--  						@else --}}-->
																	<!--<div class="col-md-12 col-lg-12 col-sm-12 deal-icon center"></div>-->
																	<!--<div class="col-md-12 col-lg-12 col-sm-12" style="cursor: auto;margin-top: 10px;background-color: #2ecc71;border-color: #2ecc71;color: #fff;text-align: center;padding: 6px;">DEAL!</div>-->
																	<!--<button style="margin-top: 14px;padding:8px;width:100%;" type="button" class="btn btn-primary">Send Delivery Info</button>	-->
																	<!--					{{-- @endif --}}-->
																	<!--</div>-->

																	<!--  				@else-->
																	<!--  				<div class="col-md-12 col-lg-12 col-sm-12" style="padding: 0px;text-align: center;">-->
																		<!--    			@if($makeanoffer->approve_seller == 2)-->
																		<!--    				<div class="col-md-12 col-lg-12 col-sm-12 not-deal-icon center"></div>-->
																		<!--    				<div class="col-md-12 col-lg-12 col-sm-12" style="cursor: auto;margin-top: 10px;background-color: #d9534f;border-color: #d9534f;color: #fff;text-align: center;padding: 6px;">REJECTED!</div>-->
																		<!--    				<button style="margin-top: 14px;" type="submit" class="btn btn-success col-md-12 col-lg-12 col-sm-12">SEND NEW OFFER</button>	-->
																		<!--    			@else-->
																		<!--    			<div style="padding: 0px;margin-top: 77px;">-->
																			<!--    			    <img src="{{ url('images/deal.png') }}" style="height: 70px;margin-bottom: 20px;">-->
																			<!--    			    <img src="{{ url('images/notdeal.png') }}" style="height: 70px;margin-bottom: 20px;">-->

																			<!--    			    <a href="{{lurl('account/makeanoffers/'.$makeanoffer->post_id.'/dealseller/'. $makeanoffer->id)}}" style="display: inline;" class="btn btn-success">Accept</a>-->
																			<!--    				<a href="{{lurl('account/makeanoffers/'.$makeanoffer->post_id.'/notdealseller/'. $makeanoffer->id)}}" style="display: inline;" class="btn btn-danger">Reject</a>-->
																			<!--   				</div>-->
																			<!--    			@endif-->
																			<!--</div>-->

																			<!--<div class="col-md-12 col-lg-12 col-sm-12">-->
																				<!--@if($makeanoffer->approve_seller == 2)-->
																				<!--<button style="margin-top: 14px;" type="submit" class="btn btn-success col-md-12 col-lg-12 col-sm-12">Send Offer</button>	-->
																				<!--@else-->
																				<!--<a href="{{lurl('account/makeanoffers/'.$makeanoffer->post_id.'/dealseller/'. $makeanoffer->id)}}" class="btn btn-success col-md-12 col-lg-12 col-sm-12">Accept</a>-->
																				<!--@endif-->
																				<!--   			</div>-->

																				<!--    		@endif-->
																				<!--   		@else-->
																				<!-- 					<div class="col-md-12 col-lg-12 col-sm-12" style="padding:0px;">-->
																					<!-- 						<button class="btn btn-default col-md-12 col-lg-12 col-sm-12" disabled>Awaiting Response</button>-->
																					<!-- 					</div>-->
																					<!--   		@endif	-->
																					<!--   	</div>	-->
																					<!--   </div>-->
																					<!--   @endif-->









																				</div>

























																				<div class="col-md-5 col-lg-5 col-sm-5 col-xs-12 nopadding">
																				<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
										<div class="col-md-6 col-lg-6 col-sm-6 col-xs-4 pull-right sellername-position">
											<h1 style="color: #3c80af !important;" class="sellername-header">{{$seller->username}}</h1>
										</div>
									</div>
									<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
										<div class="col-md-6 col-lg-6 col-sm-4 col-xs-4 seller-face pull-right">
										</div>
										<div class="col-md-6 col-lg-6 col-sm-6 col-xs-12 brefcase-grip brefcase-grip-bottom pull-right">
										</div>
									</div>
																					<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12  brefcase seller">
																						<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 nopadding">
																							<div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 nopadding picture-container-up">
																								<div class="col-md-12 col-lg-12 col-sm-12">
																									<a class="thumbnail" href="{{ lurl('/').'/'.slugify($post->title).'/'.$post->id }}" target="_blank"><img alt="" src="{{$postImg}}"></a>

																									<div class="col-md-12 col-lg-12 col-sm-12 product-name">
																										<p class="product-name-data"><span class="tooltipHere" data-placement="top" data-toggle="tooltip" data-original-title="{!! $post->title !!}">{!! $post->title !!}</span><br> 
																											<span>
																												@if ($post->price > 0)
																												<?php
																												$getcurrencycountry = \DB::table('countries')
																												->join('currencies', 'currencies.code', '=', 'countries.currency_code')
																												->select('currencies.*')
																												->where('countries.code', '=', $post->country_code)
																												->first();
																												?>

																												{!! \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator) !!}
																												<!--{!! \App\Helpers\Number::money($post->price) !!}-->
																												@else
																												<!--{!! \App\Helpers\Number::money(' --') !!}-->
																												{{t('Free')}}
																												@endif
																											</span>
																										</p>
																									</div>
																								</div>
																							</div>
                                                                                        	@if($makeanoffer->counter_offer == '1')     
                                                                                        	    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 nopadding picture-container-up-last" >
                                                                                        	@else
                                                                                        	    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 nopadding picture-container-up-last" style="<?=($makeanoffer->original_price == '0.0')?'display:none;':''?>">
                                                                                        	@endif
																							
																								<div class="col-md-12 col-lg-12 col-sm-12">
																									<div class="col-md-12 col-lg-12 col-sm-12 cash-heading">
																										{{-- <label>Cash</label> --}}
																									</div>
																								</div>
																								<div class="col-md-12 col-lg-12 col-sm-12" style="padding:0px;">
																									<div class="input-group offer-price offer-price-seller-left">
																										 
																											<input id="offer_price_seller" name="offer_price_seller" type="text" placeholder="Offer Price" maxlength="60" class="form-control offer-price" value="{{ $makeanoffer->original_price }}"  
                                                                                                            @if(($makeanoffer->counter_offer == '0') ||($makeanoffer->close_offer == 1)) 
                                                                                                            disabled
                                                                                                            @endif
                                                                                                           
                                                                                                            
                                                                                                            
                                                                                                               >
																										 
																											<span class="input-group-addon">
																												{!! substr(\App\Helpers\Number::money(''), 0 , -1) !!}
																											</span>
																										</div>
																									</div>
																								</div>

																							</div>

																							<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 nopadding">
																								<div id="container3" class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-down">
																									@if(!empty($sellerProduct1))
																									<input class="seller-product-selected" type="hidden" name="seller_product_1" value="{{ $sellerProduct1->id }}" />
																									<div>
																										@if(auth()->user()->id != $makeanoffer->offer_maker_id || $makeanoffer->approve_seller == 1 || $makeanoffer->approve_seller == 2)
																										@if($makeanoffer->approve_seller == 2)
																										@if(auth()->user()->id != $makeanoffer->offer_maker_id) 
																										    @if($makeanoffer->counter_offer != 0)
																									            <button style="color: white; background-color: #ed1c24; border-radius: 50px; padding-left: 5px; padding-right: 5px;" type="button" class="close remove-product removeprouctcontainner3" >×</button>
																											@endif	
																										@endif										        			
																										@endif										        			
																										@endif										        			
																										<div class="col-md-12 col-lg-12 col-sm-12 drag">
																											<?php

																											if (!empty($sellerProduct1->filename)) {
																												$postImgsellerProduct1 = resize($sellerProduct1->filename, 'medium');
																											} else {
																												$postImgsellerProduct1 = resize(config('larapen.core.picture.default'));
																											}
																											?>
																											<a class="thumbnail" href="{{ lurl('/').'/'.slugify($sellerProduct1->title).'/'.$sellerProduct1->id }}" target="_blank"><img alt="" src="{{$postImgsellerProduct1}}"></a>
																											<div class="col-md-12 col-lg-12 col-sm-12 product-name">
																												<p class="product-name-data"><span class="tooltipHere" data-placement="top" data-toggle="tooltip" data-original-title="{!! $sellerProduct1->title !!}">{!! $sellerProduct1->title !!}</span><br> <span>
																													@if ($sellerProduct1->price > 0)
																													{!! \App\Helpers\Number::money_price_latest($sellerProduct1->price,$sellerProduct1->html_entity,$sellerProduct1->in_left,$sellerProduct1->decimal_places,$sellerProduct1->decimal_separator) !!}
																													<!--{!! \App\Helpers\Number::money($sellerProduct1->price) !!}-->
																													@else
																													<!--{!! \App\Helpers\Number::money(' --') !!}-->
																													{{t('Free')}}
																													@endif
																												</span>
																											</p> 
																										</div>
																									</div>
																								</div>
																								@else
																								<input class="seller-product-selected" type="hidden" name="seller_product_1" value="" />    
																								@endif
																							</div>
																							<div id="container4" class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-down-last">
																								@if(!empty($sellerProduct2))
																								<input class="seller-product-selected" type="hidden" name="seller_product_2" value="{{ $sellerProduct2 ->id}}" />
																								<div>
																									@if(auth()->user()->id != $makeanoffer->offer_maker_id || $makeanoffer->approve_seller == 1 || $makeanoffer->approve_seller == 2)
																									@if($makeanoffer->approve_seller == 2)
																									@if(auth()->user()->id != $makeanoffer->offer_maker_id) 
																									    @if($makeanoffer->counter_offer != 0)
																									<button style="color: white; background-color: #ed1c24; border-radius: 50px; padding-left: 5px; padding-right: 5px;" type="button" class="close remove-product removeprouctcontainner4" >×</button>
																									    @endif								
																									@endif								
																									@endif								
																									@endif								
																									<div class="col-md-12 col-lg-12 col-sm-12 drag">
																										<?php

																										if (!empty($sellerProduct2->filename)) {
																											$postImgsellerProduct2 = resize($sellerProduct2->filename, 'medium');
																										} else {
																											$postImgsellerProduct2 = resize(config('larapen.core.picture.default'));
																										}
																										?>
																										<a class="thumbnail" href="{{ lurl('/').'/'.slugify($sellerProduct2->title).'/'.$sellerProduct2->id }}" target="_blank"><img alt="" src="{{$postImgsellerProduct2}}"></a>
																										<div class="col-md-12 col-lg-12 col-sm-12 product-name">
																											<p class="product-name-data"><span class="tooltipHere" data-placement="top" data-toggle="tooltip" data-original-title="{!! $sellerProduct2->title !!}">{!! $sellerProduct2->title !!}</span><br> <span>
																												@if ($sellerProduct2->price > 0)
																												{!! \App\Helpers\Number::money_price_latest($sellerProduct2->price,$sellerProduct2->html_entity,$sellerProduct2->in_left,$sellerProduct2->decimal_places,$sellerProduct2->decimal_separator) !!}
																												<!--{!! \App\Helpers\Number::money($sellerProduct2->price) !!}-->
																												@else
																												<!--{!! \App\Helpers\Number::money(' --') !!}-->
																												{{t('Free')}}
																												@endif
																											</span>
																										</p> 
																									</div>
																								</div>
																							</div>
																							@else
																							<input class="seller-product-selected" type="hidden" name="seller_product_2" value="" />    
																							@endif
																						</div>	
																					</div>	
																				</div>
																				{{-- <div class="container">
																					<div class='row'> --}}

																						@if(auth()->user()->id != $makeanoffer->offer_maker_id || $makeanoffer->approve_seller == 1 || $makeanoffer->approve_seller == 2)
																						@if($makeanoffer->approve_seller == 2)
																						@if(auth()->user()->id != $makeanoffer->offer_maker_id)
																						<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 product-carousel">
																							<div class="carousel slide media-carousel" id="media">
																								<div class="carousel-inner seller-carousel">
																									<?php  $j = 0 ;?>
																									@foreach($sellerPosts->unique('id') as $sellerPost)
																									<?php

																									if (!empty($picture->filename)) {
																										$postproductImg = resize($sellerPost->filename, 'medium');
																									} else {
																										$postproductImg = resize(config('larapen.core.picture.default'));
																									}


																									$displaynoneseller = '';
																									if(!empty($sellerProduct1->id) && $sellerProduct1->id == $sellerPost->id)
																									{
																										$displaynoneseller = 'display:none';
																									}
																									else if(!empty($sellerProduct2->id) && $sellerProduct2->id == $sellerPost->id)
																									{
																										$displaynoneseller = 'display:none';
																									}



																									?>

																									<div id="drag<?=$j ?>" class="col-md-4 col-lg-4 col-sm-4 col-xs-4 drag seller-drag-product buyerproducthide{{$sellerPost->id}}" style="{{ $displaynoneseller }}">
																										<a class="thumbnail" target="_blank" href="{{ lurl('/').'/'.slugify($sellerPost->title).'/'.$sellerPost->id }}"><img alt="" src="{{ $postproductImg }}"></a>
																										<input class="seller-product" type="hidden" name="seller_product" value="{{ $sellerPost->id }}" />
																										<div class="col-md-12 col-lg-12 col-sm-12 product-name">

																											<p class="product-name-data">
																												<span>
																													{!! $sellerPost->title !!}
																												</span>
																												<br>
																												<span>
																													@if ($sellerPost->price > 0)
																													{!! \App\Helpers\Number::money_price_latest($sellerPost->price,$sellerPost->html_entity,$sellerPost->in_left,$sellerPost->decimal_places,$sellerPost->decimal_separator) !!}
																													<!--{!! \App\Helpers\Number::money($sellerPost->price) !!}-->
																													@else
																													<!--{!! \App\Helpers\Number::money(' --') !!}-->
																													{{t('Free')}}
																													@endif
																												</span>
																											</p> 
																										</div>	
																									</div>

																									<?php  $j++;?>
																									@endforeach

																								</div>
																								@if($sellerPosts->unique('id')->count() > 3)
																								<a data-slide="prev" href="#media" class="left carousel-control">‹</a>
																								<a data-slide="next" href="#media" class="right carousel-control">›</a>
																								@endif
																							</div>                          
																						</div>
																						<div class="col-md-12 product-model-button hide">
																							<div class="row">
																								<div class="col-md-12 col-lg-12 col-sm-12">
																									<button class="btn btn-primary col-md-12 col-lg-12 col-sm-12">Add Products</button>
																								</div>	
																							</div>	
																						</div>
																						@endif
																						@endif
																						@endif

																					{{-- </div>
																					</div> --}}
																				</div>
																			</div>
																		</div>
																		{{-- <div class="row desktop-button">
																			<div class="col-md-12 col-lg-12 col-sm-12">
																				<div class="col-md-4 col-lg-4 col-sm-4">
																				</div>
																				<div class="col-md-4 col-lg-4 col-sm-4 button-send-offer-div">
																					<button class="btn btn-success col-md-10 col-lg-10 col-sm-10 button-send-offer ">Send Offer</button>
																				</div>
																				<div class="col-md-4 col-lg-4 col-sm-4">
																				</div>
																			</div>
																		</div> --}}

																		{{-- <div class="col-md-12 button-send-offer-div-mobile hide">
																			<div class="row">
																				<div class="col-md-12 col-lg-12 col-sm-12">
																					<button class="btn btn-success col-md-12 col-lg-12 col-sm-12">Send Offer</button>
																				</div>	
																			</div>	
																		</div> --}}
																	</form>	


																	<div style="clear:both;"></div>
																	<div class="col-md-12" style="text-align: center;margin: 20px 0px 20px 0px;font-size: 17px;">{{ t('To build a deal change the cash value and drag the items you would like to exchange inside the briefcase') }}</div>
																	<div style="clear:both;"></div>

																</div>
															</div>
														</div>
													</div>
												</div>	
												@endsection

												@section('after_styles')
												@include('layouts.inc.tools.wysiwyg.css')
												@endsection
												@section('modal_message')
												@if (auth()->check())
												@include('post.inc.proceeddelivery') 
												@endif
												@endsection
												{{-- @section('add_more')
												@if (auth()->check() or config('settings.single.guests_can_contact_seller')=='1')
												@include('account.inc.add-more', array('all_post' => $all_post, 'makeanoffers' => $makeanoffers ))
												@endif
												@endsection --}}

												{{-- @section('update_offer')
												@if (auth()->check() or config('settings.single.guests_can_contact_seller')=='1')
												@include('account.inc.update-offer', array('all_post' => $all_post, 'makeanoffers' => $makeanoffers ))
												@endif
												@endsection --}}
