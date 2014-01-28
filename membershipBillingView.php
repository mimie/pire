<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>New Membership Billing</title>
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
    $( "#datepickerStart" ).datepicker();
    $( "#datepickerEnd" ).datepicker();
});

$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#billings').jPaginate({
                'max': 15,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});

</script>
</head>
<body>
<?php
  include 'login_functions.php';
  include 'pdo_conn.php';
  include 'membership_functions.php';
  include 'billing_functions.php';

  $dbh = civicrmConnect();
  $logout = logoutDiv($dbh);
  echo $logout;
?>
    <br>
    <div style="background-color:#A9E2F3;">
  
   <table width='100%'>
    <tr>
     <td align='center' bgcolor="#084B8A"><a href='membershipNewBilling.php'>NEW MEMBERSHIP BILLING</a></td>
     <td align='center' bgcolor="#084B8A"><a href='membershipIndividualBilling.php'>INDIVIDUAL BILLING</a></td>
     <td align='center' bgcolor='#084B8A'><a href='membershipCompanyBilling.php'>COMPANY BILLING</td>
     <td align='center' bgcolor='white'><a href='membershipBillingView.php'>GENERATED BILLINGS</td>
    </tr>
   </table><br>
   
   <div style="width:80%;margin:0 auto;padding:3px;">
   <form method="POST" action="">
    <fieldset>
     <legend>Search Membership Billing</legend>
     <br>
     Search category:
     <select name="searchType">
      <option value="name">Name</option>
      <option value="email">Email</option>
      <option value="org">Organization Name</option>
      <option value="billingno">Billing No</option>
     </select>
     <input type="text" name="searchValue" placeholder="Enter search text here...">
     <input type="submit" value="SEARCH" name="search"><br><br>
     Search bill date:
     <input type="text" name="startDate" id="datepickerStart" placeholder="From">
     <input type="text" name="endDate" id="datepickerEnd" placeholder="To">
     <input type="submit" value="SEARC BILL DATE" name="searchDate">
    </fieldset>
   </form>
  </div>
<?php

  if(isset($_POST["search"])){

    if($_POST["searchType"] == 'name'){
      $searchValue = $_POST["searchValue"];
      $billings = getMembershipBillingByName($dbh,$searchValue);
      $displayBillings = displayMembershipBillings($billings);
      echo $displayBillings;
    }

    elseif($_POST["searchType"] == 'email'){
      $searchValue = $_POST["searchValue"];
      $billings = getMembershipBillingByEmail($dbh,$searchValue);
      $displayBillings = displayMembershipBillings($billings);
      echo $displayBillings;
    }

    elseif($_POST["searchType"] == 'org'){
      $searchValue = $_POST["searchValue"];
      $billings = getMembershipBillingByOrg($dbh,$searchValue);
      $displayBillings = displayMembershipBillings($billings);
      echo $displayBillings;
    }

    elseif($_POST["searchType"] == 'billingno'){
      $searchValue = $_POST["searchValue"];
      $billings = getMembershipBillingByBillingNo($dbh,$searchValue);
      $displayBillings = displayMembershipBillings($billings);
      echo $displayBillings;
    }
  }

  elseif(isset($_POST["searchDate"])){
    $startDate = $_POST["startDate"];
    $endDate = $_POST["endDate"];
    $billings = getMembershipBillingByDate($dbh,$startDate,$endDate);
    $displayBillings = displayMembershipBillings($billings);
    echo $displayBillings;
  }

  else{
     $billings = getAllMembershipBillings($dbh);
     $displayBillings = displayMembershipBillings($billings);
     echo $displayBillings;
  }
?>
 </div>
</body>
</html>
