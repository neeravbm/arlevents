<?php

/**
 * @file
 * Template to display a view as a table.
 *
 * - $title : The title of this group of rows.  May be empty.
 * - $header: An array of header labels keyed by field id.
 * - $caption: The caption for this table. May be empty.
 * - $header_classes: An array of header classes keyed by field id.
 * - $fields: An array of CSS IDs to use for each field id.
 * - $classes: A class or classes to apply to the table, based on settings.
 * - $row_classes: An array of classes to apply to each row, indexed by row
 *   number. This matches the index in $rows.
 * - $rows: An array of row items. Each row is an array of content.
 *   $rows are keyed by row number, fields within rows are keyed by field ID.
 * - $field_classes: An array of classes to apply to each field, indexed by
 *   field id, then row number. This matches the index in $rows.
 * @ingroup views_templates
 */
//dsm($rows);
//$privacyFields = array('field_keep_email_private','field_keep_name_private','field_keep_photo_private','field_keep_phone_number_private');
$privacyFields = array('field_keep_email_private','field_keep_photo_private','field_keep_phone_number_private');

$showData = false;

foreach ($rows as $k => $v) {
  
    if( empty($v['field_keep_email_private']) || 
        empty($v['field_keep_photo_private']) || 
        empty($v['field_keep_phone_number_private']) ) { $showData = true; $showRow[$k]=true; }
  
}
?>
<table <?php if ($classes) { print 'class="'. $classes . '" '; } ?><?php print $attributes; ?>>
   <?php if (!empty($title) || !empty($caption)) : ?>
     <caption><?php print $caption . $title; ?></caption>
  <?php endif; ?>
  <?php if (!empty($header)) : ?>
    <thead>
      <tr>
        <?php foreach ($header as $field => $label): ?>

         <?php if(in_array($field, $privacyFields)) continue; ?>

          <th <?php if ($header_classes[$field]) { print 'class="'. $header_classes[$field] . '" '; } ?>>
            <?php print $label; ?>
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
  <?php endif; ?>
  <tbody>
    <?php foreach ($rows as $row_count => $row): ?>
      <?php  if(!isset($showRow[$row_count]) ) { continue; } ?> 
        <tr <?php if ($row_classes[$row_count]) { print 'class="' . implode(' ', $row_classes[$row_count]) .'"';  } ?>>
        <?php foreach ($row as $field => $content): ?>
         <?php if(in_array($field, $privacyFields)) continue; ?>
          <td <?php if ($field_classes[$field][$row_count]) { print 'class="'. $field_classes[$field][$row_count] . '" '; } ?>
              <?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            
            <?php if($field == 'field_user_primary_phone' && ($row['field_keep_phone_number_private'] == 1)) continue; ?>
            <?php if($field == 'field_user_contact_email' && ($row['field_keep_email_private'] == 1)) continue; ?>
            <?php /*if($field == 'realname' && ($row['field_keep_name_private'] == 1)) continue;*/ ?>
            <?php if($field == 'picture' && ($row['field_keep_photo_private'] == 1)) continue; ?>

            <?php print $content; ?>
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
