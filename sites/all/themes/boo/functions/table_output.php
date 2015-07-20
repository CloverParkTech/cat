 <?php

// this function outputs the tables used on degree and certificate pages.
function boo_table_output($classes, $index, $credits, $max_credits) {


 echo "<table class=\"degree-table\"><thead><th>Course Number</th><th>Class Title</th><th>Credits</th></thead>";

    // index used to keep track of how many tables we've displayed.
    $a = $index;

   
    if ($a == 0) {
        $technical_credits = $credits;
      }
      else {
        $technical_credits = 0;
      }


      // if this is not the first table, technical credits are equal to the total from the first table
    if ($a > 0) {
      echo "<tr><td>&nbsp;</td><td>";
      echo "Technical Course Requirements";
      echo "</td><td>";
      echo $credits;
      if($max_credits > 0) {
        echo "-";
        echo $max_credits + $credits;
      }
      echo "</td></tr>";
    }
    // output start of table 
    


    foreach($classes as $class_item) {
      echo "<tr class=\"class-popup\" id=\"js-class-popup-";
      echo $class_item['index'];
      echo "\">";
      echo "<td>";
      if(isset($class_item['item'])) {
        echo $class_item['item'];
      }
      if($class_item['superscript']) {
        echo $class_item['superscript'];
      }
      echo "</td>";
      echo "<td>";
      if (isset($class_item['title'])) {
        echo $class_item['title'];
      }
      echo "</td>";
      echo "<td>";
      if (isset($class_item['credits'])) {
        echo $class_item['credits'];
      }
      if(isset($class_item['creditsmax'])) {
          echo "-";
          echo $class_item['creditsmax'];
        }
      echo "</td>";
      echo "</tr>";

      $technical_credits += $class_item['credits'];
      if (isset($class_item['creditsmax'])) {
        $max_credits += ($class_item['creditsmax'] - $class_item['credits']);
      }

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
        echo $credits;
        if($max_credits > 0) {
          echo "-";
          echo $credits + $max_credits;
        }
      }

      if ($a > 0) {
        // add original credits number to credits number from this time around
        echo $credits + $technical_credits;
        if ($max_credits > 0) {
          echo "-";
          echo $max_credits + $technical_credits + $credits;
        }


      }


       echo "</td></tr>";
  echo "</table>";










  // Now for the popup divs 


    foreach($classes as $class_item) {
      echo "<div class=\"class-popup-window\" id=\"js-class-popup-window-";
      echo $class_item['index'];
      echo "\">";
      echo "<div class=\"class-popup-window-inner\">";
      echo "<h4 class=\"class-title\">";
      if (isset($class_item['title'])) {
        echo $class_item['title'];
      }
      echo "</h4>";
      echo "<div class=\"class-popup-wrapper\">";
      echo "<dl>";
      if(isset($class_item['item'])) {
        echo "<dt>Item #</dt>";
        echo "<dd>";
        echo $class_item['item'];
      }  
      if (isset($class_item['superscript'])) {
        echo $class_item['superscript'];

      }
      echo "</dd>";
      echo "<dt>Total Credits</dt>";
      echo "<dd>";
      echo $class_item['credits'];
      if(isset($class_item['creditsmax'])) {
        echo "-";
        echo $class_item['creditsmax'];
      }
      echo "</dd>";
      echo "</dl>";

      if (isset($class_item['description'])) {
        echo "<p>";
          echo $class_item['description'];
        echo "</p>";
     }

     if (isset($class_item['prereqs'])) {
      echo "<h5>Prerequisites</h5>";
        echo "<p>";
          echo $class_item['prereqs'];
        echo "</p>";
     }

     if (isset($class_item['coreqs'])) {
      echo "<h5>Co-requisites</h5>";
        echo "<p>";
          echo $class_item['coreqs'];
        echo "</p>";
     }

     if (isset($class_item['notes'])) {
      echo "<h5>Notes</h5>";
        echo "<p>";
          echo $class_item['notes'];
        echo "</p>";
     }



      echo "<div class=\"popup-tables-wrapper\">";
      // output descriptions and tables for electives

      if(isset($class_item['sub_elective_group'])) {
        //count the number of tables in this array. If there are more than two, we apply the small-table class
        $count = count($class_item['sub_elective_group']);
        foreach ($class_item['sub_elective_group'] as $sub_sub_elective_group) {
          echo "<div class=\"popup-table-item\">";
          echo "<h5>";
          if (isset($sub_sub_elective_group['description'])) {
            echo $sub_sub_elective_group['description'];
          }
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
                if (isset($sub_sub_courses['credits'])) {
                  echo $sub_sub_courses['credits'];
                }
                if(isset($sub_sub_courses['creditsmax'])) {
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
?>