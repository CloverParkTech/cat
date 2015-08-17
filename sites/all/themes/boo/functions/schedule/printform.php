<?php

  /**
   * Prints search form
   * Sets default values if sent from last search
   */
   function printForm($dbh, $dbhcat, $node) {
    global $default_quarter;
    echo '<form action="" method="post">';
    //quarter
    echo '<div class="class-search-form-wrapper">';
echo '<div class="class-search-form-section">';
    echo '
    <h4>Quarter</h4> <select name="quarter">';
    
// print the options in select menu based on quarters selected in drupal

// create array of quarter codes from Drupal interface
$quarter_keys = array (
  "B561" => "Summer 2015",
  "B562" => "Fall 2015",
  "B563" => "Winter 2016",
  "B564" => "Spring 2016",
  "B671" => "Summer 2016",
  "B672" => "Fall 2016",
  "B673" => "Winter 2017",
  "B674" => "Spring 2017"
);


// create array of selected quarters in Drupal 
$quarters_array = $node->field_quarters_to_display['und'];

function get_drupal_quarters($array) {
  foreach ($array as $item) {
    if ($item['value'] == 0) {
      return "B561";
    }
    if ($item['value'] == 1) {
      return "B562";
    }
    if ($item['value'] == 2) {
      return "B563";
    }
    if ($item['value'] == 3) {
      return "B564";
    }
    if ($item['value'] == 4) {
      return "B671";
    }
    if ($item['value'] == 5) {
      return "B672";
    }
    if ($item['value'] == 6) {
      return "B673";
    }
    if ($item['value'] == 7) {
      return "B674";
    }
  }
}

$active_quarters = array_map("get_drupal_quarters", $quarters_array);

// intersect the two arrays to create key/value array of active quarters
$quarter_keys_flipped = array_flip($quarter_keys);
$active_quarters_final = array_intersect($quarter_keys_flipped, $active_quarters);

// this could probably be fixed elsewhere
$active_quarters_final = array_flip($active_quarters_final);

// if someone has already selected a quarter, move that quarter to the top of the array so it displays in the select text

if (isset($_POST['quarter'])) {
 $active_quarters_final = array($_POST['quarter'] => $active_quarters_final[$_POST['quarter']]) + $active_quarters_final;
}

foreach($active_quarters_final as $key => $value) {
  echo '<option value="';
  echo $key;
  echo '">';
  echo $value;
  echo "</option>";
}
echo '</select>';




//program

  echo '<h4>Program</h4><select name="program">';
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
      echo '>'. htmlentities($row->name) . '</option>';
    }
    echo '</select>
    ';
echo '</div>';
echo '<div class="class-search-form-section">';

    //instructor search area
    echo '

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
    echo '>';

    //starting after time search area

    echo '</div>';
echo '<div class="class-search-form-section">';
    echo '
   
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

echo '</div>';
echo '<div class="class-search-form-section">';

    echo '

    <h4>Credits</h4>';
    if(isset($_POST['credits'])) {
      printCreditsOptions($_POST['credits']);
    } else {
      printCreditsOptions(-1);
    }

    //location area
    $mode_l = array(null, 'Lakewood', 'South Hill', 'Online', 'Arranged', 'Off Campus');
    echo '
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
    echo '> Not Full</label>';
    echo '</div>';
  

     echo '</div>';
    //submit area
    echo '<input type="submit" value="Search" class="btn btn-arrow">';
  //  echo '<input type="button" value="Reset" onclick="resetForm();" class="btn">';
     
    echo '</form>';
   }
   
?>