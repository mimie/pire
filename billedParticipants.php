<html>
<head>
<title>Billed Participants</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="billingStyle.css">
<link rel="stylesheet" type="text/css" href="menu.css">
<style type="text/css">
   body{overflow:scroll;}
</style>
<script type='text/javascript'>

function reloadPage(){
   location.reload();
}

</script>
</head>
<body>
<?php

  include 'billing_functions.php';
  include 'login_functions.php';
  include 'pdo_conn.php';
  include 'company_functions.php';
  include 'billingview_functions.php';
  $dbh = civicrmConnect();
 
  /**session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }**/
 
  @$userId = $_GET["user"]; 
  echo "<div id='eventDetails'>";
  $logout = logoutDiv($dbh,$userId);
  echo $logout;

  @$billingNo = $_GET["billingNo"];
  @$eventId = $_GET["eventId"];
  @$orgId = $_GET["orgId"];

   $eventDetails = getEventDetails($dbh,$eventId);
   $eventName = $eventDetails["event_name"];
   $eventStartDate = $eventDetails["start_date"];
   $eventEndDate = $eventDetails["end_date"];
   $eventTypeName = getEventTypeName($dbh,$eventId);
   $locationDetails = getEventLocation($dbh,$eventId);
   $eventLocation = formatEventLocation($locationDetails);

   $billingDetails = getCompanyBillingDetails($dbh,$billingNo,$eventId);
   $orgName = $billingDetails["organization_name"];
   $amount = $billingDetails["total_amount"];
   $amount = number_format($amount,2); 
   $subtotal = $billingDetails["subtotal"];
   $subtotal = number_format($subtotal,2);
   $vat = $billingDetails["vat"];
   $vat = number_format($vat,2);
   $billDate = $billingDetails["bill_date"];
   $billDate = date("F j Y",strtotime($billDate));

   echo "<table border = '1' width='100%'>";
   echo "<tr>";
   echo "<th>Event Name</th><td><b><i>$eventName</i></b></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Start Date</th><td><i>$eventStartDate</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>End Date</th><td><i>$eventEndDate</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Event Type</th><td><i>$eventTypeName</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Event Location</th><td><i>$eventLocation</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Organization Name</th><td><i>$orgName</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Billing No.</th><td><i>$billingNo</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Billing Date</th><td><i>$billDate</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Total Amount</th><td><i>$amount&nbsp;PHP</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Subtotal</th><td><i>$subtotal&nbsp;PHP</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>VAT</th><td><i>$vat&nbsp;PHP</i></td>";
   echo "</tr>";
   echo "</table><br><br>";

   echo "<form action='' method='POST'>";

   $billedParticipants = getCompanyBilledParticipants($dbh,$billingNo,$eventId);
   $display = displayBilledParticipants($billedParticipants);
   echo $display;

   $participants = getNewlyAddedBillings($dbh,$eventId,$orgId);
   $countNewParticipants = count($participants);
?>
  <br><br>

  <?php if($countNewParticipants){ ?>
  <table width='100%'>
   <tr>
   <td bgcolor='#2c4f85' colspan='5'><input type="submit" value="Add Missing Billings" name="add"></td>
   </tr>
   <tr>
    <th colspan='5'>Billings To Be Added</th>
   </tr>
   <tr>
    <th>Select Participant</th>
    <th>Participant Id</th>
    <th>Participant Name</th>
    <th>Email</th>
    <th>Fee Amount</th>
   </tr>
<?php

  foreach($participants as $details){

   $participantId = $details["participant_id"];
   $name = $details["name"];
   $contactId = $details["contact_id"];
   $feeAmount = $details["fee_amount"];
   $email = getContactEmail($dbh,$contactId);

   echo "<tr>";
   echo "<td><input type='checkbox' name='participantIds[]' value='$participantId'></td>";
   echo "<td>$participantId</td>";
   echo "<td>$name</td>";
   echo "<td>$email</td>";
   echo "<td>$feeAmount</td>";
   echo "</tr>";
   
  }

?>
  </table>
  <?php }//end if there is existing new participants ?>
  </form>
  </div>
<?php
 if(isset($_POST["add"])){

   $addedAmount = 0;
   $ids = $_POST["participantIds"];

    foreach($ids as $participantId){
      $info = getDetailsForParticipant($dbh,$participantId);

      checkParticipantBillGenerated($dbh,$participantId,$eventId);
     
      $sql = $dbh->prepare("INSERT INTO billing_details (participant_id, contact_id, event_id, 
                            event_type, event_name, participant_name, email,bill_address, 
                            organization_name, org_contact_id, billing_type,fee_amount, 
                            billing_no,participant_status)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
     $participantId = $info["participant_id"];
     $contactId = $info["contact_id"];
     $eventId = $info["event_id"];
     $eventType = $info["event_type"];
     $eventName = $info["event_name"];
     $name = $info["participant_name"];
     $email = $info["email"];
     $billAddress = getContactAddress($dbh,$contactId);
     $orgName = $info["organization_name"];
     $billingType = 'Company';
     $feeAmount = $info["fee_amount"];
     $status = $info["participant_status"];

     $sql->bindValue(1,$participantId,PDO::PARAM_INT);
     $sql->bindValue(2,$contactId,PDO::PARAM_INT);
     $sql->bindValue(3,$eventId,PDO::PARAM_INT);
     $sql->bindValue(4,$eventType,PDO::PARAM_STR);
     $sql->bindValue(5,$eventName,PDO::PARAM_STR);
     $sql->bindValue(6,$name,PDO::PARAM_STR);
     $sql->bindValue(7,$email,PDO::PARAM_STR);
     $sql->bindValue(8,$billAddress,PDO::PARAM_STR);
     $sql->bindValue(9,$orgName,PDO::PARAM_STR);
     $sql->bindValue(10,$orgId,PDO::PARAM_INT);
     $sql->bindValue(11,$billingType,PDO::PARAM_STR);
     $sql->bindValue(12,$feeAmount,PDO::PARAM_INT);
     $sql->bindValue(13,$billingNo,PDO::PARAM_STR);
     $sql->bindValue(14,$status,PDO::PARAM_STR);

     $sql->execute();
     $addedAmount = $addedAmount + $feeAmount;

    }

    updateAddedAmount($dbh,$billingNo,$addedAmount);
    
    header("Location:billedParticipants.php?eventId=$eventId&billingNo=$billingNo&orgId=$orgId");
 }

 elseif(isset($_POST["update"])){
  $ids = $_POST["participant_ids"];
  foreach($ids as $participantId){
    updateChangeIndividualBilling($dbh,$participantId);    
  }

  updateCompanyTotalAmount($dbh,$billingNo);

  echo "<script type='text/javascript'>";
  echo "reloadPage()";
  echo "</script>";
 }
?>
</body>
</html>
