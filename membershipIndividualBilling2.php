<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />  
 <title>Membership Individual Billing</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
  <link rel="stylesheet" type="text/css" href="menu.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
<script>
  
$(function() {
    $( "#dateSelector" ).datepicker();
});

$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#member').jPaginate({
                'max': 15,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});

/**$(function() {
    $( "#dialog" ).dialog();
});**/
$(function() {
    $( "#confirmation" ).dialog({
      resizable: false,
      width:500,
      modal: true,
      buttons: {
        "OK": function() {
          $( this ).dialog( "close" );
        }
      }
    });
});


</script>
<style>
  img.left {float: left;}
</style>
</head>
</head>
<body>
<?php
  include 'pdo_conn.php';
  include 'membership_functions_v2.php';
  include 'membership_functions.php';
  include 'login_functions.php';
  include 'billing_functions.php';

  $menu = logoutDiv($dbh);
  echo $menu;

  $dbh = civicrmConnect();
?>
    <br>
    <div style="background-color:#A9E2F3;">
  
   <table width='100%'>
    <tr>
     <td align='center' bgcolor='#084B8A'><a href='membershipNewBilling.php'>NEW MEMBERSHIP BILLING</a></td>
     <td align='center' bgcolor="white"><a href='membershipIndividualBilling2.php'>INDIVIDUAL BILLING</a></td>
     <td align='center' bgcolor='#084B8A'><a href='membershipCompanyBilling.php?&user=<?=$userId?>'>COMPANY BILLING</td>
     <td align='center' bgcolor='#084B8A'><a href='onlineMembership.php'>ONLINE MEMBERSHIP</td>
     <td align='center' bgcolor='#084B8A'><a href='membershipBillingView.php'>GENERATED BILLINGS</td>
    </tr>
   </table><br>
<?php
  $currentYear = date("Y");
  $nextYear = $currentYear + 1;

  echo "<form action='' method='POST'>";
  echo "<fieldset>";
  echo "<legend>Search Membership</legend>";
  echo "Expiration Date:&nbsp;";
  echo "<input type='text' name='endDate' id='dateSelector' placeholder='Select expiration date..'>";
  echo "&nbsp;";
  echo "<input type='text' name='contactName' placeholder='Enter name here....'>";
  echo "&nbsp;";
  echo "<input type='submit' value='SEARCH' name='search'>";
  echo "<br><br>";
  echo "Select membership year:&nbsp;";
  echo "<select name='year'>";
  echo "<option>$currentYear</option><option>$nextYear</option>";
  echo "</select>";
  echo "<input type='submit' name='generate' value='GENERATE BILL'>";
  echo "</fieldset>";

  if(isset($_POST["search"])){
    $endDate = $_POST["endDate"];
    $expiredDate = $endDate == NULL ? '' : date("Y-m-d",strtotime($endDate));
    $name = $_POST["contactName"];
    $membership = getMembershipByNameAndDate($dbh,$name,$expiredDate);
    $display = displayMembershipDetails($membership);
    echo $display;

  }

  elseif(isset($_POST["generate"])){

    $ids = $_POST["membershipIds"];
    $year = $_POST["year"];
    foreach($ids as $membershipId){
      $billingInfo = getMembershipBillingData($dbh,$membershipId);
      $contactId = $billingInfo["contact_id"];
      $address = getAddressDetails($dbh,$contactId);
      $street = $address["street"];
      $city = $address["city"];
      $billAddress = $street." ".$city;

      $memberInfo = array();
      $memberInfo["membership_id"] = $billingInfo["membership_id"];
      $memberInfo["contact_id"] = $billingInfo["contact_id"];
      $memberInfo["member_type"] = $billingInfo["membership_type"];
      $memberInfo["name"] = $billingInfo["display_name"];
      $memberInfo["email"] = $billingInfo["email"];
      $memberInfo["street"] = $street;
      $memberInfo["city"] = $city;
      $memberInfo["address"] = $billAddress;
      $memberInfo["company"] = $billingInfo["organization_name"];
      $memberInfo["org_contact_id"] = $billingInfo["employer_id"];
      $memberInfo["fee_amount"] = $billingInfo["fee_amount"];

      insertMemberBilling($dbh,$memberInfo,$year);
    }

    $countContacts = count($ids);

    echo "<div id ='confirmation'>"
         . "<img src='images/confirm.png' alt='confirm' style='float:left;padding:5px;'i width='42' height='42'/>"
         . "<p>Successfully generated membership billing for $countContacts contact/s.</p>"
         . "</div>";
    $expiredDate = $currentYear."-12-31";
    $name = "";
    $membership = getMembershipByNameAndDate($dbh,$name,$expiredDate);
    $display = displayMembershipDetails($membership);
    echo $display;
  }

  else{
    $expiredDate = $currentYear."-12-31";
    $name = "";
    $membership = getMembershipByNameAndDate($dbh,$name,$expiredDate);
    $display = displayMembershipDetails($membership);
    echo $display;
  }
?>
 </form>
</body>
<script type="text/javascript">
  $("#check").click(function(){

    if($(this).is(":checked")){
      $("body input[type=checkbox][class=checkbox]").prop("checked",true);
    }else{
      $("body input[type=checkbox][class=checkbox]").prop("checked",false);
    }

  });
</script>
</html>
