<?php

/**
 * @file
 * Default theme implementation to display a degree or certificate
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */



/**
* Let's set up some variables for use throughout the page
**/

 
// get the program taxonomy term for the current degree and use it to get an array of all the nodes in the same taxonomy.
  $items = field_get_items('node', $node, 'field_degree_program');
  $navtid = $items[0]['tid'];
  // returns node IDs for all nodes with the same program taxonomy term as this one
  $navnids = taxonomy_select_nodes($navtid, false, false, false);

// get the path for the program taxonomy term in question, so that we can link to all the class descriptions is this category.
  $current_cat = taxonomy_term_load($navtid);
  $path = taxonomy_term_uri($current_cat);
  $caturl = url($path['path']);
  // counter used for determining total credits for sub table
  $a = 0;


// this isn't being used right now. Might want to delete this and the fields
// set $degree_type variable depending on what degrees are selected
      //aat
      $fieldaat = field_get_items('node', $node, 'field_degree_type'); 
      $aat = $fieldaat[0]['value'];


      //aas-t
      $fieldaast = field_get_items('node', $node, 'field_aas_t_degree');      $aast = $fieldaast[0]['value'];

      //certificate
      $fieldcert = field_get_items('node', $node, 'field_certificate'); 
      $cert = $fieldcert[0]['value'];

      if($aat == 1 && $aast == 1) {
        $degree_type = 1;
      }
      elseif($aat == 1) {
        $degree_type = 2;
      }
      elseif($aast == 1) {
        $degree_type = 3;
      }
      elseif($cert == 1) {
        $degree_type = 4;
      }


?>


  <div class="left-col">
     <div class="glance-wrapper">
      <h5>Degree Info at a Glance</h5>
      <dl>
        <?php if(isset($content['field_type_of_degree'])): ?>
          <dt>Type of Degree</dt>
          <dd><?php print render($content['field_type_of_degree']); ?></dd>
        <?php endif; ?>
        <?php if(isset($content['field_quarters'])): ?>
          <dt>Estimated # of Quarters</dt>
          <dd><?php print render($content['field_quarters']); ?></dd>
        <?php endif; ?>
        <?php if(isset($content['field_estimated_cost'])): ?>  
          <dt>Estimated Cost</dt>
          <dd><?php print render($content['field_estimated_cost']); ?></dd>
         <?php endif; ?>  
        <?php if(isset($content['field_admission_dates'])): ?>   
          <dt>Admission Dates</dt>
          <dd><?php print render($content['field_admission_dates']); ?></dd>
        <?php endif; ?>
        <?php if(isset($content['field_degree_prereqs'])): ?>    
          <dt>Prerequisites</dt>
          <dd><?php print render($content['field_degree_prereqs']); ?></dd>
         <?php endif; ?> 
      </dl>

    </div>
  <div class="degree-body">
    <?php print render($content['body']); ?>
 </div>





    
  <?php




$node = node_load($nid);
$i = 0;

