

<?php

	$fullUrl = url(\Illuminate\Support\Facades\Request::getRequestUri());

	$detectAdsBlockerPlugin = load_installed_plugin('detectadsblocker');

?>

<!DOCTYPE html>

<html lang="<?php echo e(config('app.locale')); ?>" <?php echo (config( 'lang.direction')=='rtl' ) ? ' dir="rtl"' : ''; ?>>



<head>

	<meta charset="utf-8">

	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"> <?php echo $__env->make('common.meta-robots', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta name="apple-mobile-web-app-title" content="<?php echo e(config('settings.app.name')); ?>">

	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo e(\Storage::url('app/default/ico/apple-touch-icon-144-precomposed.png') . getPictureVersion()); ?>">

	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo e(\Storage::url('app/default/ico/apple-touch-icon-114-precomposed.png') . getPictureVersion()); ?>">

	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo e(\Storage::url('app/default/ico/apple-touch-icon-72-precomposed.png') . getPictureVersion()); ?>">

	<link rel="apple-touch-icon-precomposed" href="<?php echo e(\Storage::url('app/default/ico/apple-touch-icon-57-precomposed.png') . getPictureVersion()); ?>">

	<link rel="shortcut icon" href="<?php echo e(\Storage::url(config('settings.app.favicon')) . getPictureVersion()); ?>">

	<title><?php echo e(MetaTag::get('title')); ?></title>



	<?php echo MetaTag::tag('description'); ?><?php echo MetaTag::tag('keywords'); ?>


	<link rel="canonical" href="<?php echo e($fullUrl); ?>" /> <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php if(strtolower($localeCode) != strtolower(config('app.locale'))): ?>

	<link rel="alternate" href="<?php echo e(LaravelLocalization::getLocalizedURL($localeCode)); ?>" hreflang="<?php echo e(strtolower($localeCode)); ?>"

	/> <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php if(count($dnsPrefetch) > 0): ?> 

	

	<?php $__currentLoopData = $dnsPrefetch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dns): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

	<link rel="dns-prefetch" href="//<?php echo e($dns); ?>"> 

	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endif; ?> <?php if(isset($post)): ?> 

	

	<?php if(isVerifiedPost($post)): ?> <?php if(config('services.facebook.client_id')): ?>

	<meta property="fb:app_id" content="<?php echo e(config('services.facebook.client_id')); ?>" /> 

	<?php endif; ?> 

	

	<?php echo $og->renderTags(); ?> <?php echo MetaTag::twitterCard(); ?> <?php endif; ?> <?php else: ?> <?php if(config('services.facebook.client_id')): ?>

	<meta property="fb:app_id" content="<?php echo e(config('services.facebook.client_id')); ?>" /> <?php endif; ?> <?php echo $og->renderTags(); ?> <?php echo MetaTag::twitterCard(); ?> <?php endif; ?> <?php echo $__env->make('feed::links', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> <?php if(config('settings.seo.google_site_verification')): ?>

	<meta name="google-site-verification" content="<?php echo e(config('settings.seo.google_site_verification')); ?>" /> <?php endif; ?> <?php if(config('settings.seo.msvalidate')): ?>

	<meta name="msvalidate.01" content="<?php echo e(config('settings.seo.msvalidate')); ?>" /> <?php endif; ?> <?php if(config('settings.seo.alexa_verify_id')): ?>

	<meta name="alexaVerifyID" content="<?php echo e(config('settings.seo.alexa_verify_id')); ?>" /> <?php endif; ?> <?php echo $__env->yieldContent('before_styles'); ?> <?php if(config('lang.direction') == 'rtl'): ?>

	<link href="https://fonts.googleapis.com/css?family=Cairo|Changa" rel="stylesheet">

	<link href="<?php echo e(url(mix('css/app.rtl.css'))); ?>" rel="stylesheet"> <?php else: ?>

	<link href="<?php echo e(url(mix('css/app.css'))); ?>" rel="stylesheet"> <?php endif; ?> <?php if(isset($detectAdsBlockerPlugin) and !empty($detectAdsBlockerPlugin)): ?>

	<link href="<?php echo e(url('assets/detectadsblocker/css/style.css') . getPictureVersion()); ?>" rel="stylesheet"> <?php endif; ?> <?php echo $__env->make('layouts.inc.tools.style', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>



	<link href="<?php echo e(url('css/custom.css') . getPictureVersion()); ?>" rel="stylesheet">



	<link href="<?php echo e(asset('css/responsive.css')); ?>?v=2" rel="stylesheet"> 

	

	<?php echo $__env->yieldContent('after_styles'); ?> 

	<?php if(isset($installedPlugins) and count($installedPlugins) > 0): ?>

	<?php $__currentLoopData = $installedPlugins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pluginName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

	<?php echo $__env->yieldContent($pluginName . '_styles'); ?> 

	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 

	<?php endif; ?> 

	

	<?php if(config('settings.style.custom_css')): ?> <?php echo printCss(config('settings.style.custom_css')) . "\n"; ?> <?php endif; ?> 

	<?php if(config('settings.other.js_code')): ?> <?php echo printJs(config('settings.other.js_code')) . "\n"; ?> <?php endif; ?>

 <?php if(config('settings.security.recaptcha_activation')): ?>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
 <?php endif; ?>

 
  



	<!--[if lt IE 9]>

	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

	<![endif]-->



	<script>

		paceOptions = {

			elements: true

		};

	</script>

	<script src="<?php echo e(url('assets/js/pace.min.js')); ?>"></script>

	<link rel="stylesheet" href="<?php echo e(url('css/font-awesome.css')); ?>">

	

</head>



<body class="<?php echo e(config('app.skin')); ?>">



	

 

    

	<div id="wrapper header_bg">



		<?php $__env->startSection('header'); ?> 

        <?php echo $__env->make('layouts.inc.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 

        <?php echo $__env->yieldSection(); ?> <?php $__env->startSection('search'); ?> 

        <?php echo $__env->yieldSection(); ?> <?php $__env->startSection('wizard'); ?> 

        <?php echo $__env->yieldSection(); ?> 

        

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

        

         <?php echo $__env->yieldContent('content'); ?> <?php $__env->startSection('info'); ?> <?php echo $__env->yieldSection(); ?> <?php $__env->startSection('footer'); ?> <?php echo $__env->make('layouts.inc.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> <?php echo $__env->yieldSection(); ?>



	</div>



	<?php $__env->startSection('modal_location'); ?> <?php echo $__env->yieldSection(); ?> <?php $__env->startSection('modal_abuse'); ?> <?php echo $__env->yieldSection(); ?> <?php $__env->startSection('modal_message'); ?> <?php echo $__env->yieldSection(); ?> <?php $__env->startSection('make_an_offer'); ?>

	<?php echo $__env->yieldSection(); ?> <?php $__env->startSection('add_more'); ?> <?php echo $__env->yieldSection(); ?> <?php $__env->startSection('update_offer'); ?> <?php echo $__env->yieldSection(); ?> <?php echo $__env->renderWhen(!auth()->check(), 'layouts.inc.modal.login', array_except(get_defined_vars(), array('__data', '__path'))); ?>

	<?php echo $__env->make('layouts.inc.modal.change-country', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> <?php echo $__env->make('cookieConsent::index', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> <?php if(isset($detectAdsBlockerPlugin) and !empty($detectAdsBlockerPlugin)): ?>

	<?php if(view()->exists('detectadsblocker::modal')): ?> <?php echo $__env->make('detectadsblocker::modal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> <?php endif; ?> <?php endif; ?>



	<script>

		/* Init.Root Vars */

		var siteUrl = "<?php echo url((!currentLocaleShouldBeHiddenInUrl() ? config('app.locale') : '' ).'/'); ?>";

		

		

		

		

		var languageCode = "<?php echo config('app.locale'); ?>";

		var countryCode = "<?php echo config('country.code ', 0); ?>";

		var timerNewMessagesChecking = "<?php echo (int)config('settings.other.timer_new_messages_checking', 0); ?>" ;



		/* Init.Translation Vars */

		var langLayout = {

			'hideMaxListItems': {

				'moreText': "<?php echo e(t('View More')); ?>",

				'lessText': "<?php echo e(t('View Less')); ?>"

			},

			'select2': {

				errorLoading: function() {

					return "<?php echo t('The results could not be loaded.'); ?>"

				},

				inputTooLong: function(e) {

					var t = e.input.length - e.maximum,

						n = <?php echo t('Please delete #t character'); ?>;

					return t != 1 && (n += 's'), n

				},

				inputTooShort: function(e) {

					var t = e.minimum - e.input.length,

						n = <?php echo t('Please enter #t or more characters'); ?>;

					return n

				},

				loadingMore: function() {

					return "<?php echo t('Loading more results…'); ?>"

				},

				maximumSelected: function(e) {

					var t = <?php echo t('You can only select #max item'); ?>;

					return e.maximum != 1 && (t += 's'), t

				},

				noResults: function() {

					return "<?php echo t('No results found'); ?>"

				},

				searching: function() {

					return "<?php echo t('Searching…'); ?>"

				}

			}

		};

	</script>



	<?php echo $__env->yieldContent('before_scripts'); ?>

	<script src="<?php echo e(url(mix('js/app.js'))); ?>"></script>

	<?php if(file_exists(public_path() . '/assets/plugins/select2/js/i18n/'.config('app.locale').'.js')): ?>

	<script src="<?php echo e(url('assets/plugins/select2/js/i18n/'.config('app.locale').'.js')); ?>"></script>

	<?php endif; ?> <?php if(isset($detectAdsBlockerPlugin) and !empty($detectAdsBlockerPlugin)): ?>

	<script src="<?php echo e(url('assets/detectadsblocker/js/script.js') . getPictureVersion()); ?>"></script>

	<?php endif; ?>



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

				var url = $(this).attr('name') == 'q' ? "/autocomplete?q="+q : "<?php echo e(url()->current()); ?>?qqq="+q;

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

				title: '<?php echo e(addslashes(MetaTag::get('

				title '))); ?>',

				text: '<?php echo addslashes(MetaTag::get('

				title ')); ?>',

				url: '<?php echo $fullUrl; ?>',

				width: 640,

				height: 480

			});



			/* Modal Login */

			<?php if(isset($errors) and $errors->any()): ?>

			<?php if($errors->any() and old('quickLoginForm') == '1'): ?>

			$('#quickLogin').modal();

			<?php endif; ?>

			<?php endif; ?>

		});

	</script>



	<?php echo $__env->yieldContent('after_scripts'); ?> <?php if(isset($installedPlugins) and count($installedPlugins) > 0): ?> <?php $__currentLoopData = $installedPlugins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pluginName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

	<?php echo $__env->yieldContent($pluginName . '_scripts'); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endif; ?> <?php if(config('settings.footer.tracking_code')): ?> <?php echo printJs(config('settings.footer.tracking_code'))

	. "\n"; ?> <?php endif; ?>

</body>



</html>