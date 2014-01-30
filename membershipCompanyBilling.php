<html lang="en">
<head>
  <title>Membership Company Billing</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
  <link rel="stylesheet" type="text/css" href="menu.css">
  <script src="js/jquery-1.9.1.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery-ui.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
<script>
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#companies').jPaginate({
                'max': 20,
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

  $dbh = civicrmConnect();
  
  //session_start();
  //if the user has not logged in
  /**if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }**/

  @$userId = $_GET["user"];

  $logout = logoutDiv($dbh,$userId);
  echo $logout;
  /**$header = headerDiv();
  echo $header;**/
  echo "<br>";
?>
   <table width='100%'>
    <tr>
     <td align='center' bgcolor='#084B8A'><a href='membershipNewBilling.php'>NEW MEMBERSHIP BILLING</a></td>
     <td align='center' bgcolor="#084B8A"><a href='membershipIndividualBilling.php'>INDIVIDUAL BILLING</a></td>
     <td align='center' bgcolor='white'><a href='membershipCompanyBilling.php'>COMPANY BILLING</td>
     <td align='center' bgcolor='#084B8A'><a href='onlineMembership.php'>ONLINE MEMBERSHIP</td>
     <td align='center' bgcolor='#084B8A'><a href='membershipBillingView.php'>GENERATED BILLINGS</td>
    </tr>
   </table><br>

   <form method="POST" action="">
     <input name="orgName" type="text">
     <input type="submit" value="Search Organization" name="search"> 
   </form>

<?php

  if(isset($_POST["search"])){
    $orgName = $_POST["orgName"];
    $companies = searchCompanyName($dbh,$orgName);
    $displayCompanies = displayAllCompanies($companies);
    echo $displayCompanies;
  }

  else{
    $companies = getAllCompanies($dbh);
    $displayCompanies = displayAllCompanies($companies);
    echo $displayCompanies;
  }
?>
</body>
</html>
