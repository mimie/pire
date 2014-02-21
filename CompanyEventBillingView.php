<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Company Event Billing</title>
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
                'max': 24,
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
</head>
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
  
  echo "<div style='width:100%;margin:0 auto;padding:3px;'>";
  echo "<form action='' method='POST'>"
       . "<fieldset>"
       . "<legend>Search Company Billing</legend>"
       . "Search category:&nbsp;"
       . "<select name='searchType'>"
       . "<option name='name'>Name</option>"
       . "<option name='eventname'>Event Name</option>"
       . "<option name='billingno'>Billing No</option>"
       . "</select>"
       . "<input type='text' lenght='15' placeholder='Enter search text here...'>"
       . "</fieldset><br>";

  $billings = getAllCompanyBillings($dbh);
  $display = displayCompanyBillings($billings);
  echo $display;
?>
  </form>
 </div>
</body>
</html>
