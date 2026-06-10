<?php if(isset($categoriesOptions) and isset($categoriesOptions['type_of_display'])): ?>

	<?php echo $__env->make('home.inc.spacer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

	<div class="container no-border">	

		<div class="col-lg-12 content-box layout-section" style="border-radius: 40px; background: #fff0 ;border: none;">

			<div class="col-lg-12 row row-featured row-featured-category" style="border-radius: 40px">

				<div class="col-lg-12 box-title no-border" style="text-align: center;background-color: #ff5555c7 ; border-radius: 40px;margin:7px;display:none;">

                <br />

					<span class="title-3" style="color: #fff"><?php echo e(t('Browse by')); ?> <span style="font-weight: bold;"><?php echo e(t('Category')); ?></span></span>

                 <br />

                 

                    <div class="inner">

                        <h2>

                            <?php $attr = ['countryCode' => config('country.icode')]; ?>

							<a href="<?php echo e(lurl(trans('routes.v-sitemap', $attr), $attr)); ?>" class="sell-your-item" style="top: 30px; display:none">

								<?php echo e(t('View more')); ?> <i class="icon-th-list"></i>

							</a>

						</h2>

					</div>

				</div>

				

				<?php if($categoriesOptions['type_of_display'] == 'c_picture_icon'): ?>

					

					<?php if(isset($categories) and $categories->count() > 0): ?>

						<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 col-lg-2-new f-category" style="border:none;border-radius: 40px" id="">

								<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $cat->slug]; 

								

								

								$final_cat_id = $cat->id;

								if(config('app.locale')!='en'){

								    

								      $cat_org = DB::table('categories')->where('id', $cat->translation_of)

	                                   

	                                    ->first();

								    

								  $final_cat_id =   $cat_org->id;

								}

								

								

								

								?>

								

								

								

								

								

								<a href="/<?php echo e(config('app.locale')); ?>/category/<?php echo e($cat->slug); ?> ">

									<img src="<?php echo e(\Storage::url($cat->picture) . getPictureVersion()); ?>" class="img-responsive" alt="img">

									<h6> <?php echo e($cat->name); ?> </h6>

								</a>

							</div>

						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

					<?php endif; ?>

					

				<?php elseif(in_array($categoriesOptions['type_of_display'], ['cc_normal_list', 'cc_normal_list_s'])): ?>

					

					<div style="clear: both;"></div>

					<?php $styled = ($categoriesOptions['type_of_display'] == 'cc_normal_list_s') ? ' styled' : ''; ?>

					 

					<?php if(isset($categories) and $categories->count() > 0): ?>

						<div class="list-categories-children<?php echo e($styled); ?>">

							<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cols): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

								<div style="padding-right: 8px;" class="col-md-2 col-sm-4 <?php echo e((count($categories) == $key+1) ? 'last-column' : ''); ?>">

									<?php $__currentLoopData = $cols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iCat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

										<div class="cat-list">

											<h3 class="cat-title rounded">

												<?php if(isset($categoriesOptions['show_icon']) and $categoriesOptions['show_icon'] == 1): ?>

													<i class="<?php echo e(isset($iCat->icon_class) ? $iCat->icon_class : 'icon-ok'); ?>"></i>&nbsp;

												<?php endif; ?>

												<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $iCat->slug]; ?>

												<a href="<?php echo e(lurl(trans('routes.v-search-cat', $attr), $attr)); ?>">

													<?php echo e($iCat->name); ?> <span class="count"></span>

												</a>

												<!--<span data-target=".cat-id-<?php echo e($iCat->id); ?>" data-toggle="collapse" class="btn-cat-collapsed collapsed">-->

												<!--	<span class="icon-down-open-big"></span>-->

												<!--</span>-->

											</h3>

											

											<!--<ul class="cat-collapse collapse in cat-id-<?php echo e($iCat->id); ?> long-list-home">-->

											<!--	<?php if(isset($subCategories) and $subCategories->has($iCat->tid)): ?>-->

											<!--		<?php $__currentLoopData = $subCategories->get($iCat->tid); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iSubCat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>-->

											<!--			<li>-->

											<!--				<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $iCat->slug, 'subCatSlug' => $iSubCat->slug]; ?>-->

											<!--				<a href="<?php echo e(lurl(trans('routes.v-search-subCat', $attr), $attr)); ?>">-->

											<!--					<?php echo e($iSubCat->name); ?>-->

											<!--				</a>-->

											<!--			</li>-->

											<!--		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>-->

											<!--	<?php endif; ?>-->

											<!--</ul>-->

											

											

										</div>

									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

								</div>

							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

							<div style="clear: both;"></div>

						</div>

					<?php endif; ?>

					

				<?php else: ?>

					

					<?php

					$listTab = [

						'c_circle_list' => 'list-circle',

						'c_check_list'  => 'list-check',

						'c_border_list' => 'list-border',

					];

					$catListClass = (isset($listTab[$categoriesOptions['type_of_display']])) ? 'list ' . $listTab[$categoriesOptions['type_of_display']] : 'list';

					?>

					<?php if(isset($categories) and $categories->count() > 0): ?>

						<div class="list-categories">

							<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

								<ul class="cat-list <?php echo e($catListClass); ?> col-xs-4 <?php echo e((count($categories) == $key+1) ? 'cat-list-border' : ''); ?>">

									<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

										<li>

											<?php if(isset($categoriesOptions['show_icon']) and $categoriesOptions['show_icon'] == 1): ?>

												<i class="<?php echo e(isset($cat->icon_class) ? $cat->icon_class : 'icon-ok'); ?>"></i>&nbsp;

											<?php endif; ?>

											<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $cat->slug]; ?>

											<a href="<?php echo e(lurl(trans('routes.v-search-cat', $attr), $attr)); ?>">

												<?php echo e($cat->name); ?>


											</a>

										</li>

									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

								</ul>

							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

						</div>

					<?php endif; ?>

					

				<?php endif; ?>

		

			</div>

		</div>

	</div>

<?php endif; ?>



<?php $__env->startSection('before_scripts'); ?>

	##parent-placeholder-094e37d5f5003ce853bb823b74f26393141d779d##

	<?php if(isset($categoriesOptions) and isset($categoriesOptions['max_sub_cats']) and $categoriesOptions['max_sub_cats'] >= 0): ?>

		<script>

			var maxSubCats = <?php echo e((int)$categoriesOptions['max_sub_cats']); ?>;

		</script>

	<?php endif; ?>

<?php $__env->stopSection(); ?>

