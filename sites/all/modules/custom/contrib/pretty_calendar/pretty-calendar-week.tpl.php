<?php
/**
 * @file
 * Theme implementation to display week line.
 * That file useful when you need to show numbers of week.
 *
 * Variables:
 * - $days: The array of day cells.
 * - $delta: Week number from beginning of month
 */
?>

<div class="pretty-calendar-week">
<?php
  foreach ($days as $day) :
    print $day;
  endforeach;
?>
</div>
