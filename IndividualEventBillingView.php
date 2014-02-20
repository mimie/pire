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
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#billings').jPaginate({
                'max': 15,
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
<body>
<?php
  include 'login_functions.php';
  include 'pdo_conn.php';
  include 'membership_functions.php';
  include 'billing_functions.php';
  include 'billingview_functions.php';

  $dbh = civicrmConnect();
  $menu = logoutDiv($dbh);
  echo $menu;
  echo "<br>";

  echo "<div style='width:80%;margin:0 auto;padding:3px;'>";
  echo "<form action='' method='POST'>"
       . "<fieldset>"
       . "<legend>Search Individual Event Billing</legend>"
       . "Search category:&nbsp;"
       . "<select name='searchType'>"
       . "<option value='name'>Name</option>"
       . "<option value='orgname'>Organization Name</option>"
       . "<option value='eventname'>Event Name</option>"
       . "<option value='billingno'>Billing No.</option>"
       . "<option value='eventtype'>Event Type</option>"
       . "</select>"
       . "&nbsp;<input type='text' name='searchText' placeholder='Enter search text here...' size='30'>"
       . "<input type='submit' name='search' value='SEARCH'>"
       . "</fieldset><br>";


  if(isset($_POST["search"])){
    $searchValue = $_POST["searchText"];
    $searchType = $_POST["searchType"];
    $billings = searchIndividualBillings($dbh,$searchType,$searchValue);
    $display = displayIndividualEventBillings($billings);
    echo $display;
  }

  elseif(isset($_POST["update"])){
    $ids = $_POST["participantIds"];
    foreach($ids as $participantId){
       updateChangeIndividualBilling($dbh,$participantId);
    }

    $billings = getAllIndividualBillings($dbh);
    $display = displayIndividualEventBillings($billings);
    echo $display;  
    
    echo "<div id='confirmation' title='Confirmation'>"
         . "<img src='images/confirm.png' alt='confirm' style='float:left;padding:5px;' width='42' height='42'/><br>Billing is successfully updated."
         . "</div>";
  }

  else{
    $billings = getAllIndividualBillings($dbh);
    $display = displayIndividualEventBillings($billings);
    echo $display;  
  }
?>
  </div>
  </form>
</body>
</html>
