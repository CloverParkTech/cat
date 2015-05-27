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
  $navnids = taxonomy_select_nodes($navtid);

// get the path for the program taxonomy term in question, so that we can link to all the class descriptions is this category.
  $current_cat = taxonomy_term_load($navtid);
  $path = taxonomy_term_uri($current_cat);
  $caturl = url($path['path']);
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


  <h2>Classes in This Degree</h2>
  <table>
    <thead>
      <th>Course Number</th>
      <th>Class Title</th>
      <th>Credits</th>
    </thead>
  <?php 




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
      $degree_classes[$i]['title'] = $class->field_class_title['und'][0]['value'];
      $degree_classes[$i]['credits'] = $class->field_credits['und'][0]['value'];
      if ($class->field_credit_maximum['und'][0]['value']) {
        $degree_classes[$i]['creditsmax'] = $class->field_credit_maximum['und'][0]['value'];
        $total_credits_max += $class->field_credit_maximum['und'][0]['value'];
      }
      else {
         $degree_classes[$i]['creditsmax'] = null;
      }
      $degree_classes[$i]['description'] = $class->field_description['und'][0]['value'];

      if ($class->field_capstone['und'][0]['value'] == 1) {
        $degree_classes[$i]['superscript'] = "CAP";
      }
      elseif ($class->field_computer_literacy['und'][0]['value'] == 1) {
        $degree_classes[$i]['superscript'] = "CL";
      }
      elseif ($class->field_diversity_requirement['und'][0]['value'] == 1) {
        $degree_classes[$i]['superscript'] = "DIV";
      }
      else {
        $degree_classes[$i]['superscript'] = null;
      }





      $total_credits += $class->field_credits['und'][0]['value'];
      $i++;
    }

    // add electives to degree_classes array
    $field_elective = field_get_items('node', $node, 'field_elective_groups');
    

    foreach($field_elective as $item) {

      $elective = $item['entity'];
      
      $degree_classes[$i]['number'] = $i;
      $degree_classes[$i]['title'] = $elective->title;
      $degree_classes[$i]['credits'] = $elective->field_elective_credits['und'][0]['value'];
      $degree_classes[$i]['description'] = $elective->field_elective_description['und'][0]['value'];
       // get the nid for the courses assigned to this elective group, put it in sub-array
        foreach($elective->field_elective_group_courses['und'] as $sub_class) {
          $sub_node = node_load($sub_class['target_id']);
          $degree_classes[$i]['sub_classes'][$j]['item'] = $sub_node->title;

          if ($sub_class->field_capstone['und'][0]['value'] == 1) {
                  $degree_classes[$i]['sub_classes'][$j]['superscript'] = "CAP";
                }
                elseif ($sub_class->field_computer_literacy['und'][0]['value'] == 1) {
                  $degree_classes[$i]['sub_classes'][$j]['superscript'] = "CL";
                }
                elseif ($sub_class->field_diversity_requirement['und'][0]['value'] == 1) {
                  $degree_classes[$i]['sub_classes'][$j]['superscript'] = "DIV";
                }
                else {
                  $degree_classes[$i]['sub_classes'][$j]['superscript'] = null;
                }



          $degree_classes[$i]['sub_classes'][$j]['title'] = $sub_node->field_class_title['und'][0]['value'];
          $degree_classes[$i]['sub_classes'][$j]['credits'] = $sub_node->field_credits['und'][0]['value'];
          if($class->field_credit_maximum['und'][0]['value']) {
            $degree_classes[$i]['sub_classes'][$j]['max_credit'] = $class->field_credit_maximum['und'][0]['value'];
          }
          $j++;
      }
      $i++;
    }



// output field_classes array into classes table    
    foreach($degree_classes as $class) { ?>
     <tr class="class-popup" id="js-class-popup-<?php echo $class['number'];?>">
      <td><?php echo $class['item']; ?>
        <?php if($class['superscript']) {
          echo "<sup>";
          echo $class['superscript'];
          echo "</sup>";
        }
          ?>
      </td>
      <td><?php echo $class['title']; ?></td>
      <td><?php echo $class['credits']; ?>
      <?php if($class['creditsmax']) {
        echo "-";
        echo $class['creditsmax'];
      }
      ?>
      </td>
    </tr>


<?php
}

