

@extends('layouts.master')







@section('content')



	@include('common.spacer')



	<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />



	<div class="main-container">



		<div class="container">



			<div class="row">



				<div class="col-sm-3 page-sidebar">



					@include('account.inc.sidebar')



				</div>



				<!--/.page-sidebar-->







				<div class="col-sm-9 page-content">







					@include('flash::message')







					@if (isset($errors) and $errors->any())



						<div class="alert alert-danger">



							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>



							<h5><strong>{{ t('Oops ! An error has occurred. Please correct the red fields in the form') }}</strong></h5>



							<ul class="list list-check">



								@foreach ($errors->all() as $error)



									<li>{{ $error }}</li>



								@endforeach



							</ul>



						</div>



					@endif







					<div class="inner-box">



						<div class="row">



							<div class="col-md-5 col-xs-4 col-xxs-12">



								<h3 class="no-padding text-center-480 useradmin">



									<a href="">



										@if (!empty($user->profile_image))



											<img class="userImg" src="<?= url('ProfilePictures/'.$user->profile_image.'') ?>" alt="user">&nbsp;

                                           



										@else

                                          <img class="userImg" src="{{ url('images/user.jpg') }}" alt="user">

											



										@endif



										{{ $user->username }}



									</a>



								</h3>



							</div>



							<div class="col-md-7 col-xs-8 col-xxs-12">



								<div class="header-data text-center-xs">



									<!-- Traffic data -->



									<div class="hdata">



										<div class="mcol-left">



											<!-- Icon with red background -->



											<i class="fa fa-eye ln-shadow"></i>



										</div>



										<div class="mcol-right">



											<!-- Number of visitors -->



											<p>



												<a href="{{ lurl('account/my-posts') }}">



													<?php $totalPostsVisits = (isset($countPostsVisits) and $countPostsVisits->total_visits) ? $countPostsVisits->total_visits : 0 ?>



													{{ \App\Helpers\Number::short($totalPostsVisits) }}



													<em>{{ trans_choice('global.count_visits', getPlural($totalPostsVisits)) }}</em>



												</a>



											</p>



										</div>



										<div class="clearfix"></div>



									</div>







									<!-- Ads data -->



									<div class="hdata">



										<div class="mcol-left">



											<!-- Icon with green background -->



											<i class="icon-th-thumb ln-shadow"></i>



										</div>



										<div class="mcol-right">



											<!-- Number of ads -->



											<p>



												<a href="{{ lurl('account/my-posts') }}">



													{{ \App\Helpers\Number::short($countPosts) }}



													<em>{{ trans_choice('global.count_posts', getPlural($countPosts)) }}</em>



												</a>



											</p>



										</div>



										<div class="clearfix"></div>



									</div>







									<!-- Favorites data -->



									<div class="hdata">



										<div class="mcol-left">



											<!-- Icon with blue background -->



											<i class="fa fa-user ln-shadow"></i>



										</div>



										<div class="mcol-right">



											<!-- Number of favorites -->



											<p>



												<a href="{{ lurl('account/favourite') }}">



													{{ \App\Helpers\Number::short($countFavoritePosts) }}



													<em>{{ trans_choice('global.count_favorites', getPlural($countFavoritePosts)) }} </em>



												</a>



											</p>



										</div>



										<div class="clearfix"></div>



									</div>



								</div>



							</div>



						</div>



					</div>







					<div class="inner-box">



						<div class="welcome-msg">



							<h3 class="page-sub-header2 clearfix no-padding">{{ t('Hello') }} {{ $user->username }} ! </h3>



							<span class="page-sub-header-sub small">



                                {{ t('You last logged in at') }}: {{ $user->last_login_at->formatLocalized(config('settings.app.default_datetime_format')) }}



                            </span>



						</div>



						<div class="panel-group" id="accordion">



							<!-- USER -->



							<div class="panel panel-default">



								<div class="panel-heading">



									<h4 class="panel-title"><a href="#userPanel" data-toggle="collapse" data-parent="#accordion"> {{ t('My details') }} </a></h4>



								</div>



								<div class="panel-collapse collapse {{ (old('panel')=='' or old('panel')=='userPanel') ? 'in' : '' }}" id="userPanel">



									<div class="panel-body">



										<form name="details" class="form-horizontal" role="form" method="POST" action="{{ url()->current() }}"  enctype="multipart/form-data">



											{!! csrf_field() !!}



											<input name="_method" type="hidden" value="PUT">



											<input name="panel" type="hidden" value="userPanel">







											<!-- gender_id -->



											<div class="form-group required <?php echo (isset($errors) and $errors->has('gender_id')) ? 'has-error' : ''; ?>">



												<label class="col-md-3 control-label">{{ t('Gender') }}</label>



												<div class="col-md-9">



													@if ($genders->count() > 0)



                                                        @foreach ($genders as $gender)



                                                            <label class="radio-inline" for="gender_id">



                                                                <input required name="gender_id" id="gender_id-{{ $gender->tid }}" value="{{ $gender->tid }}"



                                                                       type="radio" {{ (old('gender_id', $user->gender_id)==$gender->tid) ? 'checked="checked"' : '' }}>



                                                                {{ $gender->name }}



                                                            </label>



                                                        @endforeach



													@endif



												</div>



											</div>



												



											<!-- name -->



											<!--<div class="form-group required <?php echo (isset($errors) and $errors->has('name')) ? 'has-error' : ''; ?>">



												<label class="col-sm-3 control-label">{{ t('Name') }} <sup>*</sup></label>



												<div class="col-sm-9">



													<input name="name" type="text" class="form-control" placeholder="" value="{{ old('name', $user->name) }}">



												</div>



											</div>-->







											<div class="form-group required <?php echo (isset($errors) and $errors->has('first_name')) ? 'has-error' : ''; ?>">



												<label class="col-sm-3 control-label">{{ t('First Name') }} <sup>*</sup></label>



												<div class="col-sm-9">



													<input name="first_name" type="text" class="form-control" placeholder="" value="{{ old('first_name', $user->first_name) }}">



												</div>



											</div>







											<div class="form-group required <?php echo (isset($errors) and $errors->has('last_name')) ? 'has-error' : ''; ?>">



												<label class="col-sm-3 control-label">{{ t('Last Name') }} <sup>*</sup></label>



												<div class="col-sm-9">



													<input name="last_name" type="text" class="form-control" placeholder="" value="{{ old('last_name', $user->last_name) }}">



												</div>



											</div>



											



											<!-- username -->



											<div class="form-group required <?php echo (isset($errors) and $errors->has('username')) ? 'has-error' : ''; ?>">



												<label class="col-sm-3 control-label" for="email">{{ t('Username') }} <sup>*</sup></label>



												<div class="col-sm-9">



													<div class="input-group">



														<span class="input-group-addon"><i class="icon-user"></i></span>



														<input id="username" readonly type="text" class="form-control" placeholder="{{ t('Username') }}"



															   value="{{ old('username', $user->username) }}">



													</div>



												</div>



											</div>



												



											<!-- email -->



											<div class="form-group required <?php echo (isset($errors) and $errors->has('email')) ? 'has-error' : ''; ?>">



												<label class="col-sm-3 control-label">{{ t('Email') }} <sup>*</sup></label>



												<div class="col-sm-9">



													<div class="input-group">



														<span class="input-group-addon"><i class="icon-mail"></i></span>



														<input id="email" name="email" type="email"  class="form-control" placeholder="{{ t('Email') }}" value="{{ old('email', $user->email) }}">



													</div>



												</div>



											</div>







											<!-- phone_number -->



                                            <div class="form-group required <?php echo (isset($errors) and $errors->has('phone_number')) ? 'has-error' : ''; ?>">



                                                <label for="phone_number" class="col-sm-3 control-label">{{ t('Mobile') }} <sup>*</sup></label>



                                            		<div class="col-sm-9">



                                            			<div class="input-group">



                                            				<span id="phoneCountry" class="input-group-addon">{!! getPhoneIcon(old('country_code', $user->country_code)) !!}</span>



																<input id="phone_number" name="phone_number" type="text" class="form-control" placeholder="" value="{{ phoneFormat(old('phone_number', $user->phone), old('country_code', $user->country_code)) }}">



                                            					<label class="input-group-addon" style="display:none">



                                            					    <input name="phone_hiddenxxxx" id="phoneHidden" type="checkbox" value="1" {{ (old('phone_hidden', $user->phone_hidden)=='1') ? 'checked="checked"' : '' }}> {{ t('Hide') }}



                                            					</label>



                                            		</div>



                                            	</div>



											</div>



																						



										<!-- Date of Birth -->



										<div class="form-group required <?php echo (isset($errors) and $errors->has('dob')) ? 'has-error' : '';?>">



												<label class="col-sm-3 control-label">{{ t('Dob') }} </label>



												<div class="col-sm-9">



													<div class="input-group">



														<input id="dob" name="dob" type="text" class="form-control" placeholder="{{ t('Dob') }}" value="{{ old('dob', $user->dob) }}">



													</div>



												</div>



										</div>



										        



                                            <!-- country_code -->



                                            <div class="form-group required <?php echo (isset($errors) and $errors->has('country_code')) ? 'has-error' : ''; ?>">



												<label class="col-md-3 control-label" for="country_code">{{ t('Your Country') }} <sup>*</sup></label>



												<div class="col-md-9">



													<select name="country_code" class="form-control sselecter">



														<option value="0" {{ (!old('country_code') or old('country_code')==0) ? 'selected="selected"' : '' }}>



															{{ t('Select a country') }}



														</option>



														@foreach ($countries as $item)



															<option value="{{ $item->get('code') }}" {{ (old('country_code', $user->country_code)==$item->get('code')) ? 'selected="selected"' : '' }}>



																{{ $item->get('name') }}



															</option>



														@endforeach



													</select>



												</div>



											</div>



                                           <!-- <input name="country_code" type="hidden" value="{{ $user->country_code }}"> -->











                                            <!--State-->



                                            <div class="form-group" style="<?php if(config('app.locale') == 'sq') { echo 'display:none;'; }  ?>">



                                                <label class="col-sm-3 control-label">{{ t('State') }} </label>



                                                <div class="col-sm-9">



                                                	<div class="input-group">



                                                	<input name="state" class="form-control" type="text" placeholder="{{ t('State') }}" value="{{ old('state', $user->state) }}">



                                                    </div>



                                                </div>



                                            </div>











                                            <!-- city -->



                                            <div class="form-group required <?php echo (isset($errors) and $errors->has('city')) ? 'has-error' : ''; ?>">



                                                <label class="col-sm-3 control-label">{{ t('City') }} <sup>*</sup></label>



                                                <div class="col-sm-9">



                                                	<div class="input-group">



                                                	<input name="city" class="form-control" id="city" type="text" placeholder="{{ t('City') }}" value="{{ old('city', $user->city) }}">



                                                    </div>



                                                </div>



                                            </div>







                                            <!--ZipCode-->



                                            <div class="form-group required <?php echo (isset($errors) and $errors->has('zipcode')) ? 'has-error' : ''; ?>">



                                                <label class="col-sm-3 control-label">{{ t('ZipCode') }} </label>



                                                <div class="col-sm-9">



                                                	<div class="input-group">



                                                	<input name="zipcode" class="form-control" type="text" placeholder="{{ t('ZipCode') }}" value="{{ old('zipcode', $user->zipcode) }}">



                                                    </div>



                                                </div>



                                            </div>







                                            <!-- region -->



                                            <div class="form-group required <?php echo (isset($errors) and $errors->has('address')) ? 'has-error' : ''; ?>">



                                                <label class="col-sm-3 control-label">{{ t('Address') }} </label>



                                                <div class="col-sm-9">



                                                	<div class="input-group">



                                                	<input name="address" class="form-control" type="text" placeholder="{{ t('Address') }} " value="{{ old('address', $user->address) }}">



                                                    </div>



                                                </div>



                                            </div>

                                            

                                            

                                            

                                            

                                            





											<!-- user_type_id -->



											<div class="form-group required <?php echo (isset($errors) and $errors->has('user_type_id')) ? 'has-error' : ''; ?>">



												<label class="col-sm-3 control-label">{{ t('User Type') }} <sup>*</sup></label>



												<div class="col-sm-9">



													<select name="user_type_id" id="userTypeId" class="form-control selecter">



														<option value="0"



																@if (old('user_type_id')=='' or old('user_type_id')==0)



																	selected="selected"



																@endif >



															{{ t('Select') }}



														</option>



														@foreach ($userTypes as $type)



															<option value="{{ $type->id }}"



																	@if (old('user_type_id', $user->user_type_id)==$type->id)



																		selected="selected"



																	@endif



															>



																{{ t($type->name) }}



															</option>



														@endforeach



													</select>



												</div>



											</div>





	                                        <div class="form-group required ">

                    							 

                    								<div style="clear:both"></div>

                    								<div class="col-md-6" style="padding: 5px;margin-top: 4px;width: 3%;">

                    								    <input name="phone_hidden" id="phone_hidden" value="1" @if($user->phone_hidden==1) checked @endif type="checkbox">

                    								     

                    								</div>

                    								<div class="col-md-6" style="padding: 0px;" id="ShowPhoneusingCheckbox">

                        								<div class="input-group">

                        									<span class="input-group-addon"><i class="icon-phone"></i>

                                                            {{t('Show my Phone to public')}}

                                                            </span>   

                                                        </div>

                    								</div>

                    								<div style="clear:both"></div>

                    						</div>

                                            

                                              <div class="form-group required ">

                    							 

                    								<div style="clear:both"></div>

                    								<div class="col-md-6" style="padding: 5px;margin-top: 4px;width: 3%;">

                    								    <input name="email_hidden" id="email_hidden" value="1" @if($user->email_hidden==1) checked @endif type="checkbox">

                    								     

                    								</div>

                    								<div class="col-md-6" style="padding: 0px;" id="ShowEmailusingCheckbox">

                        								<div class="input-group">

                        									<span class="input-group-addon"><i class="icon-mail"></i>

                                                            {{t('Show my Email to public')}}

                                                            </span>   

                                                        </div>

                    								</div>

                    								<div style="clear:both"></div>

                    						</div>

                                            

                                            

                                                <div class="form-group required">



                                                <label class="col-sm-3 control-label">Profile Picture</label>



                                                <div class="col-sm-9">



                                                	<div class="input-group">



                                                       <input type="file" name="profile_image" id="profile_image">



                                                    </div>



                                                </div>



                                            </div>

                                            

                                               









											<div class="form-group">



												<div class="col-sm-offset-3 col-sm-9"></div>



											</div>



											



											<!-- Button -->



											<div class="form-group">



												<div class="col-sm-offset-3 col-sm-9">



													<button type="submit" class="btn btn-primary">{{ t('Update') }}</button>



												</div>



											</div>



										</form>



									</div>



								</div>



							</div>



							



							<!-- SETTINGS -->



							<div class="panel panel-default" style="display:none">



								<div class="panel-heading">



									<h4 class="panel-title"><a href="#settingsPanel" data-toggle="collapse" data-parent="#accordion"> {{ t('Settings') }} </a></h4>



								</div>



								<div class="panel-collapse collapse {{ (old('panel')=='settingsPanel') ? 'in' : '' }}" id="settingsPanel">



									<div class="panel-body">



										<form name="settings" class="form-horizontal" role="form" method="POST" action="{{ lurl('account/settings') }}">



											{!! csrf_field() !!}



											<input name="_method" type="hidden" value="PUT">



											<input name="panel" type="hidden" value="settingsPanel">



											



											<!-- disable_comments -->



											<div class="form-group" style="display:none;">



												<div class="col-sm-12">



													<div class="checkbox">



														<label>



															<input id="disable_comments" name="disable_comments" value="1" type="checkbox" {{ ($user->disable_comments==1) ? 'checked' : '' }}>



															{{ t('Disable comments on my ads') }}



														</label>



													</div>



												</div>



											</div>



											



											<!-- password -->



											<div style="margin-top:10px;" class="form-group <?php echo (isset($errors) and $errors->has('password')) ? 'has-error' : ''; ?>">



												<label class="col-sm-3 control-label">{{ t('New Password') }}</label>



												<div class="col-sm-9">



													<input id="password" name="password" type="password" class="form-control" placeholder="{{ t('Password') }}">



												</div>



											</div>



											



											<!-- password_confirmation -->



											<div class="form-group <?php echo (isset($errors) and $errors->has('password')) ? 'has-error' : ''; ?>">



												<label class="col-sm-3 control-label">{{ t('Confirm Password') }}</label>



												<div class="col-sm-9">



													<input id="password_confirmation" name="password_confirmation" type="password"



														   class="form-control" placeholder="{{ t('Confirm Password') }}">



												</div>



											</div>



											



											<!-- Button -->



											<div class="form-group">



												<div class="col-sm-offset-3 col-sm-9">



													<button type="submit" class="btn btn-primary">{{ t('Update') }}</button>



												</div>



											</div>



										</form>



									</div>



								</div>



							</div>







						</div>



						<!--/.row-box End-->







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



<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>







 	<script type="text/javascript">



        function initialize() {



            var input = document.getElementById('city');



            var options = {



            types: ['(regions)'],



                componentRestrictions: {country: "{{config('country.icode')}}"}



            };



            var autocomplete = new google.maps.places.Autocomplete(input, options);



            



        }



    



        google.maps.event.addDomListener(window, 'load', initialize);



    </script>



    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.GoogleMaps.key') }}&libraries=places&callback=initialize"



         async defer></script>



         











<script>



	$("#dob").datepicker({dateFormat: 'yy-mm-dd',changeMonth:true,changeYear:true,yearRange:'1950:2018' });



	     /*$("#dob").click(function(){



	          $(this).datepicker({dateFormat: 'yy-mm-dd' }); 



	       });*/



	    



</script>



	



<style>



.input-group {width: 100%}



</style>



@endsection



