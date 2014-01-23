<?php

function getNewlyAddedBillings($dbh,$eventId,$orgId){

  $sql = $dbh->prepare("SELECT cp.id as participant_id, cc.display_name as name,cc.id as contact_id, cp.fee_amount
                        FROM civicrm_participant cp, civicrm_value_billing_17 billing, civicrm_contact cc
                        WHERE cp.id = billing.entity_id
                        AND billing.billing_45 = 'Company'
                        AND cp.event_id = ?
                        AND cc.employer_id = ?
                        AND cc.is_deleted = 0
                        AND cp.contact_id = cc.id
                        AND cp.id NOT IN (SELECT bd.participant_id FROM billing_details bd
                                          WHERE bd.event_id = ?
                                          AND bd.org_contact_id = ?
                                          AND billing_type = 'Company')
                       ");
  $sql->bindValue(1,$eventId,PDO::PARAM_INT);
  $sql->bindValue(2,$orgId,PDO::PARAM_INT);
  $sql->bindValue(3,$eventId,PDO::PARAM_INT);
  $sql->bindValue(4,$orgId,PDO::PARAM_INT);
  $sql->execute();
 
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;

}              
?>
