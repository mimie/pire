<html>
<head>
<title>Edit Individual Bill</title>
<link rel="stylesheet" type="text/css" href="billingStyle.css">
</head>
<body>
<?php
	include 'pdo_conn.php';
        include 'bir_functions.php';
       
        $billing_no = $_GET['billing_no'];
        $bill = getInfoByBillingNo($billing_no);
        $address = $bill['street_address']." ".$bill['city_address'];
?>
      <div align='center'>
	<table>
		<tr>
			<th colspan='2'>Billing Information</th>
		</tr>
		<tr>
			<th>Billing No.</th><td><?=$billing_no?></td>
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
			<th>Current Amount</th><td><?=$bill['current_amount']?></td>
		</tr>
		<tr>
			<th>Civicrm Amount</th><td><?=$bill['civicrm_amount']?></td>
		</tr>
		<tr>
			<th>Participant Status</th><td><?=$bill['participant_status']?></td>
		</tr>
	</table>
      </div>
</body>
</html>
