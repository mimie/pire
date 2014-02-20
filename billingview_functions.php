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

  $sqlNewAmount = $dbh->prepare("SELECT fee_amount FROM civicrm_participant
                                 WHERE id = ?
                                ");
  $sqlNewAmount->bindValue(1,$participantId,PDO::PARAM_INT);
  $sqlNewAmount->execute();

  $result = $sqlNewAmount->fetch(PDO::FETCH_ASSOC);
  $newAmount = $result["fee_amount"];

  $sqlUpdate = $dbh->prepare("UPDATE billing_details SET fee_amount = ?
                              WHERE participant_id = ?
                             ");
  $sqlUpdate->bindValue(1,$newAmount,PDO::PARAM_INT);
  $sqlUpdate->bindValue(2,$participantId,PDO::PARAM_INT);
  $sqlUpdate->execute();

}
?>
