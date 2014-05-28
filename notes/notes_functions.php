<?php

function getAllNotes($dbh){

  $sql = $dbh->prepare("SELECT notes_id, notes.notes_category_id,category.category_name,notes
                        FROM billing_notes notes, billing_notes_category category
                        WHERE notes.notes_category_id = category.notes_category_id
                       ");
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

function getNoteById($dbh,$noteId){

  
  $sql = $dbh->prepare("SELECT notes_id, notes.notes_category_id,category.category_name,notes
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
  $billingTypes = getAllTypeBilling($dbh);
  
  $html = "<fieldset>"
        . "<legend>Edit Note</legend>"
        . "<table>"
        . "<tr><td colspan='2'><input type=number value='$noteId' hidden name='note_id'></td></tr>"
        . "<tr>"
        . "<th>Note</th>"
        . "<td><input type='text' value='$note' name='note'></td>"
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
       . "<td colspan='2' align='left'><input type='submit' value='Update Note' name='update'></td>"
       . "</tr>"
       . "</table>"
       . "</fieldset>";

  return $html;
}


function updateNote($dbh,array $billingNote){

  $categoryId = $billingNote["category_id"];
  $notes = $billingNote["notes"];
  $noteId = $billingNote["note_id"];

  $sql = $dbh->prepare("UPDATE FROM billing_notes
                        notes_category_id = ?,
                        notes = ?,
                        WHERE notes_id = ?
                       ");
  $sql->bindValue(1,$categoryId,PDO::PARAM_INT);
  $sql->bindValue(2,$notes,PDO::PARAM_STR);
  $sql->bindValue(3,$noteId,PDO::PARAM_INT);

  $sql->execute();

}


function addNoteForm($dbh){

  $html = "<fieldset>"
        . "<legend>Add Note</legend>"
        . "<table>"
        . "<tr>"
        . "<th>Note</th><td><input type='text' name='note'></td>"
        . "</tr>"
        . "<tr>"
        . "<th>Type of Billing</th>"
        . "<td><select name='billingType'>"
        . "<option>- Select type of billing -</option>"
        . "<option disabled></option>";
    $billingTypes = getAllTypeBilling($dbh);
        
  foreach($billingTypes as $type){

    $id = $type["notes_category_id"];
    $category = $type["category_name"];
 
    $html = $html."<option value=$id>$category</option>";
  }
        
  $html = $html. "</select></td></tr>"
       . "</tr>"
       . "<tr>"
       . "<td colspan='2'><input type='submit' value='Add Note' name='add'></td>"
       . "</table>"
       . "</fieldset>";

  return $html;
} 
?>
