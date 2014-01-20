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


 
?>
