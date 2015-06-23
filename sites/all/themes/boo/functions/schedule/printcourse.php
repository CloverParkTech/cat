<?php
  function printCourse($row, $last_row, $dbh) {
    if($last_row == null) {
      printTop($row);
    } else if($last_row->title != $row->title) {
      printBottom($last_row);
      printTop($row);
    }


    echo '


      <tr>
        <td>' . $row->class_id . '</td>
        <td>' . $row->strt_time . '</td>
        <td>' . $row->end_time . '</td>
        <td>' . $row->instr_name .'</td>
        <td>';
        //add <span> for south hill
        if(strpos($row->room_loc, "South") !== false) {
          echo '<span class="schedule-south-class">' . $row->room_loc . '</span>';
        } else {
          echo $row->room_loc;
        }

        /* need to add the day codes $row-enr is not what should be there, obviously*/
        echo '</td>
        <td>' . formatDate($row->strt_date) . '</td>
        <td>' . formatDate($row->end_date) . '</td>
      
        <td>' . $row->enr . '</td>
        
        <td>' . $row->sbctc_misc_1 .'</td>
        <td>$' . $row->class_fee .'</td>
        <td>' . $row->enr . '/' . $row->class_cap . '</td>
        

        
      </tr> 
      ';
      return $row;
  }
?>