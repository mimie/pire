<?php

 include 'company_functions.php';
 include 'pdo_conn.php';

 $dbh = civicrmConnect();

 $test = checkParticipantBillGenerated($dbh,3160,248);
 echo "<pre>";
 print_r($test);
 echo "</pre>";
?>
