<?php

/**
 *function to get membershipDetails
 *membership_type_id = 'General' from civicrm_membership_type
 */

function getMembersToExpire(PDO $dbh,$endDate){

  $sql = $dbh->prepare("SELECT id,contact_id,end_date,status_id,membership_type_id,join_date,start_date,end_date
                        FROM civicrm_membership
                        WHERE membership_type_id = '1'
                        AND end_date = ?
                       ");
  $sql->bindParam(1,$endDate,PDO::PARAM_STR,10);
  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details;
}

function getMembersByName(PDO $dbh, $endDate,$name){

  $sql = $dbh->prepare("SELECT cm.id, cm.contact_id, cm.end_date,cm.status_id,cm.membership_type_id,cm.join_date,cm.start_date,cm.end_date
                        FROM civicrm_membership cm, civicrm_contact cc
                        WHERE membership_type_id = '1'
                        AND cm.end_date = ?
                        AND cc.display_name LIKE '%$name%'
                      ");
  $sql->bindParam(1,$endDate,PDO::PARAM_STR,10);
  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details;
}

function getMembershipStatus($dbh,$statusId){

  $sql = $dbh->prepare("SELECT id, name FROM civicrm_membership_status
                        WHERE id = '$statusId'
                       ");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $status = $details["name"];

  return $status;
  
}

function getMemberFeeAmount($dbh,$typeId){

  $sql = $dbh->prepare("SELECT minimum_fee FROM civicrm_membership_type
                        WHERE id = '$typeId'");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $feeAmount = $details["minimum_fee"];

  return $feeAmount;
}

function getMemberType($dbh,$typeId){

  $sql = $dbh->prepare("SELECT name FROM civicrm_membership_type
                        WHERE id = '$typeId'");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $memberType = $details["name"];

  return $memberType;
}

function checkMembershipBilling($dbh,$membershipId,$memberBillingYear){

  $year = $memberBillingYear;
  $sql = $dbh->prepare("SELECT COUNT(*) as exist,billing_no,bill_date FROM billing_membership 
                        WHERE membership_id = '$membershipId'
                        AND year = '$year'
                       ");
  $sql->execute();
  $sqlDetails = $sql->fetch(PDO::FETCH_ASSOC);
 
  return $sqlDetails;
 
}



function displayMemberBilling($dbh,array $members,$expiredDate){
  
  $expiredYear = date("Y",strtotime($expiredDate));
  $memberBillingYear = intval($expiredYear) + 1;

  //$nextYear = date('Y', strtotime('+1 year'));
  $html = "<table id='memberInfo' width='100%'>"
        . "<thead>"
        . "<tr>"
        . "<th colspan='15'>Membership Billing</th>"
        . "</tr>"
        . "<tr>"
        . "<th>Select Members</th>"
        . "<th>Member Name</th>"
        . "<th>Email</th>"
        . "<th>Membership Status</th>"
        . "<th>Organization Name</th>"
        . "<th>Member Fee Amount</th>"
        . "<th>Print Bill</th>"
        . "<th>Send Bill</th>"
        . "<th>Payment Status</th>"
        . "<th>Billing Reference No.</th>"
        . "<th>Billing Date</th>"
        . "<th>Billing Address</th>"
        . "<th>Membership Information</th>"
        . "<th>Billing PDF Download</th>"
        . "</tr></thead>";

  $html = $html."<tbody>";

  foreach($members as $membershipId => $details){

    $name = mb_convert_encoding($details["name"], "UTF-8");
    $email = $details["email"];
    $status = $details["status"];
    $company = $details["company"];
    $amount = $details["fee_amount"];
    $address = mb_convert_encoding($details["address"], "UTF-8");
    $membersLinkInfo = membersLink($membershipId);
    $infoBilling = checkMembershipBilling($dbh,$membershipId,$memberBillingYear);
    $checkBillExist = $infoBilling["exist"];
    $billingNo = $infoBilling["billing_no"];
    $billingDate = $infoBilling["bill_date"];
    $billingDate = date("Y-m-d",strtotime($billingDate));
  
    $disabled = $checkBillExist == 1 ? 'disabled' : '';
    $checkbox = $checkBillExist == 1 ? '' : 'class=checkbox';
   $html = $html."<tr>"
          . "<td><input type='checkbox' name='membershipIds[]' value='$membershipId' $checkbox $disabled></td>"
          . "<td>$name</td>"
          . "<td>$email</td>"
          . "<td>$status</td>"
          . "<td>$company</td>"
          . "<td>$amount</td>";

    if($checkBillExist == 1){

          $year = $memberBillingYear;
          $billingId = getBillingId($dbh,$membershipId,$year);
          $html = $html . "<td>"
                . "<a href='memberBillingReference.php?billingId=$billingId' target='_blank' title='Click to print membership bill' style='text-decoration: none;'>"
                . "<img src='images/printer-icon.png' width='40' height='40'><br>Print"
                . "</a>"
                . "</td>"
                . "<td><a href='emails/membershipBilling/sendMemberBilling.php?billingId=$billingId' style='text-decoration:none;'><img src='images/email.jpg' width='40' height='40'><br>Send</a></td>"
                . "<td>Pay Later</td>"
                . "<td>$billingNo</td>"
                . "<td>$billingDate</td>"
                . "<td>$address</td>"
                . "<td>$membersLinkInfo</td>";

          $pdfFile = "pdf/membershipBilling/".$billingNo.".pdf";
          if(file_exists($pdfFile)) {
             $html = $html . "<td><a href='pdf/membershipBilling/".$billingNo.".pdf' download='IIAP_MembershipBilling_".$billingNo."' title='Click to download pdf file'><img src='images/pdf_download.jpg' width='40' height='40'></td>"
                    . "</tr>";
          }

          else{
             $html = $html . "<td><a href='pdf/membershipBilling/generatePDFMemberBilling.php?billingId=$billingId' title='Click to generate pdf'><img src='images/pdf_me.png' width='50' height='50'> </a></td>"
                   . "</tr>";
          }
      
    }

    else{
       $html = $html."<td><img src='images/not_available.png' width='25' height='25'><br></td>"
             . "<td><img src='images/not_available.png' width='25' height='25'></td>"
             . "<td></td>"
             . "<td></td>"
             . "<td></td>"
             . "<td>$address</td>"
             . "<td>$membersLinkInfo</td>"
             . "<td><img src='images/not_available_download.png' width='40' height='40'></td>"
             . "</tr>";
    }
    
  }

  $html = $html."</tbody></table>";

  return $html;
}

function membersLink($membershipId){


  $link = "<a href=\"membership_info.php?id=$membershipId\""
        . "title='Click to view membership information'"
        . "onclick=\"javascript:void window.open('membership_info.php?id=$membershipId','1384398816566','width=600,height=400,toolbar=1,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');"
        . "return false;\">"
        . "<img src='view_member.png'>"
        . "</a>"; 

  return $link;


}

function displayMemberInfo(PDO $dbh,array $memberInfo){

   $name = mb_convert_encoding($memberInfo["name"],"UTF-8");
   $email = $memberInfo["email"];
   $status = $memberInfo["status"];
   $company = mb_convert_encoding($memberInfo["company"],"UTF-8");
   $address = mb_convert_encoding($memberInfo["address"],"UTF-8");
   $joinDate = $memberInfo["join_date"];
   $startDate = $memberInfo["start_date"];
   $endDate = $memberInfo["end_date"];
   $contactId = $memberInfo["contact_id"];
   $memberId = getMemberId($dbh,$contactId);
   $memberType = $memberInfo["member_type"];

   $joinDate = date("F j Y",strtotime($joinDate));
   $startDate = date("F j Y",strtotime($startDate));
   $endDate = date("F j Y",strtotime($endDate));

   $html = "<table>"
         . "<tr><th>Member Name</th><td><b>".strtoupper($name)."</b></td></tr>"
         . "<tr><th>Member ID</th><td>$memberId</td></tr>"
         . "<tr><th>Membership Type</th><td>$memberType</td></tr>"
         . "<tr><th>Join Date</th><td>$joinDate</td></tr>"
         . "<tr><th>Start Date</th><td>$startDate</td></tr>"
         . "<tr><th>End Date</th><td>$endDate</td></tr>"
         . "<tr><th>Email</th><td>$email</td></tr>"
         . "<tr><th>Membership Status</th><td>$status</td></tr>"
         . "<tr><th>Organization Name</th><td>$company</td></tr>"
         . "<tr><th>Organization Address</th><td>$address</td></tr>"
         . "</table>";

   return $html;

}

function getOrgId($dbh,$organization){

  //$organization = stripslashes($organization); 
  $sql = $dbh->prepare("SELECT id FROM civicrm_contact
                         WHERE contact_type = 'Organization'
                         AND display_name = :organization_name");
  
  $sql->bindParam(':organization_name', $organization, PDO::PARAM_STR,250);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $orgId = $result["id"];
   
  return $orgId;

}

function insertMemberBilling($dbh,array $memberInfo,$membershipYear){

  $membership_id = $memberInfo["membership_id"];
  $contact_id = $memberInfo["contact_id"];
  $membership_type = $memberInfo["member_type"];
  $member_name = $memberInfo["name"];
  $email = $memberInfo["email"];
  $street = $memberInfo["street"];
  $city = $memberInfo["city"];
  $bill_address = $memberInfo["address"];
  $organization_name = $memberInfo["company"];
  $org_contact_id = $memberInfo["org_contact_id"];
  $fee_amount = $memberInfo["fee_amount"];
  $subtotal = $fee_amount;
  $vat = 0.0;

  $sqlMaxBillingId = $dbh->prepare("SELECT MAX(id) as prevBillingId FROM billing_membership");
  $sqlMaxBillingId->execute();
  $maxBillingId = $sqlMaxBillingId->fetch(PDO::FETCH_ASSOC);
  $maxBillingId = $maxBillingId["prevBillingId"] + 1;
  $currentYear = date("y");
  $maxBillingId = formatBillingNo($maxBillingId);
  $billing_no = "MEM-$currentYear-".$maxBillingId;
  $year = $membershipYear;
  
  $sql = $dbh->prepare("INSERT INTO billing_membership
                        (membership_id,contact_id,membership_type,member_name,email,street,city,bill_address,organization_name,org_contact_id,fee_amount,subtotal,vat,billing_no,year)
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
                       ");
  $sql->bindValue(1,$membership_id,PDO::PARAM_INT);
  $sql->bindValue(2,$contact_id,PDO::PARAM_INT);
  $sql->bindValue(3,$membership_type,PDO::PARAM_STR);
  $sql->bindValue(4,$member_name,PDO::PARAM_STR);
  $sql->bindValue(5,$email,PDO::PARAM_STR);
  $sql->bindValue(6,$street,PDO::PARAM_STR);
  $sql->bindValue(7,$city,PDO::PARAM_STR);
  $sql->bindParam(8,$bill_address,PDO::PARAM_STR);
  $sql->bindValue(9,$organization_name,PDO::PARAM_STR);
  $sql->bindValue(10,$org_contact_id,PDO::PARAM_INT);
  $sql->bindValue(11,$fee_amount,PDO::PARAM_INT);
  $sql->bindValue(12,$subtotal,PDO::PARAM_INT);
  $sql->bindValue(13,$vat,PDO::PARAM_INT);
  $sql->bindValue(14,$billing_no,PDO::PARAM_STR);
  $sql->bindValue(15,$year,PDO::PARAM_INT);


  $sql->execute();
}

function getBillingId($dbh,$membershipId,$year){

  $sql = $dbh->prepare("SELECT id FROM billing_membership
                        WHERE membership_id = '$membershipId'
                        AND year = '$year'
                       ");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $id = $result["id"];

  return $id;

}

function getMemberBillingDetails($dbh,$billingId){

  $sql = $dbh->prepare("SELECT member_name, organization_name,contact_id,bill_date, billing_no, street,city, fee_amount, year
                        FROM billing_membership
                        WHERE id = '$billingId'
                       ");
  $sql->execute();
  $billingDetails = $sql->fetch(PDO::FETCH_ASSOC);

  return $billingDetails;
}

function getAllMembershipStatus(PDO $dbh){

   $sql = $dbh->prepare("SELECT id, name FROM civicrm_membership_status");
   $sql->execute();

   $result = $sql->fetchAll(PDO::FETCH_ASSOC);
   $status = array();

   foreach($result as $key => $value){

     $id = $value["id"];
     $name = $value["name"];

     $status[$id] = $name;
   }

  return $status;
}

function getAllMembershipType(PDO $dbh){

  $sql = $dbh->prepare("SELECT id,name FROM civicrm_membership_type");
  $sql->execute();

  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  $type = array();

  foreach($result as $key => $value){
    
    $id = $value["id"];
    $name = $value["name"];

    $type[$id] = $name;
  }

  return $type;
}

/*
 *this will get all organizations listed in the civicrm contact
 */
function getAllCompanies(PDO $dbh){

 $sql = $dbh->prepare("SELECT cc.id, cc.display_name, ca.street_address, ca.city
                       FROM civicrm_contact cc
                       LEFT JOIN civicrm_address ca
                       ON cc.id = ca.contact_id
                       WHERE cc.contact_type='Organization' 
                       AND cc.is_deleted = '0'
                       ORDER BY cc.display_name");
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);
 $companies = array();

 foreach($result as $key => $value){
  
   $id = $value["id"];
   /**$orgName = $value["display_name"];
   $companies[$id] = $orgName;**/
   $companies[$id] = $value;
 }

 return $companies;

}

function searchCompanyName(PDO $dbh,$orgName){

 $sql = $dbh->prepare("SELECT cc.id, cc.display_name, ca.street_address, ca.city
                       FROM civicrm_contact cc
                       LEFT JOIN civicrm_address ca
                       ON cc.id = ca.contact_id
                       WHERE cc.contact_type='Organization' 
                       AND cc.is_deleted = '0'
                       AND cc.display_name LIKE '%$orgName%'
                       ORDER BY cc.display_name");
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);
 $companies = array();

 foreach($result as $key => $value){
  
   $id = $value["id"];
   $companies[$id] = $value;
 }

 return $companies;

}

function displayAllCompanies(array $companies){

  $html = "<table id='companies' width='100%'>"
        . "<thead>"
        . "<tr>"
        . "<th>Organization Name</th>"
        . "<th>Email</th>"
        . "<th>Billing Address</th>"
        . "<th>Select Employees For Billing</th>"
        . "</tr>"
        . "</thead>";

  $html = $html."<tbody>";
  foreach($companies as $id => $values){
    $orgName = $values["display_name"];
    $street = $values["street_address"];
    $city = $values["city"];
    $billAddress = $street." ".$city; 
    
    $html = $html."<tr>"
          . "<td>$orgName,$id</td>"
          . "<td>Email</td>"
          . "<td>$billAddress</td>"
          . "<td><a href='selectMembersBilling.php?orgId=$id' ><img src='images/add_icon.png'></a></td>"
          . "</tr>";
  }

  $html = $html."</tbody></table>";
  return $html;
}

/*
 *this will group contacts by organization
 *the organization_id => contactIds(that exist in civicrm_membership)
 */
function groupMembersByCompany(PDO $dbh){

  $sql = $dbh->prepare("SELECT DISTINCT(cc.id), cc.organization_name
                        FROM civicrm_contact cc, civicrm_membership cm
                        WHERE cc.id = cm.contact_id
                        AND cc.organization_name != 'NULL'
                        AND cc.is_deleted = '0'
                       ");
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  $groupByCompany = array();

  foreach($result as $key => $value){
    $contacts = array();

    $contactId = $value["id"];
    $orgName = $value["organization_name"];
    $orgId = getOrgId($dbh,$orgName);

    if(array_key_exists($orgId,$groupByCompany)){
       $contacts = $groupByCompany[$orgId];
       array_push($contacts,$contactId);
       $groupByCompany[$orgId] = $contacts;
    }

    else{
       array_push($contacts,$contactId);
       $groupByCompany[$orgId] = $contacts;
    }
  }

  return $groupByCompany;
}

function getIndividualMemberDetails(PDO $dbh,$contactId){

  $sql = $dbh->prepare("SELECT id,membership_type_id, join_date, start_date, end_date, status_id
                        FROM civicrm_membership
                        WHERE contact_id = :contactId
                      ");

  $sql->bindParam(':contactId',$contactId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);

  return $result;
  
}

function displayBilledMembers($dbh,$billedMembers,$orgName){

  $html = "<table id='billedMembers' style='width:100%;'>"
        . "<tr><th colspan='10'>$orgName</th></tr>"
        . "<tr>"
        . "<th>Select Members</th>"
        . "<th>Member Name</th>"
        . "<th>Email</th>"
        . "<th>Membership Status</th>"
        . "<th>Member Fee Amount</th>"
        . "<th>Address</th>"
        . "<th>Member Id</th>"
        . "<th>Join Date</th>"
        . "<th>Start Date</th>"
        . "<th>End Date</th>"
        . "</tr>";

  foreach($billedMembers as $membershipId => $details){
    
     $name = $details["name"];
     $email = $details["email"];
     $status = $details["status"];
     $feeAmount = $details["fee_amount"];
     $address = $details["address"];
     $memberId = $details["member_id"];
     $joinDate = $details["join_date"];
     $joinDate = date("F j Y",strtotime($joinDate));
     $startDate = $details["start_date"];
     $startDate = date("F j Y",strtotime($startDate));
     $endDate = $details["end_date"];
     $endDate = date("F j Y",strtotime($endDate));

    $currentYear = date("Y");

    $sql = $dbh->prepare("SELECT * FROM billing_membership
                          WHERE membership_id = ?
                          AND year = ?");
    $sql->bindValue(1,$membershipId,PDO::PARAM_INT);
    $sql->bindValue(2,$currentYear,PDO::PARAM_INT);
    $sql->execute();
    $count = $sql->rowCount();

    $disabled = $count == 0 ? "" : "disabled";

    $html = $html."<tr>"
          . "<td><input type='checkbox' $disabled></td>"
          . "<td>$name,$membershipId</td>"
          . "<td>$email</td>"
          . "<td>$status</td>"
          . "<td>$feeAmount</td>"
          . "<td>$address</td>"
          . "<td>$memberId</td>"
          . "<td>$joinDate</td>"
          . "<td>$startDate</td>"
          . "<td>$endDate</td>"
          . "</tr>";
  }   
      
  $html = $html."</table>";

  return $html;
}

function getMembersByOrgId(PDO $dbh,$orgId){

  $sql = $dbh->prepare("SELECT ci.display_name as name,cm.contact_id,
                        co.display_name as organization_name,cm.id,cm.end_date,
                        cm.start_date,cm.join_date,status_id,membership_type_id
                        FROM civicrm_contact co
                        INNER JOIN civicrm_contact ci ON co.organization_name = ci.organization_name
                        INNER JOIN civicrm_membership cm ON ci.id = cm.contact_id
                        WHERE co.contact_type = 'Organization'
                        AND co.id = :orgId
                        ORDER BY co.id, ci.id
                       ");

  $sql->bindParam(':orgId',$orgId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function getMembersByDate(PDO $dbh,$orgId,$date){

  $sql = $dbh->prepare("SELECT ci.display_name as name,cm.contact_id,
                        co.display_name as organization_name,cm.id,cm.end_date,
                        cm.start_date,cm.join_date,status_id,membership_type_id
                        FROM civicrm_contact co
                        INNER JOIN civicrm_contact ci ON co.organization_name = ci.organization_name
                        INNER JOIN civicrm_membership cm ON ci.id = cm.contact_id
                        WHERE co.contact_type = 'Organization'
                        AND co.id = :orgId
                        AND cm.end_date = :endDate
                        ORDER BY co.id, ci.id
                       ");

  $sql->bindParam(':orgId',$orgId,PDO::PARAM_INT);
  $sql->bindParam(':endDate',$date,PDO::PARAM_STR);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function getNonMembers($dbh){

 $sql = $dbh->prepare("SELECT DISTINCT cc.id, cc.sort_name, cc.organization_name, ce.email
                       FROM civicrm_contact cc 
                       LEFT JOIN civicrm_email ce ON ce.contact_id = cc.id AND ce.is_primary = '1'
                       WHERE cc.contact_type = 'Individual'
                       AND cc.is_deleted = '0'
                       AND cc.display_name <> 'Admin Mister'
                       AND cc.id NOT IN(SELECT cm.contact_id
                                     FROM civicrm_membership cm
                                     WHERE cm.contact_id = cc.id)
                       AND cc.id NOT IN(SELECT cb.contact_id
                                        FROM billing_membership cb
                                        WHERE cb.membership_id = '0'
                                       )
                       ORDER by cc.sort_name
                       ") ;

 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;

}

function displayNonMembers(array $nonMembers){

  $html = "<div align='center' padding='3px'>"
        . "<table id='memberInfo'>"
        . "<thead>"
        . "<tr>"
        . "<th colspan = '4'>IIAP Nonmembers</th>"
        . "</tr>"
        . "<tr>"
        . "<th><input type='checkbox' id='check'>Select contact</th>"
        . "<th>Contact Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Email</th>" 
        . "</tr>"
        . "</thead>"
        . "<tbody>";

  foreach($nonMembers as $key => $contact){

    $contactId = $contact["id"];
    $name = $contact["sort_name"];
    $name = mb_convert_encoding($name,"UTF-8");
    $orgName = $contact["organization_name"];
    $orgName = mb_convert_encoding($orgName,"UTF-8");
    $email = $contact["email"];

    $html = $html."<tr>"
          . "<td><input type='checkbox' value='$contactId' name='contactIds[]' class='checkbox'></td>"
          . "<td>$name</td>"
          . "<td>$orgName</td>"
          . "<td>$email</td>"
          . "<tr>";
  }

  $html = $html."</tbody></table></div>";

  return $html;

}

function searchContactByName($dbh,$name){

 $sql = $dbh->prepare("SELECT cc.id, cc.sort_name, cc.organization_name, ce.email
                       FROM civicrm_contact cc
                       LEFT JOIN civicrm_email ce ON ce.contact_id = cc.id AND is_primary = '1'
                       WHERE cc.contact_type = 'Individual'
                       AND cc.display_name <> 'Admin Mister'
                       AND cc.is_deleted = '0'
                       AND cc.display_name LIKE '%$name%'
                       AND cc.id NOT IN(SELECT cm.contact_id
                                     FROM civicrm_membership cm
                                     WHERE cm.contact_id = cc.id)
                       AND cc.id NOT IN(SELECT cb.contact_id
                                        FROM billing_membership cb
                                        WHERE cb.membership_id = '0'
                                       )
                       ORDER BY sort_name
                      ");

 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;

}

function searchContactByEmail($dbh,$email){
 
 $sql = $dbh->prepare("SELECT cc.id, cc.sort_name, cc.organization_name, ce.email
                       FROM civicrm_contact cc
                       LEFT JOIN civicrm_email ce ON ce.contact_id = cc.id AND ce.is_primary = '1'
                       WHERE cc.contact_type = 'Individual'
                       AND cc.display_name <> 'Admin Mister'
                       AND cc.is_deleted = '0'
                       AND ce.email LIKE '%$email%'
                       AND cc.id NOT IN(SELECT cm.contact_id
                                     FROM civicrm_membership cm
                                     WHERE cm.contact_id = cc.id)
                       AND cc.id NOT IN(SELECT cb.contact_id
                                        FROM billing_membership cb
                                        WHERE cb.membership_id = '0'
                                       )
                      ORDER BY sort_name
                      ");

 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;

}

function getAllMembershipBillings($dbh){

 $sql = $dbh->prepare("SELECT id,member_name, email, organization_name, fee_amount,billing_no,bill_date
                       FROM billing_membership");
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;

}

function getMembershipBillingByName($dbh,$name){

 $sql = $dbh->prepare("SELECT id,member_name, email, organization_name, fee_amount,billing_no,bill_date
                       FROM billing_membership
                       WHERE member_name LIKE '%$name%'");
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;

}

function getMembershipBillingByEmail($dbh,$email){

 $sql = $dbh->prepare("SELECT id,member_name, email, organization_name, fee_amount,billing_no,bill_date
                       FROM billing_membership
                       WHERE email LIKE '%$email%'");
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;

}

function getMembershipBillingByOrg($dbh,$org){

 $sql = $dbh->prepare("SELECT id,member_name, email, organization_name, fee_amount,billing_no,bill_date
                       FROM billing_membership
                       WHERE organization_name LIKE '%$org%'");
 //$sql->bindValue(1,$org,PDO::PARAM_STR);
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;

}

function getMembershipBillingByBillingNo($dbh,$billingNo){

 $sql = $dbh->prepare("SELECT id,member_name, email, organization_name, fee_amount,billing_no,bill_date
                       FROM billing_membership
                       WHERE billing_no LIKE '%$billingNo%'");
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;

}

function displayMembershipBillings(array $billings){

  $html = "<table id='billings' style='width:80%;' align='center'>"
        . "<thead>"
        . "<tr>"
        . "<th>Member Name</th>"
        . "<th>Email</th>"
        . "<th>Organization Name</th>"
        . "<th>Member Fee Amount</th>"
        . "<th>Payment Status</th>"
        . "<th>Billing Reference</th>"
        . "<th>Bill Date</th>"
        . "<th>Print Bill</th>"
        . "</tr>"
        . "</thead>";

  $html = $html."<tbody>";

  foreach($billings as $key => $billingInfo){

    $name = $billingInfo["member_name"];
    $email = $billingInfo["email"];
    $org = $billingInfo["organization_name"];
    $fee = $billingInfo["fee_amount"];
    $billingNo = $billingInfo["billing_no"];
    $billingId = $billingInfo["id"];
    $billingdate = $billingInfo["bill_date"];

    $html = $html."<tr>"
          . "<td>$name</td>"
          . "<td>$email</td>"
          . "<td>$org</td>"
          . "<td>$fee</td>"
          . "<td></td>"
          . "<td>$billingNo</td>"
          . "<td>$billingdate</td>"
          . "<td><a href='memberBillingReference.php?billingId=$billingId' target='_blank'><img src='printer-icon.png' height='40' width='40'></a></td>"
          . "</tr>";
  }
    
  $html = $html."</tbody></table>";

  return $html;
}

function getMembershipBillingByDate($dbh,$startDate,$endDate){

  $sql = $dbh->prepare("SELECT id,member_name,email,organization_name,fee_amount,billing_no,bill_date
                        FROM billing_membership
                        WHERE bill_date BETWEEN ? AND ?
                       ");
  $startDate = date("Y-m-d",strtotime($startDate));
  $endDate = date("Y-m-d",strtotime($endDate));
 
  $startDate = $startDate." 00:00:00";
  $endDate = $endDate." 23:59:59";

  $sql->bindParam(1,$startDate,PDO::PARAM_STR);
  $sql->bindParam(2,$endDate,PDO::PARAM_STR);
  $sql->execute();

  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function getNewMembershipBillingByName($dbh,$name,$currentYear){

  $sql = $dbh->prepare("SELECT id, member_name, email,organization_name,fee_amount,billing_no,bill_date
                        FROM billing_membership
                        WHERE membership_id = '0'
                        AND member_name LIKE '%$name%'
                        AND bill_date BETWEEN '$currentYear-01-01 00:00:00' AND '$currentYear-12-31 23:59:59'
                       ");
  $sql->execute();

  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function getOnlineMembership($dbh){

  $sql = $dbh->prepare("SELECT cc.id as contact_id,cm.id as membership_id, cc.display_name,cc.organization_name,em.email
                        FROM civicrm_membership cm, civicrm_membership_status cs, civicrm_contact cc 
                        LEFT JOIN civicrm_email em
                        ON em.contact_id = cc.id
                        WHERE cm.contact_id = cc.id
                        AND em.is_primary = '1'
                        AND cm.status_id = cs.id
                        AND cs.name = 'Pending'
                        AND cm.id NOT IN(SELECT membership_id FROM billing_membership bm
                                         WHERE bm.membership_id = cm.id) 
                       ");
  $sql->execute();

  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function getOnlineMembershipByName($dbh,$name){

  $sql = $dbh->prepare("SELECT cc.id as contact_id,cm.id as membership_id, cc.display_name,cc.organization_name,em.email
                        FROM civicrm_membership cm, civicrm_membership_status cs, civicrm_contact cc 
                        LEFT JOIN civicrm_email em
                        ON em.contact_id = cc.id
                        WHERE cm.contact_id = cc.id
                        AND em.is_primary = '1'
                        AND cm.status_id = cs.id
                        AND cs.name = 'Pending'
                        AND cc.display_name LIKE '%$name%'
                        AND cm.id NOT IN(SELECT membership_id FROM billing_membership bm
                                         WHERE bm.membership_id = cm.id) 
                    
                       ");
  $sql->execute();

  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function getOnlineMembershipByEmail($dbh,$email){

  $sql = $dbh->prepare("SELECT cc.id as contact_id,cm.id as membership_id, cc.display_name,cc.organization_name,em.email
                        FROM civicrm_membership cm, civicrm_membership_status cs, civicrm_contact cc 
                        LEFT JOIN civicrm_email em
                        ON em.contact_id = cc.id
                        WHERE cm.contact_id = cc.id
                        AND em.is_primary = '1'
                        AND cm.status_id = cs.id
                        AND cs.name = 'Pending'
                        AND em.email LIKE '%$email%'
                        AND cm.id NOT IN(SELECT membership_id FROM billing_membership bm
                                         WHERE bm.membership_id = cm.id) 
                    
                       ");
  $sql->execute();

  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function displayOnlineMembership(array $onlineMembership){

  $html = "<table id='info' style='width:100%'>"
        . "<thead>"
        . "<tr>"
        . "<th>Select contact</th>"
        . "<th>Member Name</th>"
        . "<th>Organization</th>"
        . "<th>Email</th>"
        . "</tr>"
        . "</thead>";
  $html = $html."<tbody>";

  foreach($onlineMembership as $info){
    $membershipId = $info["membership_id"];
    $name = strtolower($info["display_name"]);
    $name = ucwords($name);
    $orgName = $info["organization_name"];
    $email = $info["email"];
    $contactId = $info["contact_id"];

    $html = $html."<tr>"
          . "<td><input type='checkbox' value='$contactId' name='contactIds[]'></td>"
          . "<td>$name</td>"
          . "<td>$orgName</td>"
          . "<td>$email</td>"
          . "</tr>";
  }

  $html = $html."</tbody></table>";

  return $html;
}
?>
