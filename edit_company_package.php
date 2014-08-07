<html>
<head>
<title>Edit Individual Package</title>
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
</script>
</head>
<body>
<?php
  include 'pdo_conn.php';
  include 'login_functions.php';
  include 'bir_functions.php';
  include 'packages/packagebill_functions.php';
  include 'packages/package_functions.php';
  include 'billing_functions.php';
  include 'shared_functions.php';
  include 'company_functions.php';
  include 'notes/notes_functions.php';
  include 'packages/update_package_functions.php';

  @$billing_no = $_GET['billing_no'];
  @$bir_no = $_GET['bir_no'];
  @$billing_id = $_GET['billing_id'];
  @$uid = $_GET['uid'];
  @$pid = $_GET['pid'];
  @$billing_id = $_GET['billing_id'];

  $events = getEventsPerPackage($pid);
  $package_name = getPackageName($pid);
  $notes_opt = getNotesByCategory("Company Event Billing");

?>
	<div align='center'>
		<table>
			<tr><th colspan='4'><?=$package_name?></th></tr>
                        <tr>
			    <th>Event Id</th>
			    <th>Event Name</th>
			    <th>Start Date</th>
			    <th>End Date</th>
                        </tr>
<?php
	foreach($events as $key=>$field){
?>
        <tr>
	    <td><?=$field['event_id']?></td>
	    <td><?=$field['event_name']?></td>
	    <td><?=date_standard($field['start_date'])?></td>
	    <td><?=date_standard($field['end_date'])?></td>
        </tr>

<?php
	}

  echo "</table></br></br>";

  $bill = getBillDetailsByBillingNo($billing_no);
  echo "<pre>";
  print_r($bill);
  echo "</pre>";
  $address = $bill['street_address']." ".$bill['city_address'];
  $infobill = getEventBillDetailsByBillingNo($billing_no);
?>
    		
	        <table align='center'>
                	<tr>
			    <th>Registration No.</th><td><?=$billing_no?></td>
                        </tr>
                        <tr>
			    <th>ATP</th><td><?=$bir_no?></td>
                        </tr>
                        <tr>
			    <th>Name</th><td><?=$bill['sort_name']?></td>
                        </tr>
                        <tr>
			    <th>Package</th><td><?=$package_name?></td>
                        </tr>
                        <tr>
			    <th>Address</th><td><?=$address?></td>
                        </tr>
                        <tr>
			    <th>Current Amount</th><td><?=number_format($bill['total_amount'],2)?></td>
                        </tr>
                        <tr>
			    <th>Subtotal</th><td><?=number_format($bill['subtotal'],2)?></td>
                        </tr>
                        <tr>
			    <th>VAT</th><td><?=number_format($bill['vat'],2)?></td>
                        </tr>
                        <tr>
			    <th>Notes</th><td><?=$bill['notes']?></td>
                	</tr>
                </table></br></br>

<form action='' method='POST'>
                <table align='center'>
                	<tr>
				<th colspan='6' >LIST OF EVENTS</th>
                	</tr>
                        <tr>
				<th>Participant Id</th>
                                <th>Participant Name</th>
				<th>Event Name</th>
				<th>Status</th>
				<th>Current Amount</th>
				<th>Civicrm Amount</th>
                        </tr>
<?php

        $event_amounts = array();
	foreach($infobill as $key=>$details){
		foreach($details as $key=>$field){

                     $fee_amount = $field['fee_amount'];
                     $civicrm_amount = $field['civicrm_amount'];
                     $status = $field['status'];
                     $civicrm_amount = $status == 'Cancelled' ? 0 : $civicrm_amount;
                     $disabled = $fee_amount == $civicrm_amount ? 'disabled' : '';
                     $color = $fee_amount != $civicrm_amount ? 'red' : '';
                     $participant_id = $field['participant_id'];
                     $name = $field['participant_name'];
?>
			<tr>
				<td><input type='checkbox' value='<?=$participant_id?>' name='participantIds[]' <?=$disabled?>><?=$participant_id?></td>
                                <td><?=$name?></td>
                                <td><?=$field['event_name']?></td>
                                <td><?=$field['status']?></td>
                                <td><font color='<?=$color?>'><?=number_format($fee_amount,2)?></font></td>
                                <td><font color='<?=$color?>'><?=number_format($civicrm_amount,2)?></font></td>
			</tr>
<?php
                        $event_amounts[$participant_id] = $civicrm_amount;
                }
        }

        $nonvatable_type = $infobill['nonvatable_type'];
        $vatable = $nonvatable_type == NULL ? "checked='checked'" : "";
        $vat_exempt = $nonvatable == 'vat_exempt' ? "checked='checked'" : "";
        $vat_zero = $nonvatable_type == 'vat_zero' ? "checked='checked'" : "";
