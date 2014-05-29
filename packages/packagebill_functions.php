<?php

/*
 * Returns all events group per pid(package_id)
 */
function getAllPackageDetails(){

	$stmt = civicrmDB("SELECT bpe.pid,bp.package_name,bpe.event_id, ce.title as event_name, ce.start_date, ce.end_date
                           FROM billing_package_events bpe, billing_package bp, civicrm_event ce
                           WHERE bpe.pid = bp.pid
                           AND  ce.id = bpe.event_id");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_GROUP);

        return $result;
}

function getAllPackagesPerPackageId(){
   
  $sql = "SELECT pid,package_name FROM billing_package";
  $sql = civicrmDB($sql);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_KEY_PAIR);

  return $result;
}

function searchPackageName($packageName){

	$stmt = civicrmDB("SELECT pid,package_name FROM billing_package
                           WHERE package_name LIKE ?");
        $stmt->bindValue(1,"%".$packageName."%",PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return $result;
}

?>

