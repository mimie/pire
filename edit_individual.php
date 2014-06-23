<html>
<head><title>Edit Individual Bill</title></head>
<body>
<?php
	include 'pdo_conn.php';
        include 'bir_functions.php';
       
        $billing_no = $_GET['billing_no'];
        $bill = getInfoByBillingNo($billing_no);
        echo "<pre>";
        print_r($bill);
        echo "</pre>";

?>
</body>
</html>
