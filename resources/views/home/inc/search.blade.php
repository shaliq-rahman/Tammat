<?php
// Init.
$sForm = [
	'enableFormAreaCustomization' => '0',
	'hideTitles'                  => '0',
	'title'                       => t('Sell and buy near you'),
	'subTitle'                    => t('Simple, fast and efficient'),
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
@if (isset($sForm['enableFormAreaCustomization']) and $sForm['enableFormAreaCustomization'] == '1')
	
	@if (isset($firstSection) and !$firstSection)
		<div class="h-spacer"></div>
	@endif
	
	<?php $parallax = (isset($sForm['parallax']) and $sForm['parallax'] == '1') ? 'parallax' : ''; ?>
	<div class="wide-intro {{ $parallax }}">
		<div class="dtable hw100">
			<div class="dtable-cell hw100">
				<div class="container text-center">
					 
					@if ($sForm['hideTitles'] != '1')
					
					
					
    					@if(config('app.locale') == 'de')
    						<h1 class="intro-title animated fadeInDown"> WARUM FÜR ETWAS BEZAHLEN, WENN DU ES IN DEINER NÄHE TAUSCHEN ODER KOSTENLOS BEKOMMEN KANNST?</h1>
    						<p class="sub animateme fittext3 animated fadeIn">
    							VERKAUFEN, KAUFEN, TAUSCHEN&VERSCHENKEN!!
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
    						<h1 class="intro-title animated fadeInDown">BEDAVA ALMAK VEYA YAKININDAKİ BİRİYLE TAKAS ETMEK VARKEN İSTEDİĞİN ÜRÜNÜ SATIN ALMANA NE GEREK VAR</h1>
    						<p class="sub animateme fittext3 animated fadeIn">
    							Satmak, Satın Almak, Takas & Hediye !!
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
						
					@endif
					
					@if ($sForm['hideForm'] != '1')
			
						<div class="row search-row fadeInUp">
							<?php $attr = ['countryCode' => config('country.icode')]; ?>
							<form id="seach" name="search" action="{{ lurl(trans('routes.v-search', $attr), $attr) }}" method="GET">
							 <div class="col-lg-5 col-sm-5 search-col relative">
									<i class="icon-docs icon-append"></i>
									<input type="text" name="q" class="form-control keyword has-icon" placeholder="{{ t('What?') }}" value="">
								</div>
								<div class="col-lg-5 col-sm-5 search-col relative locationicon">
									<i class="icon-location-2 icon-append"></i>
									<!--<input type="hidden" id="lSearch" name="l" value="">-->
									@if ($showMap)
										<input type="text" id="locSearch" name="location" class="form-control locinput input-rel searchtag-input has-icon tooltipHere"
											   placeholder="{{ t('Where?') }}" value="" title="" data-placement="bottom"
											   data-toggle="tooltip" type="button"
											   data-original-title="{{ t('Enter a city name OR a state name with the prefix ":prefix" like: :prefix', ['prefix' => t('area:')]) . t('State Name') }}">
									@else
										<input type="text" id="locSearchCheck" name="location" class="form-control locinput input-rel searchtag-input has-icon tooltipHere"
											   placeholder="{{ t('Where?') }}" value="">
											   
										<!-- <input type="text" id="locSearch" name="location" class="form-control has-icon"  placeholder="{{ t('Where?') }}" value=""> -->
									@endif
								</div>
								
		<script> 
            function initialize(input) {
                var options = {
                    types: ['(cities)'],
                    componentRestrictions: {country: "{{config('country.icode')}}"}
                };

                var input = document.getElementById('locSearchCheck');
                var autocomplete = new google.maps.places.Autocomplete(input, options);
            }

        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3HKnsvpSAYaoQQ-wIeqDBTjb69hJ-vMw&libraries=places&callback=initialize"
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
									font-size: 14px !important;
									}
							</style>
						           <!-- <div class="col-lg-1 col-sm-1 search-col relative" style="background-color: #fff;font-size: 17px;color: #999;"><div style="height: 45px;padding-top: 10px;" class="align_setting">With in :</div></div>-->
							        <!--<div class="col-lg-3 col-sm-3 search-col relative">
							            
							            <div class="col-lg-3 col-sm-3 search-col relative">
							                <div style="height: 45px;padding-top: 12px;background: #fff;font-size: 14px;" class="align_setting">
							                    With in :
							                 </div>
							            </div>
							            
							            <div class="col-lg-9 col-sm-9 search-col relative">
							              <select id="locDistance" name="distance" class="form-control" style="height: 45px !important;">
                                          <!--  <option value="1">1 km (0.6 mi)</option>
                                            <option value="2">2 km (1.2 mi)</option>
                                            <option value="3">3 km (1.8 mi)</option>
                                            <option value="4">4 km (2.4 mi)</option>
                                            <option value="5">5 km (3.1 mi)</option>-->
                                            <!--<option value="6">6 km (3.7 mi)</option>
                                            <option value="7">7 km (4.3 mi)</option>
                                            <option value="8">8 km (4.9 mi)</option>
                                            <option value="9">9 km (5.5 mi)</option>
                                            <option value="10">10 km (6.2 mi)</option>
                                            <!--<option value="20">20 km (12.4 mi)</option>
                                            <option value="30">30 km (18.6 mi)</option>
                                            <option value="40">40 km (24.8 mi)</option>
                                          <option value="50">50 km (31.0 mi)</option>
                                            <!--  <option value="60">60 km (37.2 mi)</option>
                                            <option value="70">70 km (43.4 mi)</option>
                                            <option value="80">80 km (49.7 mi)</option>
                                            <option value="90">90 km (55.9 mi)</option>
                                            <option value="100">100 km (62.1 mi)</option>
                                            <option value="250">250 km (155.3 mi)</option>
                                            <option value="500">500 km (310.6 mi)</option>
                                            <option value="">No limit</option>
                                            </select>
                                        </div>
							         </div>-->
							         
								<div class="col-lg-2 col-sm-2 search-col">
									<button class="btn btn-primary btn-search btn-block">
										<i class="icon-search"></i> <strong>{{ t('Find') }}</strong>
									</button>
								</div> 
					
								{!! csrf_field() !!}
							</form>
						</div>
					@endif
					
					<div style="color: #fff;margin-top: 30px;font-size: 23px;">
					    {{ t('No more spam/scam phone calls or emails …. Deal and Bargain online !!!') }}
					    
					</div>
					
				
				</div>
			</div>
		</div>
	</div>
