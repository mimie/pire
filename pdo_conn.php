<?php

  date_default_timezone_set('Asia/Manila');

  function civicrmConnect(){

    $dbh = new PDO('mysql:host=localhost;dbname=webapp_civicrm', 'root', 'mysqladmin');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;

  }

  function weberpConnect(){

   $weberpConn = new PDO('mysql:localhost;dbname=iiap_weberp2014','root','mysqladmin');
   $weberpConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   return $weberpConn;

  }

  function civicrmDB($sql){
    $dbh = new PDO('mysql:host=localhost;dbname=webapp_civicrm', 'root', 'mysqladmin');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = $dbh->prepare($sql);

     return $sql;


  }

?>
