<?php

function getIndividualParticipantsByEventId($eventId){

	$stmt = civicrmDB("SELECT cc.id as contact_id, cp.status_id,cc.sort_name,cc.organization_name, cp.fee_amount,cp.id as participant_id, 
                     billing_type.billing_45 as bill_type,cps.label as status,
                     bd. street_address__company__3 as street_address, city__company__5 as city_address
                     FROM civicrm_participant cp, civicrm_value_billing_17 as billing_type, civicrm_participant_status_type cps, civicrm_contact cc
                     LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
                     WHERE cp.contact_id = cc.id
                     AND billing_type.entity_id = cp.id
                     AND cp.event_id = ?
                     AND billing_type.billing_45 = 'Individual'
                     AND cps.id = cp.status_id
                     AND cc.is_deleted = '0'
                     ORDER BY sort_name");
	$stmt->bindValue(1,$eventId,PDO::PARAM_INT);
        $stmt->execute();
       
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $result;
  
}

function getIndividualBilledParticipantsByEventId($eventId){

	$stmt = civicrmDB("SELECT participant_id,generated_bill,post_bill,billing_no,bir_no,bill_date,amount_paid,subtotal,vat FROM billing_details
                           WHERE event_id = ?
                           AND billing_type = 'Individual'
                          ");
        $stmt->bindValue(1,$eventId,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $participants = array();
        foreach($result as $key=>$field){
        	$participant_id = $field["participant_id"];
                unset($field["participant_id"]);
                $participants[$participant_id] = $field;
        }
        
       return $participants;
}

function getBIRDetails($billing_no){

	$stmt = civicrmDB("SELECT cc.sort_name,ce.title as event_name,ce.start_date,ce.end_date,bill.bir_no,bill.fee_amount,
                           bill.subtotal,bill.vat,bill.bill_date,bd. street_address__company__3 as street_address, 
                           city__company__5 as city_address
                           FROM billing_details bill,civicrm_event ce,civicrm_contact cc
                           LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
                           WHERE bill.billing_no = ?
                           AND bill.event_id = ce.id
                           AND bill.contact_id = cc.id
                           ");
	$stmt->bindValue(1,$billing_no,PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
}

/*
 *get participant details to be inserted in the billing_details table
 */
function getInfoByParticipantId($participant_id){

	$stmt = civicrmDB("SELECT cp.id as participant_id, cp.contact_id, cp.event_id, cov.label as event_type,ce.title as event_name, cc.sort_name, 
                           em.email,cc.organization_name, bd.street_address__company__3 as street_address, bd.city__company__5 as city_address,
                           cc.employer_id as org_contact_id, cp.fee_amount, cps.label as particicpant_status
                           FROM civicrm_participant cp, civicrm_event ce, civicrm_option_value cov, civicrm_participant_status_type cps,civicrm_contact cc
                           LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
                           LEFT JOIN civicrm_email em ON em.contact_id = bd.entity_id
                           WHERE cp.event_id = ce.id
                           AND cov.value = ce.event_type_id
                           AND cov.option_group_id = '14'
                           AND cp.contact_id = cc.id
                           AND cps.id = cp.status_id
                           AND cp.id = ?");
	$stmt->bindValue(1,$participant_id,PDO::PARAM_INT);
	$stmt->execute();

	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	return $result;
	
}

?>
