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

   $html = "<table id='packages' align='center'>"
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

function getEventCategory(){

   $stmt = civicrmDB("SELECT label,value FROM civicrm_option_value WHERE option_group_id='14'");
   $stmt->execute();
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

   return $result;
}

function getEventTypeName($eventTypeId){

  $stmt = civicrmDB("SELECT label FROM civicrm_option_value WHERE option_group_id='14' AND value = ?");
  $stmt->bindValue(1,$eventTypeId,PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $eventType = $result["label"];

  return $eventType;
}

function getEventsForPackages($eventTypeId,$eventName){

   $stmt = civicrmDB("SELECT ce.id as event_id, ce.title as event_name,ce.event_type_id,ce.start_date,ce.end_date
                      FROM civicrm_event ce
                      WHERE ce.event_type_id = ?
                      AND ce.title LIKE ?
                      ORDER BY ce.start_date DESC");
   $stmt->bindValue(1,$eventTypeId,PDO::PARAM_INT);
   $stmt->bindValue(2,"%".$eventName."%",PDO::PARAM_STR);
   $stmt->execute();
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

   return $result;

}

function displayEventPackages(array $eventPackages){

   $html = "<table id='events'>"
         . "<thead>"
         . "<tr><td colspan='5' bgcolor='#084B8A'><input type='submit' value='ADD PACKAGE' name='add'></td></tr>"
         . "<tr>"
         . "<th>Select Event</th>"
         . "<th>Event Id</th>"
         . "<th>Event Name</th>"
         . "<th>Start Date</th>"
         . "<th>End Date</th>"
         . "</tr>"
         . "<thead>";

  $html = $html."<tbody>";

  foreach($eventPackages as $field=>$key){
     $eventId = $key["event_id"];
     $eventName = $key["event_name"];
     $startDate = date("F j, Y",strtotime($key["start_date"]));
     $endDate = date("F j, Y",strtotime($key["end_date"]));

     $html = $html."<tr>"
           . "<td><input type='checkbox' value='$eventId' name='eventIds[]'/></td>"
           . "<td>$eventId</td>"
           . "<td>$eventName</td>"
           . "<td>$startDate</td>"
           . "<td>$endDate</td>"
           . "</tr>";
  }

  $html = $html."</tbody></table>";

  return $html;
}

function getEventsPerPackage($packageId){

  $stmt = civicrmDB("SELECT bpe.event_id,ce.title as event_name,ce.start_date,ce.end_date
                     FROM billing_package_events bpe, civicrm_event ce
                     WHERE ce.id = bpe.event_id
                     AND bpe.pid = ?");
  $stmt->bindValue(1,$packageId,PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function displayEventsPerPackage($packageId){

  $packageName = getPackageName($packageId);
  $events = getEventsPerPackage($packageId);

  $html = "<table>"
        . "<tr><th colspan='4'>$packageName</th></tr>"
        . "<tr>"
        . "<th>Event Id</th>"
        . "<th>Event Name</th>"
        . "<th>Start Date</th>"
        . "<th>End Date</th>"
        . "</tr>";

 foreach($events as $key => $field){

    $eventId = $field["event_id"];
    $eventName = $field["event_name"];
    $startDate = date("F j, Y",strtotime($field["start_date"]));
    $endDate = date("F j, Y",strtotime($field["end_date"]));
    $html = $html."<tr>"
          . "<td>$eventId</td>"
          . "<td>$eventName</td>"
          . "<td>$startDate</td>"
          . "<td>$endDate</td>";
 }

 $html = $html."</table>";

 return $html;

}

function insertPackageEvents(array $eventIds,$packageId){

  foreach($eventIds as $id){
   
    $stmt = civicrmDB("INSERT INTO billing_package_events(pid,event_id) VALUES (:event_id,:package_id)");
    $stmt->execute(array(':event_id'=>$id,
                         ':package_id'=>$packageId));
  }
}
?>
