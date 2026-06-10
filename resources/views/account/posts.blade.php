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

				<div class="col-sm-3 page-sidebar">
					@include('account.inc.sidebar')
				</div>
				<!--/.page-sidebar-->

				<div class="col-sm-9 page-content">
					<div class="inner-box">
					
                    
                    
                    
                    
                      <div  style="background-color: #ff5555 ; border-radius: 40px;margin:7px;padding:12px 0px 0px 14px;">
                      
                    	@if ($pagePath=='my-posts')
							<h2 class="title-2" style="color: #fff"><i class="icon-docs"></i> {{ t('All ads') }} </h2>
						@elseif ($pagePath=='approved')
							<h2 class="title-2" style="color: #fff"><i class="icon-thumbs-up"></i> {{ t('Activated ads') }} </h2>
						@elseif ($pagePath=='archived')
							<h2 class="title-2" style="color: #fff"><i class="icon-folder-close"></i> {{ t('Archived ads') }} </h2>
						@elseif ($pagePath=='favourite')
							<h2 class="title-2" style="color: #fff"><i class="icon-heart-1"></i> {{ t('Favourite ads') }} </h2>
						@elseif ($pagePath=='pending-approval')
							<h2 class="title-2" style="color: #fff"><i class="icon-hourglass"></i> {{ t('Pending approval') }} </h2>
						@else
							<h2 class="title-2" style="color: #fff"><i class="icon-thumbs-down"></i> {{ t('Rejected ads') }} </h2>
						@endif
                        
                         </div>
                        
						
						<div class="table-responsive">
								<form name="listForm" method="GET" action="">
									<div class="table-search col-xs-12">
										<div class="form-group">
											<label class="col-sm-5 control-label text-right">{{ t('Search') }} <br>
												<a title="clear filter" class="clear-filterr" href="{{url()->current()}}">[{{ t('clear') }}]</a> </label>
											<div class="col-sm-5 searchpan">
												<input value="{{request()->qqq}}" type="text" name="qqq" class="form-control" id="filter">
												<div style="display:none" class="autocompletesearch"></div>
											</div>
											<label class="col-sm-2 control-label text-right">
												<button class="newbtn btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i> {{ t('Search') }}</button> </label>
										</div>
									</div>
								</form>
							<form name="listForm" method="POST" action="{{ lurl('account/'.$pagePath.'/delete') }}">
								{!! csrf_field() !!}
								<div class="table-action">
									<label for="checkAll">
										<input type="checkbox" id="checkAll">
										{{ t('Select') }}: {{ t('All') }}  
										<button type="submit" class="newbtn btn btn-sm btn-default delete-action">
											<i class="fa fa-trash"></i> {{ t('Delete') }}
										</button>
									</label>
									{{-- <div class="table-search pull-right col-xs-7">
										<div class="form-group">
											<label class="col-xs-5 control-label text-right">{{ t('Search') }} <br>
												<a title="clear filter" class="clear-filter" href="#clear">[{{ t('clear') }}]</a> </label>
											<div class="col-xs-7 searchpan">
												<input type="text" class="form-control" id="filter">
											</div>
										</div>
									</div> --}}
								</div>
								
								<table id="addManageTable" class="table table-striped table-bordered add-manage-table table demo" data-filter="#filter" data-filter-text-only="true">
									<thead>
									<tr>
										<th data-type="numeric" data-sort-initial="true">{{ t('Select') }}</th> 
										<th>{{ t('Photos') }}</th>
										<th data-sort-ignore="true">{{ t('Ads Details') }}</th>
										<th data-type="numeric">{{ t('Price') }}</th>
									@if ($pagePath=='my-posts')	<th>{{ t('Type') }}</th>@endif
										<th>{{ t('Option') }}</th>
									</tr>
									</thead>
									<tbody>

									<?php
									if (isset($posts) && $posts->count() > 0):
									foreach($posts as $key => $post):
										// Fixed 1
										if ($pagePath == 'favourite') {
											if (isset($post->post)) {
												if (!empty($post->post)) {
													$post = $post->post;
												} else {
													continue;
												}
											} else {
												continue;
											}
										}

										// Fixed 2
										if (!$countries->has($post->country_code)) continue;

										// Get Post's URL
										$attr = ['slug' => slugify($post->title), 'id' => $post->id];
										$postUrl = lurl($post->uri, $attr);
										if (in_array($pagePath, ['pending-approval', 'archived'])) {
											$postUrl = $postUrl . '?preview=1';
										}
                                    
                                    	// Get Post's Pictures
                                        if ($post->pictures->count() > 0) {
                                            $postImg = resize($post->pictures->get(0)->filename, 'medium');
                                        } else {
                                            $postImg = resize(config('larapen.core.picture.default'));
                                        }
                             
                                    	// Get country flag
                                    	$countryFlagPath = 'images/flags/16/' . strtolower($post->country_code) . '.png';
                                    	
            	      $getcurrencycountry = \DB::table('countries')
                            ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
                            ->select('currencies.*')
                            ->where('countries.code', '=', $post->country_code)
                            ->first();
           
           
           
									?>
									<tr>
										<td style="width:2%" class="add-img-selector">
											<div class="checkbox">
												<label><input type="checkbox" name="entries[]" value="{{ $post->id }}"></label>
											</div>
										</td>
										<td style="width:14%" class="add-img-td">
											<a href="{{ $postUrl }}"><img class="thumbnail img-responsive" src="{{ $postImg }}" alt="img"></a>
										</td>
										<td style="width:58%" class="ads-details-td">
											<div>
												<p>
													<strong>
                                                        <a href="{{ $postUrl }}" title="{{ $post->title }}">{{ str_limit($post->title, 40) }}</a>
                                                    </strong>
													@if (in_array($pagePath, ['my-posts','approved' ,'archived', 'pending-approval','rejected']))
														@if (isset($post->latestPayment) and !empty($post->latestPayment))
															@if (isset($post->latestPayment->package) and !empty($post->latestPayment->package))
																<?php
																if ($post->featured == 1) {
																	$color = $post->latestPayment->package->ribbon;
																	$packageInfo = '';
																} else {
																	$color = '#ddd';
																	$packageInfo = ' (' . t('Expired') . ')';
																}
																?>
																<i class="fa fa-check-circle tooltipHere" style="color: {{ $color }};" title="" data-placement="bottom"
																   data-toggle="tooltip" data-original-title="{{ $post->latestPayment->package->short_name . $packageInfo }}"></i>
															@endif
														@endif
													@endif
                                                </p>
												<p>
													<strong><i class="icon-clock" title="{{ t('Posted On') }}"></i></strong>&nbsp;
													{{ $post->created_at->formatLocalized(config('settings.app.default_datetime_format')) }}
												</p>
												
                                                @if ($pagePath =='favourite')
                                                <p>
													<strong><i class="icon-eye" title="{{ t('Visitors') }}"></i></strong> {{ $post->visits or 0 }}
													<strong><i class="fa fa-map-marker" title="{{ t('Located In') }}"></i></strong> {{ !empty($post->city) ? $post->city->name : '-' }}
													@if (file_exists(public_path($countryFlagPath)))
														<img src="{{ url($countryFlagPath) }}" data-toggle="tooltip" title="{{ $post->country_code }}">
													@endif
												</p>
                                                @endif
                                                
											</div>
										</td>
										<td style="width:23%" class="price-td">
											<div>
												<strong>
											    	{!! \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator) !!}
													<!--{!! \App\Helpers\Number::money($post->price) !!}-->
												</strong>
											</div>
										</td>
                                       @if ($pagePath=='my-posts')
										<td>
										    @if($post->reviewed == 1 && $post->archived == 0)
										        {{ __('global.Approved') }}
										    @elseif($post->archived == 1)
										        {{ __('global.Archived') }}
										    @elseif($post->is_rejected == 1)
										        {{ __('global.Rejected') }}
										    @else
										        {{ __('global.Pending') }}
										    @endif
                                         </td>
                                         @endif
										<td style="width:10%" class="action-td">
											<div>
												@if ($post->user_id==$user->id and $post->archived==0)
													<p>
                                                        <a  style="width: 80px;" class="newbtn btn btn-warning btn-sm" href="{{ lurl('posts/' . $post->id . '/edit') }}">
                                                            <i class="fa fa-edit"></i> {{ t('Edit') }}
                                                        </a>
                                                    </p>
                                                	@if ($pagePath=='pending-approval')
                                                        <p>
                                                            <a  class="newbtn btn btn-danger btn-sm delete-action" href="{{ lurl('account/'.$pagePath.'/'.$post->id.'/deletepost') }}">
                                                                <i class="fa fa-trash"></i> {{ t('Delete') }}
                                                            </a>
                                                        </p>
                                                    @else
                                                    <p>
                                                        <a  class="newbtn btn btn-danger btn-sm delete-action" href="{{ lurl('account/'.$pagePath.'/'.$post->id.'/deletepost') }}">
                                                            <i class="fa fa-trash"></i> {{ t('Delete') }}
                                                        </a>
                                                    </p>
                                                    @endif
												@endif

												@if ($post->archived==0 && $pagePath !='favourite' && $pagePath !='rejected')
													 
                                                     
                                                      @if($post->is_rejected == 1)
                                                      @else
                                                          <p>
                                                        
                                                            <a class="newbtn btn btn-success btn-sm"                                                             
                                                            href="{{ lurl('account/'.$pagePath.'/'.$post->id.'/archivepost') }}">
                                                             <i class="fa fa-compress"></i> {{ t('Archived') }} </a>
    
                                                          </p>
                                                     @endif

												@endif
                                                
                                                
                                                

												@if (isVerifiedPost($post) and $post->archived==0)
													<!--<p>
														<a class="newbtn btn btn-info btn-sm"> <i class="fa fa-mail-forward"></i> {{ t('Share') }} </a>
													</p>-->
													
												 


												@endif
												
												@if ($pagePath=='favourite')
												   <p>
                                                        <a  class="newbtn btn btn-danger btn-sm delete-action" href="{{ lurl('account/'.$pagePath.'/'.$post->id.'/deletepostfavourite') }}">
                                                            <i class="fa fa-trash"></i> <!--{{ t('Remove favorite') }}-->{{ t('Delete') }}
                                                        </a>
                                                    </p>
												@endif
												@if ($post->user_id==$user->id and $post->archived==1)
												
												@if ($pagePath=='archived' || ($post->user_id==$user->id and $post->archived==1))
													<p>
                                                        <a  class="newbtn btn btn-info btn-sm" href="{{ lurl('account/'.$pagePath.'/'.$post->id.'/repost') }}">
                                                            <i class="fa fa-recycle"></i> {{ t('Repost') }}
                                                        </a>
                                                    </p>
												@endif

                                                    <p>
                                                        <a  class="newbtn btn btn-danger btn-sm delete-action" href="{{ lurl('account/'.$pagePath.'/'.$post->id.'/deletepost') }}">
                                                            <i class="fa fa-trash"></i> {{ t('Delete') }} {{ t('post') }}
                                                        </a>
                                                    </p>
													<p>
                                                        <a  style="width: 80px;" class="newbtn btn btn-warning btn-sm" href="{{ lurl('posts/' . $post->id . '/edit') }}">
                                                            <i class="fa fa-edit"></i> {{ t('Edit') }}
                                                        </a>
                                                    </p>
												@endif

												
											</div>
										</td>
									</tr>
									<?php endforeach; ?>
                                    <?php endif; ?>
									</tbody>
								</table>
							</form>
						</div>
                            
                        <div class="pagination-bar text-center">
                            {{ (isset($posts)) ? $posts->links() : '' }}
                        </div>

					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('after_scripts')
	<script src="{{ url('assets/js/footable.js?v=2-0-1') }}" type="text/javascript"></script>
	<script src="{{ url('assets/js/footable.filter.js?v=2-0-1') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$(function () {
			$('#addManageTable').footable().bind('footable_filtering', function (e) {
				var selected = $('.filter-status').find(':selected').text();
				if (selected && selected.length > 0) {
					e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
					e.clear = !e.filter;
				}
			});

			$('.clear-filter').click(function (e) {
				e.preventDefault();
				$('.filter-status').val('');
				$('table.demo').trigger('footable_clear_filter');
			});

			$('#checkAll').click(function () {
				checkAll(this);
			});
			
			$('a.delete-action, button.delete-action').click(function(e)
			{
				e.preventDefault(); /* prevents the submit or reload */
				var confirmation = confirm("{{ t('Are you sure you want to perform this action?') }}");
				
				if (confirmation) {
					if( $(this).is('a') ){
						var url = $(this).attr('href');
						if (url !== 'undefined') {
							redirect(url);
						}
					} else {
						$('form[name=listForm]').submit();
					}
					
				}
				
				return false;
			});
		});
	</script>
	<!-- include custom script for ads table [select all checkbox]  -->
	<script>
		function checkAll(bx) {
			var chkinput = document.getElementsByTagName('input');
			for (var i = 0; i < chkinput.length; i++) {
				if (chkinput[i].type == 'checkbox') {
					chkinput[i].checked = bx.checked;
				}
			}
		}
	</script>
@endsection
