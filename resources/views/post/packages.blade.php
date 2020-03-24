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

@section('wizard')
    @include('post.inc.wizard')
@endsection

@section('content')
	@include('common.spacer')
    <div class="main-container">
        <div class="container">
            <div class="row">
    
                @include('post.inc.notification')
                
                <div class="col-md-12 page-content">
                    <div class="inner-box category-content">
                        <h2 class="title-2"><strong> <i class="icon-tag"></i> {{ t('Payment') }}</strong></h2>
                        <div class="row">
                            <div class="col-sm-12">
                                <form class="form-horizontal" id="postForm" method="POST" action="{{ url()->current() }}">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                                    <fieldset>
                                        
                                        @if (isset($packages) and isset($paymentMethods) and $packages->count() > 0 and $paymentMethods->count() > 0)
                                            <div class="well" style="padding-bottom: 0;">
                                                <h3><i class="icon-certificate icon-color-1"></i> 
                                                      <?php
                                        		            $chosseplangsting = t('Choose plan'); 
                                        		            ?>
                                        		            
                                                {{ $chosseplangsting }} </h3>
                                                {{--<p>{{ t('The premium package help sellers to promote their products or services by giving more visibility to their ads to attract more buyers and sell faster.') }}--}}</p>
                                                <div class="form-group <?php echo (isset($errors) and $errors->has('package_id')) ? 'has-error' : ''; ?>"
                                                     style="margin-bottom: 0;">
                                                    <table id="packagesTable" class="table table-hover checkboxtable" style="margin-bottom: 0;">
														<?php
															// Get Current Payment data
															$currentPaymentMethodId = 0;
															$currentPaymentActive = 1;
															if (isset($post->latestPayment) and !empty($post->latestPayment)) {
																$currentPaymentMethodId = $post->latestPayment->payment_method_id;
																if ($post->latestPayment->active == 0) {
																	$currentPaymentActive = 0;
																}
															}
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
															if (isset($post->latestPayment) and !empty($post->latestPayment)) {
																if (isset($post->latestPayment->package) and !empty($post->latestPayment->package)) {
																	$currentPackageId = $post->latestPayment->package->tid;
																	$currentPackagePrice = $post->latestPayment->package->price;
																}
                                                            }
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
                                                                        
                                                                        <?php
                                                                        if($package->price != 0)
                                                                        { ?>
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
    									<input id="from_email" name="from_email" placeholder="i.e. you@gmail.com" class="form-control" value="{{ old('from_email', auth()->user()->email) }}" type="text">
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
    							<input id="from_phone" name="from_phone" placeholder="{{t('Phone Number')}}" maxlength="60" class="form-control" value="{{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }}" type="text">
    						</div>
						</div>
						<div style="clear:both"></div>
					</div>
                                                           
                                                                        
                                                                        
                                                                        <div style="clear:both"></div>
                                                                    <?php    }
                                                                        ?>
                                                                        
                                                                    </div>
                                                                </td>
                                                                <td style="width: 50px;">
                                                                    <p id="price-{{ $package->tid }}">
                                                                        
                                                                          <span class="price-currency">$ <span class="price-int">{{ $package->price }}</span></span>
                                                                          
                                                                        <!--@if ($package->currency->in_left == 1)-->
                                                                        <!--    <span class="price-currency">{!! $package->currency->symbol !!}</span>-->
                                                                        <!--@endif-->
                                                                        <!--<span class="price-int">{{ $package->price }}</span>-->
                                                                        <!--@if ($package->currency->in_left == 0)-->
                                                                        <!--    <span class="price-currency">{!! $package->currency->symbol !!}</span>-->
                                                                        <!--@endif-->
                                                                        
                                                                        
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        
                                                        <tr>
                                                            <td>
                                                                <div class="form-group <?php echo (isset($errors) and $errors->has('payment_method_id')) ? 'has-error' : ''; ?>"
                                                                     style="margin-bottom: 0;">
                                                                    <div class="col-md-8">
                                                                        <select class="form-control selecter" name="payment_method_id" id="paymentMethodId">
                                                                            @foreach ($paymentMethods as $paymentMethod)
                                                                                @if (view()->exists('payment::' . $paymentMethod->name))
                                                                                    <option value="{{ $paymentMethod->id }}" data-name="{{ $paymentMethod->name }}" {{ (old('payment_method_id', $currentPaymentMethodId)==$paymentMethod->id) ? 'selected="selected"' : '' }}>
                                                                                        @if ($paymentMethod->name == 'offlinepayment')
                                                                                            {{ trans('offlinepayment::messages.Offline Payment') }}
                                                                                        @else
                                                                                            {{ $paymentMethod->display_name }}
                                                                                        @endif
                                                                                    </option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
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
                                        @endif
                                        
                                        <!-- Button  -->
                                        <div class="form-group">
                                            <div class="col-md-12 mt20" style="text-align: center;">
                                                <a href="{{ lurl('account/my-posts/'.$post->id.'/deletepost') }}" class="btn btn-danger btn-lg"> {{ t('Cancel') }} </a>
                                                <a href="{{ lurl('posts/' . $post->id . '/edit') }}" class="btn btn-default btn-lg">{{ t('Previous') }}</a>
                                                @if (getSegment(2) == 'create')
                                                    <!--<a id="skipBtn" href="{{ lurl('posts/create/' . $post->tmp_token . '/finish') }}" class="btn btn-default btn-lg">{{ t('Skip') }}</a>-->
                                                @else
													<?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>
                                                    <!--<a id="skipBtn" href="{{ lurl($post->uri, $attr) }}" class="btn btn-default btn-lg">{{ t('Skip') }}</a>-->
                                                @endif

                                                    <button id="submitPostForm" class="btn btn-success btn-lg submitPostForm btn-pay" @if($is_regular) style="display: none;" @endif> {{ t('Pay') }} </button>
                                                    <button id="submitPostForm" class="btn btn-primary btn-lg submitPostForm btn-finish" @if(!$is_regular) style="display: none;" @endif> {{ t('Next') }} </button>
                                                    <!--<button id="submitPostForm" class="btn btn-success btn-lg submitPostForm btn-finish" @if(!$is_regular) style="display: none;" @endif> {{ t('Finish') }} </button>-->
                                            </div>
                                        </div>
                                        
                                        <div style="margin-bottom: 30px;"></div>
                                    
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.page-content -->
            </div>
        </div>
    </div>
