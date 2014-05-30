<?php

function date_standard($date){

	$date = date("F j,Y",strtotime($date));
	return $date;
}

function getContactName($contact_id){
	
	$stmt = civicrmDB("SELECT sort_name FROM civicrm_contact WHERE id=?");
        $stmt->bindValue(1,$contact_id,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $name = $result["sort_name"];

        return $name;
}

?>
