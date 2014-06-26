<?php


function getNewlyAddedBillings($dbh,$eventId,$orgId){

  $sql = $dbh->prepare("SELECT cp.id as participant_id, cc.display_name as name,cc.id as contact_id, cp.fee_amount, cps.label as status
                        FROM civicrm_participant cp, civicrm_value_billing_17 billing, civicrm_contact cc, civicrm_participant_status_type cps
                        WHERE cp.id = billing.entity_id
                        AND billing.billing_45 = 'Company'
                        AND cp.event_id = ?
                        AND cc.employer_id = ?
                        AND cc.is_deleted = 0
                        AND cp.contact_id = cc.id
                        AND cp.status_id NOT IN (4,7,15,17)
                        AND cp.status_id = cps.id
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
                       cp.fee_amount,cs.name as participant_status,em.email
                       FROM civicrm_participant cp, civicrm_event ce, civicrm_participant_status_type cs,civicrm_option_value cv,civicrm_contact cc
                       LEFT JOIN civicrm_email em ON em.contact_id = cc.id
                       WHERE cc.id = cp.contact_id
                       AND ce.id = cp.event_id
                       AND cv.option_group_id = '14'
                       AND ce.event_type_id = cv.value
                       AND cp.status_id = cs.id
                       AND cp.id = ?");
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

function checkParticipantBillGenerated($dbh,$participantId,$eventId){

  $sql = $dbh->prepare("SELECT * FROM billing_details
                       WHERE participant_id = ?
                       AND event_id = ?");
  $sql->bindValue(1,$participantId,PDO::PARAM_INT);
  $sql->bindValue(2,$eventId,PDO::PARAM_INT);
  $sql->execute();

  $count = $sql->rowCount();

  if($count > 0){
    $sqlDelete = $dbh->prepare("DELETE FROM billing_details WHERE participant_id = ? AND event_id = ?");

    $sqlDelete->bindValue(1,$participantId,PDO::PARAM_INT);
    $sqlDelete->bindValue(2,$eventId,PDO::PARAM_INT);
    $sqlDelete->execute();
  }
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

  $totalContacts = count($contacts);

  $html = $html."<br><b><font color='#2B547E'>Number of Contacts: $totalContacts</font></b>";

  return $html;
}

function getCompanyAddress($dbh,$orgId){

  $sql = $dbh->prepare("SELECT street_address, city FROM civicrm_address 
                        WHERE contact_id = ?
                       ");
  $sql->bindValue(1,$orgId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
 
  return $result;
}

function getCompleteCompanyAddress($dbh,$orgId){
  $sql = $dbh->prepare("SELECT street_address, city FROM civicrm_address 
                        WHERE contact_id = ?
                       ");
  $sql->bindValue(1,$orgId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $completeAddress = $result['street_address']." ".$result['city'];
  return $completeAddress;

}

function insertMembershipCompanyBilling($dbh,$orgId,$year,$membershipTypeId,$contacts){

    $sqlMembership = $dbh->prepare("SELECT id,name,minimum_fee 
                                       FROM civicrm_membership_type
                                       WHERE id = ?");
    $sqlMembership->bindValue(1,$membershipTypeId, PDO::PARAM_INT);
    $sqlMembership->execute();
    $membership = $sqlMembership->fetch(PDO::FETCH_ASSOC);


    $yearPrefix = date("y",strtotime($year));
    $billingNo = "MEM-".$yearPrefix."-$orgId";
    $totalContacts = count($contacts);
    $individualAmount = $membership["minimum_fee"];
    $billingAmount = $totalContacts*$individualAmount;
    
    $sqlOrg = $dbh->prepare("SELECT cc.id,cc.organization_name,em.email,ca.street_address,ca.city
                             FROM civicrm_contact cc
                             LEFT JOIN civicrm_email em ON cc.id = em.contact_id
                             LEFT JOIN civicrm_address ca ON ca.contact_id = em.contact_id
                             WHERE cc.contact_type = 'Organization'
                             AND cc.is_deleted = '0'
                             AND cc.id = ?
                             ");

    $sqlOrg->bindValue(1,$orgId, PDO::PARAM_INT);
    $sqlOrg->execute();

    $org = $sqlOrg->fetch(PDO::FETCH_ASSOC);
    $orgName = $org["organization_name"];
    $email = $org["email"];
    $street = $org["street_address"];
    $city = $org["city"];
    $billingAddress = $street." ".$city;

    $sqlInsert = $dbh->prepare("INSERT INTO billing_membership_company (org_contact_id,organization_name,email,street,city,bill_address,billing_amount,billing_no,year)
                                VALUES(?,?,?,?,?,?,?,?,?)
                               ");
    $sqlInsert->bindValue(1,$orgId,PDO::PARAM_INT);
    $sqlInsert->bindValue(2,$orgName,PDO::PARAM_STR);
    $sqlInsert->bindValue(3,$email,PDO::PARAM_STR);
    $sqlInsert->bindValue(4,$street,PDO::PARAM_STR);
    $sqlInsert->bindValue(5,$city,PDO::PARAM_STR);
    $sqlInsert->bindValue(6,$billingAddress,PDO::PARAM_STR);
    $sqlInsert->bindValue(7,$billingAmount,PDO::PARAM_INT);
    $sqlInsert->bindValue(8,$billingNo,PDO::PARAM_STR);
    $sqlInsert->bindValue(9,$year,PDO::PARAM_INT);

    //$sqlInsert->execute();

    insertIndividualMemberBilling($dbh,$contacts,$year,$billingNo,$membershipTypeId);

}

function insertIndividualMemberBilling($dbh,array $contacts,$year,$billingNo,$membershipTypeId){
    
    $sqlMembership = $dbh->prepare("SELECT id,name,minimum_fee 
                                       FROM civicrm_membership_type
                                       WHERE id = ?");
    $sqlMembership->bindValue(1,$membershipTypeId, PDO::PARAM_INT);
    $sqlMembership->execute();
    $membership = $sqlMembership->fetch(PDO::FETCH_ASSOC);

    /**echo "<pre>";
    print_r($contacts);
    echo "</pre>";**/

    foreach($contacts as $key =>$contactId){

        $sqlDetails = $dbh->prepare("SELECT cc.id as contact_id, cc.display_name, cc.organization_name, em.email,em.is_primary,cm.join_date, cc.employer_id,
cm.start_date, cm.end_date,cm.id as membership_id, cmt.name as membership_type,cs.name as status
FROM civicrm_contact cc   
LEFT JOIN civicrm_membership cm ON cm.contact_id = cc.id        
LEFT JOIN civicrm_membership_type cmt                           
ON cm.membership_type_id = cmt.id                               
LEFT JOIN civicrm_membership_status cs                          
ON cm.status_id = cs.id                                         
LEFT JOIN civicrm_email em                                      
ON cc.id = em.contact_id     
WHERE cc.id = ?                                  
AND cc.is_deleted = '0' 
                                  ");
         $sqlDetails->bindValue(1,$contactId,PDO::PARAM_INT);
         $sqlDetails->execute();
         $details = $sqlDetails->fetch(PDO::FETCH_ASSOC);

         echo "<pre>";
         print_r($details);
         echo "</pre>";

         //$contactId = $details["id"];
         $membershipId = $details["membership_id"];
         $membershipType = $membership["name"];
         $name = $details["display_name"];
         $email = $details["email"];
         $address = getAddressDetails($dbh,$contactId);
         $street = $address["street"];
         $city = $address["city"];
         $billingAddress = $street." ".$city;
         $orgName = $details["organization_name"];
         $orgId = $details["employer_id"];
         $feeAmount = $membership["minimum_fee"];
         $subtotal = $feeAmount;
         $vat = 0.0;

         $sql = $dbh->prepare("INSERT INTO billing_membership
                        (membership_id,contact_id,membership_type,member_name,email,street,city,bill_address,organization_name,org_contact_id,fee_amount,subtotal,vat,billing_no,year)
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
                       ");

         $sql->bindValue(1,$membershipId,PDO::PARAM_INT);
         $sql->bindValue(2,$contactId,PDO::PARAM_INT);
         $sql->bindValue(3,$membershipType,PDO::PARAM_STR);
         $sql->bindValue(4,$name,PDO::PARAM_STR);
         $sql->bindValue(5,$email,PDO::PARAM_STR);
         $sql->bindValue(6,$street,PDO::PARAM_STR);
         $sql->bindValue(7,$city,PDO::PARAM_STR);
         $sql->bindValue(8,$billingAddress,PDO::PARAM_STR);
         $sql->bindValue(9,$orgName,PDO::PARAM_STR);
         $sql->bindValue(10,$orgId,PDO::PARAM_INT);
         $sql->bindValue(11,$feeAmount,PDO::PARAM_INT);
         $sql->bindValue(12,$subtotal,PDO::PARAM_INT);
         $sql->bindValue(13,$vat,PDO::PARAM_INT);
         $sql->bindValue(14,$billingNo,PDO::PARAM_STR);
         $sql->bindValue(15,$year,PDO::PARAM_INT);

         $sql->execute();

         var_dump($sql);

         echo "$membershipId<br>";
         echo "$contactId<br>";
         echo "$membershipType<br>";
         echo "$name<br>";
         echo "$email<br>";
         echo "$street<br>";
         echo "$city<br>";
         echo "$billingAddress<br>";
         echo "$orgName<br>";
         echo "$orgId<br>";
         echo "$feeAmount<br>";
         echo "$subtotal<br>";
         echo "$vat<br>";
         echo "$billingNo<br>";
         echo "$year<br>";

       }
}

function removedBilledMembershipContacts($dbh,array $contacts,$year){

  $billedContacts = array();
  foreach($contacts as $contactId){

   $sql = $dbh->prepare("SELECT COUNT(*) as count FROM billing_membership WHERE contact_id = ? AND year = ?");
   $sql->bindValue(1,$contactId,PDO::PARAM_INT);
   $sql->bindValue(2,$year,PDO::PARAM_INT);
   $sql->execute();

   $result = $sql->fetch(PDO::FETCH_ASSOC);
   $count = $result["count"];


   if($count == 0){

     $billedContacts[] = $contactId;
   }

  }

  return $billedContacts;

}

function getNamesRemoveContacts($dbh,$contacts){

  $names = array();
  foreach($contacts as $key => $contactId){

    $sql = $dbh->prepare("SELECT display_name FROM civicrm_contact 
                          WHERE id = ? AND is_deleted = '0'");
    $sql->bindValue(1,$contactId,PDO::PARAM_INT);
    $sql->execute();

    $result = $sql->fetch(PDO::FETCH_ASSOC);
    $contactName = $result["display_name"];

    $names[] = $contactName;
  }

 return $names;
}
?>
