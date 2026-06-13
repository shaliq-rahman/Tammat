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

@section('content')
  <link href="{{ url('datepicker/bootstrap-datetimepicker.css') }}" rel="stylesheet"/>

  
	@include('common.spacer')
	<div class="main-container" style="margin-top: 50px;">
		<div class="container">
			<div class="row">
				
				@if (Session::has('flash_notification'))
					<div class="container" style="margin-bottom: -10px; margin-top: -10px;">
						<div class="row">
							<div class="col-lg-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif
				
    			@if(!empty(session('success')))
    			<div class="col-lg-12">
                        <div class="alert alert-success alert-dismissible fade in" role="alert">
                            <button class="close" aria-label="Close" data-dismiss="alert" type="button">
                              <span aria-hidden="true">×</span>
                            </button>
                             {{ session('success') }}
                        </div>
                </div>
                @endif
            
				<div class="col-sm-3 page-sidebar">
					@include('account.inc.sidebar')
				</div>
				<!--/.page-sidebar-->
				
				<div class="col-sm-9 page-content">
					<div class="inner-box">
						<h2 class="title-2">
							<i class="icon-money"></i>  {{ t('Balance') }}
						</h2>
						<div id="reloadBtn" class="mb30" style="display: none;">
							<a href="" class="btn btn-primary" class="tooltipHere" title="" data-placement="{{ (config('lang.direction')=='rtl') ? 'left' : 'right' }}"
							   data-toggle="tooltip"
							   data-original-title="{{ t('Reload to see New Messages') }}"><i class="icon-arrows-cw"></i> {{ t('Reload') }}</a>
							<br><br>
						</div>
						
						<div style="clear:both"></div>
						
						<div class="table-responsive" style="overflow: hidden;">
                        
                                       <div class="content-subheading">
                                            <i class="icon-tag"></i>
											<strong> {{ t('Payment') }} ( <span style="font-size: 13px;font-weight: bold;">
                                           
                                            {{ t('Your Current Balance') }} 
                                            <span style="color: #ff5555;">{{ auth()->user()->no_points }}</span> {{ t('Points') }}</span> )</strong>
										</div>
                                       
                                        
			 
                            <form method="POST"  >
								{!! csrf_field() !!}
                                <?php  
								        $points = \DB::table('points')->get(); 
								?>
                                <div class="row">
                    				<div class="col-sm-12">
									   <fieldset>
									        <div class="well" style="padding-bottom: 0;">
                                           
                                            {{ t('Increase Your Balance') }} 
                                            
                                            </h3>

                                     <div class="form-group " style="margin-bottom: 0;">
   		                                       <table id="packagesTable" class="table table-hover checkboxtable" style="margin-bottom: 0;">
												
												                                                                                                                                                                                  
                                                            <tbody>
                                                            
                                                            @if(!empty($points))
                                                            @foreach($points as $point)
                                                            <tr>
                                                                <td>
                                                                    <div class="radio">
                                                                        <label>
                                                        				<input class="package-selection" type="radio" name="point_id"  value="{{$point->id}}" data-name="Number Of Points" 
                                                                        data-currencysymbol="&amp;#36;" data-currencyinleft="1" checked="" onchange="showpayment()">
                                                                            <strong class="tooltipHere" title="" data-placement="right" data-toggle="tooltip" data-original-title="Number Of Points">
                                                                            {{$point->no_points}} ( {{ t('Points') }} )
                                                                            </strong>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td style="width: 18%;">
                                                                    <p id="price-1">
                                                                         <span class="price-currency">$ <span class="price-int">{{$point->price}}</span></span>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @endif
                                                             
                                                            
                                                             <tr>
                                                             <?php   $currentPaymentMethodId = 0; $currentPaymentActive = 1; $is_regular = ''; ?>
                                                            <td>
                                                                <div class="form-group <?php echo (isset($errors) and $errors->has('payment_method_id')) ? 'has-error' : ''; ?>"
                                                                     style="margin-bottom: 0;">
                                                                    <div class="col-md-12" align="center">
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
                                                                     
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td style="width: 18%;" style="display:none !important">
                                                                <p style="margin-top: 7px;display:none !important">
                                                                    <strong>
																		{{ t('Payable Amount') }} :
																		<span class="price-currency amount-currency currency-in-left" style="display: none;"></span>
                                                                        <span class="payable-amount">0</span>
																		<span class="price-currency amount-currency currency-in-right" style="display: none;"></span>
                                                                    </strong>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        
                                                        
                                                        
                                                          </tbody>
                                                       </table>
                                                       
                                                       
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

                                                <div class="row payment-plugin" id="paypalPaymentKnet" style="display: block !important;">
                                                    <div class="col-xs-12 col-md-8 box-center center" style="display: block !important;">
                                                        <img class="img-responsive box-center center" title="Payment with Paypal" style="margin-bottom: 20px;" src="http://tmmat.com/images/knet.png">
                                                    </div>
                                                </div>
                                                
                                                <script>
                                                function showpayment(){
													$('#paypalPayment').hide();													
													}
                                                
                                                </script>
                                                
                                                <br />
                                                         <div class="form-group">
                                                                <div class="col-md-12 mt20" style="text-align: center;">
                                                             <!--   <input type="submit" id="submitPostForm" class="btn btn-primary btn-lg submitPostForm btn-pay"  value="Start"/>-->
                                                             <input type="submit"  class="btn btn-primary btn-lg"  value="{{ t('Pay') }}"/>
                                                                </div>
                                                        </div>
                                           </div>
                                </div>
                                <!-- Payment Plugins -->
                                </fieldset>
                                </div>
                                </div>
                                </form>
                                </div>
                                <div style="clear:both"></div>
					
					</div>
				</div>
				<!--/.page-content-->
				
			</div>
			<!--/.row-->
		</div>
		<!--/.container-->
	</div>
	<!-- /.main-container -->
	
	
	

	
	

@endsection

@section('after_scripts')
	<script src="{{ url('assets/js/footable.js?v=2-0-1') }}" type="text/javascript"></script>
	<script src="{{ url('assets/js/footable.filter.js?v=2-0-1') }}" type="text/javascript"></script>
	
	<script src="{{ url('datepicker/moment-with-locales.js') }}"></script>
    <script src="{{ url('datepicker/bootstrap-datetimepicker.js') }}"></script>
	

	 
@endsection