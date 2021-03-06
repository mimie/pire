<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Billing Transactions</title>
	<link rel="stylesheet" type="text/css" href="../billingStyle.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
	<link rel="stylesheet" href="/resources/demos/style.css">
<script>
$(function() {
$( "#tabs" ).tabs();
});
</script>
</head>
<body>
<?php
	include '../pdo_conn.php';
	include 'orreport_functions.php';
        include '../shared_functions.php';

        @$contact_id = $_GET['contact_id'];
        $name = getContactName($contact_id);
?>
<div id="tabs">
	<ul>
		<li><a href="#event_billing">Event Billing</a></li>
		<li><a href="#membership_billing">Membership Billing</a></li>
		<li><a href="#payment">Payment Transactions</a></li>
                <li><a href='display_contacts.php'>Contacts</a></li>
	</ul>
	<div id="event_billing">
		<h3><?=$name?></h3>
<?php
		$contact_events = getContactEvents($contact_id);
                echo "<table>";
                echo "<tr>";
                echo "<th>Participant Id</th>";
                echo "<th>Organization Name</th>";
                echo "<th>Event Name</th>";
                echo "<th>Reference No.</th>";
                echo "<th>ATP</th>";
                echo "<th>Billing Type</th>";
                echo "<th>Transaction Date</th>";
                echo "<th>Fee Amount</th>";
                echo "<th>Amount Paid</th>";
                echo "<th>Balance</th>";
                echo "</tr>";
                
                foreach($contact_events as $participant_id=>$info){

                        $balance = $info['fee_amount'] - $info['amount_paid'];

                	echo "<tr>";
                        echo "<td>".$participant_id."</td>";                        
                        echo "<td>".$info['organization_name']."</td>";                        
                        echo "<td>".$info['event_name']."</td>";                        
                        echo "<td>".$info["billing_no"]."</td>";         
                        echo "<td>".$info["bir_no"]."</td>";         
                        echo "<td>".$info['billing_type']."</td>";               
                        echo "<td>".date_standard($info['bill_date'])."</td>";                        
                        echo "<td>".number_format($info['fee_amount'],2)."</td>";          
                        echo "<td>".number_format($info['amount_paid'],2)."</td>"; 
                        echo "<td>".number_format($balance,2)."</td>";            
                        echo "</tr>";                 
                }

                echo "</table>";
?>
        </div>
	<div id="membership_billing">
		<h3><?=$name?></h3>
<?php
                echo "<table>";
                echo "<tr>";
                echo "<th>Membership Id</th>";
                echo "<th>Membership Type</th>";
                echo "<th>Membership Year</th>";
                echo "<th>Organization Name</th>";
                echo "<th>Reference No.</th>";
                echo "<th>Transaction Date</th>";
                echo "<th>Fee Amount</th>";
                echo "<th>Amount Paid</th>";
                echo "<th>Balance</th>";
                echo "</tr>";
       
		$membership = getContactMembershipBillings($contact_id);
                foreach($membership as $key=>$info){
                        $balance = $info['fee_amount'] - $info['amount_paid'];
 			echo "<tr>";
                        echo "<td>".$info['membership_id']."</td>";
                        echo "<td>".$info['membership_type']."</td>";
                        echo "<td>".$info['year']."</td>";
                        echo "<td>".$info['organization_name']."</td>";
                        echo "<td>".$info['billing_no']."</td>";
                        echo "<td>".date_standard($info['bill_date'])."</td>";
                        echo "<td>".number_format($info['fee_amount'],2)."</td>";
                        echo "<td>".number_format($info['amount_paid'],2)."</td>";
                        echo "<td>".number_format($balance,2)."</td>";
                }
                echo "</table>";
?>
	</div>
	<div id="payment">
		<h3><?=$name?></h3>
<?php
		$debtorno = "IIAP".$contact_id;
                $payments = getPayments($debtorno);

                echo "<table>";
                echo "<tr>";
                echo "<th>Transaction Date</th>";
                echo "<th>Amount Paid</th>";
                echo "<th>Reference</th>";
                echo "<th>OR No.</th>";
                echo "</tr>";

		foreach($payments as $key=>$info){
                        $amount_paid = $info['ovamount'] * (-1);
                	echo "<tr>";
                        echo "<td>".date_standard($info['trandate'])."</td>";
                        echo "<td>".number_format($amount_paid,2)."</td>";
                        echo "<td>".$info['invtext']."</td>";
                        echo "<td>OR-".$info['voucherno']."</td>";
                        echo "</tr>";

                }
                echo "</table>";

?>
	</div>
</div>
</body>
</html>