@endsection

@section('after_styles')
@endsection

@section('after_scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>
    @if (file_exists(public_path() . '/assets/plugins/forms/validation/localization/messages_'.config('app.locale').'.min.js'))
        <script src="{{ url('/assets/plugins/forms/validation/localization/messages_'.config('app.locale').'.min.js') }}" type="text/javascript"></script>
    @endif
    
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
			
			var currentPackagePrice = {{ $currentPackagePrice }};
			var currentPaymentActive = {{ $currentPaymentActive }};
			$(document).ready(function ()
			{
				/* Show price & Payment Methods */
				var selectedPackage = $('input[name=package_id]:checked').val();
				var packagePrice = getPackagePrice(selectedPackage);
				var packageCurrencySymbol = $('input[name=package_id]:checked').data('currencysymbol');
				var packageCurrencyInLeft = $('input[name=package_id]:checked').data('currencyinleft');
				var paymentMethod = $('#paymentMethodId').find('option:selected').data('name');
				showAmount(packagePrice, packageCurrencySymbol, packageCurrencyInLeft);
				showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod);
				
				/* Select a Package */
				$('.package-selection').click(function () {
					selectedPackage = $(this).val();
					packagePrice = getPackagePrice(selectedPackage);
                    if(packagePrice == 0){
                        $('.btn-pay').hide();
                        $('.btn-finish').show();
                    }else{
                        $('.btn-pay').show();
                        $('.btn-finish').hide();
                    }
					packageCurrencySymbol = $(this).data('currencysymbol');
					packageCurrencyInLeft = $(this).data('currencyinleft');
					showAmount(packagePrice, packageCurrencySymbol, packageCurrencyInLeft);
					showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod);
				});
				
				/* Select a Payment Method */
				$('#paymentMethodId').on('change', function () {
					paymentMethod = $(this).find('option:selected').data('name');
					showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod);
				});
				
				/* Form Default Submission */
				$('#submitPostForm').on('click', function (e) {
					e.preventDefault();
					
					if (packagePrice <= 0) {
						$('#postForm').submit();
					}
					
					return false;
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
@endsection
