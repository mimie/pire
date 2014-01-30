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
<?php
  $onlineMembership = getOnlineMembership($dbh);
  $displayOnlineMembership = displayOnlineMembership($onlineMembership);
  echo $displayOnlineMembership;
  
?>
 </form>
 </div>
</body>
</html>
