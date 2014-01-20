<?php

  include '../pdo_conn.php';
  include 'notes_functions.php';

  $dbh = civicrmConnect();
  $notes = getAllNotes($dbh);

  echo "<table>"
       ."<thead>"
       ."<tr>"
       ."<th>Select Notes</th>"
       ."<th>Type of Billing</th>"
       ."<th>Notes</th>"
       ."<th>Status</th>"
       ."<tr>"
       ."</thead><tbody>";      

  foreach($notes as $details){

    $id = $details["notes_id"];
    $categoryId = $details["notes_category_id"];
    $billingType = $details["category_name"];
    $note = $details["notes"];
    $status = $details["notes_status"];
    $status = $status == 0 ? 'disabled' : 'enabled';

    echo "<tr>"
         ."<td><input type='checkbox' name=ids[] value=$id></td>"
         ."<td>$billingType</td>"
         ."<td>$note</td>"
         ."<td>$status</td>"
         ."</tr>";
  }

  echo "</tbody></table>";
  
  
?>
