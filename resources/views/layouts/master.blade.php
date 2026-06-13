{{-- * LaraClassified - Geo Classified Ads CMS * Copyright (c) BedigitCom. All Rights Reserved * * Website: http://www.bedigit.com

* * LICENSE * ------- * This software is furnished under a license and may be used and copied * only in accordance with the

terms of such license and with the inclusion * of the above copyright notice. If you Purchased from Codecanyon, * Please

read the full License from here - http://codecanyon.net/licenses/standard --}}

<?php

	$fullUrl = url(\Illuminate\Support\Facades\Request::getRequestUri());

	$detectAdsBlockerPlugin = load_installed_plugin('detectadsblocker');

?>

<!DOCTYPE html>

<html lang="{{ config('app.locale') }}" {!! (config( 'lang.direction')=='rtl' ) ? ' dir="rtl"' : '' !!}>



<head>

	<meta charset="utf-8">

	<meta name="csrf-token" content="{{ csrf_token() }}"> @include('common.meta-robots')

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta name="apple-mobile-web-app-title" content="{{ config('settings.app.name') }}">

	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ \Storage::url('app/default/ico/apple-touch-icon-144-precomposed.png') . getPictureVersion() }}">

	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{ \Storage::url('app/default/ico/apple-touch-icon-114-precomposed.png') . getPictureVersion() }}">

	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ \Storage::url('app/default/ico/apple-touch-icon-72-precomposed.png') . getPictureVersion() }}">

	<link rel="apple-touch-icon-precomposed" href="{{ \Storage::url('app/default/ico/apple-touch-icon-57-precomposed.png') . getPictureVersion() }}">

	<link rel="shortcut icon" href="{{ \Storage::url(config('settings.app.favicon')) . getPictureVersion() }}">

	<title>{{ MetaTag::get('title') }}</title>



	{!! MetaTag::tag('description') !!}{!! MetaTag::tag('keywords') !!}

	<link rel="canonical" href="{{ $fullUrl }}" /> @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties) @if (strtolower($localeCode) != strtolower(config('app.locale')))

	<link rel="alternate" href="{{ LaravelLocalization::getLocalizedURL($localeCode) }}" hreflang="{{ strtolower($localeCode) }}"

	/> @endif @endforeach @if (count($dnsPrefetch) > 0) 

	

	@foreach($dnsPrefetch as $dns)

	<link rel="dns-prefetch" href="//{{ $dns }}"> 

	@endforeach @endif @if (isset($post)) 

	

	@if (isVerifiedPost($post)) @if (config('services.facebook.client_id'))

	<meta property="fb:app_id" content="{{ config('services.facebook.client_id') }}" /> 

	@endif 

	

	{!! $og->renderTags() !!} {!! MetaTag::twitterCard() !!} @endif @else @if (config('services.facebook.client_id'))

	<meta property="fb:app_id" content="{{ config('services.facebook.client_id') }}" /> @endif {!! $og->renderTags() !!} {!! MetaTag::twitterCard() !!} @endif @include('feed::links') @if (config('settings.seo.google_site_verification'))

	<meta name="google-site-verification" content="{{ config('settings.seo.google_site_verification') }}" /> @endif @if (config('settings.seo.msvalidate'))

	<meta name="msvalidate.01" content="{{ config('settings.seo.msvalidate') }}" /> @endif @if (config('settings.seo.alexa_verify_id'))

	<meta name="alexaVerifyID" content="{{ config('settings.seo.alexa_verify_id') }}" /> @endif @yield('before_styles') @if (config('lang.direction') == 'rtl')

	<link href="https://fonts.googleapis.com/css?family=Cairo|Changa" rel="stylesheet">

	<link href="{{ url(mix('css/app.rtl.css')) }}" rel="stylesheet"> @else

	<link href="{{ url(mix('css/app.css')) }}" rel="stylesheet"> @endif @if (isset($detectAdsBlockerPlugin) and !empty($detectAdsBlockerPlugin))

	<link href="{{ url('assets/detectadsblocker/css/style.css') . getPictureVersion() }}" rel="stylesheet"> @endif @include('layouts.inc.tools.style')



	<link href="{{ url('css/custom.css') . getPictureVersion() }}" rel="stylesheet">



	<link href="{{asset('css/responsive.css')}}?v=2" rel="stylesheet"> 

	

	@yield('after_styles') 

	@if (isset($installedPlugins) and count($installedPlugins) > 0)

	@foreach($installedPlugins as $pluginName)

	@yield($pluginName . '_styles') 

	@endforeach 

	@endif 

	

	@if (config('settings.style.custom_css')) {!! printCss(config('settings.style.custom_css')) . "\n" !!} @endif 

	@if (config('settings.other.js_code')) {!! printJs(config('settings.other.js_code')) . "\n" !!} @endif

 @if (config('settings.security.recaptcha_activation'))
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
 @endif

 
  



	<!--[if lt IE 9]>

	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

	<![endif]-->



	<script>

		paceOptions = {

			elements: true

		};

	</script>

	<script src="{{ url('assets/js/pace.min.js') }}"></script>

	<link rel="stylesheet" href="{{url('css/font-awesome.css')}}">

	

