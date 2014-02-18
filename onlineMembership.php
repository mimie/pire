<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Online Membership</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
  <link rel="stylesheet" type="text/css" href="menu.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
<script src="js/jquery.tablesorter.js"></script>
<style>
  img.left {float: left;}
</style>
<script>
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#info').jPaginate({
                'max': 15,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});
$(function() {
    $( "#dialog" ).dialog();
});

</script>
</head>
<body>
<?php
  include 'pdo_conn.php';
  include 'login_functions.php';
  include 'billing_functions.php';
  include 'membership_functions.php';

  $dbh = civicrmConnect();
  $logout = logoutDiv($dbh);

  echo $logout;
?>
   <br>
   <table width='100%'>
    <tr>
     <td align='center' bgcolor="#084B8A"><a href='membershipNewBilling.php'>NEW MEMBERSHIP BILLING</a></td>
     <td align='center' bgcolor="#084B8A"><a href='membershipIndividualBilling2.php'>INDIVIDUAL BILLING</a></td>
     <td align='center' bgcolor='#084B8A'><a href='membershipCompanyBilling.php'>COMPANY BILLING</td>
     <td align='center' bgcolor='white'><a href='onlineMembership.php'>ONLINE MEMBERSHIP</td>
     <td align='center' bgcolor='#084B8A'><a href='membershipBillingView.php'>GENERATED BILLINGS</td>
    </tr>
   </table>
   <br>
   <div style='width:80%;margin:0 auto;padding:3px;'>
   <form action="" method="POST">
   <fieldset>
    <legend>New Membership</legend>
      <table id='generate' style='width:40%;margin:0 auto;'>
       <tr>
         <th>Select membership type:</th>
         <td>
           <select name='membershipTypeId'>
<?php
    
    $amounttypeSql = $dbh->prepare("SELECT id,name,minimum_fee FROM civicrm_membership_type");
    $amounttypeSql->execute();
    $feeType = $amounttypeSql->fetchAll(PDO::FETCH_ASSOC);

    foreach($feeType as $key => $fee){     
      $feeId = $fee["id"];
      $amount = $fee["minimum_fee"];
      $label = $fee['name']." - ".$amount;
      
      echo "<option value='$feeId'>$label</option>";
    }
?>
           </select>
         </td>
       </tr>
       <tr>
        <th>Select membership year:</th>
        <td>
           <select name='year'>
<?php
            $currentYear = date("Y");
            $nextYear = date('Y', strtotime('+1 year'));
 
            echo "<option value='$currentYear'>$currentYear</option>";
            echo "<option value='$nextYear'>$nextYear</option>";

?>
           </select>
        </td>
       </tr>
       <tr>
         <td colspan='2' style='align:right;'>
           <input type='submit' value='Generate New Membership Bill' name='generate'>
         </td>
       </tr>
      </table>
   </fieldset>
   <br>
   <div align='center'>
     Search contact:
     <select name='searchType'>
      <option value='name'>Name</option>
      <option value='email'>Email</option>    
     </select>
     <input type='text' placeholder='name or email' name='searchText'>
     <input type='submit' value='SEARCH' name='search'>
   </div>
   <br>
<?php
  if(isset($_POST["search"])){

    if($_POST["searchType"] == 'name'){
      $searchValue = $_POST["searchText"];
      $onlineMembership = getOnlineMembershipByName($dbh,$searchValue);
      $displayOnlineMembership = displayOnlineMembership($onlineMembership);
      echo $displayOnlineMembership;
    }

    elseif($_POST["searchType"] == 'email'){
      $searchValue = $_POST["searchText"];
      $onlineMembership = getOnlineMembershipByEmail($dbh,$searchValue);
      $displayOnlineMembership = displayOnlineMembership($onlineMembership);
      echo $displayOnlineMembership;
    }

  }

  elseif(isset($_POST["generate"])){

       $membershipTypeId = $_POST["membershipTypeId"];
       $year = $_POST["year"];
       $contactIds = $_POST["contactIds"];
       $sqlMembership = $dbh->prepare("SELECT id,name,minimum_fee 
                                       FROM civicrm_membership_type
                                       WHERE id = ?");
        $sqlMembership->bindValue(1,$membershipTypeId, PDO::PARAM_INT);
        $sqlMembership->execute();
        $membership = $sqlMembership->fetch(PDO::FETCH_ASSOC);
    
       foreach($contactIds as $contactId){

        $sqlDetails = $dbh->prepare("SELECT cc.id,cm.id as membership_id,cc.display_name, em.email, cc.organization_name, cc.employer_id
                              FROM civicrm_contact cc, civicrm_email em, civicrm_membership cm
                              WHERE cc.id = ?
                              AND cc.id = em.contact_id
                              AND cm.contact_id = cc.id
                              AND em.is_primary = '1'
                              AND cc.is_deleted = '0'
                             ");
         $sqlDetails->bindValue(1,$contactId,PDO::PARAM_INT);
         $sqlDetails->execute();
         $details = $sqlDetails->fetch(PDO::FETCH_ASSOC);

         $address = getAddressDetails($dbh,$contactId);
         $street = $address["street"];
         $city = $address["city"];
         $billingAddress = $street." ".$city;

         $info = array();
         
         $info["contact_id"] = $details["id"];
         $info["membership_id"] = $details["membership_id"];
         $info["member_type"] = $membership["name"];
         $info["name"] = $details["display_name"];
         $info["email"] = $details["email"];
         $info["street"] = $street;
         $info["city"] = $city;
         $info["address"] = $billingAddress;
         $info["company"] = $details["organization_name"];
         $info["org_contact_id"] = $details["employer_id"];
         $info["fee_amount"] = $membership["minimum_fee"];

         insertMemberBilling($dbh,$info,$year);

       }

      $countContacts = count($contactIds);

      echo "<div id='dialog'>"
           . "<img src='images/confirm.png' alt='confirm' class='left'/>"
           . "<p>Successfully generated new membership billing for $countContacts contact/s.</p>"
           . "</div>";

    $onlineMembership = getOnlineMembership($dbh);
    $displayOnlineMembership = displayOnlineMembership($onlineMembership);
    echo $displayOnlineMembership;
  }

  else{
    $onlineMembership = getOnlineMembership($dbh);
    $displayOnlineMembership = displayOnlineMembership($onlineMembership);
    echo $displayOnlineMembership;

    
  }
  
?>
 </form>
 </div>
</body>
</html>
