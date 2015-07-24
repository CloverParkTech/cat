<?php
  function printCourse($row, $last_row, $dbh) {
    if($last_row == null) {
      printTop($row);
    } else if($last_row->title != $row->title) {
      printBottom($last_row);
      printTop($row);
    }


    // convert day code to actual days of week
    switch($row->day_cd) {
      case 1:
        $weekday = "M";
        break;
      case 2:
        $weekday = "T";
        break;
      case 3:
        $weekday = "W";
        break;
      case 4:
        $weekday = "Th";
        break;   
      case 5:
        $weekday = "F";
        break;
      case 6:
        $weekday = "Sa";
        break;       
      case 7:
        $weekday = "Daily";
        break;
      case 8:
        $weekday = "MWF";
        break;     
      case 9:
        $weekday = "TTH";
        break;  
      case 10:
        $weekday = "MWThF";
        break;
      case 11:
        $weekday = "MTWTh";
        break;
      case 12:
        $weekday = "MW";
        break;
      case 13:
        $weekday = "WF";
        break;
      case 14:
        $weekday = "TF";
        break;
      case 15:
        $weekday = "MF";
        break;
      case 16:
        $weekday = "ThF";
        break;  
      case 17:
        $weekday = "MWTh";
        break; 
      case 18:
        $weekday = "MTWF";
        break;
      case 19:
        $weekday = "MTTh";
        break; 
      case 20:
        $weekday = "WTh";
        break;
      case 21:
        $weekday = "MTF";
        break; 
      case 22:
        $weekday = "MT";
        break;
      case 23:
        $weekday = "TWThF";
        break;
      case 24:
        $weekday = "Arranged";
        break;
      case 25:
        $weekday = "Su";
        break;
      case 26:
        $weekday = "TThF";
        break;   
      case 27:
        $weekday = "TW";
        break;
      case 28:
        $weekday = "MTh";
        break; 
      case 29:
        $weekday = "MTW";
        break;  
      case 30:
        $weekday = "MThF";
        break;                                          

      default:
        $weekday = " ";

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
      
        <td>' . $weekday . '</td>
        
        <td>' . $row->sbctc_misc_1 .'</td>
        <td>$' . $row->class_fee .'</td>
        <td>' . $row->enr . '/' . $row->class_cap . '</td>
        

        
      </tr> 
      ';
      return $row;
  }
?>