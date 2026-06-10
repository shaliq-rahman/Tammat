<?php $__currentLoopData = $feeds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $title): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <link rel="alternate" type="application/rss+xml" href="<?php echo e(route("feeds.{$name}")); ?>" title="<?php echo e($title); ?>">
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
