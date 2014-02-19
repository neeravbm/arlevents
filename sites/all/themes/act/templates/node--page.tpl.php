<?php //print render($content['body']);
drupal_add_js(path_to_theme() . '/js/mytabs.js');
$current_path = current_path();
if(!empty($node->field_tab1_heading)): ?>
	<div class="container gatewayFeature tabbedContent">
	    <ul class="gTabs doubleLineTabs">
	        <li class="tab-1 selected">
	            <a href="#glance"><?php echo $node->field_tab1_heading[LANGUAGE_NONE][0]['value']; ?></a>
	        </li>
	        <?php if(!empty($node->field_tab2_heading)): ?>
	        	<li class="tab-2">
	        	    <a href="#policy"><?php echo $node->field_tab2_heading[LANGUAGE_NONE][0]['value']; ?></a>
	        	</li>
	        <?php endif; ?>
          <?php if(!empty($node->field_tab3_heading)): ?>
		        	<li class="tab-3 last">
		        	    <a href="#history"><?php echo $node->field_tab3_heading[LANGUAGE_NONE][0]['value']; ?></a>
		        	</li>
	        <?php endif; ?>
          <?php if(!empty($node->field_tab4_heading)): ?>
		        	<li class="tab-4 last">
		        	    <a href="#members"><?php echo $node->field_tab4_heading[LANGUAGE_NONE][0]['value']; ?></a>
		        	</li>
	        <?php endif; ?>
	    </ul>
    
	    <div class="gContent">
          <div class="gHeading"><a href="#glance"><?php echo $node->field_tab1_heading[LANGUAGE_NONE][0]['value']; ?></a></div>
	        <div class="gInner1 gInner selected">
		    	<?php if(!empty($node->field_tab1_content_right)): ?>
		    		<div class="tabMain">
			    		<?php echo $node->field_tab1_content[LANGUAGE_NONE][0]['value']; ?>		
	    			</div>
	    			<div class="tabSide">
	    				<?php echo $node->field_tab1_content_right[LANGUAGE_NONE][0]['value']; ?>
	    			</div>
	    		<?php else: ?>
		        	<?php if(!empty($node->field_tab1_content)) echo $node->field_tab1_content[LANGUAGE_NONE][0]['value']; ?>		
		        <?php endif; ?>
           
          <?php echo $data['person1']; ?>		
          <?php echo $data['acc1']; ?>		
		        
            
	        </div>
	        
	        <?php if(!empty($node->field_tab2_content)): ?>
            <div class="gHeading"><a href="#"><?php echo $node->field_tab2_heading[LANGUAGE_NONE][0]['value']; ?></a></div>
	        	<div class="gInner2 gInner">
		    		<?php if(!empty($node->field_tab2_content_right)): ?>
		    			<div class="tabMain">
			    			<?php echo $node->field_tab2_content[LANGUAGE_NONE][0]['value']; ?>		
	    				</div>
	    				<div class="tabSide">
	    					<?php echo $node->field_tab2_content_right[LANGUAGE_NONE][0]['value']; ?>
	    				</div>
	    			<?php else: ?>
		        		<?php echo $node->field_tab2_content[LANGUAGE_NONE][0]['value']; ?>		
		        	<?php endif; ?>
               
          <?php echo $data['person2']; ?>		
		      <?php echo $data['acc2']; ?>		
		      
	        	</div>
        <?php endif; ?>
        
	        	<?php if(!empty($node->field_tab3_content)): ?>
              <div class="gHeading"><a href="#"><?php echo $node->field_tab3_heading[LANGUAGE_NONE][0]['value']; ?></a></div>
	            <div class="gInner3 gInner">
              	<?php if(!empty($node->field_tab3_content_right)): ?>
			        	    <div class="tabMain">
			        	    	<?php echo $node->field_tab3_content[LANGUAGE_NONE][0]['value']; ?>		
			        	    </div>
			        	    <div class="tabSide">
			        	    	<?php echo $node->field_tab3_content_right[LANGUAGE_NONE][0]['value']; ?>
			        	    </div>
			        	<?php else: ?>
			        	    <?php echo $node->field_tab3_content[LANGUAGE_NONE][0]['value']; ?>		
			        	<?php endif; ?>
                 
                <?php echo $data['person3']; ?>		
                <?php echo $data['acc3'];; ?>		
		      
		        	</div>
	        	<?php endif; ?>
	        
        
        <?php if(!empty($node->field_tab4_content)): ?>
              <div class="gHeading"><a href="#"><?php echo $node->field_tab4_heading[LANGUAGE_NONE][0]['value']; ?></a></div>
		        	<div class="gInner4 gInner">
	            	<?php if(!empty($node->field_tab4_content_right)): ?>
			        	    <div class="tabMain">
			        	    	<?php echo $node->field_tab4_content[LANGUAGE_NONE][0]['value']; ?>		
			        	    </div>
			        	    <div class="tabSide">
			        	    	<?php echo $node->field_tab4_content_right[LANGUAGE_NONE][0]['value']; ?>
			        	    </div>
			        	<?php else: ?>
			        	    <?php echo $node->field_tab4_content[LANGUAGE_NONE][0]['value']; ?>		
			        	<?php endif; ?>
                 
               <?php echo $data['person4']; ?>		
                <?php echo $data['acc4']; ?>		
                <?php if(request_path() == 'content/about-us' ) echo $data['memberlist']; ?>		
 		      
		        	</div>
	        	<?php endif; ?>
	        
        
	    </div>
	</div>
<?php endif; ?>
 
