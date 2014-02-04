<?php

  include 'login_functions.php';
  include 'pdo_conn.php';
  include 'membership_functions.php';
  include 'billing_functions.php';
  include 'company_functions.php';
  
  $dbh = civicrmConnect();
  $orgId = $_GET["orgId"];
  $logout = logoutDiv($dbh);
  echo $logout;
?>
<html lang="en">
<head>
<title>Select members for billing</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Company Billing</title>
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
        $('#contacts').jPaginate({
                'max': 20,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});

</script>
</head>
<body>
   <br>
   <table width='100%'>
    <tr>
     <td align='center' bgcolor='#084B8A'><a href='membershipNewBilling.php'>NEW MEMBERSHIP BILLING</a></td>
     <td align='center' bgcolor="#084B8A"><a href='membershipIndividualBilling.php?&user=<?=$userId?>'>INDIVIDUAL BILLING</a></td>
     <td align='center' bgcolor='white'><a href='membershipCompanyBilling.php?&user=<?=$userId?>'>COMPANY BILLING</td>
     <td align='center' bgcolor='#084B8A'><a href='membershipBillingView.php'>GENERATED BILLINGS</td>
    </tr>
   </table><br>

<?php
  $sqlOrg = $dbh->prepare("SELECT organization_name FROM civicrm_contact
                           WHERE id = ?
                           AND contact_type = 'Organization'
                          ");
  $sqlOrg->bindValue(1,$orgId,PDO::PARAM_INT);
  $sqlOrg->execute();
  $result = $sqlOrg->fetch(PDO::FETCH_ASSOC);
  $orgName = $result["organization_name"];

  echo "<div id = 'navigation'>";
  echo "<a href='membershipCompanyBilling.php'><b>Company List</b></a>";
  echo "&nbsp;&nbsp;<b>&gt;</b>&nbsp;";
  echo "<i>$orgName</i>";
  echo "</div><br>";
?>

  <form action="" method="POST">
<?php

  $lastYear = date('Y',strtotime('-1 year'));
  $currentYear = date('Y');
  $nextYear = date('Y',strtotime('+1 year'));

  $currentExpiredDate = date('Y-m-d',strtotime($currentYear.'-12-31'));
  $lastExpiredDate = date('Y-m-d',strtotime($lastYear.'-12-31'));
  $nextExpiredDate = date('Y-m-d',strtotime($nextYear.'-12-31'));

  $formatCurrent = date('F j Y',strtotime($currentExpiredDate));
  $formatLast = date('F j Y',strtotime($lastExpiredDate));
  $formatNext = date('F j Y',strtotime($nextExpiredDate));
?>
  <div style="width:70%;margin:0 auto;padding:3px;">
  <fieldset>
    <legend>Company Billing</legend>
    <table align='center'>
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
      <td colspan='2'>
           <input type='submit' value='Generate New Membership Bill' name='generate'>
      </td>
     </tr>
    </table>
  </fieldset>
  <br>
    <div style='text-align:center'>
    Select expiration date:
    <select name="endDate">
      <option value='<?=$lastExpiredDate?>'><?=$formatLast?></option>
      <option value='<?=$currentExpiredDate?>'><?=$formatCurrent?></option>
      <option value='<?=$nextExpiredDate?>'><?=$formatNext?></option>
      <option value='all'>All Contacts</option>
    </select>
    <input type="submit" value="SEARCH CONTACTS" name='search'>
    </div>
  <br>
<?php

  if(isset($_POST["search"])){
    $endDate = $_POST["endDate"];

    if($endDate == 'all'){
      $contacts = getContactsPerCompany($dbh,$orgId);
      $displayContacts = displayContactsPerCompany($contacts,$orgName);
      echo $displayContacts;
    }

    else{
 
      $contacts = filterContactsByEndDate($dbh,$orgId,$endDate);
      $displayContacts = displayContactsPerCompany($contacts,$orgName);
      echo $displayContacts;
    }
  }

  else{
    $contacts = getContactsPerCompany($dbh,$orgId);
    $displayContacts = displayContactsPerCompany($contacts,$orgName);
    echo $displayContacts;
  }

?>
 </div>
 </form>
</body>
</html>
