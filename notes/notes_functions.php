<?php

function getAllNotes($dbh){

  $sql = $dbh->prepare("SELECT notes_id, notes.notes_category_id,category.category_name,notes,notes_status
                        FROM billing_notes notes, billing_notes_category category
                        WHERE notes.notes_category_id = category.notes_category_id
                       ");
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

function getNoteById($dbh,$noteId){

  
  $sql = $dbh->prepare("SELECT notes_id, notes.notes_category_id,category.category_name,notes,notes_status
                        FROM billing_notes notes, billing_notes_category category
                        WHERE notes.notes_category_id = category.notes_category_id
                        AND notes_id = ?
                       ");
  $sql->bindValue(1,$noteId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  return $result;
 
}

function getAllTypeBilling($dbh){

  $sql = $dbh->prepare("SELECT notes_category_id,category_name
                        FROM billing_notes_category 
                       ");
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function displayBillingNote($dbh,array $billingNote){

  $noteId = $billingNote["notes_id"];
  $categoryId = $billingNote["notes_category_id"];
  $categoryName = $billingNote["category_name"];
  $note = $billingNote["notes"];
  $status = $billingNote["notes_status"];
  $billingTypes = getAllTypeBilling($dbh);
  
  $html = "<fieldset>"
        . "<legend>Edit Note</legend>"
        . "<table>"
        . "<tr>"
        . "<th>Note</th>"
        . "<td><input type='text' value=$note></td>"
        . "</tr>"
        . "<tr>"
        . "<th>Type of Billing</th>"
        . "<td><select name='billingType'>"
        . "<option>- Select type of billing -</option>"
        . "<option disabled></option>";
        
  foreach($billingTypes as $type){

    $id = $type["notes_category_id"];
    $category = $type["category_name"];
 
    $selected = $categoryId == $id ? "selected" : "";

    $html = $html."<option value=$id $selected>$category</option>";
  }
        
  $html = $html. "</select></td></tr>"
        . "<tr>"
        . "<th>Status</th>"
        . "<td>"
        . "<select name='statusType'>";

  if($status == 0){
   $disabled = "selected";
   $enabled = "";
  }

  else{
   $disabled = "";
   $enabled = "selected";
  }
       
 $html = $html. "<option value='0' $disabled>disabled</option>"
       . "<option value='1' $enabled>enabled</option>"
       . "</select>"
       . "</td>"
       . "</tr>"
       . "<tr>"
       . "<td colspan='2' align='left'><input type='submit' value='Update Note' name='update'></td>"
       . "</tr>"
       . "</table>"
       . "</fieldset>";

  return $html;
}


 
?>
