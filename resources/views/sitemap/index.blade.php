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

@section('search')
	@parent
@endsection

@section('content')
	@include('common.spacer')
	<div class="main-container inner-page">
		<div class="container">
			<div class="section-content">
				<div class="row">

					@if (Session::has('message'))
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							{{ session('message') }}
						</div>
					@endif

					@if (Session::has('flash_notification'))
						<div class="container" style="margin-bottom: -10px; margin-top: -10px;">
							<div class="row">
								<div class="col-lg-12">
									@include('flash::message')
								</div>
							</div>
						</div>
					@endif
					
					@include('home.inc.spacer')
					<h1 class="text-center"><strong>{{ t('Sitemap') }}</strong></h1>
				 
					
					<div class="container">
						<div class="col-lg-12 content-box layout-section">
							<div class="row row-featured row-featured-category">
								
                                
                                
					<div class="inner-box category-content">
                        <div class="col-lg-12 box-title no-border" style="background-color: #ff5555 ; border-radius: 40px;margin:7px">
                        
                        <h2 style="color: #fff"><i class="icon-docs"></i> 
						{{ t('List of Categories and Sub-categories') }}</h2> 
                        <br />
                    </div>
                    
                                
                                
                                
                                
                                
								
								<div style="clear: both;"></div>
								
								<div class="list-categories-children styled">
									@foreach ($cats as $key => $col)
										<div class="col-md-4 col-sm-4 {{ (count($cats) == $key+1) ? 'last-column' : '' }}" style="padding-top: 16px;">
											@foreach ($col as $iCat)
												<div class="cat-list">
													<h3 class="cat-title rounded">
														<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $iCat->slug]; ?>
														<a href="{{ lurl(trans('routes.v-search-cat', $attr), $attr) }}">
															<i class="{{ $iCat->icon_class or 'icon-ok' }}"></i>
															{{ $iCat->name }} <span class="count"></span>
														</a>
														<span data-target=".cat-id-{{ $iCat->position }}" data-toggle="collapse" class="btn-cat-collapsed collapsed">
															<span class="icon-down-open-big"></span>
														</span>
													</h3>
													<ul class="cat-collapse collapse in cat-id-{{ $iCat->position }} long-list-home">
														@if (isset($subCats) and $subCats->has($iCat->tid))
															@foreach ($subCats->get($iCat->tid) as $iSubCat)
																<li>
																	<?php $attr =  ['countryCode' => config('country.icode'), 'catSlug' => $iCat->slug, 'subCatSlug' => $iSubCat->slug]; ?>
																	<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}">
																		{{ $iSubCat->name }}
																	</a>
																</li>
															@endforeach
														@endif
													</ul>
												</div>
											@endforeach
										</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>

				

				</div>
				@include('layouts.inc.social.horizontal')
			</div>
		</div>
	</div>
@endsection

@section('before_scripts')
	@parent
	<script>
		var maxSubCats = 15;
	</script>
@endsection
