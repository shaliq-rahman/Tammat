<?php

// Init.

$sForm = [

	'enableFormAreaCustomization' => '0',

	'hideTitles'                  => '0',

	//'title'                       => t('Sell and buy near you'),
	'title'                       => '',

	//'subTitle'                    => t('Simple, fast and efficient'),

	//'underTitle'                    => t('Simple, fast and efficient'),

	'subTitle'                    =>   '',

	'underTitle'                    => '',

	'bigTitleColor'               => '', // 'color: #FFF;',

	'subTitleColor'               => '', // 'color: #FFF;',

	'backgroundColor'             => '', // 'background-color: #444;',

	'backgroundImage'             => '', // null,

	'height'                      => '', // '450px',

	'parallax'                    => '0',

	'hideForm'                    => '0',

	'formBorderColor'             => '', // 'background-color: #333;',

	'formBorderSize'              => '', // '5px',

	'formBtnBackgroundColor'      => '', // 'background-color: #4682B4; border-color: #4682B4;',

	'formBtnTextColor'            => '', // 'color: #FFF;',

];



// Get Search Form Options

if (isset($searchFormOptions)) {

	if (isset($searchFormOptions['enable_form_area_customization']) and !empty($searchFormOptions['enable_form_area_customization'])) {

		$sForm['enableFormAreaCustomization'] = $searchFormOptions['enable_form_area_customization'];

	}

	if (isset($searchFormOptions['hide_titles']) and !empty($searchFormOptions['hide_titles'])) {

		$sForm['hideTitles'] = $searchFormOptions['hide_titles'];

	}

	if (isset($searchFormOptions['title_' . config('app.locale')]) and !empty($searchFormOptions['title_' . config('app.locale')])) {

		$sForm['title'] = $searchFormOptions['title_' . config('app.locale')];

		$sForm['title'] = str_replace(['{app_name}', '{country}'], [config('app.name'), config('country.name')], $sForm['title']);

		if (str_contains($sForm['title'], '{count_ads}')) {

			try {

				$countPosts = \App\Models\Post::currentCountry()->unarchived()->count();

			} catch (\Exception $e) {

				$countPosts = 0;

			}

			$sForm['title'] = str_replace('{count_ads}', $countPosts, $sForm['title']);

		}

		if (str_contains($sForm['title'], '{count_users}')) {

			try {

				$countUsers = \App\Models\User::count();

			} catch (\Exception $e) {

				$countUsers = 0;

			}

			$sForm['title'] = str_replace('{count_users}', $countUsers, $sForm['title']);

		}

	}

	if (isset($searchFormOptions['sub_title_' . config('app.locale')]) and !empty($searchFormOptions['sub_title_' . config('app.locale')])) {

		$sForm['subTitle'] = $searchFormOptions['sub_title_' . config('app.locale')];

		$sForm['subTitle'] = str_replace(['{app_name}', '{country}'], [config('app.name'), config('country.name')], $sForm['subTitle']);

		if (str_contains($sForm['subTitle'], '{count_ads}')) {

			try {

				$countPosts = \App\Models\Post::currentCountry()->unarchived()->count();

			} catch (\Exception $e) {

				$countPosts = 0;

			}

			$sForm['subTitle'] = str_replace('{count_ads}', $countPosts, $sForm['subTitle']);

		}

		if (str_contains($sForm['subTitle'], '{count_users}')) {

			try {

				$countUsers = \App\Models\User::count();

			} catch (\Exception $e) {

				$countUsers = 0;

			}

			$sForm['subTitle'] = str_replace('{count_users}', $countUsers, $sForm['subTitle']);

		}

	}

	

	if (isset($searchFormOptions['under_title_' . config('app.locale')]) and !empty($searchFormOptions['under_title_' . config('app.locale')])) {

		$sForm['underTitle'] = $searchFormOptions['under_title_' . config('app.locale')];

		$sForm['underTitle'] = str_replace(['{app_name}', '{country}'], [config('app.name'), config('country.name')], $sForm['underTitle']);

		if (str_contains($sForm['underTitle'], '{count_ads}')) {

			try {

				$countPosts = \App\Models\Post::currentCountry()->unarchived()->count();

			} catch (\Exception $e) {

				$countPosts = 0;

			}

			$sForm['underTitle'] = str_replace('{count_ads}', $countPosts, $sForm['underTitle']);

		}

		if (str_contains($sForm['underTitle'], '{count_users}')) {

			try {

				$countUsers = \App\Models\User::count();

			} catch (\Exception $e) {

				$countUsers = 0;

			}

			$sForm['underTitle'] = str_replace('{count_users}', $countUsers, $sForm['underTitle']);

		}

	}

	

	if (isset($searchFormOptions['parallax']) and !empty($searchFormOptions['parallax'])) {

		$sForm['parallax'] = $searchFormOptions['parallax'];

	}

	if (isset($searchFormOptions['hide_form']) and !empty($searchFormOptions['hide_form'])) {

		$sForm['hideForm'] = $searchFormOptions['hide_form'];

	}

}



