<?php
/**
 * @file
 * Theme implementation to display day cell.
 *
 * Variables:
 * - $number: Day number from beginning of month
 * - $date: Day number from beginning of month
 * - $delta: Day number from beginning of week
 * - $class: Default cell class
 * - $count: Node counter
 * - $using_tooltip: Using tooltips (boolean)
 * - $is_empty: Blank cell (boolean)
 *
 * Note:
 *   We can use l() function to generate a link, but in that case,
 *   the resulting code is very difficult to read.
 */
$current_date = date('Y-m-d');
?>

<?php if ($count > 0) { ?>
<a class="tooltip" title="<?php
  print pretty_calendar_plural($count);
?>" href="javascript:calendar_data('<?php print $my_date ?>', '<?php print $action; ?>', '<?php print $uid; ?>');" <?php 
  print ($using_tooltip ? ' rel="' . $date . '"' : '');
?>>

  <div class="<?php print $class . ($is_empty ? ' blank' : ''); ?>">
    <div class="calendar-value"><?php print $number; ?></div>
  </div>
</a>

<?php } else { ?>
  <div class="<?php print $class . ($is_empty ? ' blank' : ''); ?>">
    <div class="calendar-value <?php if($current_date == $my_date) print ' active';?>" >
        <a id="empty" href="javascript:calendar_data('<?php print $my_date; ?>', '<?php print $action; ?>', '<?php print $uid; ?>');" >
            <?php print $number; ?></div>
        </a>
  </div>
<?php } ?>
