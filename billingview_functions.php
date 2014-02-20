<?php

function getAllIndividualBillings($dbh){

  $sql = $dbh->prepare("SELECT bd.participant_id,bd.contact_id,
                        cc.sort_name,event_type,event_name,
                        cc.organization_name,cps.name as status,
                        bd.fee_amount,bd.billing_no,bill_date,
                        cp.fee_amount as current_amount
                        FROM billing_details bd, civicrm_participant cp, civicrm_participant_status_type cps, civicrm_contact cc
                        WHERE bd.contact_id = cc.id
                        AND cp.id = bd.participant_id
                        AND cp.status_id = cps.id
                       ");
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;
}
?>
