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
		<li><a href="#payment">Payment Transactions</a></li>
                <li><a href='display_organization.php'>Contacts</a></li>
	</ul>
	<div id="event_billing">
		<h3><?=$name?></h3>
<?php
		$contact_events = getOrganizationEvents($contact_id);
                echo "<table>";
                echo "<tr>";
                echo "<th>Event Id</th>";
                echo "<th>Event Name</th>";
                echo "<th>Reference No.</th>";
                echo "<th>ATP</th>";
                echo "<th>Transaction Date</th>";
                echo "<th>Total Amount</th>";
                echo "<th>Amount Paid</th>";
                echo "</tr>";
                
                foreach($contact_events as $key=>$info){
                	echo "<tr>";
                        echo "<td>".$info['event_id']."</td>";                        
                        echo "<td>".$info['event_name']."</td>";                        
                        echo "<td>".$info["billing_no"]."</td>";         
                        echo "<td>".$info["bir_no"]."</td>";         
                        echo "<td>".date_standard($info['bill_date'])."</td>";                        
                        echo "<td>".number_format($info['total_amount'],2)."</td>";          
                        echo "<td>".number_format($info['amount_paid'],2)."</td>";             
                        echo "</tr>";                 
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
