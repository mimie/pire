<html>
<head>
<title>Edit Individual Bill</title>
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
        include 'bir_functions.php';
        include 'editbill_functions.php';
        include 'billing_functions.php';
       
        $billing_no = $_GET['billing_no'];
        $bill = getInfoByBillingNo($billing_no);
        $bir_no = $bill['bir_no'];
        $address = $bill['street_address']." ".$bill['city_address'];
        $status = $bill['participant_status'];
        $isEdit = $bill['edit_bill'];
        $current_amount = sprintf('%0.2f', $bill['current_amount']);
        $civicrm_amount = $bill['civicrm_amount'];
        $eventId = $bill['event_id'];
        $allowedEdit_info = $isEdit == '0' || ($isEdit == 1 && $civicrm_amount == $current_amount) ? 'Billing information cannot be updated.' : 'Update Billing Information';
?>
      <div align='center'>
	<table>
		<tr>
			<th colspan='2'>Billing Information</th>
		</tr>
		<tr>
			<th>Reference No.</th><td><?=$billing_no?></td>
		</tr>
		<tr>
			<th>BS No.</th><td><?=$bir_no?></td>
		</tr>
		<tr>
			<th>Name</th><td><?=$bill['sort_name']?></td>
		</tr>
		<tr>
			<th>Event Name</th><td><?=$bill['event_name']?></td>
		</tr>
		<tr>
			<th>Event Type</th><td><?=$bill['event_type']?></td>
		</tr>
		<tr>
			<th>Email</th><td><?=$bill['email']?></td>
		</tr>
		<tr>
			<th>Organization</th><td><?=$bill['organization_name']?></td>
		</tr>
		<tr>
			<th>Address</th><td><?=$address?></td>
		</tr>
		<tr>
			<th>Current Amount</th><td><?=$current_amount?></td>
		</tr>
		<tr>
			<th>Civicrm Amount</th><td><?=$civicrm_amount?></td>
		</tr>
		<tr>
			<th>Participant Status</th><td><?=$status?></td>
		</tr>
                <tr>
                	<th colspan='2'><?=$allowedEdit_info?></th>
                </tr>
<form action='' method='POST'>
<?php
	if($status == 'Cancelled' && $isEdit == 1){
                echo "<tr>";
	        echo "<td>Change Name</td><td>";
                echo "Account Receivable Type:";
                echo "<input type='radio' name='vat' value='vatable' checked='checked'>VATABLE ";
                echo "<input type='radio' name='vat' value='vat-exempt'>VAT-EXEMPT ";
                echo "<input type='radio' name='vat' value='vat-zero'>VAT-ZERO ";
                echo "<SELECT name='participant_id'>";
		$participants = getParticipantWithSameAmount($eventId,$civicrm_amount);
                foreach($participants as $key=>$field){
                        $participant_id = $field['participant_id'];
                        $name = $field['sort_name'];
                        $amount = $field['fee_amount'];
			echo "<option value='".$participant_id."'>".$name."-".$amount."</option>";
                }
		echo "</SELECT><input type='submit' name='update' value='UPDATE BILL'></td></tr>";
                $update_action = 'change name';

        }elseif($status !='Cancelled' && $current_amount!=$civicrm_amount && $isEdit == 1){
               echo "<tr>";
               echo "<td>Change Amount</td><td><input type='text' name='new_amount' value='$civicrm_amount' readonly>";
               echo "<input type='submit' name='update' value='UPDATE BILL'></td></tr>";
               $update_action = 'update amount';
           
        }elseif($status !='Cancelled' && $current_amount!=$civicrm_amount && $isEdit == 0){
	       echo "<tr>";
               echo "<td>Generate Bill</td>";
               echo "<td>Account Receivable Type: <input type='text' name='bs_no' placeholder='Enter BS No.' required/>";
               echo "<input type='submit' value='GENERATE BILL'></td></tr>";
               $update_action = 'regenerate';
          }
         

?>
</form>
	</table>
      </div>
<?php
	if($_POST['update'] && $update_action == 'change name'){
           $selected_participantId = $_POST['participant_id'];
           $info = getInfoByParticipantId($selected_participantId);
           $nonvatable_type = $_POST['vat'] == 'vatable' ? '' : $_POST['vat'];
           $is_vat = $_POST['vat'] == 'vatable' ? 1 : 0 ;
           updateParticipant($bir_no,$info,$is_vat,$nonvatable_type);
           $history = array('billing_no'=>$billing_no,
                            'action'=>"Change participant no. ".$participant_id." to ".$selected_participantId,
                             'bir_no'=>$bir_no);
           insertBillingHistory($history);
           echo "<div id='confirmation'><img src='images/confirm.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Successfully change participant name.</div>";
        }
	


?>
</body>
</html>
