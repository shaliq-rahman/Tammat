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
<link rel="shortcut icon" href="{{ \Storage::url(config('settings.app.favicon')) . getPictureVersion() }}">
	@if (Session::has('flash_notification'))		
			@include('common.spacer')
			<?php $paddingTopExists = true; ?>
		
        
        	<div class="container">
				<div class="row">
					<div class="col-lg-6 pstn_clss pstn_clss_h" >
						@include('flash::message')
					</div>
				</div>
			</div> 
        @else        
        	@include('common.spacer')
			<?php $paddingTopExists = true; ?>
		
        
        	<div class="container">
				<div class="row">
					<div class="col-lg-6 pstn_clss pstn_clss_h" >
						 
					</div>
				</div>
			</div> 
            
		@endif
        


@section('search')

	@parent

@endsection

@section('content')

	<div class="main-container" id="homepage">
	
		
		
		@if (isset($sections) and $sections->count() > 0)
			@foreach($sections as $section)
				@if (view()->exists($section->view))
				    @if($section->view != 'home.inc.locations' && $section->view != 'home.inc.featured')
				        @include($section->view, ['firstSection' => $loop->first])
			        @endif
				@endif
			@endforeach
		@endif
		
	</div>
@endsection

@section('after_scripts')
@endsection
