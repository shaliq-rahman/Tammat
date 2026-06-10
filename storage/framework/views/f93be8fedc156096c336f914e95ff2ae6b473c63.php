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

				

				<div class="<?php echo e($colClass1); ?>">

					<div class="footer-col">

					    

						<!--<h4 class="footer-title"><?php echo e(t('About us')); ?></h4>-->

						

						<!--<h4 class="footer-title"></h4>-->

						

						<ul class="list-unstyled footer-nav">

							<?php if(isset($pages) and $pages->count() > 0): ?>

								<?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

									<li>

										<?php

											$linkTarget = '';

											if ($page->target_blank == 1) {

												$linkTarget = 'target="_blank"';

											}

										?>

										<?php if(!empty($page->external_link)): ?>

											<a href="<?php echo $page->external_link; ?>" rel="nofollow" <?php echo $linkTarget; ?>> <?php echo e($page->name); ?> </a>

										<?php else: ?>

											<?php $attr = ['slug' => $page->slug]; ?>

											<a href="<?php echo e(lurl(trans('routes.v-page', $attr), $attr)); ?>" <?php echo $linkTarget; ?>> <?php echo e($page->name); ?> </a>

										<?php endif; ?>

									</li>

								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

							<?php endif; ?>

						</ul>

					</div>

				</div>

			

				<div class="<?php echo e($colClass2); ?>">

					<div class="footer-col">

					    

					<!--<h4 class="footer-title"></h4>-->

					

						<!--<h4 class="footer-title"><?php echo e(t('Contact & Sitemap')); ?></h4>-->

						<ul class="list-unstyled footer-nav">

							<li><a href="<?php echo e(lurl(trans('routes.contact'))); ?>"> <?php echo e(t('Contact us')); ?> </a></li>

							<?php $attr = ['countryCode' => config('country.icode')]; ?>

							<li><a href="<?php echo e(lurl(trans('routes.v-sitemap', $attr), $attr)); ?>"> <?php echo e(t('Sitemap')); ?> </a></li>

							

							

							

							<!--<?php if(\App\Models\Country::where('active', 1)->count() > 1): ?>-->

							<!--	<li><a href="<?php echo e(lurl(trans('routes.countries'))); ?>"> <?php echo e(t('Countries')); ?> </a></li>-->

							<!--<?php endif; ?>-->

							

							

							

						</ul>

					</div>

				</div>

					

				

				

				<?php if(

					config('settings.other.ios_app_url') or

					config('settings.other.android_app_url') or

					config('settings.social_link.facebook_page_url') or

					config('settings.social_link.twitter_url') or

					config('settings.social_link.google_plus_url') or

					config('settings.social_link.linkedin_url') or

					config('settings.social_link.pinterest_url')

					): ?>

					

													<div style="text-align: <?=(config('app.locale') == 'ar'?'left':'right')?>;" class="col-sm-12 col-xs-6 col-xxs-12 no-padding-lg  responsive-share-icon">

                    								    <ul>

                    								    <li>

                    								        <div style="display: inline;">

                                                                <a  style="display: inline;" class="app-icon" target="_blank"
																 href="<?php echo e(config('settings.social_link.google_plus_url')); ?>">

                                                                <img class="share-icon-size" src="https://tmmat.com/images/instagram.png">

                                                                </a>

                                                            </div>

                                                            <div class="share-icon-distance" style="display: inline;">

                                                                <a  style="display: inline;" class="app-icon" target="_blank" href="<?php echo e(config('settings.social_link.facebook_page_url')); ?>">
															 
                                                                    <img  class="share-icon-size" src="https://tmmat.com/images/facebook.png">

                                                                </a>

                                                            </div>

                                                            

                                                            <div style="display: inline;">

                                                                <a  style="display: inline;" class="app-icon" target="_blank" href="https://play.google.com/store/apps/details?id=com.gswebtech.dealnotdeal&hl=en_IN">

                                                                <span class="hide-visually"><?php echo e(t('Android App')); ?></span>

                                                                <img class="app-icon-size" src="<?php echo e(url('images/site/google-play-badge.svg')); ?>" alt="<?php echo e(t('Available on Google Play')); ?>">

                                                                </a>

                                                            </div>

                                                            <div style="display: inline;">

                                                               <a  style="display: inline;" class="app-icon" target="_blank" href="https://apps.apple.com/in/app/deal-not-deal/id1473934840">

                                                               <span class="hide-visually"><?php echo e(t('iOS app')); ?></span>

                                                               <img class="app-icon-size" src="<?php echo e(url('images/site/app-store-badge.svg')); ?>" alt="<?php echo e(t('Available on the App Store')); ?>">

                                                               </a>

                                                            </div>

                                                        </li>

                    								    </ul>

                    								</div>

				<?php endif; ?>

				

				

				<div style="clear: both"></div>

				

				

				<div class="col-lg-12">

				    <!--

					<?php if(config('settings.footer.show_payment_plugins_logos') and isset($paymentMethods) and $paymentMethods->count() > 0): ?>

						<div class="text-center paymanet-method-logo">

						    

							 

							<?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paymentMethod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

								<?php if(file_exists(plugin_path($paymentMethod->name, 'public/images/payment.png'))): ?>

									<img src="<?php echo e(url('images/' . $paymentMethod->name . '/payment.png')); ?>" alt="<?php echo e($paymentMethod->display_name); ?>" title="<?php echo e($paymentMethod->display_name); ?>">

								<?php endif; ?>

							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

						</div>

				    <?php else: ?>

						<hr>

				    <?php endif; ?>

					-->

					

					<hr>

					

					<div class="copy-info text-center" style="color:#fff">

						© <?php echo e(date('Y')); ?> <?php echo e(config('settings.app.name')); ?>. <?php echo e(t('All Rights Reserved')); ?>.

						<?php if(config('settings.footer.show_powered_by')): ?>

							<?php if(config('settings.footer.powered_by_info')): ?>

								<?php echo e(t('Powered by')); ?> <?php echo config('settings.footer.powered_by_info'); ?>


							<?php else: ?>

								<?php echo e(t('Powered by')); ?> <a href="http://www.bedigit.com" title="BedigitCom">Bedigit</a>.

							<?php endif; ?>

						<?php endif; ?>

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

	var token = '<?php echo e(csrf_token()); ?>';

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

