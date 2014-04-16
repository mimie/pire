<html>
<head>
<title>Manage Billing</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
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
        $('#billings').jPaginate({
                'max': 20,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});

$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#civicrm').jPaginate({
                'max': 20,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});

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
<style type="text/css" media="screen">
.container {
  width: 100%;
  display: table;
  background: whitel;
  border-spacing: 10px;
}
.left, .right {
  display: table-cell;
  width: 50%;
  border-style:solid;
  border-color:#0080FF;
  padding: 5px;
}
</style>
</head>
<body>
<?php
  include 'pdo_conn.php';
  include 'login_functions.php';
  include 'merge_functions.php';
  include 'billing_functions.php';

  $dbh = civicrmConnect();
  $logout = logoutDiv($dbh);
  echo $logout;
  echo "<br>";
  echo "<form action='' method='POST'>";
?>
   <div align='center'>
     <select name='bill'>
      <option value='select'>- Select billing category -</option>
      <option value=''></option>
      <option value='Event'>Event Billing</option>
      <option value='Membership'>Membership Billing</option>
     </select>
     <input type='text' name='name' placeholder='Enter search name here..' size='50'>
     <input type='submit' name='search' value='SEARCH CONTACT'>
   </div>
  
  <div class="container">
    <div class="left">
     <div align='center'><h3>BILLING CONTACTS</h3></div>
<?php

       $name = isset($_POST["search"]) ? $_POST["name"] : "";
       $eventContacts = getAllBilledEventContacts($dbh,$name);
       $billedContacts = displayBillingContacts($eventContacts);
       echo $billedContacts;
?>
    </div>
    <div class="right">
     <div align='center'><h3>CIVICRM CONTACTS</h3></div>
<?php
      $civicrmContacts = getAllCivicrmContacts($dbh,$name);
      $allContacts = displayCivicrmContacts($dbh,$civicrmContacts);
      echo $allContacts;
?>
    </div>
  </div>

  </form>
</body>

</html>
