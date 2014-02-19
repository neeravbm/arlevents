<?php
/**
 * @file
 * Theme implementation to display calendar block.
 *
 * Variables:
 * - $daynames: The array of day names
 *              By default starts from monday: $daynames[0] => 'Mon'
 * - $content: Rendered weeks.
 * - $month_name: Selected month name
 * - $month_prev: Previous month time (Unix)
 * - $month_next: Next month time (Unix)
 */
global $base_url;
$current_month_date =  date('Y-m-d',$month_current);
$current_prev = date('Y-m-d',$month_prev);
$current_next = date('Y-m-d',$month_next);
$uid = (empty($uid)) ? arg(1) : $uid; 

$month_show = arg(2) != '' ? check_plain(arg(2)) : 'all';

?>

<div class="pretty-calendar-show">
  
      <div class="month-title"><?php print t('Show'); ?>: </div>
      
      <div id="month_show_container" rel="<?php print $month_current; ?>">
        <div class="calendar-current"></div>
        <select id="month_show" onchange="calendar_go('current',this.value,'<?php print $uid;?>');calendar_data('<?php print $current_month_date;?>',this.value,'<?php print $uid;?>')" >
          <option  value="all" <?php if($month_show == 'all') print 'selected';  ?>  >ALL</option>
          <option  value="rsvp" <?php if($month_show == 'rsvp') print 'selected';  ?>  >RSVP</option>
        </select>
      </div>
      
  <?php /* ?>    <div id="month_show" class="month-show" style="display: none">
			  <a href="javascript:calendar_go('current','all','<?php print $uid;?>'),calendar_data('<?php print $current_month_date;?>','all','<?php print $uid;?>');"  title="all" rel="<?php print $month_current; ?>">
				<div class="calendar-current all">ALL </div>
			  </a> 
			  
			  <a href="javascript:calendar_go('current','rsvp','<?php print $uid;?>'),calendar_data('<?php print $current_month_date;?>','rsvp','<?php print $uid;?>');"   title="rsvp" rel="<?php print $month_current; ?>">
				<div class="calendar-current rsvp">RSVP</div>
			  </a>
		  </div>
   <?php */ ?>   
      
      <div id="actics_addtocal" ><a onclick="actics_popup()">Add to Cal</a></div>
      
      <div id="actics_popup" class="actics_popup" style="display:none">
        
        <div id="allmeetup"> All Meetings
          <div>
					  <a href='<?php print $base_url.'/actics/all/'.  check_plain(arg(1)).'/'.$current_month_date; ?>' >iCal</a> 
          | <a href='<?php print $base_url.'/actics/all/'.  check_plain(arg(1)).'/'.$current_month_date; ?>' >Outlook</a> 
          | <a href='<?php print 'https://www.google.com/calendar/render?cid='.$base_url.'/actics/all/'.  check_plain(arg(1)).'/'.$current_month_date; ?>'>Google</a>
          </div>
        </div>
        
        <div id="rsvpmeetup"> My RSVPs
          <div>
						<a href='<?php print $base_url.'/actics/rsvp/'.  check_plain(arg(1)).'/'.$current_month_date; ?>'>iCal</a> | 
						<a href='<?php print $base_url.'/actics/rsvp/'.  check_plain(arg(1)).'/'.$current_month_date; ?>'>Outlook</a> | 
						<a href='<?php print 'https://www.google.com/calendar/render?cid='.$base_url.'/actics/rsvp/'.  check_plain(arg(1)).'/'.$current_month_date; ?>'>Google</a>
					</div>
        </div>
      
      </div>
      
</div>

<div class="block-calendar">
  <div class="calendar-container">
    <div class="calendar-daynames">
      <div class="pretty-calendar-day"><div class="calendar-value"><?php print $daynames[0]; ?></div></div>
      <div class="pretty-calendar-day"><div class="calendar-value"><?php print $daynames[1]; ?></div></div>
      <div class="pretty-calendar-day"><div class="calendar-value"><?php print $daynames[2]; ?></div></div>
      <div class="pretty-calendar-day"><div class="calendar-value"><?php print $daynames[3]; ?></div></div>
      <div class="pretty-calendar-day"><div class="calendar-value"><?php print $daynames[4]; ?></div></div>
      <div class="pretty-calendar-weekend"><div class="calendar-value"><?php print $daynames[5]; ?></div></div>
      <div class="pretty-calendar-weekend pretty-calendar-last"><div class="calendar-value"><?php print $daynames[6]; ?></div></div>
    </div>
    <?php print $content; ?>
	
    <div class="pretty-calendar-month">
      <a href="javascript:calendar_go('prev','<?php print $action; ?>','<?php print $uid;?>'), calendar_data('<?php print $current_prev;?>','<?php print $action; ?>','<?php print $uid;?>');" title="all"  rel="<?php print $month_prev; ?>">
        <div class="calendar-prev">&nbsp;</div>
      </a>
      <div class="month-title"><?php print $month_name; ?></div>
      <a href="javascript:calendar_go('next','<?php print $action; ?>','<?php print $uid;?>'), calendar_data('<?php print $current_next;?>','<?php print $action; ?>','<?php print $uid;?>');"  title="all" rel="<?php print $month_next; ?>">
        <div class="calendar-next">&nbsp;</div>
      </a>
    </div>
  </div>
</div>
