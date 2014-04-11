<?php

function getAllBilledEventContacts(PDO $dbh,$name){

   $sql = $dbh->prepare("SELECT contact_id,participant_name,bill_address,organization_name,org_contact_id FROM billing_details
                         WHERE participant_name = ?");
   $sql->bindValue(1,$name,PDO::PARAM_STR);
   $sql->execute();
   $eventContacts = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $eventContacts;
}

function getAllBilledMemberContacts(PDO $dbh,$name){

   $sql = $dbh->prepare("SELECT contact_id,member_name,bill_address,organization_name,org_contact_id FROM billing_membership
                         WHERE member_name = ?");
   $sql->bindValue(1,$name,PDO::PARAM_STR);
   $sql->execute();
   $eventMembers = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $eventMembers;
}

function displayBilledContacts(array $contacts){

   $html = "<table id='billings'>"
         . "<thead>"
         . "<tr>"
         . "<th>Name</th>"
         . "<th>Billing Address</th>"
         . "<th>Organization Name</th>"
         . "<th>Organization Contact ID</th>"
         . "<tr>"
         . "</thead>";

   $html = $html."<tbody>";

   $html = $html."</tbody></table>";

   return $html;


}



?>