</head>



<body class="{{ config('app.skin') }}">



	

 

    

	<div id="wrapper header_bg">



		@section('header') 

        @include('layouts.inc.header') 

        @show @section('search') 

        @show @section('wizard') 

        @show 

        

       <?php /*?> @if (isset($siteCountryInfo))

        

		<div class="h-spacer"></div>

		<div class="container">

			<div class="row">

				<div class="col-lg-12">

					<div class="alert alert-warning  col-lg-6 pstn_clss">

						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

						{!! $siteCountryInfo !!}

					</div>

				</div>

			</div>

		</div>

		@endif<?php */?>

        

         @yield('content') @section('info') @show @section('footer') @include('layouts.inc.footer') @show



	</div>



	@section('modal_location') @show @section('modal_abuse') @show @section('modal_message') @show @section('make_an_offer')

	@show @section('add_more') @show @section('update_offer') @show @includeWhen(!auth()->check(), 'layouts.inc.modal.login')

	@include('layouts.inc.modal.change-country') @include('cookie-consent::index') @if (isset($detectAdsBlockerPlugin) and !empty($detectAdsBlockerPlugin))

	@if (view()->exists('detectadsblocker::modal')) @include('detectadsblocker::modal') @endif @endif



	<script>

		/* Init.Root Vars */

		var siteUrl = "<?php echo url((!currentLocaleShouldBeHiddenInUrl() ? config('app.locale') : '' ).'/'); ?>";

		

		

		

		

		var languageCode = "<?php echo config('app.locale'); ?>";

		var countryCode = "<?php echo config('country.code ', 0); ?>";

		var timerNewMessagesChecking = "<?php echo (int)config('settings.other.timer_new_messages_checking', 0); ?>" ;



		/* Init.Translation Vars */

		var langLayout = {

			'hideMaxListItems': {

				'moreText': "{{ t('View More') }}",

				'lessText': "{{ t('View Less') }}"

			},

			'select2': {

				errorLoading: function() {

					return "{!! t('The results could not be loaded.') !!}"

				},

				inputTooLong: function(e) {

					var t = e.input.length - e.maximum,

						n = {!!t('Please delete #t character') !!};

					return t != 1 && (n += 's'), n

				},

				inputTooShort: function(e) {

					var t = e.minimum - e.input.length,

						n = {!!t('Please enter #t or more characters') !!};

					return n

				},

				loadingMore: function() {

					return "{!! t('Loading more results…') !!}"

				},

				maximumSelected: function(e) {

					var t = {!!t('You can only select #max item') !!};

					return e.maximum != 1 && (t += 's'), t

				},

				noResults: function() {

					return "{!! t('No results found') !!}"

				},

				searching: function() {

					return "{!! t('Searching…') !!}"

				}

			}

		};

	</script>



	@yield('before_scripts')

	<script src="{{ url(mix('js/app.js')) }}"></script>

	@if (file_exists(public_path() . '/assets/plugins/select2/js/i18n/'.config('app.locale').'.js'))

	<script src="{{ url('assets/plugins/select2/js/i18n/'.config('app.locale').'.js') }}"></script>

	@endif @if (isset($detectAdsBlockerPlugin) and !empty($detectAdsBlockerPlugin))

	<script src="{{ url('assets/detectadsblocker/js/script.js') . getPictureVersion() }}"></script>

	@endif



	<style>

	.autocompletesearch {

		top: 102%;

		left: 0;

		right: 0;

		background-color: #fff;

		position: absolute;

		z-index: 1000;

		border-radius: 2px;

		border-top: 1px solid #d9d9d9;

		font-family: Arial,sans-serif;

		box-shadow: 0 2px 6px rgba(0,0,0,0.3);

		-moz-box-sizing: border-box;

		-webkit-box-sizing: border-box;

		box-sizing: border-box;

		overflow: hidden;

	}

	.autocompletesearch span {

		display: block;

		border-bottom: 1px solid gray;

		padding: 0 15px;

		margin: 4px;

		cursor: pointer;

	}

	</style>



	<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.2/jquery.ui.touch-punch.min.js"></script>

	

	<script>

		$(document).ready(function() {

		    $('#borrom_carousel').carousel({

                      interval: 3000

            })





			/* Search AutoComplete */

			$('input[name="q"]').attr('autocomplete', 'off').parent().append(`<div style="display:none" class="autocompletesearch"></div>`);

			$('input[name="q"], input[name="qqq"]').on('keyup', function(){

				var q = $(this).val();

				if (q.length <= 2){

					return false;

				}

				var url = $(this).attr('name') == 'q' ? "/autocomplete?q="+q : "{{url()->current()}}?qqq="+q;

				$.ajax({

					url: url,

				}).done(function(data) {

					if (!data.length){

						$('.autocompletesearch').fadeOut().html('');

						return false;

					}

					var spans = '';

					data.forEach(function(arr){

						spans += '<span>'+arr+'</span>';

					})

					$('.autocompletesearch').fadeIn().html(spans);

				});

			});

			$(document).on('click', '.autocompletesearch span', function(){

				$('input[name="q"], input[name="qqq"]').val($(this).text());

				$('.autocompletesearch').fadeOut().html('');

			})

			$(document).mouseup(function(e) {

				var container = $('.autocompletesearch, input[name="q"], input[name="qqq"]');

				// if the target of the click isn't the container nor a descendant of the container

				if (!container.is(e.target) && container.has(e.target).length === 0) 

				{

					$('.autocompletesearch').hide();

				}

			});

			/* Search AutoComplete */

			

			

			/* Select Boxes */

			$('.selecter').select2({

				language: langLayout.select2,

				dropdownAutoWidth: 'true',

				minimumResultsForSearch: Infinity,

				width: '100%'

			});



			/* Searchable Select Boxes */

			$('.sselecter').select2({

				language: langLayout.select2,

				dropdownAutoWidth: 'true',

				width: '100%'

			});



			/* Social Share */

			$('.share').ShareLink({

				title: '{{ addslashes(MetaTag::get('

				title ')) }}',

				text: '{!! addslashes(MetaTag::get('

				title ')) !!}',

				url: '{!! $fullUrl !!}',

				width: 640,

				height: 480

			});



			/* Modal Login */

			@if(isset($errors) and $errors->any())

			@if($errors->any() and old('quickLoginForm') == '1')

			$('#quickLogin').modal();

			@endif

			@endif

		});

	</script>



	@yield('after_scripts') @if (isset($installedPlugins) and count($installedPlugins) > 0) @foreach($installedPlugins as $pluginName)

	@yield($pluginName . '_scripts') @endforeach @endif @if (config('settings.footer.tracking_code')) {!! printJs(config('settings.footer.tracking_code'))

	. "\n" !!} @endif

</body>



</html>