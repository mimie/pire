<?php

include 'pdo_conn.php';
include 'billing_functions.php';
$dbh = civicrmConnect();
$address = getContactAddress($dbh,2983);

?>