// Country Map status (shown/hidden)

$showMap = false;

if (file_exists(config('larapen.core.maps.path') . config('country.icode') . '.svg')) {

	if (isset($citiesOptions) and isset($citiesOptions['show_map']) and $citiesOptions['show_map'] == '1') {

		$showMap = true;

	}

}

?>







<?php if(isset($sForm['enableFormAreaCustomization']) and $sForm['enableFormAreaCustomization'] == '1'): ?>

	

	<?php if(isset($firstSection) and !$firstSection): ?>

		<div class="h-spacer"></div>

	<?php endif; ?>

	

	<?php $parallax = (isset($sForm['parallax']) and $sForm['parallax'] == '1') ? 'parallax' : ''; ?>
	

	<div class="wide-intro <?php echo e($parallax); ?>" style="padding-bottom: 0px;border-bottom: 1px solid #0a0a0a;max-height: 1500px;min-height: 1000px;">

		<div class="dtable hw100" style="background: #0000008f;">

			<div class="dtable-cell hw100">

            



 

 <a href="<?php echo e(lurl('/')); ?>" class=" nav navbar-nav navbar-brand logo logo-title  visible-lg"

 style="position: absolute; left: 44%;padding-top: 0px;">

  <img src="<?php echo e(\Storage::url(config('settings.app.logo')) . getPictureVersion()); ?>"

	   alt="<?php echo e(strtolower(config('settings.app.name'))); ?>" class="tooltipHere main-logo" title=""

	   data-placement="bottom"

	   data-toggle="tooltip"

	   data-original-title="<?php echo isset($logoLabel) ? $logoLabel : ''; ?>" style="display:none"/>

	   

		<img src="/images/tammat.png"

	   alt="<?php echo e(strtolower(config('settings.app.name'))); ?>" class="tooltipHere main-logo" title=""

	   data-placement="bottom"

	   data-toggle="tooltip"

	   data-original-title="<?php echo isset($logoLabel) ? $logoLabel : ''; ?>"/>

	   

