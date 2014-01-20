<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Notes Billing</title>
  <link rel="stylesheet" type="text/css" href="../billingStyle.css">
  <link rel="stylesheet" type="text/css" href="../menu.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="../js/jquery-jPaginate.js"></script>
  <script src="../js/jquery.tablesorter.js"></script>
</head>
<body>
<?php

  include '../pdo_conn.php';
  include '../login_functions.php';
  include 'notes_functions.php';

  $dbh = civicrmConnect();
  $notes = getAllNotes($dbh);
  $logout = logoutDiv($dbh);
  echo $logout;
  echo "<br>";

?>

<form method="POST" action="">
  <div align="center">
    Select action process:<br>
    <select name="actions">
     <option value="select">- Select action type-</option>
     <option value="" disabled></option>
     <option value="edit">Edit</option>
     <option value="delete">Delete</option>
    </select>
    <input type="submit" value="Process Action">
  </div>

<?php

  echo "<br>";
  echo "<div align='center'>";
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
  echo "</div>";
  
?>
</form>
</body>
</html>
