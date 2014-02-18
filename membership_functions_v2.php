<?php

function getMembershipByNameAndDate($dbh,$name,$endDate){

  $sql = $dbh->prepare("SELECT cc.sort_name, cc.organization_name, cm.contact_id, cmt.name AS membership_type, cmt.minimum_fee as fee_amount,cm.id AS membership_id, 
                        bm.billing_no,bm.id as billing_id,bm.bill_date, cm.end_date, bm.year AS membership_year
                        FROM civicrm_contact cc, civicrm_membership cm
                        LEFT JOIN billing_membership bm ON bm.membership_id = cm.id
                        LEFT JOIN civicrm_membership_type cmt ON cm.membership_type_id = cmt.id
                        AND cmt.name =  'General'
                        WHERE cc.id = cm.contact_id
                        AND display_name !=  'Admin Mister'
                        AND cc.is_deleted =  '0'
                        AND cm.end_date LIKE ?
                        AND sort_name LIKE ?
                        ORDER BY sort_name");
  $sql->bindValue(1,"%".$endDate."%",PDO::PARAM_STR);
  $sql->bindValue(2,"%".$name."%",PDO::PARAM_STR);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;

}

function displayMembershipDetails(array $membership){

  $html = "<table id='member' width='100%'>"
        . "<thead>"
        . "<tr>"
        . "<th><input type='checkbox' id='check'>Select contact</th>"
        . "<th>Member Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Membership Type</th>"
        . "<th>Fee Amount</th>"
        . "<th>Billing Number</th>"
        . "<th>Billing Date</th>"
        . "<th>End Date</th>"
        . "<th>Membership Year</th>"
        . "<th>Print Bill</th>"
        . "</tr>"
        . "</thead>";

  $html = $html."<tbody>";

  $currentYear = date("Y");
  $currentExpiredDate = $currentYear."-12-31";

  foreach($membership as $key => $field){
    $membershipId = $field["membership_id"];
    $name = $field["sort_name"];
    $orgName = $field["organization_name"];
    $type = $field["membership_type"];
    $billingNo = $field["billing_no"];
    $billDate = $field["bill_date"];
    $endDate = $field["end_date"];
    $membershipYear = $field["membership_year"];
    $billingId = $field["billing_id"];
    $amount = $field["fee_amount"];

    if($endDate == $currentExpiredDate){
      $checkbox = 'class=checkbox';
      $disabled = '';
    }

    else{
      $checkbox = $billingNo != NULL ? '' : 'class=checkbox';
      $disabled = $billingNo != NULL ? 'disabled' : '';
    }
    $printBill = $billingNo != NULL ? "<a href='memberBillingReference.php?billingId=$billingId'><img src='images/printer-icon.png' width='30' height='30'</a>" : 'Not Available';

    $html = $html."<tr>"
          . "<td><input type='checkbox' name='membershipIds[]' value='$membershipId' $disabled $checkbox></td>"
          . "<td>$name</td>"
          . "<td>$orgName</td>"
          . "<td>$type</td>"
          . "<td>$amount</td>"
          . "<td>$billingNo</td>"
          . "<td>$billDate</td>"
          . "<td>$endDate</td>"
          . "<td>$membershipYear</td>"
          . "<td>$printBill</td>"
          . "</tr>";
    
  }
  $html = $html."</tbody></table>";

  return $html;

}

function getMembershipBillingData($dbh,$membershipId){

   $sql = $dbh->prepare("SELECT cm.id AS membership_id, cmt.name AS membership_type, cmt.minimum_fee AS fee_amount, 
                         cc.id AS contact_id, cc.display_name, cc.employer_id, cc.organization_name, ce.email
                         FROM civicrm_membership cm, civicrm_membership_type cmt, civicrm_contact cc
                         LEFT JOIN civicrm_email ce ON cc.id = ce.contact_id
                         AND is_primary =  '1'
                         WHERE cm.id =  ?
                         AND cc.id = cm.contact_id
                         AND cm.membership_type_id = cmt.id");
  $sql->bindValue(1,$membershipId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);

  return $result;
}

?>
