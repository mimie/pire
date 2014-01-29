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
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#info').jPaginate({
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
     <td align='center' bgcolor="#084B8A"><a href='membershipIndividualBilling.php'>INDIVIDUAL BILLING</a></td>
     <td align='center' bgcolor='#084B8A'><a href='membershipCompanyBilling.php'>COMPANY BILLING</td>
     <td align='center' bgcolor='#084B8A'><a href='membershipBillingView.php'>GENERATED BILLINGS</td>
     <td align='center' bgcolor='white'><a href='onlineMembership.php'>ONLINE MEMBERSHIP</td>
    </tr>
   </table>
   <br>
   <div align='center'>
<?php
  $onlineMembership = getOnlineMembership($dbh);
  $displayOnlineMembership = displayOnlineMembership($onlineMembership);
  echo $displayOnlineMembership;
  
?>
 </div>
</body>
</html>
