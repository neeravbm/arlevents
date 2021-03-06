<?php
/*
 *@file
 *  Strategic Initiatives tpl file used to show tabs 
 */
  drupal_add_js(drupal_get_path('theme','act') . '/js/act.js');
  /*$view = views_get_view('strategic_initiatives');
  $view->set_display("block");
  $view->render();  
  $counter = count($view->result);
  $results = $view->result;
  $terms = $view->result;*/
  $i = 1;
 global $base_url;
?>


<div class="content-bottom tabonhome">

    <div id="tabs-titles" class="hometabs">
      <?php foreach($results as $result): ?>
        <div id="tab-<?php print $result->tid; ?>" class="<?php print ($i == 1) ? 'current' : ''?>">
			<a href="#tab-<?php print $result->tid; ?>"><?php print $result->taxonomy_term_data_name;?></a>
		</div>
      <?php $i++; endforeach; ?>
    </div>
    
    <div id="tabs-contents">
        <?php foreach($terms as $term): ?>
          <div  id="tabcontent-<?php print $term->tid; ?>" class="content hometabs">
            <table>
				<tr>
				  <td>
				     <?php print drupal_render($term->field_field_strategic_image); ?>
				  </td>
                  <td class="focus">
				     <?php print text_summary($term->field_field_focus_areas[0]['rendered']['#markup'], $term->field_field_focus_areas[0]['raw']['format'], 400); ?>
				     <a href="/taxonomy/term/<?php print $term->tid; ?>">Read more</a>
				  </td>
				  <?php /*?>
                  <td>
				    <span class="title"><h2 class="project-title"><?php print t('Projects'); ?></h2></span>
				    <?php 
				    // Getting Upcoming Events
				          $alter = array(
							'max_length' => 200,
							'ellipsis' => TRUE,
							);
				          $output = '';
						  $view = views_get_view('strategic_initiatives_project');
						  $view->set_display("block");
						  $view->set_arguments(array($term->tid));
						  $view->render();  
						  $projects = $view->result;
						  
				          foreach($projects as $project){
							$node = node_load($project->nid);
							$body = field_get_items('node', $node, 'body');
							$output .=  '<span class="project_title"><h3>'.$project->node_title.'</h3></span>';
							$output .=  views_trim_text($alter, $body[0]['value']).l(t('read more'),"node/$node->nid");
						  }
						  
						  print $output;
						  
						  //echo  views_embed_view('strategic_initiatives_project', 'block', $term->tid);
				    ?>
                  </td>
                  * <?php */ ?>
                  
				</tr>
			  </table>
        </div>
        <?php endforeach; ?>
    </div>

</div>

    


<div id="accordion" class="homeaccordion">
<?php foreach($terms as $term): ?>
  <h3><?php print l($term->taxonomy_term_data_name,"taxonomy/term/$result->tid",array('query' => drupal_get_destination()));?></h3>
    <div>
     <div class="column first">
       <?php if (!empty($term->field_field_strategic_image[0]['raw']['filename'])): ?>
			 <?php $filename = $term->field_field_strategic_image[0]['raw']['filename'];?>
			 <img src="<?php print $base_url; ?>/sites/default/files/<?php print $filename; ?>">
       <?php endif; ?>
		  </div>
	<div class="column middle">
			 <?php 
			 $alter = array();
			 $value = $term->field_field_focus_areas[0]['raw']['value'];
			 $alter['html'] = FALSE;
             $alter['max_length'] = 317;
			 print  views_trim_text($alter, $value);?>
	</div>
	<?php /*?>
	<div class="column last">
			<span class="title"><h3 class="project-title"><?php print ('Projects'); ?></h3></span>
			<?php 
			// Getting Upcoming Events
				  $alter = array(
					'max_length' => 200,
					'ellipsis' => TRUE,
					);
				  $output = '';
				  $view = views_get_view('strategic_initiatives_project');
				  $view->set_display("block");
				  $view->set_arguments(array($term->tid));
				  $view->render();  
				  $projects = $view->result;
				  foreach($projects as $project){
					$node = node_load($project->nid);
					$body = field_get_items('node', $node, 'body');
					$output .=  views_trim_text($alter, $body[0]['value']).l(t('read more'),"node/$node->nid");
				  }
				  
				  print $output;
				  
				  //echo  views_embed_view('strategic_initiatives_project', 'block', $term->tid);
			?>
		</div>
		<?php */ ?>
	</div>
  <?php endforeach; ?>
</div>
