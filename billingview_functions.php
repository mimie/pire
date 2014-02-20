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
                       ");
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;
}

function displayIndividualEventBillings(array $billings){

  $html = "<table id='billings' width='100%'>"
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
        . "<th>Amount Change</th>";
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
    $billingNo = $field["billing_no"];
    $billingDate = $field["bill_date"];
    $amountChange = $field["current_amount"];
    $amountChange = $amount == $amountChange ? '' : $amountChange;
    $eventId = $field["event_id"];

    $html = $html."<tr>"
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
          . "<td>$amountChange</td>"
          . "</tr>";

  }
  $html = $html."</tbody></table>";
  
  return $html;

}
?>
