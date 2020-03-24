<?php
// Search parameters
$queryString = (Request::getQueryString() ? ('?' . Request::getQueryString()) : '');

// Get the Default Language
$cacheExpiration = (isset($cacheExpiration)) ? $cacheExpiration : config('settings.other.cache_expiration', 60);
$defaultLang = Cache::remember('language.default', $cacheExpiration, function () {
    $defaultLang = \App\Models\Language::where('default', 1)->first();
    return $defaultLang;
});

// Check if the Multi-Countries selection is enabled
$multiCountriesIsEnabled = false;
$multiCountriesLabel = '';
if (config('settings.geo_location.country_flag_activation')) {
    if (!empty(config('country.code'))) {
        if (\App\Models\Country::where('active', 1)->count() > 1) {
            $multiCountriesIsEnabled = true;
            $multiCountriesLabel = 'title="' . t('Select a Country') . '"';
        }
    }
}

// Logo Label
$logoLabel = '';
if (getSegment(1) != trans('routes.countries')) {
    $logoLabel = config('settings.app.name') . ((!empty(config('country.name'))) ? ' ' . config('country.name') : '');
}
?>
<div class="header">
    <nav class="navbar navbar-site navbar-default" role="navigation" style="position: relative !important;">
        <div class="container">
            <div class="nav navbar-nav ">

                {{-- Toggle Nav --}}
                <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                {{-- Country Flag (Mobile) --}}
                @if (getSegment(1) != trans('routes.countries'))
                    @if (isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled)
                        @if (!empty(config('country.icode')))
                            @if (file_exists(public_path().'/images/flags/24/'.config('country.icode').'.png'))
                                <button class="flag-menu country-flag visible-xs btn btn-default hidden"
                                        href="#selectCountry" data-toggle="modal">
                                    <img src="{{ url('images/flags/24/'.config('country.icode').'.png') . getPictureVersion() }}"
                                         style="float: left;">
                                    <span class="caret hidden-xs"></span>
                                </button>
                            @endif
                        @endif
                    @endif
                @endif

                {{--Logo For Mobile--}}
                <a href="{{ lurl('/') }}" class=" nav navbar-nav navbar-brand logo logo-title  hidden-lg mobile-logo-link">
                    <img src="{{ \Storage::url(config('settings.app.logo')) . getPictureVersion() }}"
                         alt="{{ strtolower(config('settings.app.name')) }}" class="tooltipHere main-logo mobile-logo" title=""
                         data-placement="bottom"
                         data-toggle="tooltip"
                         data-original-title="{!! isset($logoLabel) ? $logoLabel : '' !!}"/>
                </a>
                @if (!auth()->check())
                @else
                    <ul class="hidden-lg hidden-md user-details-header">
                    <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-user fa hidden-sm"></i>
                                <span>{{ auth()->user()->first_name }}</span>
                                <span class="badge badge-important count-conversations-with-new-messages-offer">0</span>
                                <i class="icon-down-open-big fa hidden-sm"></i>
                            </a>
                            <ul class="dropdown-menu user-menu">
                                <li class="active">
                                    <a href="{{ lurl('account') }}">
                                        <i class="icon-home"></i>
                                        {{ t('Personal Home') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ lurl('account/my-posts') }}">
                                        <i class="icon-th-thumb"></i> {{ t('My ads') }} 
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ lurl('account/favourite') }}">
                                        <i class="icon-heart"></i> {{ t('Favourite ads') }} 
                                    </a>
                                </li>
                                
                                <!--<li><a href="{{ lurl('account/saved-search') }}"><i-->
                                <!--                class="icon-star-circled"></i> {{ t('Saved searches') }} </a></li>-->
                                                
                                <li><a href="{{ lurl('account/pending-approval') }}"><i
                                                class="icon-hourglass"></i> {{ t('Pending approval') }} </a></li>
                                <li><a href="{{ lurl('account/archived') }}"><i
                                                class="icon-folder-close"></i> {{ t('Archived ads') }}</a></li>
                                <li>
                                    <a href="{{ lurl('account/conversations') }}">
                                        <i class="icon-mail-1"></i> {{ t('Messages') }}
                                        <!--{{ t('Conversations') }}-->
                                        <span class="badge badge-important count-conversations-with-new-messages">0</span>
                                    </a>
                                </li>
                                <li><a href="{{ lurl('account/transactions') }}"><i
                                                class="icon-money"></i> {{ t('Transactions') }}</a></li>
                                <li>
                                    <a href="{{ lurl('account/makeanoffers') }}"> 
                                        <i class="glyphicon glyphicon-hand-left" style="margin-left: 4px;"></i> 
                                            {{ t('Offers') }}
                                        <span class="badge badge-important count-conversations-with-new-offer">
                                            0
                                        </span>
                                    </a>
                                </li>
                            </ul>
                    </li>
                </ul>
                @endif                
            </div>
            <div class="navbar-collapse collapse in" style="margin-bottom: 25px;">
                <ul class="nav navbar-nav navbar-left">
                @if (count(LaravelLocalization::getSupportedLocales()) > 1)
                    <!-- Language selector -->
                        <li class="dropdown lang-menu">
                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                @if(strtoupper(config('app.locale')) == 'EN')
                                English
                                @elseif(strtoupper(config('app.locale')) == 'FR')
                                 Français 
                                @elseif(strtoupper(config('app.locale')) == 'ES')
                                 Español 
                                @elseif(strtoupper(config('app.locale')) == 'DE')
                                 German 
                                @elseif(strtoupper(config('app.locale')) == 'PT')
                                  Português 
                                @elseif(strtoupper(config('app.locale')) == 'TR')
                                   Türkçe
                                @elseif(strtoupper(config('app.locale')) == 'SQ')
                                    Shqip  
                                @elseif(strtoupper(config('app.locale')) == 'RU')
                                    Русский 
                                @elseif(strtoupper(config('app.locale')) == 'AR')
                                    العربية
                                @else
                                {{ strtoupper(config('app.locale')) }}
                                @endif
                                <span class="caret hidden-sm"> </span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                    @if (strtolower($localeCode) != strtolower(config('app.locale')))
                                        <?php
                                        // Controller Parameters
                                        $attr = [];
                                        $attr['countryCode'] = config('country.icode');
                                        if (isset($uriPathCatSlug)) {
                                            $attr['catSlug'] = $uriPathCatSlug;
                                            if (isset($uriPathSubCatSlug)) {
                                                $attr['subCatSlug'] = $uriPathSubCatSlug;
                                            }
                                        }
                                        if (isset($uriPathCityName) && isset($uriPathCityId)) {
                                            $attr['city'] = $uriPathCityName;
                                            $attr['id'] = $uriPathCityId;
                                        }
                                        if (isset($uriPathUserId)) {
                                            $attr['id'] = $uriPathUserId;
                                            if (isset($uriPathUsername)) {
                                                $attr['username'] = $uriPathUsername;
                                            }
                                        }
                                        if (isset($uriPathUsername)) {
                                            if (isset($uriPathUserId)) {
                                                $attr['id'] = $uriPathUserId;
                                            }
                                            $attr['username'] = $uriPathUsername;
                                        }
                                        if (isset($uriPathTag)) {
                                            $attr['tag'] = $uriPathTag;
                                        }
                                        if (isset($uriPathPageSlug)) {
                                            $attr['slug'] = $uriPathPageSlug;
                                        }

                                        // Default
                                        // $link = LaravelLocalization::getLocalizedURL($localeCode, null, $attr);
                                        //var_dump('$link',$attr, $localeCode);
                                        $link = lurl(null, $attr, $localeCode);
                                        $localeCode = strtolower($localeCode);
                                        if($localeCode == 'en'){
                                        	$link = "https://dealnotdeal.com/en";	
                                        }
                                        
                                        ?>
                                        <li>
                                            <a href="{{ $link }}" tabindex="-1" rel="alternate"
                                               hreflang="{{ $localeCode }}">
                                                <span class="lang-name"> {{{ $properties['native'] }}} </span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endif

                    <!--<li class="dropdown currency-menu">-->
                    <!--    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">-->
                    <!--        @if(!empty(Session::get('currency')))-->
                    <!--            {{Session::get('currency')}}-->
                    <!--        @else-->
                    <!--            Select Currency-->
                    <!--        @endif-->
                    <!--        <span class="caret hidden-sm"> </span>-->
                    <!--    </button>-->
                    <!--    <ul class="dropdown-menu" role="menu">-->
                    <!--        @if(getCurrency())-->
                    <!--            @foreach(getCurrency() as $key => $c)-->
                    <!--                <li>-->
                    <!--                    <a href="javascript:;" onclick="selectCurrency('{{$key}}')">-->
                    <!--                        <span class="lang-name">{{$key}}</span>-->
                    <!--                    </a>-->
                    <!--                </li>-->
                    <!--            @endforeach-->
                    <!--        @endif-->
                    <!--    </ul>-->
                    <!--</li>-->
                    
                    
                    {{-- Country Flag (Mobile) --}}
                    @if (getSegment(1) != trans('routes.countries'))
                        @if (isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled)
                            @if (!empty(config('country.icode')))
                                @if (file_exists(public_path().'/images/flags/24/'.config('country.icode').'.png'))

                                    <button class="flag-menu country-flag visible-s btn btn-default hidden"
                                            href="#selectCountry" data-toggle="modal">
                                        <img src="{{ url('images/flags/24/'.config('country.icode').'.png') . getPictureVersion() }}"
                                             style="float: left;">
                                        <span class="caret hidden-xs"></span>
                                    </button>
                                @endif
                            @endif
                        @endif
                    @endif
                    {{-- Country Flag --}}
                    @if (getSegment(1) != trans('routes.countries'))
                        @if (config('settings.geo_location.country_flag_activation'))
                            @if (!empty(config('country.icode')))
                                @if (file_exists(public_path().'/images/flags/32/'.config('country.icode').'.png'))
                                    <li class="flag-menu country-flag tooltipHere hidden-xs" data-toggle="tooltip"
                                        data-placement="{{ (config('lang.direction') == 'rtl') ? 'bottom' : 'right' }}" {!! $multiCountriesLabel !!}>
                                        @if (isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled)
                                            <a href="#selectCountry" data-toggle="modal">
                                                <img class="flag-icon"
                                                     src="{{ url('images/flags/32/'.config('country.icode').'.png') . getPictureVersion() }}"
                                                     style="float: left;">
                                                <span class="caret hidden-sm"></span>
                                            </a>
                                        @else
                                            <a style="cursor: default;">
                                                <img class="flag-icon"
                                                     src="{{ url('images/flags/32/'.config('country.icode').'.png') . getPictureVersion() }}"
                                                     style="float: left;">
                                            </a>
                                        @endif
                                    </li>
                                @endif
                            @endif
                        @endif
                    @endif
                </ul>


                {{-- Logo --}}
                <a href="{{ lurl('/') }}" class=" nav navbar-nav navbar-brand logo logo-title  visible-lg"
                   style="position: absolute; left: 37%;">
                    <img src="{{ \Storage::url(config('settings.app.logo')) . getPictureVersion() }}"
                         alt="{{ strtolower(config('settings.app.name')) }}" class="tooltipHere main-logo" title=""
                         data-placement="bottom"
                         data-toggle="tooltip"
                         data-original-title="{!! isset($logoLabel) ? $logoLabel : '' !!}"/>
                </a>


                <ul class="nav navbar-nav navbar-right">
                        <li class="display-mobile-menu-home" ><a href="{{ lurl('/') }}"><i class="icon-home fa"></i></a></li>
                    @if (!auth()->check())
                       
                        <li>
                            @if (config('settings.security.login_open_in_modal'))
                                <a href="#quickLogin" data-toggle="modal"><i class="icon-user fa"></i> {{ t('Log In') }}
                                </a>
                            @else
                                <a href="{{ lurl(trans('routes.login')) }}"><i
                                            class="icon-user fa"></i> {{ t('Log In') }}</a>
                            @endif
                        </li>
                        <li><a href="{{ lurl(trans('routes.register')) }}"><i class="icon-user-add fa"></i> {{ t('Register') }}</a></li>
                        <li class="postadd">
                            @if (config('settings.single.guests_can_post_ads') != '1')
                                <a class="btn btn-block btn-border btn-post btn-add-listing" href="#quickLogin"
                                   data-toggle="modal">
                                    <i class="fa fa-plus-circle"></i> {{ t('Add Listing') }}
                                </a>
                            @else
                                <a class="btn btn-block btn-border btn-post btn-add-listing"
                                   href="{{ lurl('posts/create') }}">
                                    <i class="fa fa-plus-circle"></i> {{ t('Add Listing') }}
                                </a>
                            @endif
                        </li>
                    @else
                        <li>
                            @if (app('impersonate')->isImpersonating())
                                <a href="{{ route('impersonate.leave') }}">
                                    <i class="icon-logout hidden-sm"></i> {{ t('Leave') }}
                                </a>
                            @else
                                <a href="{{ lurl(trans('routes.logout')) }}">
                                    <i class="glyphicon glyphicon-off hidden-sm"></i> {{ t('Log Out') }}
                                </a>
                            @endif
                        </li>
                        <!-- <div class="hidden-sm hidden-xs"> -->
                        <li class="dropdown hidden-sm hidden-xs">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-user fa hidden-sm"></i>
                                <span>{{ auth()->user()->first_name }}</span>
                                <span class="badge badge-important count-conversations-with-new-messages-offer">0</span>
                                <i class="icon-down-open-big fa hidden-sm"></i>
                            </a>
                            <ul class="dropdown-menu user-menu">
                                <li class="active">
                                    <a href="{{ lurl('account') }}">
                                        <i class="icon-home"></i>
                                        {{ t('Personal Home') }}
                                    </a>
                                </li>
                                <li><a href="{{ lurl('account/my-posts') }}"><i
                                                class="icon-th-thumb"></i> {{ t('My ads') }} </a></li>
                                <li><a href="{{ lurl('account/favourite') }}"><i
                                                class="icon-heart"></i> {{ t('Favourite ads') }} </a></li>
                                
                                <!--<li><a href="{{ lurl('account/saved-search') }}"><i-->
                                <!--                class="icon-star-circled"></i> {{ t('Saved searches') }} </a></li>-->
                                                
                                <li><a href="{{ lurl('account/pending-approval') }}"><i
                                                class="icon-hourglass"></i> {{ t('Pending approval') }} </a></li>
                                <li><a href="{{ lurl('account/archived') }}"><i
                                                class="icon-folder-close"></i> {{ t('Archived ads') }}</a></li>
                                <li>
                                    <a href="{{ lurl('account/conversations') }}">
                                        <i class="icon-mail-1"></i> {{ t('Messages') }}
                                        <!--{{ t('Conversations') }}-->
                                        <span class="badge badge-important count-conversations-with-new-messages">0</span>
                                    </a>
                                </li>
                                <li><a href="{{ lurl('account/transactions') }}"><i
                                                class="icon-money"></i> {{ t('Transactions') }}</a></li>
                                <li><a href="{{ lurl('account/makeanoffers') }}"> <i
                                                class="glyphicon glyphicon-hand-left" style="margin-left: 4px;"></i> {{ t('Offers') }}
                                                <span class="badge badge-important count-conversations-with-new-offer">0</span>
                                        </a></li>
                            </ul>
                        </li> 
                    <!-- </div> -->
                        <li class="postadd">
                            <a class="btn btn-block btn-border btn-post btn-add-listing"
                               href="{{ lurl('posts/create') }}">
                                <i class="fa fa-plus-circle"></i> {{ t('Add Listing') }}
                            </a>
                        </li>
                    @endif


                </ul>
            </div>
        </div>
    </nav>
</div>
{{--script for select currency--}}
<script>
    function selectCurrency(currency) {
        if (currency) {
            // if(currency == 'USD'){ var s_url = '/en?d=US'}
            // if(currency == 'EUR'){ var s_url = '/en?d=US'}
            // if(currency == 'GBP'){ var s_url = '/en?d=US'}
            $(".currency-menu").find('.btn').html(currency + ' <span class="caret hidden-sm"></span>');

            $.ajax({
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: "{{url('/setCurrency')}}/" + currency,
                type: "GET",
                success: function (response) {

                    if (response.status == 'success') {
                        // console.log(window.location.href);
                        // var pathname = window.location.pathname; // Returns path only
                        window.location.reload();
                    }
                },

            });
        }
    }
</script>