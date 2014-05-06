<?

  function civicrmConnect(){

    $dbh = new PDO('mysql:host=10.110.215.92;dbname=webapp_civicrm', 'iiap', 'mysqladmin');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;

  }

  function weberpConnect(){

   $weberpConn = new PDO('mysql:host=10.110.215.92;dbname=IIAP_DEV','iiap','mysqladmin');
   $weberpConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   return $weberpConn;

  } 

  function civicrmDB($sql){
    $dbh = new PDO('mysql:host=10.110.215.92;dbname=webapp_civicrm', 'iiap', 'mysqladmin');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = $dbh->prepare($sql);
   
     return $sql;


  } 

?>
