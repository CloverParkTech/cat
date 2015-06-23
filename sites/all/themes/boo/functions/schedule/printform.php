<?php

  /**
   * Prints search form
   * Sets default values if sent from last search
   */
   function printForm($dbh, $dbhcat) {
    global $default_quarter;
    echo '<form action="" method="post">';
    //quarter

    echo '
    <div class="col6">
    <h4>Quarter</h4> <select name="quarter">';
    

    // Summer 2015
    echo '<option value="B455">Summer 2015</option>';


    // Spring 2015
      echo '<option value="B453"';
    if(isset($_POST['quarter']) && $_POST['quarter'] == "B454") {
      echo ' selected';
    } else if(!isset($_POST['quarter'])) {
      //set up default selected based on $default_quarter
    }
    echo '>Spring 2015</option>';

    // winter 2015
      echo '<option value="B453"';
    if(isset($_POST['quarter']) && $_POST['quarter'] == "B453") {
      echo ' selected';
    } else if(!isset($_POST['quarter'])) {
      //set up default selected based on $default_quarter
    }
    echo '>Winter 2015</option>';



    // fall 2014
    echo '<option value="B452"';
    if(isset($_POST['quarter']) && $_POST['quarter'] == "B452") {
      echo ' selected';
    } else if(!isset($_POST['quarter'])) {
      //set up default selected based on $default_quarter
    }
    echo '>Fall 2014</option>';
    
    
    echo '</select>
    ';
    //program
    echo '
  

    <h4>Program</h4><select name="program">';
    //print all programs for select
   
    try {
      $stmt = $dbhcat->prepare("SELECT * FROM taxonomy_term_data WHERE vid = 2 ORDER BY name");
      $stmt->execute();
    } catch(PDOException $e) {
      echo "Error with SQL for programs: " . $e->getMessage() . "<br>";
    }
    //option for programs
    echo '<option value="-1">All</option>';
    while ($row = $stmt->fetchObject()) {
      echo '<option value="' . $row->tid . '"';
      if(isset($_POST['program']) && $_POST['program'] == $row->tid)
        echo 'selected';
      echo '>'. $row->name . '</option>';
    }
    echo '</select>
    </div>
    ';


    //instructor search area
    echo '
    <div class="col6">
    <h4>Instructor</h4><input type="text" name="instructor" ';
    if(isset($_POST['instructor'])) {
      echo 'value="' . htmlspecialchars($_POST['instructor']) . '"';
    }
    echo '><br>';
    //keyword search area
    echo '<h4>Keywords</h4><input type="text" name="keyword" ';
    if(isset($_POST['keyword'])) {
      echo 'value="' . htmlspecialchars($_POST['keyword']) . '"';
    }
    echo '>
    </div>';

    //starting after time search area
    echo '
    <div class="col6">
    <h4>Starting After</h4>';
    if(isset($_POST['after'])) {
      printTimeOptions("after", $_POST['after']);
    } else {
      printTimeOptions("after", -1);
    }
    //ending before time search area
    echo '<h4>Ending Before</h4>';
    if(isset($_POST['before'])) {
      printTimeOptions("before", $_POST['before']);
    } else {
      printTimeOptions("before", -1);
    }

    //credits area
    //ending before time search area
    echo '

    <h4>Credits</h4>';
    if(isset($_POST['credits'])) {
      printCreditsOptions($_POST['credits']);
    } else {
      printCreditsOptions(-1);
    }
    echo '</div>';
    //location area
    $mode_l = array(null, 'Lakewood', 'South Hill', 'Online', 'Arranged', 'Off Campus');
    echo '<div class="col6">
    <h4>Location</h4>';
    //check which boxes should be checked. if new search, check all. if searched, check previous checked
    for($i = 1; $i < count($mode_l); $i++) {
      echo '<label><input type="checkbox" name="mode[]" value="' . $i . '" ';
      //if no search yet, make all checked:
      if(!isset($_POST['program'])) {
        echo 'checked';
      } else {
        //if after they click search, only check ones they had checked already
        if(isset($_POST['mode']) && in_array($i, $_POST['mode'])) {
          echo 'checked';
        }
      }
      echo '> ' . $mode_l[$i];
      //fencepost backlash
      if($i < (count($mode_l) - 1)) {
        echo '<br>';
      }
      echo '</label>';
    }
    //not full area
    echo '<h4>Availability</h4><label><input type="checkbox" name="notFull" value="1"';
      if(isset($_POST['notFull'])) {
        echo ' checked';
      }
    echo '> Not Full</label></div>';
    
    //submit area
    echo '<div class="schedule-form-buttons">';
    echo '<input type="submit" value="Search" class="btn btn-arrow">';
  //  echo '<input type="button" value="Reset" onclick="resetForm();" class="btn">';
    echo '</div>';
    echo '</form>';
   }
   
?>