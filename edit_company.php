<html>
<head>
<title>Edit Company</title>
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
    //location.reload();
    window.location=window.location;
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
$(function() {
    $( "#error" ).dialog({
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
	include 'billing_functions.php';
	include 'login_functions.php';
	include 'pdo_conn.php';
	include 'company_functions.php';
	include 'billingview_functions.php';
	include 'editbill_functions.php';
	include 'notes/notes_functions.php';
        include 'bir_functions.php';

	$dbh = civicrmConnect();

	@$uid = $_GET["uid"];
	@$eventId = $_GET["eventId"];
	@$orgId = $_GET["orgId"];

  	//Event Information
        $eventDetails = getEventDetails($dbh,$eventId);
	$eventName = $eventDetails["event_name"];
	$eventStartDate = $eventDetails["start_date"];
	$eventEndDate = $eventDetails["end_date"];
	$eventTypeName = getEventTypeName($dbh,$eventId);
	$locationDetails = getEventLocation($dbh,$eventId);
	$eventLocation = formatEventLocation($locationDetails);
  	$orgName = getCompanyNameByOrgId($orgId);

	//Current Bill Information
	$currentbill = getCurrentCompanyBillByEvent($orgId,$eventId);
  	$billing_no = $currentbill['billing_no'];
  	$bir_no = $currentbill['bir_no'];
	$total_amount = number_format($currentbill['total_amount'],'2','.','');
	$subtotal = number_format($currentbill['subtotal'],'2','.','');
	$vat = number_format($currentbill['vat'],'2','.','');
	$billing_date = $currentbill['bill_date'];
	$notes = $currentbill['notes'];
	$old_notes_id = $currentbill['notes_id'];
	$is_edit = $currentbill['edit_bill'];
	$nonvatable_type = $currentbill['nonvatable_type'];
	$is_vatable = $nonvatable_type == NULL ? "checked='checked'" : '';
	$is_exempt = $nonvatable_type == 'vat_exempt' ? "checked='checked'" : '';
	$is_zero = $nonvatable_type == 'vat_zero' ? "checked='checked'" : '';

  //Billed Participants
  $participants = getCompanyBilledParticipants($dbh,$billing_no,$eventId);

  //New Participants
  $new_participants = getNewlyAddedBillings($dbh,$eventId,$orgId);

  //notes
  $notes_opt = getNotesByCategory("Company Event Billing");
?>
<div id='eventDetails'>
 <table border='1' width='100%'>
        <tr>
        	<th colspan='2'>EVENT INFORMATION</th>
        </tr>
	<tr>
        	<th>Event Name</th><td><b><i><?=$eventName?></i></b></td>
	</tr>
	<tr>
		<th>Start Date</th><td><i><?=$eventStartDate?></i></td>
	</tr>
	<tr>
		<th>End Date</th><td><i><?=$eventEndDate?></i></td>
	</tr>
	<tr>
		<th>Event Type</th><td><i><?=$eventTypeName?></i></td>
	</tr>
	<tr>
		<th>Event Location</th><td><i><?=$eventLocation?></i></td>
	</tr>
	<tr>
		<th>Organization Name</th><td><i><?=$orgName?></i></td>
	</tr>
</table><br></br>

<table border='1' width='100%'>
        <tr>
        	<th colspan='2'>BILLING INFORMATION</th>
        </tr>
	<tr>
        	<th>Reference No.</th><td><b><i><?=$billing_no?></i></b></td>
	</tr>
	<tr>
        	<th>BS No.</th><td><b><i>BS-<?=$bir_no?></i></b></td>
	</tr>
	<tr>
        	<th>Billing Date</th><td><b><i><?=date("F j, Y",strtotime($billing_date))?></i></b></td>
	</tr>
	<tr>
        	<th>Total</th><td><b><i><?=$total_amount?></i></b></td>
	</tr>
	<tr>
        	<th>Subtotal</th><td><b><i><?=$subtotal?></i></b></td>
	</tr>
	<tr>
        	<th>VAT</th><td><b><i><?=$vat?></i></b></td>
	</tr>
	<tr>
        	<th>Notes</th><td><b><i><?=$notes?></i></b></td>
	</tr>
</table><br/><br/>

<form action='' method='POST'>

<table border='1' width='100%'>
        <tr>
        	<th colspan='7'>LIST OF BILLED PARTICIPANTS</th>
        </tr>
        <tr>
		<th>Select Participant Id</th>
		<th>Participant Name</th>
		<th>Email</th>
		<th>Fee Amount</th>
                <th>Civicrm Amount</th>
                <th>Status</th>
        </tr>
<?php
        //billed participants
	foreach($participants as $participant_id=>$field){
                $name = $field['participant_name'];
                $email = $field['email'];
                $status = $field['status'];
                $fee_amount = number_format($field['fee_amount'],'2','.','');
                $civicrm_amount = $status == 'Cancelled' ? '0.00' : number_format($field['civicrm_amount'],'2','.','');
                $color = $fee_amount != $civicrm_amount ? 'red' : '';
                $disabled = $fee_amount == $civicrm_amount ? 'disabled' : '';

?>
	<tr>
        	<td><input type='checkbox' name='ids[]' value='<?=$participant_id?>' <?=$disabled?>><?=$participant_id?></td>
        	<td><?=$name?></td>
        	<td><?=$email?></td>
        	<td><font color='<?=$color?>'><?=$fee_amount?></font></td>
        	<td><font color='<?=$color?>'><?=$civicrm_amount?></font></td>
          <td><?=$status?></td>
        </tr>

<?php
	}

        if($new_participants){
?>
        <tr>
        	<th colspan='6'>NEW PARTICIPANTS</th>
        </tr>
	<tr>
        	<th>Select Participant Id</th>
        	<th>Participant Name</th>
        	<th>Email</th>
        	<th colspan='2'>Civicrm Amount</th>
                <th>Status</th>
        </tr>
<?php
	//new participants
		foreach($new_participants as $participant_id=>$field){
			$name = $field['name'];
			$contact_id = $field['contact_id'];
			$fee_amount = $field['fee_amount'];
			$email = getContactEmail($dbh,$contact_id);
			$status = $field['status'];
?>
		<tr>
			<td><input type='checkbox' name='ids[]' value='<?=$participant_id?>'><?=$participant_id?></td>
			<td><?=$name?></td>
			<td><?=$email?></td>
			<td colspan='2'><?=$fee_amount?></td>
			<td><?=$status?></td>
		</tr>
<?php
		}
	}

        //conditions for edit
	if($is_edit == 1){


?>
		<tr>
	           <td colspan='6'>Account Receivable Type:
	             <input type='radio' name='vat' value='vatable' <?=$is_vatable?>>VATABLE
	             <input type='radio' name='vat' value='vat_exempt' <?=$is_exempt?>>VAT-EXEMPT
	             <input type='radio' name='vat' value='vat_zero' <?=$is_zero?>>VAT-ZERO</br>
                     <SELECT name='notes_id'><option value='select'>- Select optional billing notes -</option><option>-----------------</option>
<?php
                $options = '';
		foreach($notes_opt as $key=>$field){
    			$id = $field["notes_id"];
    			$notes = $field["notes"];
          $selected = $old_notes_id == $id ? 'selected' : '';
    			$options = $options."<option value='$id' $selected>$notes</option>";
          echo $options;
    		}

          echo "</SELECT><input type='submit' name='update' value='UPDATE BILLING'></td>";
          echo "</tr>";
          $update_action = 'update amount';
?>

<?php
        echo "<tr>";
        }else{
?>
          <td colspan='13'>Account Receivable Type:
                <input type='radio' name='vat' value='vatable' <?=$is_vatable?>>VATABLE
                <input type='radio' name='vat' value='vat_exempt' <?=$is_exempt?>>VAT-EXEMPT
                <input type='radio' name='vat' value='vat_zero' <?=$is_zero?>>VAT-ZERO
                </br>BS. No. : <input type='text' id='bs_no' name='bs_no' placeholder='Enter BS No. start number...' required>
                <SELECT name='notes_id'><option value='select'>- Select optional billing notes -</option><option>-----------------</option>
<?php
                $options = '';
		foreach($notes_opt as $key=>$field){
    			$id = $field["notes_id"];
    			$notes = $field["notes"];
                        $selected = $old_notes_id == $id ? 'selected' : '';
    			$options = $options."<option value='$id' $selected>$notes</option>";
                echo $options;
               }

    echo "</SELECT><input type='submit' name='update' value='REGENERATE BILL'><input type='submit' name='new_bill' value='ADD NEW BILL'></td>";
    echo "</tr>";
    $update_action = 'regenerate';
	}
?>
</table></form>
</br></br>
</div>

<?php
  $new_total = $total_amount;

	if(isset($_POST['update']) && $update_action == 'update amount'){
		$participant_ids = $_POST['ids'];
    		$notes_id = $_POST['notes_id'];
    		$vatable = $_POST['vat'] == 'vatable' ? 1 : 0;
    		$nonvatable_type = $_POST['vat'] == 'vatable' ? '' : $_POST['vat'];

              foreach($participant_ids as $id){
                  //existing participants
            			
                  if(array_key_exists($id,$participants)){

            				  $info = $participants[$id];
                      $old_amount = $info['fee_amount'];
                      $new_amount = $info['civicrm_amount'];
                      $status = $info['status'];

                      if($status == 'Cancelled'){
                           $new_total = $new_total - $old_amount;
                           $new_amount = 0.00;
            	      }
                      elseif($old_amount < $new_amount){
            		   $add_amount = $new_amount - $old_amount;
                           $new_total = $new_total + $add_amount;
            	      }elseif($old_amount > $new_amount){
            		   $deduct_amount = $old_amount - $new_amount;
                           $new_total = $new_total - $deduct_amount;
                      }

                    updateIncludedNameByParticipantId($id,$bir_no,$new_amount);
                    $history = array('billing_no'=>$billing_no,
                                     'action'=>"Update participant amount to ".$new_amount."of participant no. ".$id,
                                     'bir_no'=>$bir_no);
                    insertBillingHistory($history);

                  }
                  elseif(array_key_exists($id,$new_participants)){
            	        $info = $new_participants[$id];
                  	$new_total = $new_total + $info['fee_amount'];

                   	$details = array('bs_no' => $bir_no,
                                         'vatable' => $vatable,
                                         'notes_id' => $notes_id,
                                         'nonvatable_type' => $nonvatable_type,
                                         'billing_type' => 'Company',
                                         'billing_id' => $billing_no
                                        );
                         generateIndividualBill($id,$details);
                         $history = array('billing_no'=>$billing_no,
                                          'action'=>"Added participant no. ".$id,
                                          'bir_no'=>$bir_no);
                         insertBillingHistory($history);

                  }
              }

              updateCompanyBillByBIRNo($bir_no,$vatable,$new_total,$nonvatable_type,$notes_id);

              if($notes_id != $old_notes_id){
              		$action = "Updated notes";
              }elseif($currentbill['nonvatable_type'] != $nonvatable_type){
                	$action = "Updated account receivable type";
              }elseif($total_amount != $new_total){
              		$action = "Total amount is updated to ".$new_total;
              }

              $history = array('billing_no'=>$billing_no,
                               'action'=>$action,
                               'bir_no'=>$bir_no);
              insertBillingHistory($history);
    }

    elseif(isset($_POST['update']) && $update_action = 'regenerate'){
	$participants = getCompanyParticipantsByOrgId($eventId,$orgId);
      
    }
?>

</body>
</html>
