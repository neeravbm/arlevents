<?php
/*
 * Implementing hook_preprocess_node()
 */
function act_alpha_preprocess_node(&$vars) {
  
  if (array_key_exists('node', $vars)) {
    if (is_object($vars['node'])) {
      $node = $vars['node'];
      
      
      if ($node->type == 'innerpage') {
        $field_custom_layout = field_get_items('node', $node, 'field_custom_layout');
        $custom_layout = $field_custom_layout[0]['value'];
        if ($custom_layout) {
          $vars['theme_hook_suggestions'][] = 'node__innerpage__standard';
        }
      }
      
      if ($node->type == 'page') {
          $vars['data']  = _act_about_us_tabs_content($vars['node']);
          
            $view = views_embed_view('event_member_list','page_1');
            $vars['data']['memberlist'] = $view;
            
      }
      
      
    }
    
    if ($node->type == 'group' || $node->type == 'sub_community') {
		global $user;
			$vars['content']['links']['custom_messages'] = array(
					'#theme' => 'link',
					'#text' => 'Send Message',
					'#attributes' => array(
							 'class' => 'custom_message_links',
						),
					 '#links' => array(
						'my_message_link' => array(
							'title' => l('send Message','messages/new/',array('attributes' => array('class' => 'message-wrapper message-link'),'query' => array('t' => 'community', 'n' => $node->nid))),
							'html' => TRUE,
						),
					),
				);
	}
   
  }
  
  if (is_numeric(arg(1))) {
    $node = node_load(arg(1));
    if (is_object($node)) {
      if ($node->type == 'events') {
        $vars['classes_array'][] = 'events-custom';
      }
    }
  }
  
  
  if (array_key_exists('content', $vars)) {
    $content = &$vars['content'];
      if (array_key_exists('field_document_interests', $content)) {
        foreach ($content['field_document_interests']['#items'] as $index => $info) {
          $tid = $info['tid'];
          $content['field_document_interests'][$index]['#href'] = 'taxonomies/terms/' . $tid;
        }
      }
  }
  
  if($vars['view_mode'] == 'teaser') {
		$vars['user_picture'] = '';
	}
  
}



/*
 * Function callback for Page 
 * Return Member list for About us Page
 * Used in node--page.tpl.php
 */
function _act_about_us_tabs_content ($node) {


  $person1 = empty($node->field_person) ? '' : drupal_render(field_view_field('node', $node, 'field_person'));
  $person2 = empty($node->field_person_2) ? '' : drupal_render(field_view_field('node', $node, 'field_person_2'));
  $person3 = empty($node->field_person_3) ? '' : drupal_render(field_view_field('node', $node, 'field_person_3'));
  $person4 = empty($node->field_person_4) ? '' : drupal_render(field_view_field('node', $node, 'field_person_4'));
  
  
  $acc1 = $acc2 = $acc3 = $acc4 ='';

  if(!empty($node->field_accordian_1['und'])) {
    $accItems = $node->field_accordian_1['und'];
    $acc1 = '<div id="accr_page_tab1">';
    foreach ($accItems as $key => $value) {
      $item = entity_load('field_collection_item', array($value['value']));
      $h = !empty($item[$value['value']]->field_acc_heading['und'][0]['value']) ? $item[$value['value']]->field_acc_heading['und'][0]['value'] : '';
      $c = !empty($item[$value['value']]->field_acc_content['und'][0]['value']) ? $item[$value['value']]->field_acc_content['und'][0]['value'] : '';
      $acc1 .= '<div class="accr_sect_page"><a>'.$h.'</a></div>
                <div class="accr_content">'.$c.'</div>';
    }
    $acc1 .= '</div>';
  }

  if(!empty($node->field_accordian_2['und'])) {
    $accItems = $node->field_accordian_2['und'];
    $acc2 = '<div id="accr_page_tab2">';
    foreach ($accItems as $key => $value) {
      $item = entity_load('field_collection_item', array($value['value']));
      $h = !empty($item[$value['value']]->field_acc_heading_2['und'][0]['value']) ? $item[$value['value']]->field_acc_heading_2['und'][0]['value'] : '';
      $c = !empty($item[$value['value']]->field_acc_content_2['und'][0]['value']) ? $item[$value['value']]->field_acc_content_2['und'][0]['value'] : '';
      $acc2 .= '<div class="accr_sect_page"><a>'.$h.'</a></div>
                <div class="accr_content">'.$c.'</div>';
    }
    $acc2 .= '</div>';
  }

  if(!empty($node->field_accordian_3['und'])) {
    $accItems = $node->field_accordian_3['und'];
    $acc3 = '<div id="accr_page_tab3">';
    foreach ($accItems as $key => $value) {
      $item = entity_load('field_collection_item', array($value['value']));
      $h = !empty($item[$value['value']]->field_acc_heading_3['und'][0]['value']) ? $item[$value['value']]->field_acc_heading_3['und'][0]['value'] : '';
      $c = !empty($item[$value['value']]->field_acc_content_3['und'][0]['value']) ? $item[$value['value']]->field_acc_content_3['und'][0]['value'] : '';
      $acc3 .= '<div class="accr_sect_page"><a>'.$h.'</a></div>
                <div class="accr_content">'.$c.'</div>';
    }
    $acc3 .= '</div>';
  }

  if(!empty($node->field_accordian_4['und'])) {
    $accItems = $node->field_accordian_4['und'];
    $acc4 = '<div id="accr_page_tab4">';
    foreach ($accItems as $key => $value) {
      $item = entity_load('field_collection_item', array($value['value']));
      $h = !empty($item[$value['value']]->field_acc_heading_4['und'][0]['value']) ? $item[$value['value']]->field_acc_heading_4['und'][0]['value'] : '';
      $c = !empty($item[$value['value']]->field_acc_content_4['und'][0]['value']) ? $item[$value['value']]->field_acc_content_4['und'][0]['value'] : '';
      $acc4 .= '<div class="accr_sect_page"><a>'.$h.'</a></div>
                <div class="accr_content">'.$c.'</div>';
    }
    $acc4 .= '</div>';
  }

  
  return array(
                'person1'=>$person1,'person2'=>$person2,'person3'=>$person3,'person4'=>$person4,
                'acc1'=>$acc1,'acc2'=>$acc2,'acc3'=>$acc3,'acc4'=>$acc4);
}