// give this function the name of an entity reference field that contains classes and elective clusters
// it outputs the whole table and popup boxes
  function boo_classes_output($field_name, $pagenode) {
  
  $total_credits = 0;
  $max_credits = 0;
  $max_credits_total = 0;
  $field = field_get_items('node', $pagenode, $field_name);
  global $i;
  $classes = array();
  foreach($field as $item) {
    $class = $item['entity'];
    $subnode = node_load($class->nid);
  

    if($subnode->type == 'elective_cluster') {
       $classes[$i]['index'] = $i;
      $classes[$i]['item'] = null;
      $classes[$i]['title'] = $class->title;
      $classes[$i]['credits'] = $class->field_total_credits[$pagenode->language][0]['value'];
      $classes[$i]['superscript'] = null;
    
      // create the sub array of classes assigned to the elective cluster
      $j = 0;
      
      foreach($class->field_elective_group[$pagenode->language] as $sub_class) {
        // load the fields associated with the field group
        $entity = entity_load('field_collection_item', array($sub_class['value']));
        
          $k = 0;
          foreach($entity as $sub_entity) {
            if (isset($sub_entity->field_description_sub_elective[$pagenode->language][0]['safe_value'])) {
              $classes[$i]['sub_elective_group'][$j]['description'] = $sub_entity->field_description_sub_elective[$pagenode->language][0]['safe_value'];
            }
              
               // load each of the class nodes listed in this elective group
               
              foreach($sub_entity->field_courses_sub as $sub_courses) {
                foreach($sub_courses as $sub_course) {
                  $sub_course_nid = $sub_course['target_id'];
                   $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['nid'] = $sub_course_nid;
                 $sub_course_node = node_load($sub_course_nid);



                 //  $sub_course_node = $node_load($sub_course['target_id']);

                  if (isset($sub_course_node->title)) {
                    $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['item'] = $sub_course_node->title;
                  }
                  if (isset($sub_course_node->field_credits[$pagenode->language][0]['value'])) {
                    $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['credits'] = $sub_course_node->field_credits[$pagenode->language][0]['value'];
                  }
                   $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['creditsmax'] = $sub_course_node->field_credit_maximum[$pagenode->language][0]['value'];
                   $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['title'] = $sub_course_node->field_class_title[$pagenode->language][0]['value'];
                   // set superscript value
                    if ($sub_course_node->field_capstone[$pagenode->language][0]['value'] == 1) {
                      $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['superscript'] = "<sup class=\"tooltip\" data-hover=\"CAP designates that this course meets the capstone requirement.\">CAP</sup>";
                    }
                    elseif ($sub_course_node->field_computer_literacy[$pagenode->language][0]['value'] == 1) {
                      $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['superscript'] = "<sup class=\"tooltip\" data-hover=\"CL designates that this course meets the computer literacy requirement.\">CL</sup>";
                    }
                    elseif ($sub_course_node->field_diversity_requirement[$pagenode->language][0]['value'] == 1) {
                      $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['superscript'] = "<sup class=\"tooltip\" data-hover=\"DIV designates that this course meets the diversity requirement.\">DIV</sup>";
                    }
                    else {
                      $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['superscript'] = null;
                    }
                            


                  $k++;
                }
              }
              $j++;


        }
      }
    }

    

    if($subnode->type == 'class') {
      $classes[$i]['index'] = $i;
      $classes[$i]['item'] = $class->title;
      $classes[$i]['title'] = $class->field_class_title[$pagenode->language][0]['value'];
      $classes[$i]['credits']= $class->field_credits[$pagenode->language][0]['value'];
      $classes[$i]['description'] = $class->field_description[$pagenode->language][0]['safe_value'];
      if (isset($class->field_credit_maximum[$pagenode->language][0]['value'])) {
        $classes[$i]['creditsmax'] = $class->field_credit_maximum[$pagenode->language][0]['value'];
      }

      // set superscript value
      if ($class->field_capstone[$pagenode->language][0]['value'] == 1) {
        $classes[$i]['superscript'] = "<sup class=\"tooltip\" data-hover=\"CAP designates that this course meets the capstone requirement.\">CAP</sup>";
      }
      elseif ($class->field_computer_literacy[$pagenode->language][0]['value'] == 1) {
        $classes[$i]['superscript'] = "<sup class=\"tooltip\" data-hover=\"CL designates that this course meets the computer literacy requirement.\">CL</sup>";
      }
      elseif ($class->field_diversity_requirement[$pagenode->language][0]['value'] == 1) {
        $classes[$i]['superscript'] = "<sup class=\"tooltip\" data-hover=\"DIV designates that this course meets the diversity requirement.\">DIV</sup>";
      }
      else {
        $classes[$i]['superscript'] = null;
      }

    }
    $i++;
  
  }



// use our global counter to determine credit totals and if we should display the total technical credits
  echo "<table class=\"degree-table\"><thead><th>Course Number</th><th>Class Title</th><th>Credits</th></thead>";
    global $a;
    global $technical_credits;
    global $max_credits_total;
    if ($a == 0) {
        $technical_credits = $total_credits;
      }
    if ($a > 0) {
      echo "<tr><td>&nbsp;</td><td>";
      echo "Technical Course Requirements";
      echo "</td><td>";
      echo $technical_credits;
      if($max_credits_total > $technical_credits) {
        echo "-";
        echo $max_credits_total;
      }
      echo "</td></tr>";
    }
    // output start of table 
    


    foreach($classes as $class_item) {
      echo "<tr class=\"class-popup\" id=\"js-class-popup-";
      echo $class_item['index'];
      echo "\">";
      echo "<td>";
      echo $class_item['item'];
        if($class_item['superscript']) {
     
          echo $class_item['superscript'];
    
        }
      echo "</td>";
      echo "<td>";
      echo $class_item['title'];
      echo "</td>";
      echo "<td>";
      echo $class_item['credits'];
      if($class_item['creditsmax']) {
          echo "-";
          echo $class_item['creditsmax'];
        }
      echo "</td>";
      echo "</tr>";
      $total_credits += $class_item['credits'];
      $max_credits += $class_item['creditsmax'];

    }

     echo "<tr><td>&nbsp;</td><td>";
     
     

      if ($a == 0) {
        echo "Technical Credits";
      }
      else {
        echo "Total Credits";
      }
      echo "</td><td>";
      if ($a == 0) {
        echo $total_credits;
        if($max_credits > 0) {
          echo "-";
          $max_credits_total = $total_credits + $max_credits;
          echo $max_credits_total;
        }
      }
      if ($a > 0) {
        echo $total_credits + $technical_credits;
        if ($max_credits_total > 0) {
          echo "-";
          echo $max_credits_total + $technical_credits;
        }


      }
      // set the technical credits value if this is our first time through.
      if ($a == 0) {
        $technical_credits = $total_credits;
      }
      $a++;
       echo "</td></tr>";
  echo "</table>";

  // Now for the popup divs 

    foreach($classes as $class_item) {
      echo "<div class=\"class-popup-window\" id=\"js-class-popup-window-";
      echo $class_item['index'];
      echo "\">";
      echo "<div class=\"class-popup-window-inner\">";
      echo "<h4 class=\"class-title\">";
      echo $class_item['title'];
      echo "</h4>";
      echo "<div class=\"class-wrapper\">";
      echo "<dl>";
      if($class_item['item'] !== null) {
        echo "<dt>Item #</dt>";
        echo "<dd>";
        echo $class_item['item'];
      }  
      if ($class_item['superscript'] !== null) {
  
        echo $class_item['superscript'];

      }
      echo "</dd>";
      echo "<dt>Total Credits</dt>";
      echo "<dd>";
      echo $class_item['credits'];
      if($class_item['creditsmax'] !== null) {
        echo "-";
        echo $class_item['creditsmax'];
      }
      echo "</dd>";
      echo "</dl>";


      echo "<p>";
      echo $class_item['description'];
      echo "</p>";
      echo "<div class=\"popup-tables-wrapper\">";
      // output descriptions and tables for electives

      if($class_item['sub_elective_group'] !== null) {
        //count the number of tables in this array. If there are more than two, we apply the small-table class
        $count = count($class_item['sub_elective_group']);
        foreach ($class_item['sub_elective_group'] as $sub_sub_elective_group) {
          echo "<div class=\"popup-table-item\">";
          echo "<h5>";
          echo $sub_sub_elective_group['description'];
          echo "</h5>";
            echo "<table class=\"degree-table";
            if($count > 2) {
              echo " table-small";
            }
            echo "\">";
            foreach($sub_sub_elective_group['sub_courses'] as $sub_sub_courses) {
              echo "<tr>";
              echo "<td>";
                echo $sub_sub_courses['item'];
                if($sub_sub_courses['superscript']) {
                  echo $sub_sub_courses['superscript'];
                }
                echo "</td>";
                echo "<td>";
                echo $sub_sub_courses['title'];
                echo "</td>";
                echo "<td>";
                echo $sub_sub_courses['credits'];
                if($sub_sub_courses['creditsmax']) {
                   echo "-";
                  echo $sub_sub_courses['creditsmax'];
                 }
                echo "</td>";
                
              echo "</tr>";
            }
            echo "</table>";
            echo "</div>";

        }
      }
      echo "</div>";
        
      echo "</div>";
      echo "<div class=\"class-popup-window-close\" id=\"js-popup-window-close-";
      echo $class_item['index'];
      echo "\">";
      echo "CLOSE";
      echo "</div></div></div>";

  }
}





