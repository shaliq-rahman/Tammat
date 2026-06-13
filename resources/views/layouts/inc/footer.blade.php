<?php



if (

	config('settings.other.ios_app_url') ||

	config('settings.other.android_app_url') ||

	config('settings.social_link.facebook_page_url') ||

	config('settings.social_link.twitter_url') ||

	config('settings.social_link.google_plus_url') ||

	config('settings.social_link.linkedin_url') ||

	config('settings.social_link.pinterest_url')

	) {

	$colClass1 = 'col-lg-3 col-md-3 col-sm-3 col-xs-6';

	$colClass2 = 'col-lg-3 col-md-3 col-sm-3 col-xs-6';

	$colClass3 = 'col-lg-2 col-md-2 col-sm-2 col-xs-12';

	$colClass4 = 'col-lg-4 col-md-4 col-sm-4 col-xs-12';

} else {

	$colClass1 = 'col-lg-4 col-md-4 col-sm-4 col-xs-6';

	$colClass2 = 'col-lg-4 col-md-4 col-sm-4 col-xs-6';

	$colClass3 = 'col-lg-4 col-md-4 col-sm-4 col-xs-12';

	$colClass4 = 'col-lg-4 col-md-4 col-sm-4 col-xs-12';

}

?>









<footer class="main-footer" style="">

    

	<div class="footer-content" style="background-color: #ff5555;">

		<div class="container">

			<div style="clear: both"></div>

			<div class="row">

				

				<div class="{{ $colClass1 }}">

					<div class="footer-col">

					    

						<!--<h4 class="footer-title">{{ t('About us') }}</h4>-->

						

						<!--<h4 class="footer-title"></h4>-->

						

						<ul class="list-unstyled footer-nav">

							@if (isset($pages) and $pages->count() > 0)

								@foreach($pages as $page)

									<li>

										<?php

											$linkTarget = '';

											if ($page->target_blank == 1) {

												$linkTarget = 'target="_blank"';

											}

										?>

										@if (!empty($page->external_link))

											<a href="{!! $page->external_link !!}" rel="nofollow" {!! $linkTarget !!}> {{ $page->name }} </a>

										@else

											<?php $attr = ['slug' => $page->slug]; ?>

											<a href="{{ lurl(trans('routes.v-page', $attr), $attr) }}" {!! $linkTarget !!}> {{ $page->name }} </a>

										@endif

									</li>

								@endforeach

							@endif

						</ul>

					</div>

				</div>

			

				<div class="{{ $colClass2 }}">

					<div class="footer-col">

					    

					<!--<h4 class="footer-title"></h4>-->

					

						<!--<h4 class="footer-title">{{ t('Contact & Sitemap') }}</h4>-->

						<ul class="list-unstyled footer-nav">

							<li><a href="{{ lurl(trans('routes.contact')) }}"> {{ t('Contact us') }} </a></li>

							<?php $attr = ['countryCode' => config('country.icode')]; ?>

							<li><a href="{{ lurl(trans('routes.v-sitemap', $attr), $attr) }}"> {{ t('Sitemap') }} </a></li>

							

							

							

							<!--@if (\App\Models\Country::where('active', 1)->count() > 1)-->

							<!--	<li><a href="{{ lurl(trans('routes.countries')) }}"> {{ t('Countries') }} </a></li>-->

							<!--@endif-->

							

							

							

						</ul>

					</div>

				</div>

					

				{{--<div class="{{ $colClass3 }}">

					<div class="footer-col">

						<h4 class="footer-title">{{ t('My Account') }}</h4>

						<ul class="list-unstyled footer-nav">

							@if (!auth()->user())

								<li>

									@if (config('settings.security.login_open_in_modal'))

										<a href="#quickLogin" data-toggle="modal"> {{ t('Log In') }} </a>

									@else

										<a href="{{ lurl(trans('routes.login')) }}"> {{ t('Log In') }} </a>

									@endif

								</li>

								<li><a href="{{ lurl(trans('routes.register')) }}"> {{ t('Register') }} </a></li>

							@else

								<li><a href="{{ lurl('account') }}"> {{ t('Personal Home') }} </a></li>

								<li><a href="{{ lurl('account/my-posts') }}"> {{ t('My ads') }} </a></li>

								<li><a href="{{ lurl('account/favourite') }}"> {{ t('Favourite ads') }} </a></li>

							@endif

						</ul>

					</div>

				</div>--}}

				

				@if (

					config('settings.other.ios_app_url') or

					config('settings.other.android_app_url') or

					config('settings.social_link.facebook_page_url') or

					config('settings.social_link.twitter_url') or

					config('settings.social_link.google_plus_url') or

					config('settings.social_link.linkedin_url') or

					config('settings.social_link.pinterest_url')

					)

					

													<div style="text-align: <?=(config('app.locale') == 'ar'?'left':'right')?>;" class="col-sm-12 col-xs-6 col-xxs-12 no-padding-lg  responsive-share-icon">

                    								    <ul>

                    								    <li>

                    								        <div style="display: inline;">

                                                                <a  style="display: inline;" class="app-icon" target="_blank"
																 href="{{ config('settings.social_link.google_plus_url') }}">

                                                                <img class="share-icon-size" src="{{ url('images/instagram.png') }}">

                                                                </a>

                                                            </div>

                                                            <div class="share-icon-distance" style="display: inline;">

                                                                <a  style="display: inline;" class="app-icon" target="_blank" href="{{ config('settings.social_link.facebook_page_url') }}">
															 
                                                                    <img  class="share-icon-size" src="{{ url('images/facebook.png') }}">

                                                                </a>

                                                            </div>

                                                            

                                                            <div style="display: inline;">

                                                                <a  style="display: inline;" class="app-icon" target="_blank" href="https://play.google.com/store/apps/details?id=com.gswebtech.dealnotdeal&hl=en_IN">

                                                                <span class="hide-visually">{{ t('Android App') }}</span>

                                                                <img class="app-icon-size" src="{{ url('images/site/google-play-badge.svg') }}" alt="{{ t('Available on Google Play') }}">

                                                                </a>

                                                            </div>

                                                            <div style="display: inline;">

                                                               <a  style="display: inline;" class="app-icon" target="_blank" href="https://apps.apple.com/in/app/deal-not-deal/id1473934840">

                                                               <span class="hide-visually">{{ t('iOS app') }}</span>

                                                               <img class="app-icon-size" src="{{ url('images/site/app-store-badge.svg') }}" alt="{{ t('Available on the App Store') }}">

                                                               </a>

                                                            </div>

                                                        </li>

                    								    </ul>

                    								</div>

				@endif

				

				

				<div style="clear: both"></div>

				

				

				<div class="col-lg-12">

				    <!--

					@if (config('settings.footer.show_payment_plugins_logos') and isset($paymentMethods) and $paymentMethods->count() > 0)

						<div class="text-center paymanet-method-logo">

						    

							 {{-- Payment Plugins --}}

							@foreach($paymentMethods as $paymentMethod)

								@if (file_exists(plugin_path($paymentMethod->name, 'public/images/payment.png')))

									<img src="{{ url('images/' . $paymentMethod->name . '/payment.png') }}" alt="{{ $paymentMethod->display_name }}" title="{{ $paymentMethod->display_name }}">

								@endif

							@endforeach

						</div>

				    @else

						<hr>

				    @endif

					-->

					

					<hr>

					

					<div class="copy-info text-center" style="color:#fff">

						© {{ date('Y') }} {{ config('settings.app.name') }}. {{ t('All Rights Reserved') }}.

						@if (config('settings.footer.show_powered_by'))

							@if (config('settings.footer.powered_by_info'))

								{{ t('Powered by') }} {!! config('settings.footer.powered_by_info') !!}

							@else

								{{ t('Powered by') }} <a href="http://www.bedigit.com" title="BedigitCom">Bedigit</a>.

							@endif

						@endif

					</div>

				</div>

			    

			</div>

		</div>

	</div>

</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script>

function getPath(){

	  return '<?php echo $baseUrl = URL::to('/'); ?>';

   }

   beforeSendCSRF =  function (xhr) {

	var token = '{{csrf_token()}}';

	if (token) {

		  return xhr.setRequestHeader('X-CSRF-TOKEN', token);

	}

 }

function isValidEmailAddress(emailAddress) {

    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;

    return pattern.test(emailAddress);

}



    $("body").delegate("#newsletter_email_submit", "click", function() {

        newsLetterVal = $("#newsletter_email").val();

        $("#newsletter_email_message").html('');

        if(!$.isNumeric(newsLetterVal))

    	{

    		if(!isValidEmailAddress(newsLetterVal))

    		{

    			$("#newsletter_email_message").html('<p style="color:red;">Invalid Email</p>');

    			return false;

    		}

    	}

    	

    param = "newsLetterVal="+newsLetterVal;

	$.ajax({

	type: "post",

	data : param,

	url: getPath()+'/save-news-letter-email',

    dataType: 'JSON',

	beforeSend: beforeSendCSRF,

	success: function(msg) {

// 		console.log(msg)

		$("#newsletter_email_message").html('<p style="color:green;">'+msg.msg+'</p>');

   		return false;

		}

	});

});

</script>

