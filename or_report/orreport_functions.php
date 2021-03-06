<?php

function displayContactsWithEvents($searchValue){

	$sql = civicrmDB("SELECT cc.id as contact_id, cc.sort_name, cc.organization_name
                          FROM civicrm_contact cc
                          WHERE cc.contact_type = 'Individual'
                          AND (cc.sort_name LIKE ? OR cc.organization_name LIKE ?)
                          AND cc.is_deleted = '0'
                          ORDER BY cc.sort_name");
        $sql->bindValue(1,"%".$searchValue."%",PDO::PARAM_STR);
        $sql->bindValue(2,"%".$searchValue."%",PDO::PARAM_STR);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_UNIQUE);
 
        return $result;
}

function displayOrganizationWithEvents($searchValue){

	$sql = civicrmDB("SELECT cc.id as contact_id, cc.sort_name, cc.organization_name
                          FROM civicrm_contact cc
                          WHERE cc.contact_type = 'Organization'
                          AND cc.sort_name LIKE ?
                          AND cc.is_deleted = '0'
                          ORDER BY cc.sort_name");
        $sql->bindValue(1,"%".$searchValue."%",PDO::PARAM_STR);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_UNIQUE);
 
        return $result;
}

function getContactEvents($contact_id){

	$sql = civicrmDB("SELECT bd.participant_id,bd.event_id,ce.title as event_name,bd.organization_name,bd.billing_type,bd.bir_no,bd.fee_amount,bd.amount_paid,
                          bd.billing_no,bd.bill_date,bd.nonvatable_type,bd.bir_no
                          FROM billing_details bd, civicrm_event ce
                          WHERE bd.contact_id = ?
                          AND bd.event_id = ce.id");
        $sql->bindValue(1,$contact_id,PDO::PARAM_INT);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_UNIQUE);

	return $result;
}

function getOrganizationEvents($contact_id){

	$sql = civicrmDB("SELECT bc.org_contact_id,bc.bir_no,bc.event_id,ce.title as event_name,bc.organization_name,bc.total_amount,bc.amount_paid,bc.billing_no,bc.bill_date,nonvatable_type
                FROM billing_company bc, civicrm_event ce
                WHERE bc.org_contact_id = ?
                AND bc.event_id = ce.id");
        $sql->bindValue(1,$contact_id,PDO::PARAM_INT);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	return $result;
}

function getContactMembershipBillings($contact_id){

	$sql = civicrmDB("SELECT membership_id,organization_name,membership_type,billing_no,billing_type,fee_amount,bill_date,year,amount_paid
                        FROM billing_membership WHERE contact_id = ?");
        $sql->bindValue(1,$contact_id,PDO::PARAM_INT);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $result;
}

function getPayments($debtorno){
	$sqltrans = weberpDB("SELECT gltrans.voucherno,trans.trandate, trans.ovamount,trans.invtext 
                              FROM debtortrans trans, gltrans 
                              WHERE trans.debtorno = ?
                              AND trans.transno = gltrans.typeno
                              AND trans.type = gltrans.type
                              AND trans.invtext = gltrans.narrative");
        $sqltrans->bindValue(1,$debtorno,PDO::PARAM_STR);
        $sqltrans->execute();
        $result = $sqltrans->fetchAll(PDO::FETCH_ASSOC);

        return $result;

}


?>
