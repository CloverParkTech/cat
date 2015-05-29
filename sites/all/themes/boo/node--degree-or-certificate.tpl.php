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


// set $degree_type variable depending on what degrees are selected
      //aat
      $fieldaat = field_get_items('node', $node, 'field_degree_type'); 
      $aat = $fieldaat[0]['value'];


      //aas-t
      $fieldaast = field_get_items('node', $node, 'field_aas_t_degree'); 
      $aast = $fieldaast[0]['value'];

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

<div class="grid">
  <div class="col17">
     <div class="glance-wrapper">
      <h5>Degree Info at a Glance</h5>
      <?php print render($content['field_type_of_degree']); ?>
      <?php print render($content['field_quarters']); ?>
      <?php print render($content['field_estimated_cost']); ?>
      <?php print render($content['field_admission_dates']); ?>
      <?php print render($content['field_degree_prereqs']); ?>

    </div>
  <div class="degree-body">
    <?php print render($content['body']); ?>
 </div>





    
  <?php
  // given the ID of a class or elective group, outputs a table row and a lightbox with the same ID





$node = node_load($nid);

// give this function the name of an entity reference field that contains classes and elective clusters
// it outputs the whole table and popup boxes
  function boo_classes_output($field_name, $pagenode) {
  
  $total_credits = 0;
  $field = field_get_items('node', $pagenode, $field_name);
  $i = 0;
  $classes = array();
  foreach($field as $item) {
    $class = $item['entity'];
    $subnode = node_load($class->nid);
  

    if($subnode->type == 'elective_cluster') {
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
              $classes[$i]['sub_elective_group'][$j]['description'] = $sub_entity->field_description_sub_elective[$pagenode->language][0]['safe_value'];
        
              
               // load each of the class nodes listed in this elective group
               
              foreach($sub_entity->field_courses_sub as $sub_courses) {
                foreach($sub_courses as $sub_course) {
                  $sub_course_nid = $sub_course['target_id'];
                   $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['nid'] = $sub_course_nid;
                 $sub_course_node = node_load($sub_course_nid);



                 //  $sub_course_node = $node_load($sub_course['target_id']);

                   $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['item'] = $sub_course_node->title;
                   $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['credits'] = $sub_course_node->field_credits[$pagenode->language][0]['value'];
                   $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['title'] = $sub_course_node->field_class_title[$pagenode->language][0]['value'];

              


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
      if ($class->field_credit_maximum[$pagenode->language][0]['value']) {
        $classes[$i]['creditsmax'] = $class->field_credit_maximum[$pagenode->language][0]['value'];
      }

      // set superscript value
      if ($class->field_capstone[$pagenode->language][0]['value'] == 1) {
        $classes[$i]['superscript'] = "CAP";
      }
      elseif ($class->field_computer_literacy[$pagenode->language][0]['value'] == 1) {
        $classes[$i]['superscript'] = "CL";
      }
      elseif ($class->field_diversity_requirement[$pagenode->language][0]['value'] == 1) {
        $classes[$i]['superscript'] = "DIV";
      }
      else {
        $classes[$i]['superscript'] = null;
      }

    }
    $i++;
  
  }



// use our global counter to determine credit totals and if we should display the total technical credits
  echo "<table><thead><th>Course Number</th><th>Class Title</th><th>Credits</th></thead>";
    global $a;
    global $technical_credits;
    if ($a == 0) {
        $technical_credits = $total_credits;
      }
    if ($a > 0) {
      echo "<tr><td>&nbsp;</td><td>";
      echo "Technical Course Requirements";
      echo "</td><td>";
      echo $technical_credits;
      echo "</td></tr>";
    }
    // output start of table 
    


    foreach($classes as $class_item) {
      echo "<tr class=\"class-popup\" id=\"js-class-popup-";
      echo $class_item['index'];
      echo "\">";
      echo "<td>";
      echo $class_item['item'];
      echo "</td>";
      echo "<td>";
      echo $class_item['title'];
      echo "</td>";
      echo "<td>";
      echo $class_item['credits'];
      echo "</td>";
      echo "</tr>";
      $total_credits += $class_item['credits'];

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
      }
      if ($a > 0) {
        echo $total_credits + $technical_credits;
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
        echo "<sup>";
        echo $class_item['superscript'];
        echo "</sup>";
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

      // output descriptions and tables for electives
      if($class_item['sub_elective_group'] !== null) {
        foreach ($class_item['sub_elective_group'] as $sub_sub_elective_group) {
          echo "<h5>";
          echo $sub_sub_elective_group['description'];
          echo "</h5>";
            echo "<table>";
            foreach($sub_sub_elective_group['sub_courses'] as $sub_sub_courses) {
              echo "<tr>";
              echo "<td>";
                echo $sub_sub_courses['item'];
                echo "</td>";
                echo "<td>";
                echo $sub_sub_courses['credits'];
                echo "</td>";
                echo "<td>";
                echo $sub_sub_courses['title'];
                echo "</td>";
              echo "</tr>";
            }
            echo "</table>";

        }
      }
        
      echo "</div>";
      echo "<div class=\"class-popup-window-close\" id=\"js-popup-window-close-";
      echo $class_item['index'];
      echo "\">";
      echo "CLOSE";
      echo "</div></div></div>";



  }



}

boo_classes_output('field_classes_in_this_degree', $node);

$degree_1_title = field_get_items('node', $node, 'field_degree_option_1_title'); 
$degree_1_title_val = $degree_1_title[0]['value'];
if($degree_1_title_val !== null) {

  echo "<h2>";
  echo $degree_1_title_val;
  echo "</h2>";
  boo_classes_output('field_degree_option_1_courses', $node);

}


$degree_2_title = field_get_items('node', $node, 'field_degree_option_2_title'); 
$degree_2_title_val = $degree_2_title[0]['value'];
if($degree_2_title_val !== null) {

  echo "<h2>";
  echo $degree_2_title_val;
  echo "</h2>";
  boo_classes_output('field_degree_option_2_courses', $node);

}

/*
echo "<h2>END TEST</h2>";





    // display all the courses associated with this degree and their credit values
    $node = node_load($nid);

    $field = field_get_items('node', $node, 'field_classes_in_this_degree');
    $total_credits = 0;
    $total_credits_max = 0;
    // counter for js IDs
    $i = 0;
    $j = 0;
    $degree_classes = array();
    foreach($field as $item) {
      $class = $item['entity'];

      // create array of all classes to use in template. 
      $degree_classes[$i]['number'] = $i;
      $degree_classes[$i]['item'] = $class->title;
      $degree_classes[$i]['title'] = $class->field_class_title[$node->language][0]['value'];
      $degree_classes[$i]['credits'] = $class->field_credits[$node->language][0]['value'];
      if ($class->field_credit_maximum[$node->language][0]['value']) {
        $degree_classes[$i]['creditsmax'] = $class->field_credit_maximum[$node->language][0]['value'];
        $total_credits_max += $class->field_credit_maximum[$node->language][0]['value'];
      }
      else {
         $degree_classes[$i]['creditsmax'] = null;
      }
      $degree_classes[$i]['description'] = $class->field_description[$node->language][0]['value'];

      if ($class->field_capstone[$node->language][0]['value'] == 1) {
        $degree_classes[$i]['superscript'] = "CAP";
      }
      elseif ($class->field_computer_literacy[$node->language][0]['value'] == 1) {
        $degree_classes[$i]['superscript'] = "CL";
      }
      elseif ($class->field_diversity_requirement[$node->language][0]['value'] == 1) {
        $degree_classes[$i]['superscript'] = "DIV";
      }
      else {
        $degree_classes[$i]['superscript'] = null;
      }

      $total_credits += $class->field_credits[$node->language][0]['value'];
      $i++;
    }

    // add electives to degree_classes array
    $field_elective = field_get_items('node', $node, 'field_elective_groups');
    

    foreach($field_elective as $item) {

      $elective = $item['entity'];
      
      $degree_classes[$i]['number'] = $i;
      $degree_classes[$i]['title'] = $elective->title;
      $degree_classes[$i]['credits'] = $elective->field_elective_credits[$node->language][0]['value'];
      $degree_classes[$i]['description'] = $elective->field_elective_description[$node->language][0]['value'];
       // get the nid for the courses assigned to this elective group, put it in sub-array
        foreach($elective->field_elective_group_courses[$node->language] as $sub_class) {
         // ***
          $sub_node = node_load($sub_class['target_id']);
        
          $degree_classes[$i]['sub_classes'][$j]['item'] = $sub_node->title;

          if ($sub_node->field_capstone[$node->language][0]['value'] == 1) {
                  $degree_classes[$i]['sub_classes'][$j]['superscript'] = "CAP";
                }
                if ($sub_node->field_computer_literacy[$node->language][0]['value'] == 1) {
                  $degree_classes[$i]['sub_classes'][$j]['superscript'] = "CL";
                }
                if ($sub_node->field_diversity_requirement[$node->language][0]['value'] == 1) {
                  $degree_classes[$i]['sub_classes'][$j]['superscript'] = "DIV";
                }
                



          $degree_classes[$i]['sub_classes'][$j]['title'] = $sub_node->field_class_title[$node->language][0]['value'];
          $degree_classes[$i]['sub_classes'][$j]['credits'] = $sub_node->field_credits[$node->language][0]['value'];
          if(isset($class->field_credit_maximum[$node->language][0]['value'])) {
            $degree_classes[$i]['sub_classes'][$j]['max_credit'] = $class->field_credit_maximum[$node->language][0]['value'];
          }
          $j++;
      }
      $i++;
    }


// create function that outputs a popup degree row, since we use this several times
function boo_degree_row($index, $itemnum, $superscript, $title, $credits, $creditsmax){
  echo "<tr class=\"class-popup\" id=\"js-class-popup-";
  echo $index;
  echo "\">";
  echo "<td>";
  echo $itemnum;
  if($superscript !== null) {
    echo "<sup>";
    echo $superscript;
    echo "</sup>";
  }
  echo "</td>";
  echo "<td>";
  echo $title;
  echo "</td>";
  echo "<td>";
  echo $credits;
  if($creditsmax !== null) {
    echo "-";
    echo $creditsmax;
  }
  echo "</td>";
  echo "</tr>";
}

// create function for all the markup used at the start of the popup windows, before the tables start
function boo_degree_popup_start($index, $itemnum, $title, $superscript, $credits, $creditsmax){
  echo "<div class=\"class-popup-window\" id=\"js-class-popup-window-";
  echo $index;
  echo "\">";
  echo "<div class=\"class-popup-window-inner\">";
  echo "<h4 class=\"class-title\">";
  echo $title;
  echo "</h4>";
  echo "<div class=\"class-wrapper\">";
  echo "<dl>";
  if($itemnum !== null) {
    echo "<dt>Item #</dt>";
    echo "<dd>";
    echo $itemnum;
    if ($superscript !== null) {
      echo "<sup>";
      echo $superscript;
      echo "</sup>";
    }
    echo "</dd>";
  }
  echo "<dt>Total Credits</dt>";
  echo "<dd>";
  echo $credits;
  if($creditsmax !== null) {
    echo "-";
    echo $creditsmax;
  }
  echo "</dd>";
  echo "</dl>";

}

function boo_degree_popup_end($index) {
  echo "</div>";
  echo "<div class=\"class-popup-window-close\" id=\"js-popup-window-close-";
  echo $index;
  echo "\">";
  echo "CLOSE";
  echo "</div></div></div>";

}



// function that outputs all the descrriptions and tables for AAT and AAS-T popups given the nid of the elective group
// changing this to work for classes as well as elective clusters
function boo_aa_output($groupnid) {
  $node = node_load($groupnid);
  
  if($node->type == 'elective_cluster') {
      $elective_group = field_get_items('node', $node, 'field_elective_group');
      foreach($elective_group as $entity_id) {
      // load the fields within the field group
       $entity = entity_load('field_collection_item', array($entity_id['value']));
       // loop through each field group
        foreach($entity as $group) {
          // print the description for each field group
          echo "<p>";
          echo $group->field_description_sub_elective[$node->language][0]['safe_value'];
          echo "</p>";
          echo "<table>";
          // load the nodes for the classes in question
              foreach($group->field_courses_sub[$node->language] as $subnid) {
                $subclass = node_load($subnid['target_id']);
                echo "<tr>";
                echo "<td>";
                echo $subclass->title;

              if($subclass->field_capstone[$node->language][0]['value'] == 1) { 
              $supscript = "CAP";
              }
              if($subclass->field_computer_literacy[$node->language][0]['value'] == 1) { 
              $supscript = "CL";
              }
              if($subclass->field_diversity_requirement[$node->language][0]['value'] == 1) { 
              $supscript = "DIV";
              }

            if(isset($supscript)) {
              echo "<sup>";
              echo $supscript;
              echo "</sup>";
            }
            $supscript = null;

          echo "</td>";
          echo "<td>";
          echo $subclass->field_class_title[$node->language][0]['safe_value']; 
          echo "</td>";
          echo "<td>";
          echo $subclass->field_credits[$node->language][0]['value'];
          if($subclass->creditsmax[$node->language][0]['value']) {
            echo "-";
            echo $subclass->creditsmax[$node->language][0]['value'];
          }
          echo "</td>";
          echo "</tr>";
        }
      echo "</table>";
     }
    }
  }
  if($node->type == 'class') {
    echo "class yo";
  }

 




}




// output field_classes array into classes table    
  foreach($degree_classes as $class) { 
    boo_degree_row($class['number'], $class['item'], $class['superscript'], $class['title'], $class['credits'], $class['creditsmax']);
}

// output total credits and range, if there is one
echo "<tr><td>&nbsp;</td><td>Total";
if($degree_type == 1) {
    echo " Technical";
  }
echo " Credits</td><td>";
echo $total_credits;
if ($total_credits_max !== 0) {
  echo "-";
  echo $total_credits_max;
}
echo "</td></tr></table>";






// function that outputs the tables under AAT Requirements and AAS-T Requirements, since they're pretty much the same
function boo_aa_tables($generaled_id, $index, $total_credits, $node) {
  echo "<table>";
  echo "<tr><td>Technical Course Requirements</td><td>";
  echo $total_credits;
  echo "</td></tr>";
  $aatnode = node_load($generaled_id);
  $field = field_get_items('node', $aatnode, 'field_total_credits');
  $aat_credits = $field[0]['value'];
  echo "<tr class=\"class-popup\" id=\"js-class-popup-";
  echo $index;
  echo "\">";
  echo "<td>";
  echo $aatnode->title;
  echo "</td><td>";
  echo $aat_credits; 
  echo "</td></tr>";
  // display the computer literacy requirement if that option is selected
   $clfield = field_get_items('node', $node, 'field_add_computer_literacy'); 
  if($clfield[0]['value'] == 1) {
    $clindex = $index + 1;
    $clnode = node_load(71);
   $field = field_get_items('node', $clnode, 'field_elective_credits');
   $clcredits = $field[0][value];
     echo "<tr class=\"class-popup\" id=\"js-class-popup-";
  echo $clindex;
  echo "\">";
   echo "<td>";
  echo $clnode->title; 
   echo "</td>";
   echo "<td>";
    echo $clcredits;
    echo "</td>";
    echo "</tr>";
  }
  
  echo "<tr>
  <td>Total Credits for AAT Degree</td>
  <td>";

  echo $total_credits + $aat_credits + $clcredits; 
  echo "</td></tr></table>";

  boo_degree_popup_start($index, null, $aatnode->title, $aat_credits, null, null);
  boo_aa_output($generaled_id);
  boo_degree_popup_end($index);


  if($clfield[0]['value'] == 1) {
  boo_degree_popup_start($clindex, null, $clnode->title, $clcredits, null, null);
  boo_aa_output(71);
  boo_degree_popup_end($clindex);
  }
}



// if this is both an AAT and AAS-T degree, produce the individual tables for both
if($degree_type == 1) { 
  $i++;
  echo "<h2>AAT Requirements</h2>";
  boo_aa_tables(69, $i, $total_credits, $node);
  $i = $i + 2;
  echo "<h2>AAS-T Requirements</h2>";
  boo_aa_tables(70, $i, $total_credits, $node);

}


// go through the degree_classes array again and output info into the lightbox divs

foreach($degree_classes as $class) { 
  
  boo_degree_popup_start($class['number'], $class['item'], $class['title'], $class['superscript'], $class['credits'], $class['creditsmax']);
  ?>

      <p><?php echo $class['description']; ?></p>
        <?php if(isset($class['sub_classes'])) {
          echo "
            <table>
            <thead>
              <th>Course Number</th>
              <th>Class Title</th>
              <th>Credits</th>
            </thead>
          ";
          foreach($class['sub_classes'] as $elective_sub_class) 
          {
            
            ?>
            <tr>
              <td><?php echo $elective_sub_class['item']; ?>

              <?php 
               if(isset($elective_sub_class['superscript'])){ 
                echo "<sup>";
              echo $elective_sub_class['superscript'];
              echo "</sup>";
              } ?>

              </td>
              <td><?php echo $elective_sub_class['title']; ?></td>
              <td><?php echo $elective_sub_class['credits']; ?>

              <?php 
               if(isset($elective_sub_class['max_credit'])){ 
                echo "-";
              echo $elective_sub_class['max_credit'];
              } ?>

              </td>
           </tr>


            <?php
         
           
          }
    }


       ?>
      </table>


      <a href="<?php echo $caturl; ?>">View All <?php echo $current_cat->name; ?> Classes</a>


<?php
     boo_degree_popup_end($class['number']); 





}





  ?>

</table>

<h2>NEW WAY OF DOING IT — should look exactly the same as above.</h2>





<?php 





// you give this the id of an elective cluster, and it'll output all the stuff that goes in the popup window.
// boo_aa_output(69);


$degree_1_title = field_get_items('node', $node, 'field_degree_option_1_title'); 
$degree_1_title_val = $degree_1_title[0]['value'];
if($degree_1_title_val !== null) {




  // for the second row of the table, need to load:
// One: title and credits for all of the classes and elective groups assigned to field_degree_option_1_courses
// two: title and credits for all classes within elective groups selected in field_degree_option_1_courses

$field_degree_1_courses = field_get_items('node', $node, 'field_degree_option_1_courses');











  echo "<h2>";
  echo $degree_1_title_val;
  echo "</h2>";
   echo "<table>";
  echo "<tr><td>&nbsp;</td><td>Technical Course Requirements</td><td>";
  echo $total_credits;
  echo "</td></tr>";
  // here we list all of the courses selected from field_degree_option_1_courses
  foreach($field_degree_1_courses as $top_item) {



      $type = $top_item['entity']->type;
      if($type == 'class') {
        $itemnum = $top_item['entity']->title;
        $superscipt = null; // will add later. just getting it sorted out now
        $title = $top_item['entity']->field_class_title['und'][0]['safe_value'];
        $credits = $top_item['entity']->field_credits['und'][0]['value'];
        $creditsmax = $top_item['entity']->field_credit_maximum['und'][0]['value'];

      }

      if($type =='elective_cluster') {
        $itemnum = null;
        $superscipt = null;
        $title = $top_item['entity']->title;
        $credits = $top_item['entity']->field_total_credits['und'][0]['value'];
        $creditsmax = $top_item['entity']->field_total_credits_max['und'][0]['value'];

      }
       boo_degree_row($i, $itemnum, $superscript, $title, $credits, $creditsmax);

      $addtl_credits += $credits;
  }




  echo "<tr><td>&nbsp;</td><td>Total</td><td>";
  echo $total_credits + $addtl_credits;
  echo "</td></tr>";
  echo "</table>";

}

// now we output the hidden lightboxes for each of those. fairly certain ids won't be right this first time
foreach($field_degree_1_courses as $top_item) {
  boo_aa_output($top_item['target_id']);

}

     

*/


      ?>



  </div>


<div class="col7">
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

</ul>


  <a href="<?php echo $caturl;?>">View All <?php echo $current_cat->name; ?> Classes</a>


  <?php boo_snippet('sidebar-menu.php'); ?>






  </nav>
</div>
</div>
<pre>

</pre>
<div class="datestamp-wrapper">
This page was last updated on <?php echo date("F d, Y", $node->revision_timestamp); ?>.
</div>


</div>
