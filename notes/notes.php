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
<style type="text/css">
    .fieldset-auto-width {
         display: inline-block;
    }
</style>
</head>
<body>
<?php

  include '../pdo_conn.php';
  include '../login_functions.php';
  include 'notes_functions.php';

  $dbh = civicrmConnect();
  $notes = getAllNotes($dbh);

  $menu = logoutDiv($dbh);
  echo $menu;
  echo "<br>";

?>

<form method="POST" action="">
  <div align="center">
    Select action process:<br>
    <select name="actions">
     <option value="select">- Select action type -</option>
     <option value="" disabled></option>
     <option value="add">Add</option>
     <option value="edit">Edit</option>
     <option value="delete">Delete</option>
    </select>
    <input type="submit" value="Process Action" name="process">
  </div>

<?php


  if(isset($_POST["process"]) && $_POST["actions"] == 'edit'){

    $noteId = $_POST["id"];
    $billingNote = getNoteById($dbh,$noteId);
    $displayNote = displayBillingNote($dbh,$billingNote);
    echo "<center>";
    echo "<div style = 'width:40%'>";
    echo $displayNote;
    echo "</div>";
    echo "</center>";
    
  
}

  elseif(isset($_POST["process"]) && $_POST["actions"] == 'delete'){

   $noteId = $_POST["id"];
   $sql = $dbh->prepare("DELETE FROM billing_notes WHERE notes_id = ?");
   $sql->bindValue(1,$noteId,PDO::PARAM_INT);
   $sql->execute();
   header("Location:notes.php");
  }

  elseif(isset($_POST["process"]) && $_POST["actions"] == 'add'){
 
    $displayAddForm = addNoteForm($dbh);
    echo "<center>";
    echo "<div style = 'width:40%'>";
    echo $displayAddForm;
    echo "</div>";
    echo "</center>";

  }

  elseif(isset($_POST["add"])){

    $note = $_POST["note"];
    $categoryId = $_POST["billingType"];

    $sql = $dbh->prepare("INSERT INTO billing_notes(notes_category_id,notes) VALUES (?,?)");

    $sql->bindValue(1,$categoryId,PDO::PARAM_INT);
    $sql->bindValue(2,$note,PDO::PARAM_STR);

    $sql->execute();
    header("Location:notes.php");

  }
   elseif(isset($_POST["update"])){

    $note = $_POST["note"];
    $categoryId = $_POST["billingType"];
    $noteId = $_POST["note_id"];

    try{
	    $sql = $dbh->prepare("UPDATE billing_notes
				  SET notes_category_id = ?,
				  notes = ?
				  WHERE notes_id = ?
				 ");
	    $sql->bindValue(1,$categoryId,PDO::PARAM_INT);
	    $sql->bindValue(2,$note,PDO::PARAM_STR);
	    $sql->bindValue(3,$noteId,PDO::PARAM_INT);

	    $sql->execute();
    }
    catch(PDOException $error){
	echo $error->getMessage();
    }
    header("Location:notes.php");
  
  }
  echo "<br>";
  echo "<div align='center'>";

  echo "<table>"
       ."<thead>"
       ."<tr>"
       ."<th>Select Notes</th>"
       ."<th>Type of Billing</th>"
       ."<th>Notes</th>"
       ."<tr>"
       ."</thead><tbody>";      

  foreach($notes as $details){

    $id = $details["notes_id"];
    $categoryId = $details["notes_category_id"];
    $billingType = $details["category_name"];
    $note = $details["notes"];

    echo "<tr>"
         ."<td><input type='checkbox' name='id' value='$id'></td>"
         ."<td>$billingType</td>"
         ."<td>$note</td>"
         ."</tr>";
  }

  echo "</tbody></table>";
  echo "</div>";
  
?>
</form>
</body>
</html>
