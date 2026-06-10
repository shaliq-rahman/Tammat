

<link rel="shortcut icon" href="<?php echo e(\Storage::url(config('settings.app.favicon')) . getPictureVersion()); ?>">
	<?php if(Session::has('flash_notification')): ?>		
			<?php echo $__env->make('common.spacer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			<?php $paddingTopExists = true; ?>
		
        
        	<div class="container">
				<div class="row">
					<div class="col-lg-6 pstn_clss pstn_clss_h" >
						<?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
					</div>
				</div>
			</div> 
        <?php else: ?>        
        	<?php echo $__env->make('common.spacer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			<?php $paddingTopExists = true; ?>
		
        
        	<div class="container">
				<div class="row">
					<div class="col-lg-6 pstn_clss pstn_clss_h" >
						 
					</div>
				</div>
			</div> 
            
		<?php endif; ?>
        


<?php $__env->startSection('search'); ?>

	##parent-placeholder-3559d7accf00360971961ca18989adc0614089c0##

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

	<div class="main-container" id="homepage">
	
		
		
		<?php if(isset($sections) and $sections->count() > 0): ?>
			<?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php if(view()->exists($section->view)): ?>
				    <?php if($section->view != 'home.inc.locations' && $section->view != 'home.inc.featured'): ?>
				        <?php echo $__env->make($section->view, ['firstSection' => $loop->first], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			        <?php endif; ?>
				<?php endif; ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		<?php endif; ?>
		
	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('after_scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>