// output total credits and range, if there is one
echo "<tr><td>&nbsp;</td><td>Total Credits</td><td>";
echo $total_credits;
if ($total_credits_max !== 0) {
  echo "-";
  echo $total_credits_max;
}
echo "</td></tr></table>";



// go through the degree_classes array again and output info into the lightbox divs

foreach($degree_classes as $class) { ?>
<div class="class-popup-window" id="js-class-popup-window-<?php echo $class['number'];?>">
  <div class="class-popup-window-inner">
    <h4 class="class-title">
      <?php echo $class['title']; ?>
    </h4>
    <div class="class-wrapper">
      <dl>
        <dt>Item Number</dt>
        <dd><?php echo $class['item']; ?>
        <?php if($class['superscript']) {
          echo "<sup>";
          echo $class['superscript'];
          echo "</sup>";
        }
        ?>
        </dd>
        <dt>Credits</dt>
        <dd><?php echo $class['credits']; ?>
        <?php if($class['creditsmax']) {
          echo "-";
          echo $class['creditsmax'];
        }
        ?>
        </dd>
      </dl>
      <p><?php echo $class['description']; ?></p>
        <?php if($class['sub_classes']) {
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
               if($elective_sub_class['superscript']){ 
              echo $elective_sub_class['superscript'];
              } ?>

              </td>
              <td><?php echo $elective_sub_class['title']; ?></td>
              <td><?php echo $elective_sub_class['credits']; ?>

              <?php 
               if($elective_sub_class['max_credit']){ 
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
    </div>
    <div class="class-popup-window-close" id="js-popup-window-close-<?php echo $class['number'];?>">
      CLOSE
    </div>
  </div>
</div>



<?php
}






/*




      
      echo "<tr data-js=\"js-popup\" class=\"class-popup\" id=\"js-class-popup-";
      echo $i;
      echo "\">";
      echo "<td>";
      echo $class->title;
      // check for superscripts, e.g. DIV, CAP, etc.
      if ($class->field_capstone['und'][0]['value'] == 1) {
        echo "<sup>CAP</sup>";
      }
      if ($class->field_computer_literacy['und'][0]['value'] == 1) {
        echo "<sup>COM</sup>";
      }
      if ($class->field_diversity_requirement['und'][0]['value'] == 1) {
        echo "<sup>DIV</sup>";
      }
     echo "</td>";
    // there's a better way to access these. Once I have that, make this a function
    // adding IDs for javascript lightbox
    echo "<td>";
    echo $class->field_class_title['und'][0]['value'];
    echo "</td>";

  


    echo "<td>";
    echo $class->field_credits['und'][0]['value'];
     // check to see if there's a credit maximum on this class
      if ($class->field_credit_maximum['und'][0]['value']) {
        echo "-";
        echo $class->field_credit_maximum['und'][0]['value'];
        
      }

     echo "</td>";
      echo "</tr>";

*/
// going to write all of the popup divs to an array that we can output after the table so that our HTML is remotely compliant
      
   





// popup lightbox window. need to extract this as a function
/*
    echo "<div class=\"class-popup-window\"  id=\"js-class-popup-window-";
    echo $i;
     echo "\">";
     echo "<div class=\"class-popup-window-inner\">";
     echo "<h4 class=\"class-title\">";
   echo $class->title;
   echo "</h4>";
   echo "<div class=\"class-wrapper\">";
   echo "<dl>";
   echo "<dt>";
   echo "Item Number";
   echo "</dt>";
   echo "<dd>";
   echo $class->title;
   echo "</dd>";
   echo "<dt>";
   echo "Credits";
   echo "</dt>";
   echo "<dd>";
   echo $class->field_credits['und'][0]['value'];
     // check to see if there's a credit maximum on this class
      if ($class->field_credit_maximum['und'][0]['value']) {
        echo "-";
        echo $class->field_credit_maximum['und'][0]['value'];
        $total_credits_max += $class->field_credit_maximum['und'][0]['value'];
      }
      echo "</dd>";
   echo "<p>";

   echo $class->field_description['und'][0]['value'];
   echo "<a href=\"";


    echo $caturl;
    echo "\">";
    echo "View All "; 




     echo $current_cat->name; 
     echo " Classes";
     echo "</a>";
     echo "</div>";
    echo "<div class=\"class-popup-window-close\" id=\"js-class-popup-window-close-";
    echo $i;
    echo "\">CLOSE</div>";
    echo "</div>";
    echo "</div>";




//    $output = field_view_value('node', $node, 'field_another_entity_test', $field[$delta]);

      // add the credits from this class to the total credits number
      
      $i++;
      // end class table loop
    }



// start loop for elective groups
    $node = node_load($nid);
    $field = field_get_items('node', $node, 'field_elective_groups');
    foreach($field as $item) {


    $elective = $item['entity'];
   
   // print_r($class);
    echo "<tr data-js=\"js-popup\" class=\"class-popup\" id=\"js-class-popup-";
      echo $i;
      echo "\">";
    echo "<td>";
  //  echo $class->field_item_number['und'][0]['value'];
    echo "</td>";
    // there's a better way to access these. Once I have that, make this a function
    echo "<td>";
    echo $elective->title;
    
     echo "</td>";
    echo "<td>";
    echo $elective->field_elective_credits['und'][0]['value'];
     echo "</td>";
      echo "</tr>";





// lightbox popups for elective group
    echo "<div class=\"class-popup-window\"  id=\"js-class-popup-window-";
    echo $i;
     echo "\">";
     echo "<div class=\"class-popup-window-inner\">";
     echo "<h4 class=\"class-title\">";
   echo $elective->title;
   echo "</h4>";
   echo "<div class=\"class-wrapper\">";
   echo "<dl>";
   echo "<dt>";
   echo "Credits";
   echo "</dt>";
   echo "<dd>";
   echo $elective->field_elective_credits['und'][0]['value'];
     // check to see if there's a credit maximum on this class 
   /*
      if ($class->field_credit_maximum['und'][0]['value']) {
        echo "-";
        echo $class->field_credit_maximum['und'][0]['value'];
        $total_credits_max += $class->field_credit_maximum['und'][0]['value'];
      } */

      /*
      echo "</dd>";
      echo "</dl>";
   echo "<p>";

 echo $elective->field_elective_description['und'][0]['value'];
   echo "</p>";
  
// start table of available electives
    echo "
      <table>
    <thead>
      <th>Course Number</th>
      <th>Class Title</th>
      <th>Credits</th>
    </thead>";
    echo "<tr>";
    echo "<td>";
    echo "hey";
    echo "</td>";
    echo "<td>";
    echo "hey";
    echo "</td>";
    echo "<td>";
    echo "hey";
    echo "</td>";
    echo "</tr>";



    echo "</table>";


     echo "</div>";
    echo "<div class=\"class-popup-window-close\" id=\"js-class-popup-window-close-";
    echo $i;
    echo "\">CLOSE</div>";
    echo "</div>";
    echo "</div>";



// field_elective_group_courses




      // add the credits from this class to the total credits number
      $total_credits += $class->field_elective_credits['und'][0]['value'];


    }





    echo "<tr>";
    echo "<td></td>";
    echo "<td>";
    echo "Total Credits";
    echo "</td>";
    echo "<td>";
    echo $total_credits;
    if($total_credits_max !== 0) {
      $total_credits_max = $total_credits + $total_credits_max;
      echo "-";
      echo $total_credits_max;
    }

    echo "</td>";
    echo "</tr>";
    */
  ?>

</table>
  </div>


<div class="col7">
<?php boo_snippet('search.php'); ?>
  <nav>
  <h3>Related Degrees & Certificates</h3>

  <ul>
  <?php 


  foreach($navnids as $navnid) {
  // check if node's content type is degree or certificate
    $navnode = node_load($navnid);
    $type =$navnode->type;
    // check if node is the current node we're on
    if($type == 'degree_or_certificate' && $navnid !== $nid) {
      echo "<li>";
      echo "<a href=\">";
      echo boo_url($navnid);
      echo "\">";
      echo $navnode->title;
      echo "</a>";
      echo "</li>";

    }




  }
  ?>

</ul>

<?php // probably a better way to get the taxonomy name 


?>
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
