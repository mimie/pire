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

function getBillByPackageId($packageId){

	$stmt = civicrmDB("SELECT bdp.bir_no, bdp.contact_id, bdp.subtotal, bdp.vat, bdp.total_amount, bdp.amount_paid,bdp.bill_date,
                           bn.notes, cc.sort_name, cc.organization_name
                           FROM civicrm_contact cc,billing_details_package bdp
                           LEFT JOIN billing_notes bn ON bdp.notes_id = bn.notes_id
                           WHERE cc.id = bdp.contact_id
                           AND bdp.pid = ?
                            ");
       $stmt->bindValue(1,$packageId,PDO::PARAM_INT);
       $stmt->execute();
       $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
       return $result;
}

/*
 * Return contact details of the bill with package
 * @bir_no = BS No. in the voucher
 */
function getBillDetailsByBIRNo($bir_no){

	$stmt = civicrmDB("SELECT cc.sort_name, bdp.subtotal,bdp.vat, bdp.total_amount, bn.notes, bdp.bill_date, bd.street_address__company__3 as street_address,bd.city__company__5 as city_address
                           FROM civicrm_contact cc, billing_details_package bdp
                           LEFT JOIN billing_notes bn ON bdp.notes_id = bn.notes_id
                           LEFT JOIN civicrm_value_business_data_1 bd ON bdp.contact_id = bd.entity_id
                           WHERE cc.id = bdp.contact_id
                           AND bdp.bir_no = ?
                          ");
        $stmt->bindValue(1,$bir_no,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;

}

function getEventBillDetailsByBIRNo($bir_no){

	$stmt = civicrmDB("SELECT bd.event_id, bd.fee_amount,ce.title as event_name, ce.start_date, ce.end_date
                           FROM billing_details bd, civicrm_event ce
                           WHERE bd.event_id = ce.id
                           AND bir_no = ?
                          ");
        $stmt->bindValue(1,$bir_no,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
}

?>

