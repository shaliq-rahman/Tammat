<?php if(isset($paddingTopExists)): ?>
	<?php if(isset($firstSection) and !$firstSection): ?>
		<div class="h-spacer"></div>
	<?php else: ?>
		<?php if(!$paddingTopExists): ?>
			<div class="h-spacer"></div>
		<?php endif; ?>
	<?php endif; ?>
<?php else: ?>
	<div class="h-spacer"></div>
<?php endif; ?>