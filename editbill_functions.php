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
                           AND NOT EXISTS(SELECT bd.participant_id FROM billing_details bd WHERE bd.event_id=? AND bd.participant_id = cp.id);
                          ");
        $stmt->bindValue(1,$eventId,PDO::PARAM_INT);
        $stmt->bindValue(2,$amount,PDO::PARAM_INT);
        $stmt->bindValue(3,$eventId,PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
}


?>
