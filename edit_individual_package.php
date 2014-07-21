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
  include 'notes/notes_functions.php';
  include 'packages/packagebill_functions.php';
  include 'packages/package_functions.php';
  include 'billing_functions.php';
  include 'shared_functions.php';
  include 'company_functions.php';

  @$billing_no = $_GET['billing_no'];
  @$bir_no = $_GET['bir_no'];
  @$billing_id = $_GET['billing_id'];
  @$uid = $_GET['uid'];
  @$pid = $_GET['pid'];

  $events = getEventsPerPackage($pid);
  $package_name = getPackageName($pid);

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
                <table align='center'>
                	<tr>
				<th colspan='5' >LIST OF EVENTS</th>
                	</tr>
                        <tr>
				<th>Participant Id</th>
				<th>Event Name</th>
				<th>Status</th>
				<th>Current Amount</th>
				<th>Civicrm Amount</th>
                        </tr>
<?php

	foreach($infobill as $key=>$details){
		foreach($details as $key=>$field){

                     $fee_amount = $field['fee_amount'];
                     $civicrm_amount = $field['civicrm_amount'];
                     $status = $field['status'];
                     $disabled = $fee_amount == $civicrm_amount ? 'disabled' : '';
?>
			<tr>
				<td><input type='checkbox' value='<?=$field['participant_id']?>' name='participantIds[]' <?=$disabled?>><?=$field['participant_id']?></td>
                                <td><?=$field['event_name']?></td>
                                <td><?=$field['status']?></td>
                                <td><?=number_format($fee_amount,2)?></td>
                                <td><?=number_format($civicrm_amount,2)?></td>
			</tr>
<?php
                }
        }
?>
              </table> 
                </table>
	</div>
</body>
</html>