</a>




                <div class="h-spacer"></div>

                <div class="h-spacer"></div>

                <div class="h-spacer"></div>

                <div class="h-spacer"></div>

                <div class="h-spacer"></div>

                <div class="h-spacer"></div>

                

				<div class="container text-center fix-width" style="padding-top: 253px">

					 

					
					<?php if($sForm['hideForm'] != '1'): ?>

			

						<div class="row search-row fadeInUp">

							<?php $attr = ['countryCode' => config('country.icode')]; ?>

							<form id="seach" name="search" action="<?php echo e(lurl(trans('routes.v-search', $attr), $attr)); ?>" method="GET">

							 <div class="col-lg-5 col-sm-5 search-col relative">

									<i class="icon-docs icon-append"></i>

									<input type="text" name="q" class="form-control keyword has-icon" placeholder="<?php echo e(t('What?')); ?>" value="">

								</div>

								<div class="col-lg-5 col-sm-5 search-col relative locationicon">

									<i class="icon-location-2 icon-append"></i>

									<!--<input type="hidden" id="lSearch" name="l" value="">-->

									<?php if($showMap): ?>

										<input type="text" id="locSearch" name="location" class="form-control locinput input-rel searchtag-input has-icon tooltipHere"

											   placeholder="<?php echo e(t('Where?')); ?>" value="" title="" data-placement="bottom"

											   data-toggle="tooltip" type="button"

											   data-original-title="<?php echo e(t('Enter a city name OR a state name with the prefix ":prefix" like: :prefix', ['prefix' => t('area:')]) . t('State Name')); ?>">

									<?php else: ?>

										<input type="text" id="locSearchCheck" name="location" class="form-control locinput input-rel searchtag-input has-icon tooltipHere"

											   placeholder="<?php echo e(t('Where?')); ?>" value="">

	

									<?php endif; ?>

								</div>

								

		<script> 

            function initialize(input) {

                var options = {

                    types: ['(regions)'],

                    componentRestrictions: {country: "<?php echo e(config('country.icode')); ?>"}

                };



                var input = document.getElementById('locSearchCheck');

                var autocomplete = new google.maps.places.Autocomplete(input, options);

            }



        </script>

        <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('services.GoogleMaps.key')); ?>&libraries=places&callback=initialize"

         async defer></script>

        

							<style>

								.align_setting{ text-align: right; }

										@media (max-width: 768px){

											.align_setting{

												text-align: left !important ;

												padding-left: 20px;

											}

                                    

										}

                                    .search-row .search-col .form-control, .search-row button.btn-search, .search-row-wrapper .form-control, .search-row-wrapper .select2-container--default .select2-selection--single, .search-row-wrapper button.btn {

									font-size: 15px !important;

									}

							</style>

						           

							         

								<div class="col-lg-2 col-sm-2 search-col">

									<button class="btn btn-primary btn-search btn-block">

										<i class="icon-search"></i> <strong><?php echo e(t('Find')); ?></strong>

									</button>

								</div> 

					

								<?php echo csrf_field(); ?>


							</form>

						</div>

					<?php endif; ?>

					
