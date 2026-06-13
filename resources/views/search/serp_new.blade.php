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
	$fullUrl = url(\Illuminate\Support\Facades\Request::getRequestUri());
	$tmpExplode = explode('?', $fullUrl);
	$fullUrlNoParams = current($tmpExplode);
?>
@extends('layouts.master')

<?php /*?>@section('search')
	@parent
	@include('search.inc.form')
@endsection<?php */?>

@section('content')
	<div class="main-container" style="margin-top: 6px;">
 
        
        	<div class="container" style="padding-bottom: 2%;">
			<div class="row">
            
           
        
        <div class="panel panel-default">

							 
                                <div class="col-md-4" align="center" style="padding-top: 10px;">
                                <h3 class="no-padding text-center-480 useradmin">
									
                                    
                                    <a href="">
										@if (!empty($user->profile_image))

											<img class="userImg" src="<?= url('ProfilePictures/'.$user->profile_image.'') ?>" alt="user">&nbsp;                                           

										@else
											<img class="userImg" src="{{ url('images/user.jpg') }}" alt="user">
										@endif
										
									</a>
                                    
                                    <h1 align="center">{{ $user->username }}</h1>
                                    
                                    
                                    @if (auth()->check())
										    @if (auth()->user()->id != $user->id)
        										<a class="btn btn-sm make-favorite-user btn-success" id="{{ $user->id }}">
        												@if (\App\Models\SavedUser::where('user_id', auth()->user()->id)->where('fav_user_id', $user->id)->count() > 0)
        													<i class="fa fa-heart tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Remove favorite') }}"></i>
                                                            {{ t('Remove favorite') }}
        												@else
        													<i class="fa fa-heart" class="tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Save ad') }}"></i>
                                                            {{ t('Add to Favorites') }}
        												@endif
        											 
        										</a>
										    @endif
										@else
        									<a class="newbtn make-favorite-user btn btn-primary btn-block showphone" id="{{ $user->id }}">
    										<i class="fa fa-heart" class="tooltipHere" data-toggle="tooltip" data-original-title="{{ t('Save ad') }}"></i>
        										{{ t('Add to Favorites') }}
        									</a>
										@endif
                                    
								</h3>
                                </div>
                            <div class="col-md-8">
                            
								<div class="panel-collapse collapse {{ (old('panel')=='' or old('panel')=='userPanel') ? 'in' : '' }}" id="userPanel">
									<div class="panel-body">                                           
                                            
                                            
                                             
                                            <!-- email -->
                                            <div class="form-group required">
                                                <div class="col-sm-9">
                                                
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="icon-mail"></i>
                                                     @if($user->email_hidden==1)
                                                     {{ old('email', $user->email) }}
                                                     @else
                                                     *******************************
                                                     @endif
                                                     </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                               <div class="form-group required">
                                                <div class="col-sm-9">
                                                
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="icon-location"></i>
                                                     {{$user->city}}
                                                     </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                             <div class="form-group required">
                                                <div class="col-sm-9">
                                                
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="icon-clock"></i>
                                                     {{$user->created_at}}
                                                     </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group required">
                                               <div class="col-sm-9">
                                                  <div class="input-group">
                                                  <span id="phoneCountry" class="input-group-addon">  
                                                    <i class="glyphicon glyphicon-phone"></i>
                                                   
                                                  @if($user->phone_hidden==1) 
                                                  {!! getPhoneIcon(old('country_code', $user->country_code)) !!}                                                  
												  {{ phoneFormat(old('phone_number', $user->phone), old('country_code', $user->country_code)) }}
                                                   @else
                                                   **********************
                                                   @endif 
                                                  </span>
                                                  </div>
                                                </div>
											</div>
                                           
                                            <!-- country_code -->

                                            <div class="form-group required" style="display:none">
                                            <div class="col-md-9">
                                            <span id="phoneCountry" class="input-group-addon">
                                                  {!! getPhoneIcon(old('country_code', $user->country_code)) !!}
                                            
                                            @foreach ($countries as $item)
                                            {{ (old('country_code', $user->country_code)==$item->get('code')) ? $item->get('name') : '' }} 
                                            @endforeach
                                            {{ old('city', $user->city) }}
                                            {{ old('address', $user->address) }}
                                            </span>
                                            </div>
                                            </div>
                                            
                                      </div>

								</div>

							</div>
                            
                            </div>
                            
                            
                        </div>
                        </div>    
                            
                            
                            
                            
                            
		
		<div class="container">
			<div class="row">
            <?php $contentColSm = 'col-sm-12'; ?>
                 <!-- Content2222 -->
				<div class="col-sm-12 page-content col-thin-left">
					<div class="category-list">
					    <a style="font-size:40px;"></a>
						<div class="tab-box">

							<!-- Nav tabs -->
							
						
							@if (config('settings.listing.left_sidebar'))
								<!-- Mobile Filter bar -->
								
								 
								<!-- Mobile Filter bar End-->
								
								<?php
									$tabFilterHideXs = 'hide-xs';
									$listingFilterHiddenXs = 'hidden-xs';
								?>
						        @else
								<?php
									$tabFilterHideXs = '';
									$listingFilterHiddenXs = '';
								?>
							@endif
							
							
							

						</div>

						<div class="pt-30 listing-filter {{ $listingFilterHiddenXs }}">
							<div class="pull-left col-sm-10 col-xs-12">
								<div class="breadcrumb-list text-center-xs">
									{!! (isset($htmlTitle)) ? $htmlTitle : '' !!}
								</div>
                                <div style="clear:both;"></div>
							</div>
                            
							@if ($paginator->getCollection()->count() > 0)
								<div class="pull-right col-xs-2 text-right listing-view-action">
									<span class="list-view"><i class="icon-th"></i></span>
									<span class="compact-view"><i class="icon-th-list"></i></span>
									<span class="grid-view active"><i class="icon-th-large"></i></span>
								</div>
							@endif

							<div style="clear:both"></div>
						</div>

						<div class="adds-wrapper{{ ($contentColSm == 'col-sm-12') ? ' noSideBar' : '' }}">
							@include('search.inc.posts')
						</div>

						<div class="tab-box save-search-bar text-center">
							@if (Request::filled('q') and Request::get('q') != '' and $count->get('all') > 0)
								<a name="{!! qsurl($fullUrlNoParams, Request::except(['_token', 'location'])) !!}" id="saveSearch"
								   count="{{ $count->get('all') }}">
									<i class="icon-star-empty"></i> {{ t('Save Search') }}
								</a>
							@else
								<a href="#"> &nbsp; </a>
							@endif
						</div>
					</div>

					<div class="pagination-bar text-center">
						{!! $paginator->appends(request()->query())->render() !!}
					</div>

					<!--<div class="post-promo text-center" style="margin-bottom: 30px;">
						<h2> {{ t('Do have anything to sell or rent?') }} </h2>
						<h5>{{ t('Sell your products and services online FOR FREE. It\'s easier than you think !') }}</h5>
						@if (!auth()->check() and config('settings.single.guests_can_post_ads') != '1')
							<a href="#quickLogin" class="btn btn-border btn-post btn-add-listing" data-toggle="modal">{{ t('Start Now!') }}</a>
						@else
							<a href="{{ lurl('posts/create') }}" class="btn btn-border btn-post btn-add-listing">{{ t('Start Now!') }}</a>
						@endif
					</div>-->

				</div>
				
				<div style="clear:both;"></div>

				<!-- Advertising -->
				@include('layouts.inc.advertising.bottom')

			</div>
		</div>
	</div>
@endsection

@section('modal_location')
	@include('layouts.inc.modal.location')
@endsection

@section('after_scripts')
	<script>
		$(document).ready(function () {
			$('#postType a').click(function (e) {
				e.preventDefault();
				var goToUrl = $(this).attr('href');
				redirect(goToUrl);
			});
			$('#orderBy').change(function () {
				var goToUrl = $(this).val();
				redirect(goToUrl);
			});
			
			
			$('#ShowPhoto').change(function () {
				var goToUrl = $(this).val();
				redirect(goToUrl);
			});
			
			
			
			
			$('#orderBySideBar').change(function () {
				var goToUrl = $(this).val();
				redirect(goToUrl);
			});
			
			
		});
	</script>
@endsection
