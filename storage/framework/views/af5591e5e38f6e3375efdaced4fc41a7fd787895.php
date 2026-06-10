<?php echo $__env->make('home.inc.spacer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="container">
	<div class="page-info page-info-lite rounded">
		<div class="text-center section-promo">
			<div class="row">
	
				<div class="col-sm-4 col-xs-6 col-xxs-12">
					<div class="iconbox-wrap">
						<div class="iconbox">
							<div class="iconbox-wrap-icon">
								<i class="icon icon-docs"></i>
							</div>
							<div class="iconbox-wrap-content">
								<h5><span><?php echo e($countPosts); ?></span></h5>
								<div class="iconbox-wrap-text"><?php echo e(t('Free ads')); ?></div>
							</div>
						</div>
					</div>
				</div>
	
				<div class="col-sm-4 col-xs-6 col-xxs-12">
					<div class="iconbox-wrap">
						<div class="iconbox">
							<div class="iconbox-wrap-icon">
								<i class="icon icon-group"></i>
							</div>
							<div class="iconbox-wrap-content">
								<h5><span><?php echo e($countUsers); ?></span></h5>
								<div class="iconbox-wrap-text"><?php echo e(t('Trusted Sellers')); ?></div>
							</div>
						</div>
					</div>
				</div>
	
				<div class="col-sm-4 col-xs-6 col-xxs-12">
					<div class="iconbox-wrap">
						<div class="iconbox">
							<div class="iconbox-wrap-icon">
								<i class="icon icon-map"></i>
							</div>
							<div class="iconbox-wrap-content">
								<h5><span><?php echo e($countCities . '+'); ?></span></h5>
								<div class="iconbox-wrap-text"><?php echo e(t('Locations')); ?></div>
							</div>
						</div>
					</div>
				</div>
	
			</div>
		</div>
	</div>
</div>
