

@extends('layouts.master')

@section('content')

	<?php if (!(isset($paddingTopExists) and $paddingTopExists)) {?>

		<div class="h-spacer"></div>

	<?php } ?>

	<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />

	<div class="main-container">

		<div id="register-page">

		<div class="container">

			<div class="row">



				<?php if (isset($errors) and $errors->any()) { ?>

					<div class="col-lg-12">

						<div class="alert alert-danger">

							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

							<h5><strong>{{ t('Oops ! An error has occurred. Please correct the red fields in the form') }}</strong></h5>

							<ul class="list list-check">

								@foreach ($errors->all() as $error)

									<li>{{ $error }}</li>

								@endforeach

							</ul>

						</div>

					</div>

                    <?php } ?>



				<?php if (Session::has('flash_notification')) { ?>

				    <div class="container" style="margin-bottom: -10px; margin-top: -10px;">

						<div class="row">

							<div class="col-lg-12">

								@include('flash::message')

							</div>

						</div>

					</div>

                    <?php } ?>



				<div class="col-md-8 page-content">

					<div class="inner-box category-content">

						<h2 class="title-2"><strong> <i class="icon-user-add"></i> {{ t('Create your account, Its free') }}</strong></h2>

						<div class="row">

							

							<?php if (config('settings.social_auth.social_login_activation')) { ?>

								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center mb30">

									<div class="row row-centered">

										<div class="col-lg-11 col-md-11 col-sm-12 col-xs-12 col-centered small-gutter">

											<div class="col-md-6 col-xs-12 mb5">

												<div class="col-xs-12 btn btn-lg btn-fb">

													<a href="{{ lurl('auth/facebook') }}" class="btn-fb"><i class="icon-facebook"></i> {!! t('Connect with Facebook') !!}</a>

												</div>

											</div>

											<div class="col-md-6 col-xs-12 mb5">

												<div class="col-xs-12 btn btn-lg btn-danger">

													<a href="{{ lurl('auth/google') }}" class="btn-danger"><i class="icon-googleplus-rect"></i> {!! t('Connect with Google') !!}</a>

												</div>

											</div>

										</div>

									</div>



									<div class="row row-centered loginOr">

										<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 col-centered mb5">

											<hr class="hrOr">

											<span class="spanOr rounded">{{ t('or') }}</span>

										</div>

									</div>

								</div>

								<?php } ?>

							

							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

								<form id="signupForm" class="form-horizontal" method="POST" action="{{ url()->current() }}">

									{!! csrf_field() !!}

									<fieldset>



									

										

										<!--First Name-->

											<div class="form-group required <?php echo (isset($errors) and $errors->has('first_name')) ? 'has-error' : ''; ?>">

											<label class="col-md-4 control-label">{{ t('Firtst Name') }} <sup>*</sup></label>

											<div class="col-md-6">

												<input name="first_name" placeholder="{{ t('Firtst Name') }}" class="form-control input-md" type="text" value="{{ old('first_name') }}">

											</div>

											</div>

											 

										 <!--Last Name-->

											<div class="form-group required <?php echo (isset($errors) and $errors->has('last_name')) ? 'has-error' : ''; ?>">

											<label class="col-md-4 control-label">{{ t('Last Name') }}<sup>*</sup></label>

											<div class="col-md-6">

												<input name="last_name" placeholder="{{ t('Last Name') }}" class="form-control input-md" type="text" value="{{ old('last_name') }}">

											</div>

											</div>

											 

									   

										

										<div class="form-group required <?php echo (isset($errors) and $errors->has('phone_number')) ? 'has-error' : ''; ?>">

											<label class="col-md-4 control-label">{{ t('Mobile') }}<sup>*</sup>

												<?php if (!isEnabledField('email')) {?>

													<sup>*</sup>

                                                <?php } ?>

											</label>

											<div class="col-md-6">

												<div class="input-group">

													<span id="phoneCountry" class="input-group-addon">{!! getPhoneIcon(old('country', config('country.code'))) !!}</span>

													

													<input name="phone_number" placeholder="{{ t('Without Country Code') }}"

														   class="form-control input-md" type="number" maxlength="12" value="{{ phoneFormat(old('phone_number'), old('Phone_Coumtry', config('country.code'))) }}" >

													

													<label style="display:none;" class="input-group-addon" class="tooltipHere" data-placement="top"

														   data-toggle="tooltip"

														   data-original-title="{{ t('Hide the phone number on the ads.') }}">

														<input name="phone_hidden" id="phoneHidden" type="checkbox"

															   value="1" {{ (old('phone_hidden')=='1') ? 'checked="checked"' : '' }}>

														{{ t('Hide') }}

													</label>

												</div>

											</div>

										</div>

										

										



                                        <!--city -->

                                        <div class="form-group required <?php echo (isset($errors) and $errors->has('city')) ? 'has-error' : ''; ?>">

											<label class="col-md-4 control-label">{{ t('City') }} <sup>*</sup></label>

											<div class="col-md-6">

												<input name="city" id="city" class="form-control input-md" type="text" placeholder="{{ t('City') }}"  value="{{ old('city') }}" required="required">

											</div>

										</div>

										<input type="hidden" name="gender_id" value="3" />

                                 <?php /*?>

										<!-- Gender -->

										<div class="form-group required <?php echo (isset($errors) and $errors->has('gender_id')) ? 'has-error' : ''; ?>">

											<label class="col-md-4 control-label">{{ t('Gender') }} <sup>*</sup></label>

											<div class="col-md-6">

											    <label style="font-weight: normal;">

												<input required name="gender_id"  class="input-md" type="radio" value="1" {{ (old('gender_id')==1) ? 'checked="checked"' : '' }}> {{ t('Male') }} 

												</label >

												<label style="font-weight: normal;">

												<input name="gender_id"  class="input-md" type="radio" value="2" {{ (old('gender_id')==2) ? 'checked="checked"' : '' }}> {{ t('Female') }}

												</label>

											</div>

										</div><?php */?>

										

										

										<!-- user_type_id -->

										

								       <div class="form-group required <?php echo (isset($errors) and $errors->has('user_type_id')) ? 'has-error' : ''; ?>">

											<label class="col-md-4 control-label">{{ t('User Type') }} <sup>*</sup></label>

											<div class="col-md-6">

												<select name="user_type_id" id="userTypeId" class="form-control selecter"  required>

													<option {{ (old('user_type_id') == '0')?'selected':'' }} value="">

														{{ t('Select') }}

													</option>

													@foreach ($userTypes as $type)

														<option {{ (old('user_type_id') == $type->id)?'selected':'' }} value="{{ $type->id }}">

															{{ t($type->name) }}

														</option>

													@endforeach

												</select>

											</div>

										</div>

									

									

										<?php if (isEnabledField('email')) { ?>

										<!-- email -->

										<div class="form-group required <?php echo (isset($errors) and $errors->has('email')) ? 'has-error' : ''; ?>">

											<label class="col-md-4 control-label" for="email">{{ t('Email') }}

												<?php if (!isEnabledField('phone')) { ?>

													<sup>*</sup>

                                                <?php } ?>

											</label>

											<div class="col-md-6">

												<div class="input-group">

													<span class="input-group-addon"><i class="icon-mail"></i></span>

													<input id="email" name="email" type="email" class="form-control" placeholder="{{ t('Email') }}" value="{{ old('email') }}">

												</div>

											</div>

										</div>

                                    	<?php } ?>

									

									

										<!-- username -->

										<div class="form-group required <?php echo (isset($errors) and $errors->has('username')) ? 'has-error' : ''; ?>">

											<label class="col-md-4 control-label" for="email">{{ t('Username') }} <sup>*</sup></label>

											<div class="col-md-6">

													<input id="username" name="username" type="text" class="form-control" placeholder="{{ t('Username') }}" value="{{ old('username') }}">

													{{--<p class="help-block">{{ $errors->first('username') }}</p>--}}

													<p class="help-block"><?php if($errors->first('username')) { ?> {{$errors->first('username')}} <?php } else { ?> {{ t('At least 5 characters') }} <?php } ?></p>

											</div>

										</div>

										

										

										<!-- password -->

										<div class="form-group required <?php echo (isset($errors) and $errors->has('password')) ? 'has-error' : ''; ?>">

											<label class="col-md-4 control-label" for="password">{{ t('Password') }} <sup>*</sup></label>

											<div class="col-md-6">

												<input id="password" name="password" type="password" class="form-control" placeholder="{{ t('Password') }}">

												<br>

												<input id="password_confirmation" name="password_confirmation" type="password" class="form-control"

													   placeholder="{{ t('Password Confirmation') }}">

												<p class="help-block">{{ t('At least 5 characters') }}</p>

											</div>

										</div>


									 

										<?php if (config('settings.security.recaptcha_activation')) { ?>

											<!-- g-recaptcha-response -->

											 <div class="form-group required <?php echo (isset($errors) and $errors->has('g-recaptcha-response')) ? 'has-error' : ''; ?>"> 

											 	<label class="col-md-4 control-label" for="g-recaptcha-response"></label> 

											 	<div class="col-md-6"> 

											 		{!! Recaptcha::render(['lang' => config('app.locale')]) !!} 

											 	</div> 

											 </div> 

                                    <?php } ?>



									    <!-- Newsteller -->

									    <div class="form-group required <?php echo (isset($errors) and $errors->has('newsletter')) ? 'has-error' : ''; ?>"

											 style="margin-top: -10px;">

											<label class="col-md-4 control-label"></label>

											<div class="col-md-8">

												<div class="termbox mb10">

													<label class="checkbox-inline" for="newsletter">

														<input name="newsletter" id="newsletter" value="yes" type="checkbox" {{ (old('term')=='1') ? 'checked="checked"' : '' }}>

														{{ t('SignUp For Newsletter') }} 

													</label>

												</div>

												<div style="clear:both"></div>

											</div>

										</div>

										<!-- term -->

										<div class="form-group required <?php echo (isset($errors) and $errors->has('term')) ? 'has-error' : ''; ?>"

											 style="margin-top: -10px;">

											<label class="col-md-4 control-label"></label>

											<div class="col-md-8">

												<div class="termbox mb10">

													<label class="checkbox-inline" for="term">

														<input name="term" id="term" value="1" type="checkbox" {{ (old('term')=='1') ? 'checked="checked"' : '' }}>

														{!! t('I have read and agree to the <a :attributes>Terms & Conditions</a>', ['attributes' => getUrlPageByType('terms')]) !!}

													</label>

												</div>

												<div style="clear:both"></div>

											</div>

										</div>



										<!-- Button  -->

										<div class="form-group">

											<label class="col-md-4 control-label"></label>

											<div class="col-md-6">

												<button id="signupBtn" class="btn btn-success btn-lg"> {{ t('Register') }} </button>

											</div>

										</div>



										<div style="margin-bottom: 30px;"></div>



									</fieldset>

								</form>

							</div>

						</div>

					</div>

				</div>



				<div class="col-md-4 reg-sidebar">

					<div class="reg-sidebar-inner text-center">

						<div class="promo-text-box"><i class="icon-picture fa fa-4x icon-color-1"></i>

							<h3><strong>{{ t('Post a Free Classified') }}</strong></h3>

							<p>

							   <?php

							   $sstinglanguage = t('Do you have something to sell, buy, exchange or just give it away? Post it on our website, its free, for local business and very easy to use!');

							 //  $sstinglanguage =  str_replace("global.","",$sstinglanguage);

							   ?> 

								{{ $sstinglanguage }}

							</p>

						</div>

						<div class="promo-text-box"><i class=" icon-pencil-circled fa fa-4x icon-color-2"></i>

							<h3><strong>{{ t('Create and Manage Items') }}</strong></h3>

							<p>{{ t('Become a best seller or buyer. Create and Manage your ads. Repost your old ads, etc.') }}</p>

						</div>

						<div class="promo-text-box"><i class="icon-heart-2 fa fa-4x icon-color-3"></i>

							<h3><strong>{{ t('Create your Favorite ads list.') }}</strong></h3>

							<p>{{ t('Create your Favorite ads list, and save your searches. Don\'t forget any deal!') }}</p>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

 

