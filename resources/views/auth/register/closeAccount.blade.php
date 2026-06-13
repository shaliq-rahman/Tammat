

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

						<h2 class="title-2"><strong> <i class="icon-user-add"></i>Close Account</strong></h2>

						<div class="row">
 		

							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

								<form id="signupForm" class="form-horizontal" method="POST" action="{{ url()->current() }}">

									{!! csrf_field() !!}

									<fieldset>
   

								       
									

									

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

									 

										

										<!-- password -->

										<div class="form-group required <?php echo (isset($errors) and $errors->has('password')) ? 'has-error' : ''; ?>">

											<label class="col-md-4 control-label" for="password">{{ t('Password') }} <sup>*</sup></label>

											<div class="col-md-6">

												<input id="password" name="password" type="password" class="form-control" placeholder="{{ t('Password') }}">

												 

											</div>

										</div>



										 


									    <!-- Newsteller -->

									    <div class="form-group required <?php echo (isset($errors) and $errors->has('close_account_confirmation')) ? 'has-error' : ''; ?>"

											 style="margin-top: -10px;">

											<label class="col-md-4 control-label"></label>

											<div class="col-md-8">

												<div class="termbox mb10">

													<label class="checkbox-inline" for="close_account_confirmation">

														<input name="close_account_confirmation" id="close_account_confirmation" value="1" type="checkbox" {{ (old('close_account_confirmation')=='1') ? 'checked="checked"' : '' }}>

														Close Account

													</label>

												</div>

												<div style="clear:both"></div>

											</div>

										</div>

										 



										<!-- Button  -->

										<div class="form-group">

											<label class="col-md-4 control-label"></label>

											<div class="col-md-6">

												<button id="signupBtn" class="btn btn-success btn-lg"> Close Account </button>

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