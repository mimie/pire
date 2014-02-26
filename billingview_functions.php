<?php

function getAllIndividualBillings($dbh){

  $sql = $dbh->prepare("SELECT bd.participant_id,bd.contact_id,
                        cc.sort_name,event_type,event_name,cp.event_id,
                        cc.organization_name,cps.name as status,
                        bd.fee_amount,bd.billing_no,bill_date,
                        cp.fee_amount as current_amount
                        FROM billing_details bd, civicrm_participant cp, civicrm_participant_status_type cps, civicrm_contact cc
                        WHERE bd.contact_id = cc.id
                        AND cp.id = bd.participant_id
                        AND cp.status_id = cps.id
                        AND billing_type = 'Individual'
                       ");
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;
}

function searchIndividualBillings($dbh,$searchType,$searchText){

  switch($searchType) {

   case 'name':
        $searchQuery = 'AND cc.sort_name LIKE ?';
        break;

   case 'orgname':
        $searchQuery = 'AND cc.organization_name LIKE ?';
        break;

   case 'eventname':
        $searchQuery = 'AND bd.event_name LIKE ?';
        break;

   case 'billingno':
        $searchQuery = 'AND bd.billing_no LIKE ?';
        break;

   case 'eventtype':
        $searchQuery = 'AND bd.event_type LIKE ?';
        break;

  }

  $sql = $dbh->prepare("SELECT bd.participant_id,bd.contact_id,
                        cc.sort_name,event_type,event_name,cp.event_id,
                        cc.organization_name,cps.name as status,
                        bd.fee_amount,bd.billing_no,bill_date,
                        cp.fee_amount as current_amount
                        FROM billing_details bd, civicrm_participant cp, civicrm_participant_status_type cps, civicrm_contact cc
                        WHERE bd.contact_id = cc.id
                        AND cp.id = bd.participant_id
                        AND cp.status_id = cps.id
                        AND billing_type = 'Individual'
                        $searchQuery"
                       );
 $sql->bindValue(1,"%".$searchText."%",PDO::PARAM_STR);
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;
}


function displayIndividualEventBillings(array $billings){

  $html = "<table id='billings' width='100%'>"
        . "<thead>"
        . "<tr><td colspan='12' bgcolor='#2c4f85'><input type='submit' value='Update Billing' name='update'></td></tr>"
        . "<tr>"
        . "<th>Select Participant</th>"
        . "<th>Participant Id</th>"
        . "<th>Participant Name</th>"
        . "<th>Event Type</th>"
        . "<th>Event Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Participant Status</th>"
        . "<th>Fee Amount</th>"
        . "<th>Billing Number</th>"
        . "<th>Billing Date</th>"
        . "<th>Print Bill</th>"
        . "<th>Amount Change</th>"
        . "</tr>"
        . "</thead>";
  $html = $html."<tbody>";

  foreach($billings as $key => $field){
    $participantId = $field["participant_id"];
    $name = $field["sort_name"];
    $name = mb_convert_encoding($name,"UTF-8");
    $eventType = $field["event_type"];
    $eventName = $field["event_name"];
    $orgName = $field["organization_name"];
    $orgName = mb_convert_encoding($orgName,"UTF-8");
    $status = $field["status"];
    $amount = $field["fee_amount"];
    $amount = number_format($amount, 2, '.', '');
    $billingNo = $field["billing_no"];
    $billingDate = $field["bill_date"];
    $amountChange = $field["current_amount"];
    $disabled = $amount == $amountChange ? 'disabled' : '';
    $changes = $amount == $amountChange ? '' : $amountChange;
    $eventId = $field["event_id"];

    $html = $html."<tr>"
          . "<td><input type='checkbox' name='participantIds[]' value='$participantId' $disabled></td>"
          . "<td>$participantId</td>"
          . "<td>$name</td>"
          . "<td>$eventType</td>"
          . "<td>$eventName</td>"
          . "<td>$orgName</td>"
          . "<td>$status</td>"
          . "<td>$amount</td>"
          . "<td>$billingNo</td>"
          . "<td>$billingDate</td>"
          . "<td><a href='individualBillingReference.php?billingRef=$billingNo&eventId=$eventId' target='_blank'><img src='images/printer-icon.png' height='40' width='40'></a></td>"
          . "<td>$changes</td>"
          . "</tr>";

  }
  $html = $html."</tbody></table>";
  
  return $html;

}

function updateChangeIndividualBilling($dbh,$participantId){

  $sqlNewAmount = $dbh->prepare("SELECT bd.event_type,cp.fee_amount FROM civicrm_participant cp,billing_details bd
                                 WHERE cp.id = ?
                                 AND bd.participant_id = cp.id
                                ");
  $sqlNewAmount->bindValue(1,$participantId,PDO::PARAM_INT);
  $sqlNewAmount->execute();

  $result = $sqlNewAmount->fetch(PDO::FETCH_ASSOC);
  $newAmount = $result["fee_amount"];
  $eventType = $result["event_type"];

  if($eventType == 'CON' && $eventType == 'MBA'){
    $taxQuery = '';
  }

   else{
     $tax = $newAmount/9.3333;
     $tax = number_format($tax, 2, '.', '');
     $subtotal = $newAmount - $tax;
     $taxQuery = ", subtotal=".$subtotal.",vat="."$tax";

   }

  $sqlUpdate = $dbh->prepare("UPDATE billing_details SET fee_amount = ? $taxQuery
                              WHERE participant_id = ?
                             ");
  $sqlUpdate->bindValue(1,$newAmount,PDO::PARAM_INT);
  $sqlUpdate->bindValue(2,$participantId,PDO::PARAM_INT);
  $sqlUpdate->execute();

}

function getAllCompanyBillings($dbh){

  $sql = $dbh->prepare("SELECT bc.org_contact_id, bc.event_id,ce.title as event_name,cc.organization_name,bc.billing_no,bc.total_amount,bc.subtotal,bc.vat,bc.bill_date
                        FROM billing_company bc,civicrm_contact cc, civicrm_event ce
                        WHERE bc.event_id = ce.id
                        AND bc.org_contact_id = cc.id
                        AND cc.is_deleted = '0'
                        AND bc.total_amount != '0'
                        ORDER BY cc.organization_name
                       ");
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function searchCompanyBillings($dbh,$searchType,$searchText){

  switch($searchType){

    case 'name' :
         $searchQuery = 'AND cc.organization_name LIKE ?';
         break;
    case 'eventname':
         $searchQuery = 'AND ce.title LIKE ?';
         break;
    case 'billingno':
         $searchQuery = 'AND bc.billing_no LIKE ?';
         break;
   
  }

  $sql = $dbh->prepare("SELECT bc.org_contact_id, bc.event_id,ce.title as event_name,cc.organization_name,bc.billing_no,bc.total_amount,bc.subtotal,bc.vat,bc.bill_date
                        FROM billing_company bc,civicrm_contact cc, civicrm_event ce
                        WHERE bc.event_id = ce.id
                        AND bc.org_contact_id = cc.id
                        AND cc.is_deleted = '0'
                        AND bc.total_amount != '0'
                        $searchQuery
                        ORDER BY cc.organization_name
                       ");
  $sql->bindValue(1,"%".$searchText."%",PDO::PARAM_STR);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

function displayCompanyBillings(array $billings){
    
  $html = "<table id = 'billings' width='100%'>"
        . "<thead>"
        . "<th>Organization Name</th>"
        . "<th>Event Name</th>"
        . "<th>Billing No</th>"
        . "<th>Total Amount</th>"
        . "<th>Subtotal</th>"
        . "<th>VAT</th>"
        . "<th>Billing Date</th>"
        . "<th>Print Bill</th>"
        . "<th>Billed Participants</th>"
        . "</thead>";

   $html = $html."<tbody>";

   foreach($billings as $key => $field){

     $orgName = $field["organization_name"];
     $eventName = $field["event_name"];
     $billingNo = $field["billing_no"];
     $total = $field["total_amount"];
     $subtotal = $field["subtotal"];
     $vat = $field["vat"];
     $billingDate = $field["bill_date"];

     $html = $html."<tr>"
           . "<td>$orgName</td>"
           . "<td>$eventName</td>"
           . "<td>$billingNo</td>"
           . "<td>$total</td>"
           . "<td>$subtotal</td>"
           . "<td>$vat</td>"
           . "<td>$billingDate</td>"
           . "<td></td>"
           . "<td></td>"
           . "<tr>";
   }

  $html = $html."</tbody></table>";

  return $html;
}
?>