<?php /*
					<div style="color: #fff;margin-top: 30px;font-size: 23px;"> 

                            {!! $sForm['underTitle'] !!}

					</div>
*/ ?>
					



					<?php if($sForm['hideTitles'] != '1'): ?>

					<h1 style="font-size: 24px;padding-top: 55px;color:white" class="intro-title animated fadeInDown  visible-lg"> <?php echo e($sForm['title']); ?></h1>

    					 

    					<!--	<div class="h-spacer"></div>-->

    						<h1 style="font-size: 24px;color:#ff5555" class="sub animateme fittext3 animated fadeIn">

    							<?php echo $sForm['subTitle']; ?>


							</h1>

					

					<?php /*?>

    					@if(config('app.locale') == 'de')

    						<h1 class="intro-title animated fadeInDown"> Wenn du deine Produkte NICHT verkaufen, oder ein anderes Produkt kaufen kannst... Probiere es doch einfach zu tauschen!</h1>

    						<p class="sub animateme fittext3 animated fadeIn">

    							Der Gesellschaft helfen, indem Du verschenkst

    						</p>

    					@elseif(config('app.locale') == 'ru')

    						<h1 class="intro-title animated fadeInDown">ЗАЧЕМ ТРАТИТЬ ВРЕМЯ НА ПОИСКИ, ЕСЛИ ВСЕ МОЖНО НАЙТИ И ПОЛУЧИТЬ НА ОДНОМ САЙТЕ</h1>

    						<p class="sub animateme fittext3 animated fadeIn">

    							Покупайте, продавайте, обменивайте. Просто, быстро, эффективно.

    						</p>

    					@elseif(config('app.locale') == 'es')

    						<h1 class="intro-title animated fadeInDown">¿POR QUÉ PAGAR ALGO CUANDO PUEDE OBTENERLO GRATIS O INTERCAMBIARLO CERCA DE USTED?</h1>

    						<p class="sub animateme fittext3 animated fadeIn">

    							¡Venda, Compre, Intercambie o Simplemente Regale!

    						</p>

    					@elseif(config('app.locale') == 'sq')

    						<h1 class="intro-title animated fadeInDown">shit dhe ble pranë</h1>

    						<p class="sub animateme fittext3 animated fadeIn">

    							thjeshtë, shpejtë dhe në mënyrë  efiçente

    						</p>

    					@elseif(config('app.locale') == 'tr')

    						<h1 class="intro-title animated fadeInDown">EĞER SATAMIYORSAN VEYA ALAMIYORSAN…DAHA EĞLENCELİ OLAN TAKASI DENE!</h1>

    						<p class="sub animateme fittext3 animated fadeIn">

    							Hibe ederek Topluluğa kazandır

    						</p>

						@elseif(config('app.locale') == 'fr')

    						<h1 class="intro-title animated fadeInDown">Pourquoi payer quand vous pouvez l'avoir gratuitement ou à l'échange à côté de chez vous?</h1>

    						<p class="sub animateme fittext3 animated fadeIn">

    							Vendez, achetez, échangez, ou donnez!

    						</p>

						@elseif(config('app.locale') == 'pt')

    						<h1 class="intro-title animated fadeInDown">Por que você pagaria por algo quando você pode obtê-lo gratuitamente ou trocar por algo perto de você?</h1>

    						<p class="sub animateme fittext3 animated fadeIn">

    							Venda, Compre, Troque ou Simplesmente Doe!

    						</p>

    					@else

    						<h1 class="intro-title animated fadeInDown"> {{ $sForm['title'] }}</h1>

    						<p class="sub animateme fittext3 animated fadeIn">

    							{!! $sForm['subTitle'] !!}

    						</p>

    					@endif

						<?php */?>

					<?php endif; ?>					 
					 
					  <?php echo $__env->make('home.inc.hm_categories', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
				

				</div>

				
			</div>
			

		</div>


		


	</div>

<?php else: ?>



<?php echo $__env->make('home.inc.spacer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


	<div class="container">

		<div class="intro">

			<div class="dtable hw100">

				<div class="dtable-cell hw100">

                

                <div class="h-spacer"></div>

                <div class="h-spacer"></div>

                <div class="h-spacer"></div>

                <div class="h-spacer"></div>

                <div class="h-spacer"></div>

                <div class="h-spacer"></div>

                

                

					<div class="container text-center fix-width">

						<div class="row search-row fadeInUp">

							<?php $attr = ['countryCode' => config('country.icode')]; ?>

							<form id="seach" name="search" action="<?php echo e(lurl(trans('routes.v-search', $attr), $attr)); ?>" method="GET">

								<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 search-col relative">

									<i class="icon-docs icon-append"></i>

									<input type="text" name="q" class="form-control keyword has-icon" placeholder="<?php echo e(t('What?')); ?>" value="">

								</div>

								<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 search-col relative locationicon">

									<i class="icon-location-2 icon-append"></i>

									<input type="hidden" id="lSearch" name="l" value="">

									<?php if($showMap): ?>

										<input type="text" id="locSearch" name="location" class="form-control locinput input-rel searchtag-input has-icon tooltipHere"

											   placeholder="<?php echo e(t('Where?')); ?>" value="" title="" data-placement="bottom"

											   data-toggle="tooltip" type="button"

											   data-original-title="<?php echo e(t('Enter a city name OR a state name with the prefix ":prefix" like: :prefix', ['prefix' => t('area:')]) . t('State Name')); ?>">

									<?php else: ?>

										<input type="text" id="locSearch" name="location" class="form-control locinput input-rel searchtag-input has-icon"

											   placeholder="<?php echo e(t('Where?')); ?>" value="">

									<?php endif; ?>

								</div>

								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 search-col">

									<button class="btn btn-primary btn-search btn-block">

										<i class="icon-search"></i> <strong><?php echo e(t('Find')); ?></strong>

									</button>

								</div>

								<?php echo csrf_field(); ?>


							</form>

						</div>

	

					</div>

				</div>

			</div>

		</div>


	</div>

	<?php echo $__env->make('home.inc.hm_categories', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php endif; ?>

