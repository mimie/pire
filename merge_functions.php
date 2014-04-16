<?php

function getAllBilledEventContacts(PDO $dbh,$name){

   $sql = $dbh->prepare("SELECT contact_id,participant_name as name,bill_address,organization_name,org_contact_id FROM billing_details
                         WHERE participant_name LIKE ? AND participant_name != 'Admin Mister'
                         ORDER BY participant_name");
   $sql->bindValue(1,"%".$name."%",PDO::PARAM_STR);
   $sql->execute();
   $eventContacts = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $eventContacts;
}

function getAllBilledMemberContacts(PDO $dbh,$name){

   $sql = $dbh->prepare("SELECT contact_id,member_name as name,bill_address,organization_name,org_contact_id FROM billing_membership
                         WHERE member_name LIKE ?
                         ORDER BY member_name");
   $sql->bindValue(1,"%".$name."%",PDO::PARAM_STR);
   $sql->execute();
   $eventMembers = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $eventMembers;
}

function getAllCivicrmContacts(PDO $dbh, $name){

  $sql = $dbh->prepare("SELECT id as contact_id,sort_name as name,organization_name,employer_id as org_contact_id
                        FROM civicrm_contact
                        WHERE sort_name LIKE ?
                        AND is_deleted = '0'
                        AND contact_type = 'Individual'
                        ORDER BY sort_name 
                       ");
  $sql->bindValue(1,"%".$name."%",PDO::PARAM_STR);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function displayBillingContacts(array $contacts){

   $html = "<table id='billings' width='100%'>"
         . "<thead>"
         . "<tr>"
         . "<th>Select Contact</th>"
         . "<th>Contact Id</th>"
         . "<th>Name</th>"
         . "<th>Billing Address</th>"
         . "<th>Organization Id</th>"
         . "<th>Organization Name</th>"
         . "<tr>"
         . "</thead>";

   $html = $html."<tbody>";

   foreach($contacts as $key => $field){
     $name = $field["name"];
     $address = $field["bill_address"];
     $org = $field["organization_name"];
     $orgId = $field["org_contact_id"];
     $contactId = $field["contact_id"];

     $html = $html."<tr>"
           . "<td><input type='checkbox' name='billContactId' value='$contactId'></td>"
           . "<td>$contactId</td>"
           . "<td>$name</td>"
           . "<td>$address</td>"
           . "<td>$orgId</td>"
           . "<td>$org</td>"
           . "</tr>";
   }

   $html = $html."</tbody></table>";

   return $html;

}

function displayCivicrmContacts(PDO $dbh,array $contacts){

   $html = "<table id='civicrm' style='width:100%'>"
         . "<thead>"
         . "<tr>"
         . "<th>Select Contact</th>"
         . "<th>Contact Id</th>"
         . "<th>Name</th>"
         . "<th>Billing Address</th>"
         . "<th>Organization Id</th>"
         . "<th>Organization Name</th>"
         . "<tr>"
         . "</thead>";

   $html = $html."<tbody>";

   foreach($contacts as $key => $field){
     $contactId = $field["contact_id"];
     $name = $field["name"];
     $address = getContactAddress($dbh,$contactId);
     $org = $field["organization_name"];
     $orgId = $field["org_contact_id"];

     $html = $html."<tr>"
           . "<td><input type='checkbox' name='civicrmContactId' value='$contactId'></td>"
           . "<td>$contactId</td>"
           . "<td>$name</td>"
           . "<td>$address</td>"
           . "<td>$orgId</td>"
           . "<td>$org</td>"
           . "</tr>";
   }

   $html = $html."</tbody></table>";

   return $html;

}


?>
