  <?php

  //function to display degrees. takes an array of degree nids
  function boo_display_degrees($nids) {
    // alphabetize list before display
    asort($nids);
      foreach($nids as $degree_nid => $degree_title) {
        echo "<li>";
        echo "<a href=\"";
        echo boo_url($degree_nid);
        echo "\">";
        echo $degree_title;
        echo "</a>";
        echo "</li>";
   }
  }

  ?>