<?php
global $user;
$currentpage = (is_string(arg(0))) ? arg(0) : '';
$node = (is_numeric(arg(1))) ? node_load(arg(1)) : '';
$nid = $node->nid;
$url = $currentpage . '/' . $nid;
if ($nid && $user->uid):
  ?>
  <div id="eventcustom_tabs" class="tabs clearfix">
    <ul class="tabs primary clearfix">

      <li class="active"><?php print l(t('View'), $url); ?></li>

      <?php if (og_user_access_entity('administer group', 'node', $node, $user)): ?>
        <li><?php print l(t('Administer group'), "node/$nid/group"); ?></li>
      <?php endif; ?>

      <?php if ($user->uid == 1 || og_user_access_entity("update any events content", 'node', $node, $user) ||
        (og_user_access_entity("update own events content", 'node', $node, $user) && $user->uid == $node->uid)
      ):
        ?>
        <li><?php print l(t('Edit'), "node/$nid/edit"); ?></li>
      <?php endif; ?>

      <?php if ($user->uid == 1): ?>
        <li class="last"><?php print l(t('Devel'), "node/$nid/devel"); ?></li>
      <?php endif; ?>


    </ul>
  </div>
<?php endif; ?>
