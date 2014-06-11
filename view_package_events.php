<html>
<head>
<title>Generate Package Events Bill</title>
<link rel="stylesheet" type="text/css" href="billingStyle.css">
<link rel="stylesheet" type="text/css" href="menu.css">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
<script type='text/javascript' language='javascript'>
function reloadPage()
  {
    location.reload();
  }
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#packages').jPaginate({
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
          //$( this ).dialog( "close" );
          reloadPage();
        }
      }
    });
});

function isNumeric(elem, helperMsg){
        var numericExpression = /^[0-9]+$/;
        if(elem.value.match(numericExpression)){
                return true;
        }else{
                alert(helperMsg);
                elem.focus();
                return false;
        }
}


function isCheck(elem, helperMsg){
        var length = 0;
        for(var i=0;i<elem.length;i++){
           length = elem[i].checked ? length + 1 : length;
        }
        
        if(length == 0){
          alert(helperMsg);
          return false;
        }else{
          return true;
         } 
}

function validator(){

        var checkbox = document.getElementsByName('ids[]');
        var bs_no = document.getElementById('bs_no');

        if(isNumeric(bs_no,"Please enter a valid number for BS No. field.")){
           if(isCheck(checkbox,"Please select a participant name.")){
             return true;
           }
        }

        return false;
}

</script>
</head>
<body>
<?php

  include 'pdo_conn.php';
  include 'shared_functions.php';
  include 'login_functions.php';
  include 'bir_functions.php';
  include 'packages/packagebill_functions.php';
  include 'packages/package_functions.php';
  include 'notes/notes_functions.php';

  $dbh = civicrmConnect();
  $menu = logoutDiv($dbh);
  echo $menu;
  echo "<br>";
  @$uid = $_GET['uid'];
  @$pid = $_GET['pid'];

  $events = getEventsPerPackage($pid);
  $package_name = getPackageName($pid);
  $participants = getParticipantsPerPackage($pid);

   echo "<table width='100%'>";
   echo "<tr>";
   echo "<td align='center'  bgcolor='#084B8A'><a href='package_events.php?pid=".$pid."&uid=".$uid."'>GENERATE PACKAGE BILL</a></td>";
   echo "<td align='center'><a href='view_package_events.php?pid=".$pid."&uid=".$uid."'>VIEW PACKAGE BILLS</td>";
   echo "</tr>";
   echo "</table></br>"; 
?>
  <div align='center'>
  <form action='' method='POST'>
    <input type='text' name='searchtext' placeholder='Type name here...' />
    <input type='submit' name='search' value='SEARCH PARTICIPANT'>
  </form></br>

<?php
  $display = "<table align='center'>"
           . "<tr><th colspan='4'>$package_name</th></tr>"
           . "<tr><th>Event Id</th><th>Event Name</th><th>Start Date</th><th>End Date</th></tr>";

  $eventIds = array();
  foreach($events as $key=>$field){
  	$display = $display."<tr>"
                 . "<td>".$field['event_id']."</td>"
                 . "<td>".$field['event_name']."</td>"
                 . "<td>".date_standard($field['start_date'])."</td>"
                 . "<td>".date_standard($field['end_date'])."</td>"
                 . "</tr>";
        $eventIds[] = $field['event_id'];
  }

  $display = $display."</table></div><br><br>";

  $bills = getBillByPackageId($pid);
  $display = $display."<table width='100%' align='center' id='packages'>"
           . "<thead>"
           . "<tr>"
           . "<th>Name</th>"
           . "<th>Organization</th>"
           . "<th>Fee</th>"
           . "<th>Subtotal</th>"
           . "<th>12% VAT</th>"
           . "<th>Print Bill</th>"
           . "<th>Amount Paid</th>"
           . "<th>BS No.</th>"
           . "<th>Billing Date</th>"
           . "<th>Notes</th>"
           . "</tr></thead><tbody>";

   $preview_img = "<img src='images/preview.png' height='30' width='30'>";
   $print_img = "<img src='printer-icon.png' width='30' height='30'>";

   foreach($bills as $key=>$field){
   	 $display = $display."<tr>"
                  . "<td>".$field['sort_name']."</td>"
                  . "<td>".$field['organization_name']."</td>"
                  . "<td>".number_format($field['total_amount'],2)."</td>"
                  . "<td>".number_format($field['subtotal'],2)."</td>"
                  . "<td>".number_format($field['vat'],2)."</td>"
                  . "<td><a href='BIRForm/birform_package.php?bir_no=".$field['bir_no']."&uid=".$uid."'>$preview_img</a>"
                  . "</td>"
                  . "<td>".number_format($field['amount_paid'],2)."</td>"
                  . "<td>".$field['bir_no']."</td>"
                  . "<td>".date("F j, Y",strtotime($field['bill_date']))."</td>"
                  . "<td>".$field['notes']."</td>"
                  . "</tr>";
   }
           

  $display = $display."</tbody></table>";
  echo $display;
?>
</body>
</html>