echo "<h2 class=\"bar-heading\">";
echo "Degree Requirements";
echo "</h2>";

boo_classes_output('field_classes_in_this_degree', $node);

$degree_1_title = field_get_items('node', $node, 'field_degree_option_1_title'); 
$degree_1_title_val = $degree_1_title[0]['value'];
if($degree_1_title_val !== null) {

  echo "<h2 class=\"bar-heading\">";
  echo $degree_1_title_val;
  echo "</h2>";
  boo_classes_output('field_degree_option_1_courses', $node);

}


$degree_2_title = field_get_items('node', $node, 'field_degree_option_2_title'); 
$degree_2_title_val = $degree_2_title[0]['value'];
if($degree_2_title_val !== null) {

  echo "<h2 class=\"bar-heading\">";
  echo $degree_2_title_val;
  echo "</h2>";
  boo_classes_output('field_degree_option_2_courses', $node);

}

      ?>



  </div>


<div class="right-col">
<?php boo_snippet('search.php'); ?>
  
  <?php 

  foreach($navnids as $navnid) {
  // check if node's content type is degree or certificate

    $navnode = node_load($navnid);
    $type =$navnode->type;
    // check if node is the current node we're on
    $q = 0;

    if($type == 'degree_or_certificate' && $navnid !== $nid) {

      if($q == 0) {
        echo "
        
        <h3>Related Degrees & Certificates</h3>

        <ul>";
      }
      echo "<li>";
      echo "<a href=\"";
      echo boo_url($navnid);
      echo "\">";
      echo $navnode->title;
      echo "</a>";
      echo "</li>";
      if($q == 0) {
        echo "
      

        </ul>";
      }
      $q++;

    }




  }
  ?>


  <li>
  <a href="<?php echo $caturl;?>">View All <?php echo $current_cat->name; ?> Classes</a>
</li>
</ul>


<?php boo_snippet('lead-form.php'); ?>
<?php boo_snippet('sidebar-menu.php'); ?>





  </nav>
</div>
</div>
<pre>

</pre>
<div class="datestamp-wrapper">
This page was last updated on <?php echo date("F d, Y", $node->revision_timestamp); ?>.
</div>


