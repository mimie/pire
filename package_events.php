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
  $uid = $_GET['uid'];
  $pid = $_GET['pid'];

  $events = getEventsPerPackage($pid);
  $package_name = getPackageName($pid);
  $participants = getParticipantsPerPackage($pid);

   echo "<table width='100%'>";
   echo "<tr>";
   echo "<td align='center'><a href='package_events.php?pid=".$pid."&uid=".$uid."'>GENERATE PACKAGE BILL</a></td>";
   echo "<td align='center' bgcolor='#084B8A'><a href='view_package_events.php?pid=".$pid."&uid=".$uid."'>VIEW PACKAGE BILLS</td>";
   echo "</tr>";
   echo "</table></br>"; 

  echo "<div align='center'>";
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
  echo "</div>";
?>

<div align='center'>
  <form action='' method='POST'>
    <input type='text' name='searchtext' placeholder='Type name here...' />
    <input type='submit' name='search' value='SEARCH PARTICIPANT'>
  </form>
</div>

<?php
  $participants = $_POST['search'] ? searchParticipantsPerPackage($pid,$_POST['searchtext']) : getParticipantsPerPackage($pid);

  $display = $display."</table></br></br>";

  echo "<form action='' method='POST' onsubmit=\"return validator()\">";

  $display = $display."<table id='packages' align='center'>"
           . "<thead>"
           . "<tr><td colspan='4'>Account Receivable Type : "
           . "<input type='radio' name='vat' value='1' checked='checked'>VATABLE"
           . "<input type='radio' name='vat' value='2'>NON-VATABLE</br>"
           . "BS No. : <input name='bs_no' id='bs_no' type='text' placeholder='Enter BS No. start number' required>";
    $notes_opt = getNotesByCategory("Individual Event Billing");
    $notes_collection = array();
    $display = $display."<SELECT name='notes'><option value='select'>- Select optional billing notes -</option><option>-----------------</option>";
    foreach($notes_opt as $key=>$field){
        $id = $field["notes_id"];
        $notes = $field["notes"];
    	$display = $display."<option value='$id'>$notes</option>";
        //stores notes in an array for reference display of notes in the table
        $notes_collection[$id] = $notes;
    }


  $display = $display."</SELECT><input type='submit' name='generate' value='GENERATE BILL'></td></tr>";
  $display = $display. "<tr><td colspan='4' bgcolor='05123E'>LIST OF PARTICIPANTS</td></tr></thead><tbody>";

  //billing details for package events
  foreach($participants as $contact_id=>$details){
     $name = getContactName($contact_id);
     $display = $display."<tr><th colspan='13'><input type='checkbox' value='$contact_id' name='ids[]'>$name</th></tr>"
              . "<th>Participant Id</th>"
              . "<th>Event Name</th>"
              . "<th>Status</th>"
              . "<th>Fee</th>";
     $total = 0;
     foreach($details as $key=>$field){
        $participant_id = $field['participant_id'];
     	      $display = $display."<tr>"
                   . "<td>".$field['participant_id']."</td>"
                   . "<td>".$field['event_name']."</td>"
                   . "<td>".$field['status']."</td>"
                   . "<td>".$field['fee_amount']."</td>";
                   $total = $total + $field['fee_amount'];
                   $organization = $field['organization_name'];

     }

     $address = $field['street_address'].",".$field['city_address'];
     $display = $display. "<tr><td colspan='3'>Total</td><td>".number_format($total,2)."</td></tr>"
              . "<tr><td colspan='2'>Organization</td><td colspan='2'>".htmlspecialchars($organization)."</td></tr>"
              . "<tr><td colspan='2'>Billing Address</td><td colspan='2'>$address</td></tr>";

  }

  $display = $display."</tbody></table>";
  echo $display;
  echo "</form>";

  if($_POST['generate']){
    $contact_ids = $_POST['ids'];
    $bs_no = $_POST["bs_no"];
    $is_vatable = $_POST["vat"];
    $note_id = $_POST["notes"] == 'select' ? NULL : $_POST["notes"];

    foreach($contact_ids as $contact_id){
      $bir_no = formatBSNo($bs_no);
      $details = $participants[$contact_id];
      generatePackageBill($contact_id,$details,$bir_no,$is_vatable,$note_id,$pid);
      $bs_no++;
    }
    echo "<div id='confirmation'><img src='images/confirm.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Successfully generated bill.</div>";
  }

?>
</body>
</html>
