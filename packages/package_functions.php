<?php

function createPackage($packageName){

  //$sql = $dbh->prepare("INSERT INTO billing_package(package_name) VALUES(:package)");
  $sql = "INSERT INTO billing_package(package_name) VALUES(:package)";
  $sql = civicrmDB($sql);
  $sql->execute(array(':package' => $packageName));
}

function editPackage($dbh,$packageId){

}
?>
