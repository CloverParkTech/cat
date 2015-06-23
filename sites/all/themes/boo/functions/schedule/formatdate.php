<?php



  /**
   * Formats a date to fit school standards
   * In form "Feb. 7, 2014"
   * Month abbreviations are: "Jan.", "Feb.", "March", "April", "May", "June", "July", "Aug.", "Sept.", "Oct.", "Nov.", "Dec."
   * Returns formatted string
   */
  function formatDate($str) {
    //if date info not available, return "Arranged"
    if($str == "0000-00-00") {
      return "Arranged";
    }
    //converts MySQL date format to school format (minus the month, which will be replaced next)
    $str = date("M j, Y", strtotime($str));
    //PHP month abbreviations (to search for)
    $monthsF = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
    //School abbreviations to replace with
    $monthsR = array("Jan.", "Feb.", "March", "April", "May", "June", "July", "Aug.", "Sept.", "Oct.", "Nov.", "Dec.");
    //find which month to replace, then replace it
    for($i = 0; $i < count($monthsF); $i++) {
      $str = str_replace($monthsF[$i], $monthsR[$i], $str);
    }
    return $str;
  }
?>