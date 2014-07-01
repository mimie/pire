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
function reloadPage(){
  //location.reload();
  window.location=window.location;
}
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#billings').jPaginate({
                'max': 5,
                'page': 1,
                'links': 'buttons'
        });
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
           if(isCheck(checkbox,"Please select a company name.")){
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
  include 'company_functions.php';
  include 'notes/notes_functions.php';
  include 'bir_functions.php';

   $dbh = civicrmConnect();
 
   @$uid = $_GET["uid"];
  
   $logout = logoutDiv($dbh,$userId);
   echo $logout;
   echo "<br>";
   @$eventId = $_GET["eventId"];

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

   $comp_names = getCompanyNames();
?>

<?php  

   echo "<table width='100%'>";
   echo "<tr>";
   echo "<td align='center' bgcolor='#084B8A'><a href='individualBilling.php?eventId=$eventId&billingType=individual&uid=".$uid."'>INDIVIDUAL BILLING</a></td>";
   echo "<td align='center'><a href='companyBilling.php?eventId=$eventId&billingType=company&uid=".$uid."'>COMPANY BILLING</td>";
   echo "</tr>";
   echo "</table><br>";

   echo "<form action='' method='POST' onsubmit=\"return validator()\">";

   $display = "<table width='100%' id='billings'>" 
            . "<thead>"
            . "<tr>"
            . "<td colspan='13'>"
            . "Account Receivable Type: <input type='radio' name='vat' value='vatable' checked='checked'>VATABLE  "
            . "<input type='radio' name='vat' value='vat_exempt'>VAT-EXEMPT "
            . "<input type='radio' name='vat' value='vat_zero'>VAT-ZERO "
            . "</br>BS. No. : <input type='text' id='bs_no' name='bs_no' placeholder='Enter BS No. start number...' required>";

    $notes_opt = getNotesByCategory("Company Event Billing");
    $notes_collection = array();
    $display = $display."<SELECT name='notes'><option value='select'>- Select optional billing notes -</option><option>-----------------</option>";
    foreach($notes_opt as $key=>$field){
    	$id = $field["notes_id"];
    	$notes = $field["notes"];
    	$display = $display."<option value='$id'>$notes</option>";
   	 //stores notes in an array for reference display of notes in the table
    	$notes_collection[$id] = $notes;
    }
            

   $display = $display. "</SELECT><input type='submit' name='generate' value='GENERATE BILL'></td>"
            . "</tr>"
            . "<tr>"
            . "<th><input type='checkbox' id='check'>Organization Name</th>"
            . "<th>Total Fee</th>"
            . "<th>Civicrm Amount</th>"
            . "<th>Subtotal</th>"
            . "<th>12% VAT</th>"
            . "<th>Print Bill</th>"
            . "<th>Amount Paid</th>"
            . "<th>Billing Reference</th>"
            . "<th>BS No.</th>"
            . "<th>Billing Date</th>"
            . "<th>Notes</th>"
            . "<th>Edit</th>"
            . "</tr>"
            . "</thead><tbody>";

   $comp_participants = getCompanyParticipantsByEventId($eventId);
   $totals = array();

   foreach($comp_participants as $orgId => $participants){
        $total_fee = 0.0;
	foreach($participants as $key=>$field){
		$total_fee = $total_fee + $field['fee_amount'];
        }

        $bill_info = checkCompanyBillGenerated($orgId,$eventId);
        $orgName = $comp_names[$orgId];
        $total_per_org = $total_fee;
        $total_fee = number_format($total_fee,2);

        if($bill_info){

            foreach($bill_info as $key=>$field){

                //bill_total = generated bill total
                //total_fee = actual civicrm amount
                $bill_total = number_format($field['total_amount'],2);
                $color = $bill_total != $total_fee || $bill_total == 0.00 || $total_fee == 0.00 ? "red" : "";
                $notes_id = $field['notes_id'];
                $notes = $notes_collection[$notes_id];
                $billing_no = $field['billing_no'];
                $bir_no = $field['bir_no'];

                $bill_date = date("F j, Y",strtotime($field['bill_date']));
                $img_link = "<a href='edit_company.php?eventId=$eventId&orgId=$orgId&bir_no=$bir_no&uid=$uid' onclick=\"window.open(this.href,'edit_company.php?eventId=$eventId&orgId=$orgId&uid=$uid','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=900,height=900');return false;\"><img src='images/edit_bill.png'></a>";
                
		$display = $display."<tr>"
			 . "<td><input type='checkbox' name='ids[]' value='$orgId' disabled>".$orgName."</td>"
			 . "<td><font color='$color'>$bill_total</font></td>"
			 . "<td><font color='$color'>$total_fee</font></td>"
			 . "<td><font color='$color'>".number_format($field['subtotal'],2)."</font></td>"
			 . "<td><font color='$color'>".number_format($field['vat'],2)."</font></td>"
			 . "<td><a href='#'><a href='BIRForm/print_company.php?orgId=$orgId&event_id=$event_id&bir_no=$bir_no&billing_no=$billing_no&uid=$uid'><img src='images/preview.png' width='30' height='30'></a>"
                         . "<a href='#'><img src='printer-icon.png' width='30' height='30'></a></td>"
			 . "<td>".number_format($field['amount_paid'],2)."</td>"
			 . "<td>$billing_no</td>"
			 . "<td>".$bir_no."</td>"
			 . "<td>".$bill_date."</td>"
			 . "<td>".$notes."</td>"
			 . "<td>".$img_link."</td>"
			 . "</tr>"; 
           }
        }else{

                $disabled = $total_fee == 0.00 || $orgName == NULL ? 'disabled' : '';
                $class = $total_fee == 0.00 || $orgName == NULL ? '' : 'checkbox';
                $color = $total_fee == 0.00 ? 'red' : '';

		$display = $display."<tr>"
			 . "<td><input type='checkbox' name='ids[]' value='$orgId' $disabled class='$class'>".$orgName."</td>"
			 . "<td></td>"
			 . "<td><font color='$color'>".$total_fee."</font></td>"
			 . "<td></td>"
			 . "<td></td>"
			 . "<td></td>"
			 . "<td></td>"
			 . "<td></td>"
			 . "<td></td>"
			 . "<td></td>"
			 . "<td></td>"
			 . "<td></td>"
			 . "</tr>"; 
      	}
        $totals[$orgId] = number_format($total_per_org,2, '.', '');;


  }

   $display = $display."</tbody></table>";
   echo $display;
   echo "</form>";
   echo "</div>";

   if($_POST['generate']){
	$orgIds = $_POST['ids'];
        $note_id = $_POST['notes'];
        $vatable = $_POST['vat'] == 'vatable' ? 1 : 0;
        $nonvatable_type = $_POST['vat'] == 'vatable' ? '' : $_POST['vat'];
        $bs_no = $_POST['bs_no'];
        
        foreach($orgIds as $id){
                $participants = $comp_participants[$id];
           
                $bir_no = formatBSNo($bs_no);
		$max_stmt = civicrmDB("SELECT MAX(cbid) as max_id FROM billing_company");
		$max_stmt->execute();
		$billing_id = formatBillingNo($max_stmt->fetchColumn(0) + 1);
		$current_year = date("y");
		$billing_no = $eventTypeName."-".$current_year."-".$billing_id;
                $subtotal = $vatable == 1 ? $totals[$id]/1.12 : $totals[$id];
                $vat = $totals[$id] - $subtotal;
                $subtotal = number_format($subtotal, 2, '.', '');
                $vat = number_format($vat, 2, '.', ''); 
                $billing_information = array('event_id' => $eventId,
                                             'event_type' => $eventTypeName,
                                             'event_name' => $eventName,
                                             'org_id' => $id,
                                             'org_name' => $comp_names[$id],
                                             'address' => getCompleteCompanyAddress($dbh,$id),
                                             'billing_no' => $billing_no,
                                             'total_amount' => $totals[$id],
                                             'subtotal' => $subtotal,
                                             'vat' => $vat,
                                             'bir_no' => $bir_no, 
                                             'notes_id' => $note_id,
                                             'generator_uid' => $uid,
                                             'nonvatable_type' => $nonvatable_type);
                generateCompanyBill($billing_information);

                foreach($participants as $key=>$field){
                        $participant_id = $field['participant_id'];
                        $details = array('bs_no' => $bir_no,
                                         'vatable' => $vatable,
                                         'notes_id' => $note_id,
                                         'nonvatable_type' => $nonvatable_type,
                                         'billing_type' => 'Company',
                                         'billing_id' => $billing_no
                                          );
                	generateIndividualBill($participant_id,$details);
                }
                $bs_no++;
        }
        
        echo "<div id='confirmation'><img src='images/confirm.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Successfully generated company bill.</div>";

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
