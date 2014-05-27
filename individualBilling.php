<html>
<head>
<title>Billing List</title>
<link rel="stylesheet" type="text/css" href="billingStyle.css">
<link rel="stylesheet" type="text/css" href="menu.css">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
<script>
function reloadPage()
  {
  location.reload();
  }
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#billings').jPaginate({
                'max': 10,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});
$(function() {
    $( "#confirmation" ).dialog({
      resizable: false,
      width:500,
      modal: true,
      buttons: {
        "OK": function() {
          $( this ).dialog( "close" );
        }
      }
    });
  });

</script>
</head>
<body>
<?php

  include 'dbcon.php';
  include 'pdo_conn.php';
  include 'badges_functions.php';
  include 'weberp_functions.php';
  include 'billing_functions.php';
  include 'send_functions.php';
  include 'login_functions.php';
  include 'bir_functions.php';

  $dbh = civicrmConnect();
 
  $logout = logoutDiv($dbh);
  echo $logout;
  echo "<br>";

   @$eventId = $_GET["eventId"];
   @$uid = $_GET["uid"];

   $eventDetails = getEventDetails($dbh,$eventId);
   $eventName = $eventDetails["event_name"];
   $eventStartDate = $eventDetails["start_date"];
   $eventEndDate = $eventDetails["end_date"];
   $eventTypeName = getEventTypeName($dbh,$eventId);
   $locationDetails = getEventLocation($dbh,$eventId);
   $eventLocation = formatEventLocation($locationDetails);
   //navigation
   echo "<div id = 'navigation'>";
   echo "<a href='events2.php?&uid=".$uid."'><b>Event List</b></a>";
   echo "&nbsp;&nbsp;<b>&gt;</b>&nbsp;";
   echo "<i>$eventName</i>";
   echo "</div>";

   echo "<div id='eventDetails'>";
   echo "<table border = '1'>";
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
   echo "</table>";
   echo "</div>";

?>

<?php 

   echo "<div id='billingNav'>";
   echo "<table width='100%'>";
   echo "<tr>";
   echo "<td align='center'><a href='individualBilling.php?eventId=$eventId&billingType=individual&uid=".$uid."'>INDIVIDUAL BILLING</a></td>";
   echo "<td align='center' bgcolor='#084B8A'><a href='companyBilling.php?eventId=$eventId&billingType=company&uid=".$uid."'>COMPANY BILLING</td>";
   echo "</tr>";
   echo "</table></br>"; 

   echo "<form action='' method='POST'>"; 

   $display = "<table id='billings' style='width:100%;'>"
            . "<thead>"
            . "<tr>"
            . "<th><input type='checkbox' id='check'>Participant Name</th>"
            . "<th>Status</th>"
            . "<th>Organization</th>"
            . "<th>Fee</th>"
            . "<th>Subtotal</th>"
            . "<th>12% VAT</th>"
            . "<th>Print Bill</th>"
            . "<th>Amount Paid</th>"
            . "<th>Billing Reference</th>"
            . "<th>Billing Date</th>"
            . "<th>Billing Address</th>"
            . "<tr>"
            . "</thead>"
            . "<tbody>";
   
   $participants = getIndividualParticipantsByEventId($eventId);
   $billedParticipants = getIndividualBilledParticipantsByEventId($eventId);

   foreach($participants as $key => $field){
	$display = $display."<tr>"
                 . "<td><input type='checkbox' class='checkbox' name='ids[]' value='".$field['participant_id']."'>".$field['sort_name']."</td>"
                 . "<td>".$field['status']."</td>"
                 . "<td>".$field['organization_name']."</td>"
                 . "<td>".$field['fee_amount']."</td>";
        if(array_key_exists($field['participant_id'],$billedParticipants)){
            $bill = array();
            $bill = $billedParticipants[$field['participant_id']];
            $display = $display. "<td>".$bill['subtotal']."</td>"
                     . "<td>".$bill['vat']."</td>"
                     . "<td><a href='BIRForm/BIRForm.php?event_id=$eventId&billing_no=".$bill['billing_no']."&uid=$uid' target='_blank'><img src='printer-icon.png' width='50' height='50'></a></td>"
                     . "<td>".number_format($bill['amount_paid'],2)."</td>"
                     . "<td>".$bill['billing_no']."</td>"
                     . "<td>".date("F j, Y",strtotime($bill['bill_date']))."</td>";
         }else{
           $display = $display. "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>";
          }

           $display = $display. "<td>".$field['street_address']." ".$field['city_address']."</td>"
                 . "</tr>";	
   }
   $display = $display."</tbody></table>";
   echo $display;
   echo "</form>";
   echo "</div>";
?>
</body>
<script type="text/javascript">
  $("#check").click(function(){

    if($(this).is(":checked")){
      $("body input[type=checkbox][class=checkbox]").prop("checked",true);
    }else{
      $("body input[type=checkbox][class=checkbox]").prop("checked",false);
    }

  });
</script>
</html>
