<?php


    
    /**
     * Prints credits selector
     */
  function printCreditsOptions($pre) {
    $ops = array('All', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
    $vals = array(-1, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100);
    echo '<select name="credits">';
    for($i = 0; $i < count($ops); $i++) {
      echo '<option value="' . $vals[$i] . '"';
      if(isset($pre) && $pre == $vals[$i])
        echo 'selected';
      echo '>' . $ops[$i] . '</option>';
    }
    echo '</select>';
  }

?>