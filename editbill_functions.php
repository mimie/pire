<?php

function getParticipantWithSameAmount($eventId,$amount){

	$stmt = civicrmDB("SELECT cp.id as participant_id, cc.sort_name, cp.fee_amount
                           FROM civicrm_participant cp, civicrm_contact cc, civicrm_value_billing_17 as billing_type
                           WHERE event_id = ?
                           AND cp.fee_amount = ?
                           AND cp.status_id <> '4'
                           AND cc.id = cp.contact_id
                           AND billing_type.billing_45 = 'Individual'
                           AND billing_type.entity_id = cp.id
                           AND NOT EXISTS(SELECT bd.participant_id FROM billing_details bd WHERE bd.event_id=? AND bd.participant_id = cp.id)
                           ORDER BY cc.sort_name;
                          ");
        $stmt->bindValue(1,$eventId,PDO::PARAM_INT);
        $stmt->bindValue(2,$amount,PDO::PARAM_INT);
        $stmt->bindValue(3,$eventId,PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
}

/*
 * $info - details of the participant
 */
function updateParticipant($bir_no,array $info){

	
   try{
	$stmt = civicrmDB("UPDATE billing_details
                           SET participant_id = ?,
                           contact_id = ?,
                           participant_name = ?,
                           email = ?,
                           bill_address = ?,
                           organization_name = ?,
                           org_contact_id = ?,
                           billing_no = ?,
                           participant_status = ?,
                           generator_uid = ?
                           WHERE bir_no = ?
                          ");
         var_dump($stmt);
         

        $billing_no = $info['event_type']."-".date("y")."-".formatBillingNo($info['participant_id']);

        $address = $info['street_address']." ".$info['city_address'];
        $stmt->bindValue(1,$info['participant_id'],PDO::PARAM_INT);
        $stmt->bindValue(2,$info['contact_id'],PDO::PARAM_INT);
        $stmt->bindValue(3,$info['sort_name'],PDO::PARAM_STR);
        $stmt->bindValue(4,$info['email'],PDO::PARAM_STR);
        $stmt->bindValue(5,$address,PDO::PARAM_STR);
        $stmt->bindValue(6,$info['organization_name'],PDO::PARAM_STR);
        $stmt->bindValue(7,$info['org_contact_id'],PDO::PARAM_INT);
        $stmt->bindValue(8,$billing_no,PDO::PARAM_STR);
        $stmt->bindValue(9,$info['participant_status'],PDO::PARAM_STR);
        $stmt->bindValue(10,$_GET['uid'],PDO::PARAM_INT);
        $stmt->bindValue(11,$bir_no,PDO::PARAM_STR);
        
        $stmt->execute();
    }

    catch(PDOException $e){

	echo $e->getCode();
    }
}


?>
