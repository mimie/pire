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

function getDetailsForParticipant($dbh,$participantId){

 $sql = $dbh->prepare("SELECT cp.id as participant_id,cp.contact_id,cp.event_id,
                       cv.label as event_type,ce.title as event_name,cc.display_name as participant_name, cc.organization_name,
                       em.email, cp.fee_amount,cs.name as participant_status
                       FROM civicrm_contact cc, civicrm_participant cp, civicrm_event ce, civicrm_email em,civicrm_participant_status_type cs,civicrm_option_value cv
                       WHERE cc.id = cp.contact_id
                       AND ce.id = cp.event_id
                       AND cv.option_group_id = '14'
                       AND ce.event_type_id = cv.value
                       AND cp.status_id = cs.id
                       AND em.contact_id = cp.contact_id
                       AND em.location_type_id = '1'
                       AND em.is_primary = '1'
                       AND cp.id = ?
  ");

 $sql->bindValue(1,$participantId,PDO::PARAM_INT);
 $sql->execute();
 $result = $sql->fetch(PDO::FETCH_ASSOC);

 return $result;

}

function updateAddedAmount($dbh,$billingNo,$addedAmount){

  $sql = $dbh->prepare("SELECT total_amount FROM billing_company
                        WHERE billing_no = ?
                       ");
  $sql->bindValue(1,$billingNo,PDO::PARAM_STR);
  $sql->execute();

  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $totalAmount = $result["total_amount"];

  $totalAmount = $totalAmount + $addedAmount;
  $vat = $totalAmount/9.3333;
  $vat = number_format($vat, 2, '.', '');
  $subtotal = $totalAmount - $vat;
  $subtotal = number_format($subtotal, 2, '.','');
  $updateTime = date("Y-m-d h:i:s");

  $sqlUpdate = $dbh->prepare("UPDATE billing_company
                             SET total_amount = ?, subtotal = ?, vat = ?,bill_date = ?
                             WHERE billing_no = ?");
  $sqlUpdate->bindValue(1,$totalAmount,PDO::PARAM_INT);
  $sqlUpdate->bindValue(2,$subtotal,PDO::PARAM_INT);
  $sqlUpdate->bindValue(3,$vat,PDO::PARAM_INT);
  $sqlUpdate->bindValue(4,$updateTime,PDO::PARAM_INT);
  $sqlUpdate->bindValue(5,$billingNo,PDO::PARAM_STR);

  $sqlUpdate->execute();
             
}

function getContactsPerCompany($dbh,$orgId){

  $sql = $dbh->prepare("SELECT cc.id as contact_id, cc.display_name, cc.organization_name, em.email,cm.join_date, 
                        cm.start_date, cm.end_date,cm.id as membership_id, cmt.name as membership_type,cs.name as status
                        FROM civicrm_contact cc 
                        LEFT JOIN civicrm_membership cm ON cm.contact_id = cc.id
                        LEFT JOIN civicrm_membership_type cmt
                        ON cm.membership_type_id = cmt.id
                        LEFT JOIN civicrm_membership_status cs
                        ON cm.status_id = cs.id
                        LEFT JOIN civicrm_email em
                        ON cc.id = em.contact_id
                        AND em.is_primary = '1'
                        WHERE cc.employer_id = ?
                        AND cc.is_deleted = '0'
                        ORDER by cc.display_name
                      ");

  $sql->bindValue(1,$orgId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function filterContactsByEndDate($dbh,$orgId,$endDate){

  $sql = $dbh->prepare("SELECT cc.id as contact_id, cc.display_name, cc.organization_name, em.email,cm.join_date, 
                        cm.start_date, cm.end_date,cm.id as membership_id, cmt.name as membership_type,cs.name as status
                        FROM civicrm_contact cc 
                        INNER JOIN civicrm_membership cm ON cm.contact_id = cc.id
                        INNER JOIN civicrm_membership_type cmt
                        ON cm.membership_type_id = cmt.id
                        INNER JOIN civicrm_membership_status cs
                        ON cm.status_id = cs.id
                        LEFT JOIN civicrm_email em
                        ON cc.id = em.contact_id
                        AND em.is_primary = '1'
                        WHERE cc.employer_id = ?
                        AND cm.end_date = ?
                        AND cc.is_deleted = '0'
                        ORDER by cc.display_name
                      ");

  $sql->bindValue(1,$orgId,PDO::PARAM_INT);
  $sql->bindValue(2,$endDate,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;

}              

function displayContactsPerCompany(array $contacts,$orgName){

  $html = "<table id='contacts' style='width:100%;'>"
        . "<thead>"
        . "<tr><th colspan='10'>$orgName</th></tr>"
        . "<tr>"
        . "<th>Select Contact</th>"
        . "<th>Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Email</th>"
        . "<th>Join Date</th>"
        . "<th>Start Date</th>"
        . "<th>End Date</th>"
        . "<th>Membership Type</th>"
        . "<th>Membership Status</th>"
        . "<th>Classification</th>"
        . "</tr>"
        . "</thead>";

  $html = $html."<tbody>";

  foreach($contacts as $key => $info){
     $contactId = $info["contact_id"];
     $name = $info["display_name"];
     $name = mb_convert_encoding($name,"UTF-8");
     $orgName = $info["organization_name"];
     $orgName = mb_convert_encoding($orgName,"UTF-8");
     $email = $info["email"];
     $joinDate = $info["join_date"];
     $startDate = $info["start_date"];
     $endDate = $info["end_date"];
     $type = $info["membership_type"];
     $status = $info["status"];
     $membershipId = $info["membership_id"];

     $classification = $membershipId == NULL ? 'Nonmember':'Member';

   $html = $html."<tr>"
         . "<td><input type='checkbox' name='contactIds[]' value='$contactId'></td>"
         . "<td>$name</td>"
         . "<td>$orgName</td>"
         . "<td>$email</td>"
         . "<td>$joinDate</td>"
         . "<td>$startDate</td>"
         . "<td>$endDate</td>"
         . "<td>$type</td>"
         . "<td>$status</td>"
         . "<td>$classification</td>"
         . "</tr>";

  }
  $html = $html."</tbody></table>";

  return $html;
}
?>
