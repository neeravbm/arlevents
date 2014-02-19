<?php
/*
* @file 
*/


?>


<?php $tag = $block->subject ? 'section' : 'div'; ?>
<<?php print $tag; ?><?php print $attributes; ?>>
<div id="readytojoin">
  <div class="block-inner clearfix">
     <div class="block_action"><a href="#"> > <?php print t('Compare'); ?></a></div>
    <?php print render($title_prefix); ?>
    <?php if ($block->subject): ?>
      <h2<?php print $title_attributes; ?> class="block_title"><?php print $block->subject; ?></h2>
    <?php endif; ?>
    <?php print render($title_suffix); ?>
    
    <div<?php print $content_attributes; ?>>
      <?php print $content ?>
    </div>
  </div>
 </div> 
</<?php print $tag; ?>>