@else

	@include('home.inc.spacer')
	<div class="container">
		<div class="intro">
			<div class="dtable hw100">
				<div class="dtable-cell hw100">
					<div class="container text-center">
						<div class="row search-row fadeInUp">
							<?php $attr = ['countryCode' => config('country.icode')]; ?>
							<form id="seach" name="search" action="{{ lurl(trans('routes.v-search', $attr), $attr) }}" method="GET">
								<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 search-col relative">
									<i class="icon-docs icon-append"></i>
									<input type="text" name="q" class="form-control keyword has-icon" placeholder="{{ t('What?') }}" value="">
								</div>
								<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 search-col relative locationicon">
									<i class="icon-location-2 icon-append"></i>
									<input type="hidden" id="lSearch" name="l" value="">
									@if ($showMap)
										<input type="text" id="locSearch" name="location" class="form-control locinput input-rel searchtag-input has-icon tooltipHere"
											   placeholder="{{ t('Where?') }}" value="" title="" data-placement="bottom"
											   data-toggle="tooltip" type="button"
											   data-original-title="{{ t('Enter a city name OR a state name with the prefix ":prefix" like: :prefix', ['prefix' => t('area:')]) . t('State Name') }}">
									@else
										<input type="text" id="locSearch" name="location" class="form-control locinput input-rel searchtag-input has-icon"
											   placeholder="{{ t('Where?') }}" value="">
									@endif
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 search-col">
									<button class="btn btn-primary btn-search btn-block">
										<i class="icon-search"></i> <strong>{{ t('Find') }}</strong>
									</button>
								</div>
								{!! csrf_field() !!}
							</form>
						</div>
	
					</div>
				</div>
			</div>
		</div>
	</div>
	
@endif
