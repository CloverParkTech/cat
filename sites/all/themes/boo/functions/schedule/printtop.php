<?php
  /**
   * Prints top area of a grouped course area (everything leading up to the <tr> for the specific course section)
   * Will get called before each unique course
   * Will not get called between two similar courses (ie, if there are two ENG 101, will not get called between the two courses)
   */

  


  function printTop($row) {
    echo '<div class="schedule-class-item-wrapper"> 
      <h2 class="schedule-class-title">';
      //Use title provided by XML if able, if not, use from database
      if($row->field_class_title_value != null && $row->field_class_title_value != "") {
        echo $row->field_class_title_value;
      } 
      echo '</h2>
      <div class="schedule-class-item-inner">
      <h3 class="schedule-class-subtitle">' . $row->title . '</h3>
      <table class="schedule-class-table">
        <thead>
          <tr>
            <th>Item #</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Instructor</th>
            <th>Location</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Days</th>
            <th>Format</th>
            <th>Addtl Fee</th>
            <th>Enrollment</th>
          </tr>
        </thead>
        <tbody>';
  }
?>