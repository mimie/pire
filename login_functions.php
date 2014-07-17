<?php

function createSalt(){

  $string = md5(uniqid(rand(), true));
  return substr($string, 0, 3);
}

function insertUser(PDO $dbh,array $registration){

   $username = $registration["username"];
   $hash = $registration["hash"];
   $salt = $registration["salt"];
   $firstname = $registration["firstname"];
   $middlename = $registration["middlename"];
   $lastname = $registration["lastname"];
   $designation = $registration["designation"];

   $sql = $dbh->prepare("INSERT INTO billing_users (username,password,salt,firstname,middlename,lastname,designation)
                         VALUES('$username','$hash','$salt','$firstname','$middlename','$lastname','$designation')
                        ");
  $sql->execute();
}

function getUserDetails(PDO $dbh,$username){
   $sql = $dbh->prepare("SELECT password, salt FROM billing_users
                         WHERE username = '$username'
                        ");
   $sql->execute();
   $userDetails = $sql->fetch(PDO::FETCH_ASSOC);

   return $userDetails;
}

function getUserId(PDO $dbh,$username){

  $sql = $dbh->prepare("SELECT id FROM billing_users
                        WHERE username = '$username'
                       ");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $userId = $result["id"];

  return $userId;
}

function getUsername(PDO $dbh,$userId){

  $sql = $dbh->prepare("SELECT username FROM billing_users
                        WHERE id = '$userId'
                       ");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $username = $result["username"];

  return $username;
}

function getUserFullName(PDO $dbh,$userId){

  $sql = $dbh->prepare("SELECT firstname,middlename,lastname FROM billing_users
                        WHERE id = '$userId'
                       ");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $firstname = $result["firstname"];
  $middlename = $result["middlename"];
  $lastname = $result["lastname"];

  $middleInitial = $middlename[0];

  $fullName = $firstname." ".$middleInitial.". ".$lastname;
  $fullName = strtoupper($fullName);
  
  return $fullName;
}

function validateUser()
{
    session_regenerate_id (); //this is a security measure
    $_SESSION['valid'] = 1;
    $_SESSION['userid'] = $userid;
}

function isLoggedIn()
{
    if(isset($_SESSION['valid']) && $_SESSION['valid'])
        return true;
    return false;
}

function logout()
{
    $_SESSION = array(); //destroy all of the session variables
    session_destroy();
}

function headerDiv(){

  $html = "<img src='header.jpg' width='100%' height='100px'>";

  return $html;

}

function logoutDiv($dbh){
/**  $html = "<div align='right' width='100%' height='10px' style='background-color:black;padding:6px;'>"
        . "<a href='logout.php'>Logout</a>"
        . "</div>";**/

     //$username = getUsername($dbh,$uiserId);
     $uid = $_GET["uid"];

     $ciafile_dir = 'packages/ManagePackages.php';
     $ciafile_dir = file_exists($ciafile_dir) ? $ciafile_dir : '../packages/ManagePackages.php';
     $ciafile_dir = file_exists($ciafile_dir) ? $ciafile_dir : 'ManagePackages.php';

     $events_dir = 'events2.php';
     $events_dir = file_exists($events_dir) ? $events_dir : '../events2.php';

     $mem_dir = 'membershipIndividualBilling2.php';
     $mem_dir = file_exists($mem_dir) ? $mem_dir: '../membershipIndividualBilling2.php';

     $individualBill_dir = 'IndividualEventBillingView.php';
     $individualBill_dir = file_exists($individualBill_dir) ? $individualBill_dir : '../IndividualEventBillingView.php';


     $companyBill_dir = 'CompanyEventBillingView.php';
     $companyBill_dir = file_exists($companyBill_dir) ? $companyBill_dir : '../CompanyEventBillingView.php';
 
     $manageBill_dir = 'manageBilling.php';
     $manageBill_dir = file_exists($manageBill_dir) ? $manageBill_dir: '../manageBilling.php';

     $notes_dir = 'notes/notes.php';
     $notes_dir = file_exists($notes_dir) ? $notes_dir : '../notes/notes.php';
     $notes_dir = file_exists($notes_dir) ? $notes_dir : basename($_SERVER['PHP_SELF']);

     $package_dir = 'individual_package_bill.php';
     $package_dir = file_exists($package_dir) ? $package_dir: '../individual_package_bill.php';

     $comp_package_dir = 'company_package_bill.php';
     $comp_package_dir = file_exists($comp_package_dir) ? $comp_package_dir: '../company_package_bill.php';
     

     $html = "<div width='100%' style='background-color:black; padding:1px;'>"
           . "<ul>"
           . "<li><a href='".$events_dir."?uid=$uid'>Events</a></li>"
           . "<li><a href='#'>Membership</a>"
           . "<ul><li><a href='".$mem_dir."?uid=$uid'>Membership Billing</a></li></ul>"
           . "</li>"
           . "<li><a href='".$notes_dir."?uid=$uid'>Notes</a></li>"
           . "<a href='#'><li>Billing Report</a>"
           . "<ul><li><a href='".$invidualBill_dir."?uid=$uid'>Individual Event Billing</a></li>"
           . "<li><a href='".$companyBill_dir."?uid=$uid'>Company Event Billing</a></li>"
           . "<li><a href='".$manageBill_dir."?uid=$uid'>Update Billed Participants</a></li>"
           . "</ul>"
           . "</li>"
           . "<li><a href='#'>Package Events</a>"
           . "<ul>"
           . "<li><a href='".$ciafile_dir."?uid=$uid'>Manage Package Events</a></li>"
           . "<li><a href='".$package_dir."?uid=$uid'>Individual Package Bill</a></li>"
           . "<li><a href='".$comp_package_dir."?uid=$uid'>Company Package Bill</a></li>"
           . "</ul>"
           . "</li>"
           . "</ul><br><br>"
           . "</div>";


  return $html;
}

function getUserDetailsById(PDO $dbh,$userId){

  $sql = $dbh->prepare("SELECT username,firstname,middlename,lastname, designation
                        FROM billing_users
                        WHERE id = '$userId'
                       ");

  $sql->execute();
  $userDetails = $sql->FETCH(PDO::FETCH_ASSOC);
  
  return $userDetails;
}

/*
 * get the name of the person who generated the bill
 */
function getGeneratorName($drupalId){

  $stmt = civicrmDB("SELECT cc.sort_name FROM civicrm_contact cc,civicrm_uf_match uf 
                     WHERE uf.uf_id = ?
                     AND cc.id=uf.contact_id");
  $stmt->bindValue(1,$drupalId,PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $name = $result["sort_name"];
  
  return $name;
}
?>