?>
                        <tr>
                                <td colspan='6'>Account Receivable Type: 
                                                <input type='radio' name='vat' value='' <?=$vatable?>>VATABLE
                                                <input type='radio' name='vat' value='vat_exempt' <?=$vat_exempt?>>VAT-EXEMPT
                                                <input type='radio' name='vat' value='vat_zero' <?=$vat_zero?>>VAT-ZERO</br>
                                                BS No. : <input type='text' name='bs_no' value=<?=$bir_no?>></br>
                                                <SELECT name='notes_id'><option value='select'>- Select optional billing notes -</option><option>-----------------</option>
<?php
               $options = '';

	       foreach($notes_opt as $key=>$field){
    			$id = $field["notes_id"];
    			$notes = $field["notes"];
                        $selected = $bill['notes_id'] == $id ? 'selected' : '';
    			$options = $options."<option value='$id' $selected>$notes</option>";
               }

               echo $options;
               echo "</SELECT><input type='submit' name='update' value='UPDATE BILL'>";
?>
                                </td>
                        </tr>
              </table><br><br>
<?php
	$additional_participants = getAdditionalCompanyParticipantsByPackageId($pid,$_GET['orgId']);
        if(count($additional_participants) != 0){
		echo "<form action='' method='POST'>";
		$tbl_html = "<table width='100%'>"
			  . "<tr><th colspan='5'>Additional Participants</th></tr>"
			  . "<tr>"
			  . "<th>Participant Id</th>"
			  . "<th>Participant Name</th>"
			  . "<th>Event Name</th>"
			  . "<th>Status</th>"
			  . "<th>Civicrm Amount</th>"
			  . "</tr>";

		foreach($additional_participants as $participant_id=>$info){

			$tbl_html = $tbl_html."<tr>"
				  . "<td><input type='checkbox' name='add_ids[]' value='".$participant_id."'>".$participant_id."</td>"
				  . "<td>".$info['sort_name']."</td>"
				  . "<td>".$info['event_name']."</td>"
				  . "<td>".$info['status']."</td>"
				  . "<td>".$info['fee_amount']."</td>"
				  . "</tr>";        

		}
		 
		 $tbl_html = $tbl_html."<tr><td colspan='5'><input type='submit' name='add' value='ADD PARTICIPANTS'></td></tr></table>";
		 echo $tbl_html;
		 echo "</form>";

         }
?> 
	</div>
<?php


	if($_POST['update']){

		$participantIds = $_POST['participantIds'];
                $amounts = array();
		foreach($participantIds as $id){
			$amounts[$id] = $event_amounts[$id];
                }

                $nonvatable_type = $_POST['vat'];
                $notes_id = $_POST['notes_id'];
                $bir_no = $_POST['bs_no'] == NULL ? '' : formatBSNo($_POST['bs_no']);
                updateIndividualPackage($nonvatable_type,$amounts,$notes_id,$billing_no,$bir_no);

        }

        elseif($_POST['add']){
             
              $participantIds = $_POST['add_ids'];
              foreach($participantIds as $key=>$id){
              	     $participant_info = $additional_participants[$id];
                     updatePackageAdditionalParticipants($participant_info,$id,$bir_no,$billing_no);

              }
		updateTotalPackageAmount($billing_id,$billing_no,$nonvatable_type);

        }
?>
</form>
</body>
</html>
