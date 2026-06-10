{{-- * LaraClassified - Geo Classified Ads CMS * Copyright (c) BedigitCom. All Rights Reserved * * Website: http://www.bedigit.com
* * LICENSE * ------- * This software is furnished under a license and may be used and copied * only in accordance with the
terms of such license and with the inclusion * of the above copyright notice. If you Purchased from Codecanyon, * Please
read the full License from here - http://codecanyon.net/licenses/standard --}}
<?php
if (auth()->check()) {
    $addmore = '#addmore';
} else {
    $contactMakeAnOffer = '#quickLogin';
}

if (auth()->check()) {
    $updateoffer = '#updateoffer';
} else {
    $contactMakeAnOffer = '#quickLogin';
}
?>
@extends('layouts.master') @section('content')
<!-- <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
<script src="http://code.jquery.com/ui/1.8.21/jquery-ui.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script> -->

<div class="main-container" style="margin-top: 85px;">
	<div class="container">
		<div class="row">
			<div class="col-md-12 page-content">
				<div class="inner-box category-content">
					<h2 style="text-align: center;" class="title-2">{{ t('To build a deal change the cash value and drag the items you would like to exchange inside the briefcase') }}</h2>

					<form class="form-horizontal" id="postForm" method="POST" action="{{ url((config('app.locale')!='en'?config('app.locale'):'en').'/account/makeanoffers/store')}}"
					 enctype="multipart/form-data">
						<input type="hidden" name="post-id" value="{{ $post->id }}" />

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
											<h1 class="buyername-header" >{{ $buyer->username }}</h1>
										</div>
									</div>
									<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
										<div class="col-md-6 col-lg-6 col-sm-4 col-xs-4 pull-left"  align="center">>
                                        <i class="icon-user fa" style="font-size: 50px;"></i>
										</div>
										<div class="col-md-6 col-lg-6 col-sm-6 col-xs-12 brefcase-grip brefcase-grip-top pull-left">
										</div>
									</div>
									<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 brefcase buyer">
										<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 nopadding">
											<div id="container5" class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-up nopadding">
												<input class="buyer-product-selected" type="hidden" name="buyer_product_3" value="" />
												<div class="col-md-12 col-lg-12 col-sm-12 hide">
													<a class="thumbnail" href="#">
														<img alt="" src="{{$postImg}}">
													</a>
													<div class="col-md-12 col-lg-12 col-sm-12 product-name">
														<p class="product-name-data">
															<span class="tooltipHere" data-placement="top" data-toggle="tooltip" data-original-title="{!! $post->title !!}">{!! $post->title !!}</span>
															<br>
															<span>
																@if ($post->price > 0) {!! \App\Helpers\Number::money($post->price) !!} @else
																<!--{!! \App\Helpers\Number::money(' --') !!}-->
																{{t('Free')}} @endif
															</span>
														</p>
													</div>
												</div>
											</div>
											<div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-up-last nopadding">
												<div class="col-md-12 col-lg-12 col-sm-12">
													<div class="col-md-12 col-lg-12 col-sm-12 cash-heading">
														{{--
														<label>Cash</label> --}}
													</div>
												</div>
												<div class="col-md-12 col-lg-12 col-sm-12" style="padding:0px;">
													<div class="input-group offer-price">
														{{-- @if(auth()->user()->user_type_id == 2)
														<input id="offer_price_buyer" name="offer_price_buyer" type="text" placeholder="Offer Price" maxlength="60" class="form-control offer-price"
														 value="{{ $post->price }}" disabled> @else --}}
														<input id="offer_price_buyer" name="offer_price_buyer" type="text" placeholder="Offer Price" maxlength="60" class="form-control offer-price"
														 value="{{ $post->price }}"> {{-- @endif --}}
														<span class="input-group-addon">
															{!! substr(\App\Helpers\Number::money(''), 0 , -1) !!}
														</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 nopadding">
											<div id="container1" class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-down nopadding">
												<input class="buyer-product-selected" type="hidden" name="buyer_product_1" value="" />
											</div>
											<div id="container2" class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-down-last nopadding">
												<input class="buyer-product-selected" type="hidden" name="buyer_product_2" value="" />
											</div>
										</div>
									</div>

									<!-- <div class="container">
										<div class='row'> -->
									<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 product-carousel">
										<div class="carousel slide media-carousel" id="media1">
											<div class="carousel-inner buyer-carousel">
												@php $j = 0; @endphp @foreach($buyerPosts->unique('id') as $buyerPost) @php if (!empty($buyerPost->filename)) { $postproductImg
												= resize($buyerPost->filename, 'medium'); } else { $postproductImg = resize(config('larapen.core.picture.default'));
												} @endphp

												<div id="drag{{$j}}" class="col-md-4 col-lg-4 col-sm-4 col-xs-4 drag buyer-drag-product buyerproducthide{{$buyerPost->id}}">
													<a class="thumbnail" href="{{ lurl('/').'/'.slugify($buyerPost->title).'/'.$buyerPost->id }}" target="_blank">
														<img alt="" src="{{ $postproductImg }}">
													</a>
													<input class="buyer-product" type="hidden" name="buyer_product" value="{{ $buyerPost->id }}" />
													<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 product-name">
														<p class="product-name-data">
															<span>
																{!! $buyerPost->title !!}
															</span>
															<br>
															<span>
																@if ($buyerPost->price > 0) {!! \App\Helpers\Number::money_price_latest($buyerPost->price,$buyerPost->html_entity,$buyerPost->in_left,$buyerPost->decimal_places,$buyerPost->decimal_separator)
																!!}
																<!--{!! \App\Helpers\Number::money($buyerPost->price) !!}-->
																@else
																<!--{!! \App\Helpers\Number::money(' --') !!}-->
																{{t('Free')}} @endif
															</span>
														</p>
													</div>
												</div>

												<?php $j++;?>
												@endforeach
											</div>
											@if($buyerPosts->unique('id')->count() > 3)
											<a data-slide="prev" href="#media1" class="left carousel-control">‹</a>
											<a data-slide="next" href="#media1" class="right carousel-control">›</a>
											@endif
										</div>
									</div>
									<div class="col-md-12 col-sm-12 col-xs-12 product-model-button hide">
										<div class="row">
											<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
												<button class="btn btn-primary col-md-12 col-lg-12 col-sm-12 col-xs-12">{{ t('Add Products') }}</button>
											</div>
										</div>
									</div>
									<!-- </div>
									</div> -->
									{{--
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-3 col-lg-3 col-sm-3">
											</div>
											<div class="col-md-6 col-lg-6 col-sm-6">
												<div class="form-group">
													<button type="submit" class="btn btn-success col-md-12 col-lg-12 col-sm-12">{{ t('Send Offer') }}</button>
												</div>
											</div>
											<div class="col-md-3 col-lg-3 col-sm-3">
											</div>
										</div>
									</div> --}}
								</div>
								<div class="col-md-2 col-lg-2 col-sm-2 col-xs-12">
									<div class="col-md-12 col-lg-12 col-sm-12">
										<div class="form-group">
											<button type="submit" style="margin-top: 150px;z-index: 1000;" class="btn btn-success col-md-12 col-lg-12 col-sm-12 send-offer-css">{{ t('Send Offer') }}</button>
											<div class="col-md-12 col-lg-12 col-sm-12 offer-arrow"></div>

										</div>
									</div>
								</div>
								<div class="col-md-5 col-lg-5 col-sm-5 col-xs-12 nopadding">
									<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
										<div class="col-md-6 col-lg-6 col-sm-6 col-xs-4 pull-right  sellername-position ">
											<h1 class="sellername-header" >{{$seller->username}}</h1>
										</div>
									</div>
									<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
										<div class="col-md-6 col-lg-6 col-sm-4 col-xs-4 pull-right" align="center">
                                         <i class="icon-user fa" style="font-size: 50px;"></i>
										</div>
										<div class="col-md-6 col-lg-6 col-sm-6 col-xs-12 brefcase-grip brefcase-grip-bottom pull-right">
										</div>
									</div>
									<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 brefcase seller">
										<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 nopadding">
											<div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 nopadding picture-container-up">
												<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 nopadding">
													<a class="thumbnail" href="{{ lurl('/').'/'.slugify($post->title).'/'.$post->id }}" target="_blank">
														<img alt="" src="{{$postImg}}">
													</a>
													<div class="col-md-12 col-lg-12 col-sm-12 product-name">
														<p class="product-name-data">
															<span class="tooltipHere" data-placement="top" data-toggle="tooltip" data-original-title="{!! $post->title !!}">{!! $post->title !!}</span>
															<br>
															<span>
																@if ($post->price > 0) @php $getcurrencycountry = \DB::table('countries') ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
																->select('currencies.*') ->where('countries.code', '=', $post->country_code) ->first(); @endphp {!! \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator)
																!!}

																<!--{!! \App\Helpers\Number::money($post->price) !!}-->
																@else
																<!--{!! \App\Helpers\Number::money(' --') !!}-->
																{{t('Free')}} @endif
															</span>
														</p>
													</div>
												</div>
											</div>
											<div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 nopadding picture-container-up-last">
												<div class="col-md-12 col-lg-12 col-sm-12">
													<div class="col-md-12 col-lg-12 col-sm-12 cash-heading">
														{{--
														<label>Cash</label> --}}
													</div>
												</div>
												<div class="col-md-12 col-lg-12 col-sm-12" style="padding: 0px;">
													<div class="input-group offer-price offer-price-seller-left">
														{{-- @if(auth()->user()->user_type_id == 3)
														<input id="offer_price_seller" name="offer_price_seller" type="text" placeholder="Offer Price" maxlength="60" class="form-control offer-price"
														 value="{{ (empty($makeanoffer)) ? '0.0' : $post->price }}" disabled> @else --}}
														<input id="offer_price_seller" name="offer_price_seller" type="text" placeholder="Offer Price" maxlength="60" class="form-control offer-price"
														 value="{{ (empty($makeanoffer)) ? '0.0' : $post->price }}"> {{-- @endif --}}
														<span class="input-group-addon">
															{!! substr(\App\Helpers\Number::money(''), 0 , -1) !!}
														</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 nopadding">
											<div id="container3" class="col-md-6 col-lg-6 col-sm-6 col-xs-6  picture-container-down">
												<input class="seller-product-selected" type="hidden" name="seller_product_1" value="" />
											</div>
											<div id="container4" class="col-md-6 col-lg-6 col-sm-6 col-xs-6 picture-container-down-last">
												<input class="seller-product-selected" type="hidden" name="seller_product_2" value="" />
											</div>
										</div>
									</div>
									{{--
									<div class="container">
										<div class='row'> --}}
											<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 product-carousel">
												<div class="carousel slide media-carousel" id="media">
													<div class="carousel-inner seller-carousel">
														@php $j = 0; @endphp @foreach($sellerPosts->unique('id') as $sellerPost) @php if (!empty($sellerPost->filename)) { $postproductImg
														= resize($sellerPost->filename, 'medium'); } else { $postproductImg = resize(config('larapen.core.picture.default'));
														} @endphp

														<div id="drag{{$j}}" class="col-md-4 col-lg-4 col-sm-4 col-xs-4 drag seller-drag-product buyerproducthide{{$sellerPost->id}}">
															<a class="thumbnail" href="{{ lurl('/').'/'.slugify($sellerPost->title).'/'.$sellerPost->id }}" target="_blank">
																<img alt="" src="{{ $postproductImg }}">
															</a>
															<input class="seller-product" type="hidden" name="seller_product" value="{{ $sellerPost->id }}" />
															<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 product-name">
																<p class="product-name-data">
																	<span>
																		{!! $sellerPost->title !!}
																	</span>
																	<br>
																	<span>
																		@if ($sellerPost->price > 0) {!! \App\Helpers\Number::money_price_latest($sellerPost->price,$sellerPost->html_entity,$sellerPost->in_left,$sellerPost->decimal_places,$sellerPost->decimal_separator)
																		!!}
																		<!--{!! \App\Helpers\Number::money($sellerPost->price) !!}-->
																		@else
																		<!--{!! \App\Helpers\Number::money(' --') !!}-->
																		{{t('Free')}} @endif
																	</span>
																</p>
															</div>
														</div>

														<?php $j++;?>
														@endforeach

													</div>
													@if($sellerPosts->unique('id')->count() > 3)
													<a data-slide="prev" href="#media" class="left carousel-control">‹</a>
													<a data-slide="next" href="#media" class="right carousel-control">›</a>
													@endif
												</div>
											</div>
											<div class="col-md-12 col-sm-12 col-xs-12 product-model-button hide">
												<div class="row">
													<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
														<button class="btn btn-primary col-md-12 col-lg-12 col-sm-12 col-xs-12">{{ t('Add Products') }}</button>
													</div>
												</div>
											</div>
											{{-- </div>
									</div> --}}
									<div class="col-md-12 hide">
										<div class="row">
											<div class="col-md-6 col-lg-6 col-sm-6">
												<button class="btn btn-success col-md-12 col-lg-12 col-sm-12">Accept</button>
											</div>
											<div class="col-md-6 col-lg-6 col-sm-6">
												<button class="btn btn-danger col-md-12 col-lg-12 col-sm-12">Reject</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						{{--
						<div class="row desktop-button">
							<div class="col-md-12 col-lg-12 col-sm-12">
								<div class="col-md-4 col-lg-4 col-sm-4">
								</div>
								<div class="col-md-4 col-lg-4 col-sm-4 button-send-offer-div">
									<button class="btn btn-success col-md-10 col-lg-10 col-sm-10 button-send-offer ">{{ t('Send Offer') }}</button>
								</div>
								<div class="col-md-4 col-lg-4 col-sm-4">
								</div>
							</div>
						</div> --}} {{--
						<div class="col-md-12 button-send-offer-div-mobile hide">
							<div class="row">
								<div class="col-md-12 col-lg-12 col-sm-12">
									<button class="btn btn-success col-md-12 col-lg-12 col-sm-12">{{ t('Send Offer') }}</button>
								</div>
							</div>
						</div> --}}
					</form>

					<div style="clear:both;"></div>
					<br /><br />
					<!--<div class="col-md-12" style="text-align: center;margin: 20px 0px 20px 0px;font-size: 17px;">{{ t('To build a deal change the cash value and drag the items you would like to exchange inside the briefcase') }}</div>-->
					<div style="clear:both;"></div>
				</div>

			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.css" /> @endsection @section('after_styles') @include('layouts.inc.tools.wysiwyg.css') @endsection {{-- @section('add_more') @if
(auth()->check() or config('settings.single.guests_can_contact_seller')=='1') @include('account.inc.add-more', array('all_post'
=> $all_post, 'makeanoffers' => $makeanoffers )) @endif @endsection --}} {{-- @section('update_offer') @if (auth()->check()
or config('settings.single.guests_can_contact_seller')=='1') @include('account.inc.update-offer', array('all_post' => $all_post,
'makeanoffers' => $makeanoffers )) @endif @endsection --}}