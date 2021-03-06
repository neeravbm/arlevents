<?php
/*
 *@file
 *  Strategic Initiatives tpl file used to show tabs 
 */
drupal_add_js(drupal_get_path('theme','act') . '/js/act.js');
  $view = views_get_view('strategic_initiatives');
  $view->set_display("block");
  $view->render();  
  $counter = count($view->result);
  $results = $view->result;
  $terms = $view->result;
  $i = 1;
 global $base_url;
?>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<script>
 $(function() {
  //$( "#accordion" ).accordion();
});
</script>

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
				     <h2 class="block_title"><?php print l($term->taxonomy_term_data_name,"taxonomy/term/$result->tid",array('query' => drupal_get_destination()));?></h2>
				     <?php print drupal_render($term->field_field_strategic_image); ?>
				  </td>
                  <td class="focus">
				     <span class="title"><h2 class="block_title">Focus Areas</h2></span>
				     <?php print drupal_render($term->field_field_focus_areas); ?>
				  </td>
                  <td>
				    <span class="title"><h2 class="project-title">Projects</h2></span>
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
							$output .=  views_trim_text($alter, $body[0]['value']).l('read more',"node/$node->nid");
						  }
						  
						  print $output;
						  
						  //echo  views_embed_view('strategic_initiatives_project', 'block', $term->tid);
				    ?>
                  </td>
                  
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
			 <h2 class="block_title"><?php print l($term->taxonomy_term_data_name,"taxonomy/term/$result->tid",array('query' => drupal_get_destination()));?></h2>
			 <?php 
			 $filename = $term->field_field_strategic_image[0]['raw']['filename'];
			 print drupal_render($term->field_field_strategic_image); ?>
			 <img src="<?php print $base_url; ?>/sites/default/files/<?php print $filename; ?>" width=100% >
		  </div>
	<div class="column middle">
			 <span class="title"><h2 class="block_title">Focus Areas</h2></span>
			 <?php print $term->field_field_focus_areas[0]['raw']['value']; ?>
	</div>
	<div class="column last">
			<span class="title"><h2 class="project-title">Projects</h2></span>
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
					$output .=  views_trim_text($alter, $body[0]['value']).l('read more',"node/$node->nid");
				  }
				  
				  print $output;
				  
				  //echo  views_embed_view('strategic_initiatives_project', 'block', $term->tid);
			?>
		</div>
	</div>
  <?php endforeach; ?>
</div>
