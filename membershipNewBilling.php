<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>New Membership Billing</title>
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
  <link rel="stylesheet" type="text/css" href="menu.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
<script>
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#memberInfo').jPaginate({
                'max': 35,
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
     <td align='center' bgcolor="white"><a href='membershipNewBilling.php'>NEW MEMBERSHIP BILLING</a></td>
     <td align='center' bgcolor="#084B8A"><a href='membershipIndividualBilling.php?&user=<?=$userId?>'>INDIVIDUAL BILLING</a></td>
     <td align='center' bgcolor='#084B8A'><a href='membershipCompanyBilling.php?&user=<?=$userId?>'>COMPANY BILLING</td>
    </tr>
   </table><br>
<?php

    $amounttypeSql = $dbh->prepare("SELECT name,minimum_fee FROM civicrm_membership_type");
    $amounttypeSql->execute();
    $feeType = $amounttypeSql->fetchAll(PDO::FETCH_ASSOC);

    echo "<div style='width:50%;margin:0 auto;padding:3px;'>"
         . "<fieldset>"
         . "<legend>New Membership</legend>"
         . "<table id='generate' style='width:40%;margin:0 auto;'>"
         . "<tr>"
         . "<th>Select membership type:</th>"
         . "<td>"
         . "<form action='' method='POST'>"
         . "<select name='amount'>";


    foreach($feeType as $key => $fee){
      $amount = $fee["minimum_fee"];
      $label = $fee['name']." - ".$amount;
      
      echo "<option value=$amount>$label</option>";
    }

    echo "</select></td></tr>";

    echo "<tr>"
         . "<th>Select membership year:</th>";

    $currentYear = date("Y");
    $nextYear = date('Y', strtotime('+1 year'));
    
    echo "<td>"
         . "<select name='year'>"
         . "<option value='$currentYear'>$currentYear</option>"
         . "<option value='$nextYear'>$nextYear</option>"
         . "</select>"
         . "</td></tr>";

    echo "<tr>"
         . "<td colspan='2' style='align:right;'>"
         . "<input type='submit' value='Generate New Membership Bill' name='generate'>"
         . "</td>"
         . "</tr>";

    echo "</table>"
         . "</fieldset></div><br>";

    echo "<div align='center'>"
         ."Seart contact: " 
         . "<select name='searchType'>"
         . "<option value='name'>Name"
         . "<option value='email'>Email"
         . "</select>"
         . "<input type='text' placeholder='name or email' name='searchText'>"
         . "<input type='submit' value='SEARCH' name='search'>"
         . "</div><br>";
    
    if(isset($_POST["search"])){
       if($_POST["searchType"] == 'name'){
         $searchName = $_POST["searchText"];
         $nonMembers = searchContactByName($dbh,$searchName);
         $displayNonMembers = displayNonMembers($nonMembers);
       }

       else{
         $searchEmail = $_POST["searchText"];
         $nonMembers = searchContactByEmail($dbh,$searchEmail);
         $displayNonMembers = displayNonMembers($nonMembers);
       }

    }

    else{
      $nonMembers = getNonMembers($dbh);
      $displayNonMembers = displayNonMembers($nonMembers);
    }
    
    echo $displayNonMembers;
    echo "</form>";
?>
</body>
</html>
