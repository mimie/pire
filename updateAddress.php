<?php

include "pdo_conn.php";

$dbh = civicrmConnect();

function readOrganizationPerLine($filename){
// $filename = "Performance.log";
 
  $fp = @fopen($filename, 'r'); 

  $array = array();
  // Add each line to an array
  if ($fp) {
     $array = explode("\n", fread($fp, filesize($filename)));
  }

  return $array;

}

$filename = "companies.txt";

$companies = readOrganizationPerLine($filename);
$companies = array_filter($companies);


function getAllContactIds($dbh,$companies){

   $contacts = array();

  foreach($companies as $org){
    $sql = $dbh->prepare("SELECT id FROM civicrm_contact
                          WHERE organization_name = ? AND contact_type = 'Organization' AND is_deleted = '0'
                         ");
    $sql->bindValue(1,$org,PDO::PARAM_STR);
    $sql->execute();

    $result = $sql->fetch(PDO::FETCH_ASSOC);
    $id = $result["id"];
    $contacts[] = $org."-".$id;
  }

  return $contacts;
}

$contactIds = getAllContactIds($dbh,$companies);

foreach($contactIds as $id){

  echo $id."<br>";

}

?>
