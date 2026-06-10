<!-- Modal Change Country -->
<div class="modal fade modalHasList" id="selectCountry" tabindex="-1" role="dialog" aria-labelledby="selectCountryLabel"
	 aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width: 1216px !important;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only"><?php echo e(t('Close')); ?></span>
				</button>
				<h4 class="modal-title uppercase font-weight-bold" id="selectCountryLabel">
					<i class="icon-map"></i> <?php echo e(t('Select your Country')); ?>

				</h4>
			</div>
			<div class="modal-body" style="max-height: 550px;">
				<div class="row">
					<div class="row" style="padding: 0 20px">
						<?php if(isset($countryCols)): ?>
							<?php $__currentLoopData = $countryCols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<ul class="cat-list col-sm-2 col-xs-6" style="margin:0px;padding:0px">
									<?php $__currentLoopData = $col; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php
										$countryLang = App\Helpers\Localization\Country::getLangFromCountry($country->get('languages'));
										?>
									<li>
										<img src="<?php echo e(url('images/blank.gif') . getPictureVersion()); ?>" class="flag flag-<?php echo e(($country->get('icode')=='uk') ? 'gb' : $country->get('icode')); ?>" style="margin-bottom: 4px; margin-right: 5px;">
										<a href="<?php echo e(url($countryLang->get('abbr') . '/?d=' . $country->get('code'))); ?>">
											<?php echo e(str_limit($country->get('name'), 100)); ?>

										</a>
									</li>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</ul>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /.modal -->
