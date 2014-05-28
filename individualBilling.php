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
<script type='text/javascript' language='javascript'>
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
          //$( this ).dialog( "close" );
          reloadPage();
        }
      }
    });
});

function isNumeric(elem, helperMsg){
	var numericExpression = /^[0-9]+$/;
	if(elem.value.match(numericExpression)){
		return true;
	}else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}


function isCheck(elem, helperMsg){
	var length = 0;
	for(var i=0;i<elem.length;i++){
           length = elem[i].checked ? length + 1 : length;
        }
        
        if(length == 0){
          alert(helperMsg);
          return false;
        }else{
          return true;
         } 
}

function validator(){

	var checkbox = document.getElementsByName('ids[]');
        var bs_no = document.getElementById('bs_no');

        if(isNumeric(bs_no,"Please enter a valid number for BS No. field.")){
           if(isCheck(checkbox,"Please select a participant name.")){
             return true;
           }
        }

        return false;
}

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
  include 'notes/notes_functions.php';

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
   echo "<div id='billingNav'>";
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

   echo "<table width='100%'>";
   echo "<tr>";
   echo "<td align='center'><a href='individualBilling.php?eventId=$eventId&billingType=individual&uid=".$uid."'>INDIVIDUAL BILLING</a></td>";
   echo "<td align='center' bgcolor='#084B8A'><a href='companyBilling.php?eventId=$eventId&billingType=company&uid=".$uid."'>COMPANY BILLING</td>";
   echo "</tr>";
   echo "</table></br>"; 

   echo "<form action='' method='POST' onsubmit=\"return validator()\">"; 

   $display = "<table id='billings' style='width:100%;'>"
            . "<thead>"
            . "<tr>"
            . "<td colspan='12'>Account Receivable Type : <input type='radio' name='vat' value='1' checked='checked'>VATABLE <input type='radio' name='vat' value='0'>NON-VATABLE"
            . "</br>BS. No. : <input type='text' id='bs_no' name='bs_no' placeholder='Enter BS No. start number...' required>";
    $notes_opt = getNotesByCategory("Individual Event Billing");
    $display = $display."<SELECT name='notes'><option value='select'>- Select optional billing notes -</option><option>-----------------</option>";
    foreach($notes_opt as $key=>$field){
        $id = $field["notes_id"];
        $notes = $field["notes"];
    	$display = $display."<option value='$id'>$notes</option>";
    }

    $display = $display."</SELECT><input type='submit' name='generate' value='GENERATE BILL'></td>"
             . "</tr>"
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
             . "<th>BS No.</th>"
             . "<th>Billing Date</th>"
             . "<th>Billing Address</th>"
             . "<tr>"
             . "</thead>"
             . "<tbody>";
   
   $participants = getIndividualParticipantsByEventId($eventId);
   $billedParticipants = getIndividualBilledParticipantsByEventId($eventId);

   foreach($participants as $key => $field){
        $bill = array();
        $bill = $billedParticipants[$field['participant_id']];

        $checkbox = (array_key_exists($field['participant_id'],$billedParticipants) && $bill['post_bill'] == 1) || (array_key_exists($field['participant_id'],$billedParticipants) && $bill['generated_bill'] == 1) || $field['fee_amount'] == 0 ? "" : "class='checkbox'";
        $disabled = (array_key_exists($field['participant_id'],$billedParticipants) && $bill['post_bill'] == 1) || (array_key_exists($field['participant_id'],$billedParticipants) && $bill['generated_bill'] == 1) || $field['fee_amount'] == 0 ? 'disabled' : '';

        //status = 4 = Cancelled - Strike the column if the participant status is cancelled.
        $strike = $field['status_id'] == 4 || $field['status_id'] == 7 || $field['status_id'] == 15 ? '<strike>' : '';
        $endstrike = $field['status_id'] == 4 || $field['status_id'] == 7 || $field['status_id'] == 15 ? '</strike>' : '';

	$display = $display."<tr>"
                 . "<td>$strike<input type='checkbox' $checkbox $disabled name='ids[]' value='".$field['participant_id']."'>".$field['sort_name']."$endstrike</td>"
                 . "<td>$strike".$field['status']."$endstrike</td>"
                 . "<td>$strike".$field['organization_name']."$endstrike</td>"
                 . "<td>$strike".$field['fee_amount']."$endstrike</td>";

        if(array_key_exists($field['participant_id'],$billedParticipants)){
            $display = $display. "<td>$strike".$bill['subtotal']."$endstrike</td>"
                     . "<td>$strike".$bill['vat']."$endstrike</td>"
                     . "<td><a href='BIRForm/BIRForm.php?event_id=$eventId&billing_no=".$bill['billing_no']."&uid=$uid' target='_blank'><img src='printer-icon.png' width='50' height='50'></a></td>"
                     . "<td>$strike".number_format($bill['amount_paid'],2)."$endstrike</td>"
                     . "<td>$strike".$bill['billing_no']."$endstrike</td>"
                     . "<td>$strike".$bill['bir_no']."$endstrike</td>"
                     . "<td>$strike".date("F j, Y",strtotime($bill['bill_date']))."$endstrike</td>";
         }else{
           $display = $display. "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>";
          }

           $display = $display. "<td>$strike".$field['street_address']." ".$field['city_address']."$endstrike</td>"
                 . "</tr>";	
   }
   $display = $display."</tbody></table>";
   echo $display;
   echo "</form>";
   echo "</div>";

   if($_POST["generate"]){
     $participantIds = $_POST["ids"];
     $bs_no = $_POST["bs_no"];
     $is_vatable = $_POST["vat"];
     $note_id = $_POST["notes"];
     
     foreach($participantIds as $id){
        $bir_no = formatBSNo($bs_no);
     	generateIndividualBill($id,$bir_no,$is_vatable,$note_id);
        $bs_no++;
     }
     
     echo "<div id='confirmation'>Successfully generated bill.</div>";
   }
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
