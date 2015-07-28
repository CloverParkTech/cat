<?php
  /**
   * Prints bottom area of a grouped course area (everything after the </tr> for the specific course section, closing the <div> from printTop())
   * Will get called after each unique course
   * Will not get called between two similar courses (ie, if there are two ENG 101, will not get called between the two courses)
   */
  function printBottom($row) {
    echo '        
        </tbody>
        </table>
          <div class="desc">
            <p><strong>Credits</strong>: ' . $row->cr . '</p><p>';
          if($row->field_description_value) {
            echo $row->field_description_value;
          } else {
            echo 'No description available.';
          }
          echo '
          </p> ';
          if($row->field_prerequisites_value) {
            echo "<h4>Prerequisites</h4>";
            echo "<p>";
            echo $row->field_prerequisites_value;
            echo "</p>";
          }

           if($row->field_co_requisites_value) {
            echo "<h4>Co-requisites</h4>";
            echo "<p>";
            echo $row->field_co_requisites_value;
            echo "</p>";
          }

echo '
          </div>
    </div>
    </div>';
  }
?>