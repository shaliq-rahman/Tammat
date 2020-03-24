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
 * Please read the full License from here - http://codecanyon.net/licenses/standardc
--}}
@extends('layouts.master')

@section('wizard')
	@include('post.inc.wizard2')
@endsection
  
@section('content')
	@include('common.spacer')
<?php
if(isset($categoryid)){
$categoryname = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $categoryid)
                        ->first(); 
} else {
    $categorynamedee = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $catid)
                        ->first(); 
            if($categorynamedee->parent_id!=0){
                 $categoryname2 = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $categorynamedee->parent_id)
                        ->first(); 
                     if($categoryname2->parent_id!=0){
                         $categoryname2 = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $categoryname2->parent_id)
                        ->first(); 
                     } else {
                         $categoryname=$categoryname2;
                     }   
            } else {
                $categoryname=$categorynamedee;
            }
        
}

$cattrid = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $catid)
                        ->first(); 
 //print_r($cattrid);exit();
?>
    
	<div class="main-container">

		<div class="container">
			<div class="row">
				
				@include('post.inc.notification')

				<div class="col-md-9 page-content">
					<div class="inner-box category-content">
						<h2 class="title-2"><strong> <i class="icon-docs"></i> 
					{{ t('Ad\'s Details') }}
						</strong></h2>
						<div class="row">
							<div class="col-sm-12">

                                {{-- @if(config('app.locale')!='en') --}}
                              
								<form class="form-horizontal" id="postForm" method="POST" action="{{url('')}}/{{config('app.locale')}}/posts/create" enctype="multipart/form-data">
								{{-- @else
							
								<form class="form-horizontal" id="postForm" method="POST" action="{{url('')}}/posts/create" enctype="multipart/form-data">
								@endif --}}

									{!! csrf_field() !!}
									<fieldset>
									    
									 	<input type="hidden" id="pidd" name="parent_id"  value="{{$p_idd}}">
									 	<!--<input type="hidden" id="catidd" name="category_id"  value="{{$catid}}">-->
                                        <input type="hidden" id="subbb" name="subcategory_id"  value="{{$cattrid->parent_id}}">
                                        <input type="hidden" id="catidd" name="category_id"  value="{{$cattrid->translation_of}}">

										<!-- post_type_id -->
										<div style="display: none;">
										<div id="postTypeBloc" class="form-group required <?php echo (isset($errors) and $errors->has('post_type_id')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label">{{ t('Seller Type') }} <sup>*</sup></label>
											<div class="col-md-8">
												@foreach ($postTypes as $postType)
													<label class="radio-inline" for="postTypeId-{{ $postType->tid }}">
														<input name="post_type_id" id="postTypeId-{{ $postType->tid }}" value="{{ $postType->tid }}"
															   type="radio" {{ (old('post_type_id')==$postType->tid) ? 'checked="checked"' : '' }} checked="checked">
														{{ $postType->name }}
													</label> 
												@endforeach
											</div>
										</div>
										
										</div>

										<!-- title -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('title')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="title">{{ t('Ad\'s title') }} <sup>*</sup></label>
											<div class="col-md-8">
												<input id="title" name="title" placeholder="{{ t('Ad\'s title') }}" class="form-control input-md"
													   type="text" value="{{ old('title') }}">
												<span class="help-block">{{ t('A great title needs at least 60 characters.') }} </span>
											</div>
										</div>

									
										
										<!-- customFields -->
										<div id="customFields"></div>

										<!-- price -->
										<div id="priceBloc" class="form-group <?php echo (isset($errors) and $errors->has('price')) ? 'has-error' : ''; ?>">
										    
										    <div class="col-md-3 control-label">
    											<label class="control-label" for="price">
    											    {{ t('Price') }}
    											</label><sup> *</sup>
											</div>
											
											<div class="col-md-8">
												<div class="input-group">
												   
													<span class="input-group-addon">{!! config('currency')['symbol'] !!}</span>
											
												@if($categoryname->name=='Free')
												<input style="z-index: 0;"  id="price" value="0" name="price" class="form-control" placeholder="{{ t('e.i. 15000') }}" type="text" value="{{ old('price') }}" disabled>
												@else
													<input style="z-index: 0;"  id="price" value="0" name="price" class="form-control" placeholder="{{ t('e.i. 15000') }}" type="text" value="{{ old('price') }}">
												@endif
												
													<!--<label class="input-group-addon">
														<input id="negotiable" name="negotiable" type="checkbox"
															   value="1" {{ (old('negotiable')=='1') ? 'checked="checked"' : '' }}>
														{{ t('Negotiable') }}
													</label>-->
												</div>
											</div>
										</div>
										
										<!-- country_code -->
										@if (empty(config('country.code')))
											<div class="form-group required <?php echo (isset($errors) and $errors->has('country_code')) ? 'has-error' : ''; ?>">
												<label class="col-md-3 control-label" for="country_code">{{ t('Your Country') }} <sup>*</sup></label>
												<div class="col-md-8">
													<select id="countryCode" name="country_code" class="form-control sselecter">
														<option value="0" {{ (!old('country_code') or old('country_code')==0) ? 'selected="selected"' : '' }}> {{ t('Select a country') }} </option>
														@foreach ($countries as $item)
															<option value="{{ $item->get('code') }}" {{ (old('country_code', (!empty(config('ipCountry.code'))) ? config('ipCountry.code') : 0)==$item->get('code')) ? 'selected="selected"' : '' }}>{{ $item->get('name') }}</option>
														@endforeach
													</select>
												</div>
											</div>
										@else
											<input id="countryCode" name="country_code" type="hidden" value="{{ config('country.code') }}">
										@endif
										
										<?php
										/*
										@if (\Illuminate\Support\Facades\Schema::hasColumn('posts', 'address'))
										<!-- address -->
										<div class="form-group required <?php echo ($errors->has('address')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="title">{{ t('Address') }} </label>
											<div class="col-md-8">
												<input id="address" name="address" placeholder="{{ t('Address') }}" class="form-control input-md"
													   type="text" value="{{ old('address') }}">
												<span class="help-block">{{ t('Fill an address to display on Google Maps.') }} </span>
											</div>
										</div>
										@endif
										*/
										?>
										
										@if (config('country.admin_field_active') == 1 and in_array(config('country.admin_type'), ['1', '2']))
										<!-- admin_code -->
											<div id="locationBox" class="form-group required <?php echo (isset($errors) and $errors->has('admin_code')) ? 'has-error' : ''; ?>">
												<label class="col-md-3 control-label" for="admin_code">{{ t('Location') }} <sup>*</sup></label>
												<div class="col-md-8">
													<select id="adminCode" name="admin_code" class="form-control sselecter">
														<option value="0" {{ (!old('admin_code') or old('admin_code')==0) ? 'selected="selected"' : '' }}>
															{{ t('Select your Location') }}
														</option>
													</select>
												</div>
											</div>
										@endif
									
										<!-- city_id -->
										<!--<div id="cityBox" class="form-group required <?php echo (isset($errors) and $errors->has('city_id')) ? 'has-error' : ''; ?>">-->
										<!--	<label class="col-md-3 control-label" for="city_id">{{ t('City') }} <sup>*</sup></label>-->
										<!--	<div class="col-md-8">-->
										<!--		<select id="cityId" name="city_id" class="form-control sselecter">-->
										<!--			<option value="0" {{ (!old('city_id') or old('city_id')==0) ? 'selected="selected"' : '' }}>-->
										<!--				{{ t('Select a city') }}-->
										<!--			</option>-->
										<!--		</select>-->
										<!--	</div>-->
										<!--</div>-->
										
										<!-- city_name -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('city_name')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="city_id">{{ t('City') }} <sup>*</sup></label>
											<div class="col-md-6">
												<input   id="city_name"  name="city_name" class="form-control" placeholder="{{ t('City Name') }}" type="text" value="{{ old('city_name') }}">
											</div>
											<div class="col-md-2">
                                                <img src="https://www.dealnotdeal.com/storage/app/logo/Map_icon.png" style="width: 20px;margin-top: 10px;" id="element" class="show-modal"/>
                                            </div>
										</div>
										
                                        <!-- model start-->
                                        <div id="testmodal" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        <h4 class="modal-title" style="margin-top:10px;">Drag City</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=AIzaSyAG_X_AbkCsqu1YpnGZLzlhC-PiLwcAkL4&libraries=places"></script>
                                                <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>-->
                                        <script type="text/javascript">
                                            function initialize() {
                                              var map;
                                              var position = new google.maps.LatLng(50.45, 4.45);    // set your own default location.
                                              var geocoder = new google.maps.Geocoder();
                                                    var infowindow = new google.maps.InfoWindow();
                                              var myOptions = {
                                                zoom: 15,
                                                center: position
                                              };
                                              var map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
                                        
                                              // We send a request to search for the location of the user.  
                                              // If that location is found, we will zoom/pan to this place, and set a marker
                                              navigator.geolocation.getCurrentPosition(locationFound, locationNotFound);
                                        
                                              function locationFound(position) {
                                                // we will zoom/pan to this place, and set a marker
                                                var location = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                                                // var accuracy = position.coords.accuracy;
                                             //  console.log(map.address);
                                        //alert(location);
                                                map.setCenter(location);
                                                var marker = new google.maps.Marker({
                                                    position: location, 
                                                    map: map, 
                                                    draggable: true,
                                                    title: "You are here! Drag the marker to the exact location."
                                                });
                                        geocoder.geocode({'latLng': location }, function(results, status) {
                                                            if (status == google.maps.GeocoderStatus.OK) {
                                                                if (results[0]) {
                                                                 // console.log(results[0])
                                                                    $('#city_name').val(results[0].formatted_address);
                                                                    // $('#latitude').val(marker.getPosition().lat());
                                                                    // $('#longitude').val(marker.getPosition().lng());
                                                                    infowindow.setContent(results[0].formatted_address);
                                                                    infowindow.open(map, marker);
                                                                }
                                                            }
                                                        });
                                             
                                          //       var infowindow = new google.maps.InfoWindow({
                                          //   content: 'Latitude: ' + location.lat() +
                                          //   '<br>Longitude: ' + location.lng()
                                          // });
                                          // infowindow.open(map,marker);
                                        
                                                // set the value an value of the <input>
                                                updateInput(location.lat(), location.lng());
                                        
                                                // Add a "drag end" event handler
                                                google.maps.event.addListener(marker, 'dragend', function() {
                                                  //var l = document.getElementById('my_location').value;
                                                  //alert(marker.getPosition());
                                                  geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
                                                    //alert(google.maps.GeocoderStatus.OK);
                                                            if (status == google.maps.GeocoderStatus.OK) {
                                                                if (results[0]) {
                                                                 // console.log(results[0]);
                                                                     $('#city_name').val(results[0].formatted_address);
                                                                    // $('#latitude').val(marker.getPosition().lat());
                                                                    // $('#longitude').val(marker.getPosition().lng());
                                                                    infowindow.setContent(results[0].formatted_address);
                                                                    infowindow.open(map, marker);
                                                                }
                                                            }
                                                        });
                                                  updateInput(this.position.lat(), this.position.lng());
                                                });
                                        
                                              }
                                        
                                              function locationNotFound() {
                                                // location not found, you might want to do something here
                                              }
                                        
                                            }
                                            function updateInput(lat, lng) {
                                              document.getElementById("my_location").value = lat + ',' + lng;
                                            }
                                            google.maps.event.addDomListener(window, 'load', initialize);
                                        
                                        </script>
                                        <style>
                                        #map-canvas {
                                          width: 100%;
                                          height: 400px;
                                        }
                                        </style>
                                        <div id="map-canvas"></div>
                                        <input id="my_location" readonly="readonly" type="hidden">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
                                        <script>
                                            $(document).ready(function(){
                                          var show_btn=$('.show-modal');
                                          var show_btn=$('.show-modal');
                                          //$("#testmodal").modal('show');
                                          
                                            show_btn.click(function(){
                                              $("#testmodal").modal('show');
                                          })
                                        });
                                        
                                        $(function() {
                                                $('#element').on('click', function( e ) {
                                                    Custombox.open({
                                                        target: '#testmodal-1',
                                                        effect: 'fadein'
                                                    });
                                                    e.preventDefault();
                                                });
                                            });
                                        </script>
                                        <!--model end-->
                                        
										
											<!-- description -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('description')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="description">{{ t('Description') }} <sup>*</sup></label>
											<!--<div class="col-md-8"></div>-->
                                            <!--<div class="col-md-8" style="position: relative; float: right; padding-top: 10px;">-->
                                            <div class="col-md-8">
                                                <?php $ckeditorClass = (config('settings.single.ckeditor_wysiwyg')) ? 'ckeditor' : ''; ?>
                                                <textarea class="form-control {{ $ckeditorClass }}" id="description" name="description" rows="10">{{ old('description') }}</textarea>
												<p class="help-block">{{ t('Describe what makes your ad unique') }}...</p>
                                            </div>
										</div>
										
										<!-- tags -->
										<!--<div class="form-group <?php echo (isset($errors) and $errors->has('tags')) ? 'has-error' : ''; ?>">-->
										<!--	<label class="col-md-3 control-label" for="title">{{ t('Tags') }}</label>-->
										<!--	<div class="col-md-8">-->
										<!--		<input id="tags" name="tags" placeholder="{{ t('Tags') }}" class="form-control input-md" type="text" value="{{ old('tags') }}">-->
										<!--		<span class="help-block">{{ t('Enter the tags separated by commas.') }}</span>-->
										<!--	</div>-->
										<!--</div>-->
										
										<div style="display: none;">
										<!--
										<div class="content-subheading">
											<i class="icon-user fa"></i>
											<strong>{{ t('Seller information') }}</strong>
										</div>
										-->
										
										<!-- contact_name -->
										@if (auth()->check())
											<input id="contact_name" name="contact_name" type="hidden" value="{{ auth()->user()->username }}">
										@else
											<div class="form-group required <?php echo (isset($errors) and $errors->has('contact_name')) ? 'has-error' : ''; ?>">
												<label class="col-md-3 control-label" for="contact_name">{{ t('Your name') }} <sup>*</sup></label>
												<div class="col-md-8">
													<input id="contact_name" name="contact_name" placeholder="{{ t('Your name') }}"
														   class="form-control input-md" type="text" value="{{ old('contact_name') }}">
												</div>
											</div>
										@endif
									
										<!-- email -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('email')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="email"> {{ t('Email') }} <sup>*</sup></label>
											<div class="col-md-8">
												<div class="input-group">
													<span class="input-group-addon">
														<i class="icon-mail"></i>
													</span>
													<input id="email" name="email"
														   class="form-control" placeholder="{{ t('Email') }}" type="text"
														   value="{{ old('email', ((auth()->check() and isset(auth()->user()->email)) ? auth()->user()->email : '')) }}">
												</div>
											</div>
										</div>
										
										<?php
											if (auth()->check()) {
												$formPhone = (auth()->user()->country_code == config('country.code')) ? auth()->user()->phone : '';
											} else {
												$formPhone = '';
											}
										?>
										<!-- phone -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('phone')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="phone">{{ t('Phone Number') }}<sup>*</sup></label>
											<div class="col-md-8">
												<div class="input-group">
                                                    <span id="phoneCountry" class="input-group-addon">{!! getPhoneIcon(config('country.code')) !!}</span>
													
													<input id="phone" name="phone"
														   placeholder="{{ t('Phone Number') }}"
														   class="search-location form-control input-md" type="text"
														   value="{{ phoneFormat(old('phone', $formPhone), old('country', config('country.code'))) }}"
													>
													
													<!--<label class="input-group-addon">
														<input name="phone_hidden" id="phoneHidden" type="checkbox"
															   value="1" {{ (old('phone_hidden')=='1') ? 'checked="checked"' : '' }}>
														{{ t('Hide') }}
													</label>-->
												</div>
											</div>
										</div>
                                        
										<!--@if (config('settings.security.recaptcha_activation'))-->
                                            <!-- g-recaptcha-response -->
										<!--	<div class="form-group required <?php echo (isset($errors) and $errors->has('g-recaptcha-response')) ? 'has-error' : ''; ?>">-->
										<!--		<label class="col-md-3 control-label" for="g-recaptcha-response"></label>-->
										<!--		<div class="col-md-8">-->
										<!--			{!! Recaptcha::render(['lang' => config('app.locale')]) !!}-->
										<!--		</div>-->
										<!--	</div>-->
										<!--@endif-->

										<!-- term -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('term')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label"></label>
											<div class="col-md-8">
												<label class="checkbox-inline" for="term-0" style="margin-left: -20px;">
													{!! t('By continuing on this website, you accept our <a :attributes>Terms of Use</a>', ['attributes' => getUrlPageByType('terms')]) !!}
												</label>
											</div>
										</div>
										
										
										</div>

									
								 <div class="row">
                    				<div class="col-sm-12">
									   <fieldset>
									         @if (isset($packages) and isset($paymentMethods) and $packages->count() > 0 and $paymentMethods->count() > 0)
                                            <div class="well" style="padding-bottom: 0;">
                                                <h3><i class="icon-certificate icon-color-1"></i> 
                                                  <?php
                                		            $chosseplangsting = t('Choose plan'); 
                                		            ?>
                        		              {{ $chosseplangsting }} </h3>

   <div class="form-group <?php echo (isset($errors) and $errors->has('package_id')) ? 'has-error' : ''; ?>" style="margin-bottom: 0;">
   		<table id="packagesTable" class="table table-hover checkboxtable" style="margin-bottom: 0;">
												<?php
																
														$currentPaymentMethodId = 0;
														$currentPaymentActive = 1;
															
                                                        $is_regular = '';
														?>
                                                        @foreach ($packages as $package)
                                                            <?php
                                                            if($package->price == 0){
                                                                $is_regular = '1';
                                                            }
                                                            $currentPackageId = 0;
                                                            $currentPackagePrice = 0;
                                                            $packageStatus = '';
                                                            $badge = '';
															
                                                            // Prevent Package's Downgrading
                                                            if ($currentPackagePrice > $package->price) {
                                                                $packageStatus = ' disabled';
                                                                $badge = ' <span class="label label-danger">'. t('Not available') . '</span>';
                                                            } elseif ($currentPackagePrice == $package->price) {
                                                                $badge = '';
                                                            } else {
                                                                $badge = ' <span class="label label-success">'. t('Upgrade') . '</span>';
                                                            }
                                                            if ($currentPackageId == $package->tid) {
                                                                $badge = ' <span class="label label-default">'. t('Current') . '</span>';
																if ($currentPaymentActive == 0) {
																	$badge .= ' <span class="label label-warning">'. t('Payment pending') . '</span>';
																}
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="radio">
                                                                        <label>
                                                        				<input class="package-selection" type="radio" name="package_id"
                                                                                   id="packageId-{{ $package->tid }}"
                                                                                   value="{{ $package->tid }}"
																				   data-name="{{ $package->name }}"
																				   data-currencysymbol="{{ $package->currency->symbol }}"
																				   data-currencyinleft="{{ $package->currency->in_left }}"
                                                                                    {{ (old('package_id', $currentPackageId)==$package->tid) ? ' checked' : (($package->price==0) ? ' checked' : '') }} {{ $packageStatus }}>
                                                                            <strong class="tooltipHere" title="" data-placement="right" data-toggle="tooltip" data-original-title="{!! $package->description !!}">{!! $package->name . $badge !!} </strong>
                                                                        </label>
                                                                    </div>
                                                                    <div class="plan-description">
                                                                        {!!$package->plan_full_description !!}
                                                                        <div class="SelectpremiumShow" style="display:none">
                                                                        @if(!empty($package->more_description))
                                                                           <blink> {!!$package->more_description !!}</blink>
                                                                        @endif
                                                                        </div>
                                                                        
                                                                        <?php
                                                                        if($package->price != 0)
                                                                        { 	?>
                                                                        <br />
                                                                        <div style="clear:both"></div>
                                                                        
                                                                        
                        <div class="form-group required ">
								<label for="from_email" style="margin-bottom: 6px;" class="control-label">{{t('Show my Email to public')}}</label>
								<div style="clear:both"></div>
								<div class="col-md-6" style="padding: 0px;margin-top: 9px;width: 3%;">
								    <input id="EmailCheckbox" checked type="checkbox">
								    <input id="from_email_checkbox" type="hidden" value="{{ old('from_email', auth()->user()->email) }}">
								</div>
								<div class="col-md-6" style="padding: 0px;" id="ShowEmailusingCheckbox">
    								<div class="input-group">
    									<span class="input-group-addon"><i class="icon-mail"></i></span>
    									<input id="from_email" name="from_email" placeholder="i.e. you@gmail.com" class="form-control" value="{{ old('from_email', auth()->user()->email) }}" type="text" readonly>
    								</div>
								</div>
								<div style="clear:both"></div>
						</div>
                                                                        
                                                           
	                    <div class="form-group required ">
							<label for="phone" style="margin-bottom: 6px;" class="control-label">{{t('Show my Phone to public')}}</label>
							<div style="clear:both"></div>
							<div class="col-md-6"  style="padding: 0px;margin-top: 9px;width: 3%;">
						      <input id="PhoneCheckbox" checked type="checkbox">
						    	<input id="from_phone_checkbox" type="hidden" value="{{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }}">
							</div>
							<div class="col-md-6" id="ShowPhoneusingCheckbox" style="padding: 0px;">
	    						<div class="input-group">
	    							<span class="input-group-addon"><i class="icon-phone-1"></i></span>
	    							<input id="from_phone" name="from_phone" placeholder="{{t('Phone Number')}}" maxlength="60" class="form-control" value="{{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }}" type="text" readonly>
	    						</div>
							</div>
							<div style="clear:both"></div>
						</div>
                                                           
                                                                        
                                                                        
                                                <div style="clear:both"></div>
                                                                    <?php    }
                                                                        ?>
                                                                        
                                                                    </div>
                                                                </td>
                                                                <td style="width: 18%;">
                                                                    <p id="price-{{ $package->tid }}">
                                                                        
                                                                          <span class="price-currency">$ <span class="price-int">{{ $package->price }}</span></span>
                                                   
                                                                        
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        @endforeach


                                                        <tr>
                                                            <td>
                                                                <div class="form-group <?php echo (isset($errors) and $errors->has('payment_method_id')) ? 'has-error' : ''; ?>"
                                                                     style="margin-bottom: 0;">
                                                                    <div class="col-md-8">
                                                                    @foreach ($paymentMethods as $paymentMethod)
                                                                            @if($paymentMethod->id == 1)
                                                                                <label>
                                                                    				<input class="paymentMethodId" type="radio"  name="payment_method_id"  value="{{ $paymentMethod->id }}"  data-name="{{ $paymentMethod->name }}" {{ (old('payment_method_id', 1)==$paymentMethod->id) ? 'checked' : '' }} >
                                                                                    <strong>{{ $paymentMethod->display_name }} </strong>
                                                                                </label> &nbsp;&nbsp;&nbsp;&nbsp;
                                                                            @else
                                                                                <label>
                                                                    				<input class="paymentMethodId" type="radio"  name="payment_method_id"  value="{{ $paymentMethod->id }}"  data-name="{{ $paymentMethod->name }}" {{ (old('payment_method_id', $currentPaymentMethodId)==$paymentMethod->id) ? 'checked' : '' }} >
                                                                                    <strong>{{ $paymentMethod->display_name }} </strong>
                                                                                </label>
                                                                            @endif
                                                                            
                                                                        
                                                                    @endforeach                                                                        
                                                                        
                                                                        
                                                                        
                                                                        <!--<select class="form-control selecter" name="payment_method_id" id="paymentMethodId">-->
                                                                        <!--    @foreach ($paymentMethods as $paymentMethod)-->
                                                                        <!--    {{--    @if (view()->exists('payment::' . $paymentMethod->name)) --}}-->
                                                                        <!--            <option value="{{ $paymentMethod->id }}" data-name="{{ $paymentMethod->name }}" {{ (old('payment_method_id', $currentPaymentMethodId)==$paymentMethod->id) ? 'selected="selected"' : '' }}>-->
                                                                        <!--                @if ($paymentMethod->name == 'offlinepayment')-->
                                                                        <!--                    {{ trans('offlinepayment::messages.Offline Payment') }}-->
                                                                        <!--                @else-->
                                                                        <!--                    {{ $paymentMethod->display_name }}-->
                                                                        <!--                @endif-->
                                                                        <!--            </option>-->
                                                                        <!--      {{--  @endif --}}-->
                                                                        <!--    @endforeach-->
                                                                        <!--</select>-->
                                                                        
                                                                        
                                                                        
                                                                        
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td style="width: 18%;">
                                                                <p style="margin-top: 7px;">
                                                                    <strong>
																		{{ t('Payable Amount') }} :
																		<span class="price-currency amount-currency currency-in-left" style="display: none;"></span>
                                                                        <span class="payable-amount">0</span>
																		<span class="price-currency amount-currency currency-in-right" style="display: none;"></span>
                                                                    </strong>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    
                                                        
   		</table>
	</div>
</div>


 											@if (isset($paymentMethods) and $paymentMethods->count() > 0)
                                                <!-- Payment Plugins -->
                                                <?php $hasCcBox = 0; ?>
                                                @foreach($paymentMethods as $paymentMethod)
                                                    @if (view()->exists('payment::' . $paymentMethod->name))
                                                        @include('payment::' . $paymentMethod->name, [$paymentMethod->name . 'PaymentMethod' => $paymentMethod])
                                                    @endif
                                                    <?php if ($paymentMethod->has_ccbox == 1 && $hasCcBox == 0) $hasCcBox = 1; ?>
                                                @endforeach
                                            @endif

                                                <div class="row payment-plugin" id="paypalPaymentKnet" style="display: none;">
                                                    <div class="col-xs-12 col-md-8 box-center center">
                                                        <img class="img-responsive box-center center" title="Payment with Paypal" style="margin-bottom: 20px;" src="http://dealnotdeal.com/images/knet.png">
                                                    </div>
                                                </div>   
                                            
                    		            	@endif
									   </fieldset>
									</div>
								</div>
								
								<!-- Button  -->
                                        <div class="form-group">
                                            <div class="col-md-12 mt20" style="text-align: center;">
                                                    <button id="submitPostForm" class="btn btn-success btn-lg submitPostForm btn-pay" @if($is_regular) style="display: none;" @endif> {{ t('Pay') }} </button>
                                                    <button id="submitPostForm" class="btn btn-primary btn-lg submitPostForm btn-finish" @if(!$is_regular) style="display: none;" @endif> {{ t('Next') }} </button>
                                                    
                                                    
                                            </div>
                                        </div>
                                        

										<!-- Button  -->
										{{-- <div class="form-group">
											<div class="col-md-12" style="text-align: center;">
												<button id="nextStepBtn" class="btn btn-primary btn-lg"> {{ t('Submit') }} </button>
											</div>
										</div> --}}

										<div style="margin-bottom: 30px;"></div>

									</fieldset>
								</form>
								<!--<a href="javascript:window.open('','_self').close();">close</a>-->

							<!--<input type="button" id="cancel_edit" class="btn btn-primary btn-lg" value="Window Close"></input>-->
							</div>
						</div>
					</div>
				</div>
				<!-- /.page-content -->

				<div class="col-md-3 reg-sidebar">
					<div class="reg-sidebar-inner text-center">
					<!--	<div class="promo-text-box"><i class=" icon-picture fa fa-4x icon-color-1"></i>
							<h3><strong>{{ t('Post Free Ads') }}</strong></h3>
							<p>
								{{ t('Do you have something to sell, to rent, any service to offer or a job offer? Post it at :app_name, its free, local, easy, reliable and super fast!', ['app_name' => config('app.name')]) }}
							</p>
						</div>-->

						<div class="panel sidebar-panel">
							<div class="panel-heading uppercase">
								<small><strong>{{ t('How to sell quickly?') }}</strong></small>
							</div>
							<div class="panel-content">
								<div class="panel-body text-left">
									<ul class="list-check" style="font-size: 15px;">
										<li> {{ t('Use a brief title and description of the item') }} </li>
										<li> {{ t('Make sure you post in the correct category') }}</li>
										<li> {{ t('Add nice photos to your ad') }}</li>
										<li> {{ t('Put a reasonable price') }}</li>
										<li> {{ t('Check the item before publish') }}</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	

         
@endsection

@section('after_styles')
	@include('layouts.inc.tools.wysiwyg.css')
@endsection

@section('after_scripts')
    @include('layouts.inc.tools.wysiwyg.js')

 <script>
        	 $("#cancel_edit").click(function(){
        var customWindow = window.open('', '_blank', '');
    customWindow.close();
   // setTimeout(window.close, 200);
    });
        </script>
<script type="text/javascript">
	       // $('document').ready(function(){
                
        //         $('body').delegate('#city_name','keypress',function(){
        //                 var input = document.getElementById('city_name');
        //                 initialize(input);
        //         });
                
	       // });    
                function initialize() {
                    //   new google.maps.places.Autocomplete(input);
                    var input = document.getElementById('city_name');
                    var options = {
                    types: ['(cities)'],
                        componentRestrictions: {country: "{{config('country.icode')}}"}
                    };
                    var autocomplete = new google.maps.places.Autocomplete(input, options);
                    
                }
	        
                google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3HKnsvpSAYaoQQ-wIeqDBTjb69hJ-vMw&libraries=places&callback=initialize"
         async defer></script>
         

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	





 <script>
 	


	    $('#EmailCheckbox').change(function(){
            if($(this).is(":checked")) 
	        {
	            var email = $('#from_email_checkbox').val();
	            $('#from_email').val(email);
	            $('#ShowEmailusingCheckbox').show();
	        }
	        else
	        {
	            $('#from_email').val('');
	            $('#ShowEmailusingCheckbox').hide();
	        }
	    });
	    
	    
        $('#PhoneCheckbox').change(function(){
            if($(this).is(":checked")) 
	        {
	            var phone = $('#from_phone_checkbox').val();
	            $('#from_phone').val(phone);
	            
	            $('#ShowPhoneusingCheckbox').show();
	        }
	        else
	        {
	            $('#from_phone').val('');
	            $('#ShowPhoneusingCheckbox').hide();
	        }
	        
	    });
	    
	</script>    
    <script>
        @if (isset($packages) and isset($paymentMethods) and $packages->count() > 0 and $paymentMethods->count() > 0)
				
		 	$('.package-selection').each(function () {
		 	   if($(this).is(":checked")) 
		        {
		        	selectedPackage = $(this).val();
					packagePrice = getPackagePrice(selectedPackage);
                    if(packagePrice == 0){
                        $('.SelectpremiumShow').hide();
                        $('.btn-pay').hide();
                        $('.btn-finish').show();
                    }else{
                        $('.btn-pay').show();
                        $('.btn-finish').hide();
                        $('.SelectpremiumShow').show();
                        
                    }
		        }
		 	});


			var currentPackagePrice = {{ $currentPackagePrice }};
			var currentPaymentActive = {{ $currentPaymentActive }};
			$(document).ready(function ()
			{
				/* Show price & Payment Methods */
				var selectedPackage = $('input[name=package_id]:checked').val();
				var packagePrice = getPackagePrice(selectedPackage);
				var packageCurrencySymbol = $('input[name=package_id]:checked').data('currencysymbol');
				var packageCurrencyInLeft = $('input[name=package_id]:checked').data('currencyinleft');
				
				// var paymentMethod = $('#paymentMethodId').find('option:selected').data('name');
				var paymentMethod = '';
				
				$('.paymentMethodId').each(function(){
				        if(this.checked)
				        {
				             paymentMethod = $(this).attr('data-name');
				        }
				});
				
				 
				showAmount(packagePrice, packageCurrencySymbol, packageCurrencyInLeft);
				showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod);
				
				/* Select a Package */
				$('.package-selection').click(function () {
					selectedPackage = $(this).val();
					packagePrice = getPackagePrice(selectedPackage);
                    if(packagePrice == 0){
                        $('.SelectpremiumShow').hide();
                        $('.btn-pay').hide();
                        $('.btn-finish').show();
                    }else{
                        $('.SelectpremiumShow').show();
                        $('.btn-pay').show();
                        $('.btn-finish').hide();
                    }
					packageCurrencySymbol = $(this).data('currencysymbol');
					packageCurrencyInLeft = $(this).data('currencyinleft');
					showAmount(packagePrice, packageCurrencySymbol, packageCurrencyInLeft);
					showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod);
				});
				
				/* Select a Payment Method */
				$('.paymentMethodId').on('change', function () {
				    
				    paymentMethod = $(this).attr('data-name');
				    // paymentMethod = $(this).find('option:selected').data('name');
					showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod);
				});
				
				/* Form Default Submission */
				$('#submitPostForm').on('click', function (e) {
				// 	e.preventDefault();
					
				// 	if (packagePrice <= 0) {
						$('#postForm').submit();
				// 	}
					
				// 	return false;
				});
			});
        
        @endif

		/* Show or Hide the Payment Submit Button */
		/* NOTE: Prevent Package's Downgrading */
		/* Hide the 'Skip' button if Package price > 0 */
		function showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod)
		{
			if (packagePrice > 0) {
				$('#submitPostForm').show();
				$('#skipBtn').hide();
				
				if (currentPackagePrice > packagePrice) {
					$('#submitPostForm').hide();
				}
				if (currentPackagePrice == packagePrice) {
					if (paymentMethod == 'offlinepayment' && currentPaymentActive != 1) {
						$('#submitPostForm').hide();
						$('#skipBtn').show();
					}
				}
			} else {
				$('#skipBtn').show();
			}
		}
    </script>







	@if (file_exists(public_path() . '/assets/plugins/forms/validation/localization/messages_'.config('app.locale').'.min.js'))
		<script src="{{ url('assets/plugins/forms/validation/localization/messages_'.config('app.locale').'.min.js') }}" type="text/javascript"></script>
	@endif
	
		  
         
         
	<script>
	function closer(){
                  self.close();
                }
	$('document').ready(function(){
	
	
//	   $('#customFields').delegate('.ChangeCheckboxMoving','change',function(){
       //           var value = $(this).attr('data-val');
        //          $('.claralltext').val('');
        //         if(value == '0')
         //         {
         //            $('#DisplayRecord').show();
         //         }
         //       {
          //            $('#DisplayRecord').hide();
          //       }
       //  }); 
	
       
	
	
                  var getvalue =  $( "#parentId option:selected" ).text();
        	       if($.trim(getvalue) == "Free")
        	       {
    	                $('#price').val('0');
    	                $('#price').prop('readonly',true);
        	       }
        	       
            	$('#parentId').change(function(){
            	        
            	       var getvalue =  $( "#parentId option:selected" ).text();
            	       if($.trim(getvalue) == "Free")
            	       {
        	                $('#price').val('0');
        	                $('#price').prop('readonly',true);
            	       }
            	       else
            	       {
        	                $('#price').val('');
        	                $('#price').prop('readonly',false);
            	       }
            	        
            	});
            	
            	$('#closeTab').click(function(){
            	     window.location.href = "{{URL::to('posts/create_step3/finish')}}";
            	     setTimeout("closer()", 20000)
            	});
            	
            	
            	
	});
	
		/* Translation */
		var lang = {
			'select': {
				'category': "{{ t('Select a category') }}",
				'subCategory': "{{ t('Select a sub-category') }}",
				'country': "{{ t('Select a country') }}",
				'admin': "{{ t('Select a location') }}",
				'city': "{{ t('Select a city') }}"
			},
			'price': "{{ t('Price') }}",
			'salary': "{{ t('Salary') }}",
			'nextStepBtnLabel': {
			    'next': "{{ t('Next') }}",
                'submit': "{{ t('Submit') }}"
			}
		};
		
		/* Categories */
		var category = {{ old('parent_id', 0) }};
		var categoryType = '{{ old('parent_type') }}';
		if (categoryType=='') {
			var selectedCat = $('select[name=parent_id]').find('option:selected');
			categoryType = selectedCat.data('type');
		}
		var subCategory = {{ old('category_id', 0) }};
		
		/* Custom Fields */
		var errors = '{!! addslashes($errors->toJson()) !!}';
		var oldInput = '{!! addslashes(collect(session()->getOldInput('cf'))->toJson()) !!}';
		var postId = '';
		
		/* Locations */
        var countryCode = '{{ old('country_code', config('country.code', 0)) }}';
        var adminType = '{{ config('country.admin_type', 0) }}';
        var selectedAdminCode = '{{ old('admin_code', (isset($admin) ? $admin->code : 0)) }}';
        var cityId = '{{ old('city_id', (isset($post) ? $post->city_id : 0)) }}';
		
		/* Packages */
		var packageIsEnabled = false;
        @if (isset($packages) and isset($paymentMethods) and $packages->count() > 0 and $paymentMethods->count() > 0)
            packageIsEnabled = true;
        @endif
		
		//Auto load city
		var input = document.getElementById('city_id');
		var opts = {
		  types: ['(cities)']
		};
		var autocomplete = new google.maps.places.Autocomplete(input,opts);
		
		// Begin of the code made by MonTech Team
		var currgeocoder;
		function get_location(){
			 navigator.geolocation.getCurrentPosition(function(position, html5Error) {
				 geo_loc = processGeolocationResult(position);
				 currLatLong = geo_loc.split(",");
				 initializeCurrent(currLatLong[0], currLatLong[1]);
			});
		}
		//Get geo location result
       function processGeolocationResult(position) {
             html5Lat = position.coords.latitude; //Get latitude
             html5Lon = position.coords.longitude; //Get longitude
             html5TimeStamp = position.timestamp; //Get timestamp
             html5Accuracy = position.coords.accuracy; //Get accuracy in meters
             return (html5Lat).toFixed(8) + ", " + (html5Lon).toFixed(8);
       }

        //Check value is present or not & call google api function
        function initializeCurrent(latcurr, longcurr) {
             currgeocoder = new google.maps.Geocoder();
             //console.log(latcurr + "-- ######## --" + longcurr);

             if (latcurr != '' && longcurr != '') {
                 var myLatlng = new google.maps.LatLng(latcurr, longcurr);
                 return getCurrentAddress(myLatlng);
             }
       }

        //Get current address
         function getCurrentAddress(location) {
			 currgeocoder.geocode({'location': location}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    //console.log(results[1]);
					if (results[1]) {
						result = results[1];
						var country = null, countryCode = null, city = null, cityAlt = null;
						var c, lc, component;
						for (c = 0, lc = result.address_components.length; c < lc; c += 1) {
								component = result.address_components[c];
									if (component.types[0] === 'locality') {
										city = component.long_name;
										break;
							}
						}
						if(city){
							$("input[name='city_id']").val(city);
						}
					}
                } else {
                    console.log('Geocode was not successful for the following reason: ' + status);
                }
            });
         }
		// End of the code made by MonTech Team
	</script>
	<script>
		$(document).ready(function() {
			$('#tags').tagit({
				fieldName: 'tags',
				placeholderText: '{{ t('add a tag') }}',
				caseSensitive: false,
				allowDuplicates: false,
				allowSpaces: false,
				tagLimit: {{ (int)config('settings.single.tags_limit', 15) }},
				singleFieldDelimiter: ','
			});
			
			
			
		});
	</script>
	
	<script src="{{ url('assets/js/app/d.select.category.js') . vTime() }}"></script>
	<script src="{{ url('assets/js/app/d.select.location.js') . vTime() }}"></script>
	
	
	<script>
	    
	    	$(document).ready(function() {
		var category = $('#pidd').val();
	
	    var	subCategory = $('#subbb').val();

		getCustomFieldsByCategory(siteUrl, languageCode, category, subCategory);
	});
	
		$(document).ready(function() {
		
		
		var category = $('#pidd').val();
		//alert(category);
	    var	subCategory = $('#subbb').val();
	   // alert(subCategory);
		
		/* Get the category and subcategory's custom fields (merged) */
		if (category != 0 && subCategory != 0) {
			getCustomFieldsByCategory(siteUrl, languageCode, category, subCategory);
		}
		
	});
	
	    
	</script>
	<style>
	#location_btn_div{padding:0;}
	</style>
@endsection
