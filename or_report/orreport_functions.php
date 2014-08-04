<?php

function displayContactsWithEvents($searchValue){

	$sql = civicrmDB("SELECT DISTINCT (cp.contact_id), cc.sort_name, cc.organization_name
                          FROM civicrm_participant cp, civicrm_contact cc
                          WHERE cp.contact_id = cc.id
                          AND (cc.sort_name LIKE ? OR cc.organization_name LIKE ?)
                          AND cc.contact_type = 'Individual'
                          ORDER BY cc.sort_name");
        $sql->bindValue(1,"%".$searchValue."%",PDO::PARAM_STR);
        $sql->bindValue(2,"%".$searchValue."%",PDO::PARAM_STR);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_UNIQUE);
 
        return $result;
}
?>
