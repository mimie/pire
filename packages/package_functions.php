<?php

function createPackage($packageName){

  $sql = "INSERT INTO billing_package(package_name) VALUES(:package)";
  $sql = civicrmDB($sql);
  $sql->execute(array(':package' => $packageName));
}

function editPackage($packageId,$packageName){

  $sql = civicrmDB("UPDATE billing_package SET package_name=? WHERE pid=?");
  $sql->bindValue(1,$packageName,PDO::PARAM_STR);
  $sql->bindValue(2,$packageId,PDO::PARAM_INT);
  $sql->execute();

}

function getPackageName($packageId){

  $sql = civicrmDB("SELECT package_name FROM billing_package WHERE pid=?");
  $sql->bindValue(1,$packageId,PDO::PARAM_STR);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $packageName = $result["package_name"];

  return $packageName;
}

function getPackages(){

  $sql = "SELECT pid,package_name FROM billing_package";
  $sql = civicrmDB($sql);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function displayPackages($packages){

   $html = "<table id='' align='center'>"
         . "<thead>"
         . "<th>Edit</th>"
         . "<th>View</th>"
         . "<th>Event Package Name</th>"
         . "</thead>";
   $html = $html."<tbody>";

   foreach($packages as $key => $field){
      $packageId = $field["pid"];
      $packageName = $field["package_name"];
      
    $html = $html."<tr>"
          . "<td><a href='ManagePackages.php?pid=$packageId'><img src='../images/edit.png' height=25' width='25'></a></td>"
          . "<td><a href='ViewEventPackage.php?pid=$packageId'><img src='../images/view.png' height='25' width='25'></td>"
          . "<td>$packageName</td>"
          . "</tr>";
   }

   $html = $html."</tbody></table>";
   return $html;
}
?>