@endsection



@section('after_scripts')



 	<script src="https://code.jquery.com/jquery-1.9.1.js"></script>

  	<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

  	<script type="text/javascript">

        function initialize() {

            var input = document.getElementById('city');

            var options = {

            types:['(regions)'],

                componentRestrictions: {country: "{{config('country.icode')}}"}

            };

            var autocomplete = new google.maps.places.Autocomplete(input, options);

            

        }

    

        google.maps.event.addDomListener(window, 'load', initialize);

    </script>

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key={{ config('services.GoogleMaps.key') }}&libraries=places&callback=initialize"

         async defer></script>



    <script src="{{ url('public/assets/js/jquery.validate.min.js') }}"></script>

	<script>

		$(function() {  

			$("#signupForm").validate({

			   rules : {

		                password : {

		                    minlength : 5

		                },

		                password_confirmation : {

		                    minlength : 5,

		                    equalTo : "#password"

		                }

		        },

				messages: {

					first_name: "Please enter your first name",

				    password : {

		                    minlength : '{{ t('Please enter at least 5 characters') }}'

	                },

				    password_confirmation: {

				        minlength : '{{ t('Please enter at least 5 characters') }}',

				        equalTo : "{{ t('Please enter the same value again') }}"

    				},

    				email:"{{ t('please enter a valid email address') }}",

				},

				



				submitHandler: function(form) {

					form.submit();

				}

			});

		});



		$(document).ready(function () {

			/* Submit Form */

			$("#signupBtn").click(function () {

				$("#signupForm").submit();

				return false;

			});

		});

							

	</script>

	<script>

	$("#dob").datepicker({dateFormat: 'yy-mm-dd',changeMonth:true,changeYear:true,yearRange:'1950:2018' });

	     /*$("#dob").click(function(){

	          $(this).datepicker({dateFormat: 'yy-mm-dd' }); 

	       });*/

	</script>



@endsection