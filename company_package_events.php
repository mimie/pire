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
    //location.reload();
    window.location=window.location;
  }
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#packages').jPaginate({
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

           if(isCheck(checkbox,"Please select a participant name.")){
             return true;
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
  include 'billing_functions.php';

  $dbh = civicrmConnect();
  $menu = logoutDiv($dbh);
  echo $menu;
  echo "<br>";
  $uid = $_GET['uid'];
  $pid = $_GET['pid'];

  $events = getEventsPerPackage($pid);
  $package_name = getPackageName($pid);
  $participants = getParticipantsPerPackage($pid);
  $comp_names = getCompanyNames();

   echo "<table width='100%'>";
   echo "<tr><th colspan='2'>COMPANY PACKAGE BILL</th></tr>";
   echo "<tr><td colspan='2' bgcolor='#2EFEF7'></td></tr>";
   echo "<tr>";
   echo "<td align='center'><a href='company_package_events.php?pid=".$pid."&uid=".$uid."'>GENERATE PACKAGE BILL</a></td>";
   echo "<td align='center' bgcolor='#084B8A'><a href='view_company_package_events.php?pid=".$pid."&uid=".$uid."'>VIEW PACKAGE BILLS</td>";
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
  $display = $display."</table></br></br>";
  echo $display;
?>

<?php

  $companies = getParticipantsPackageCompanyByPackageId($pid);

  echo "<form action='' method='POST' onsubmit=\"return validator()\">";

  echo "<table align='center' style='width:60%;'>"
       . "<thead>"
       . "<tr><td>Account Receivable Type : "
       . "<input type='radio' name='vat' value='vatable' checked='checked'>VATABLE"
       . "<input type='radio' name='vat' value='vat_exempt'>VAT-EXEMPT"
       . "<input type='radio' name='vat' value='vat_zero'>VAT-ZERO"
       . "</br>"
       . "BS No. : <input name='bs_no' id='bs_no' type='text' placeholder='Enter BS No. start number'>";
    $notes_opt = getNotesByCategory("Company Event Billing");
    echo "</br>Notes : <SELECT name='notes'><option value='select'>- Select optional billing notes -</option><option>-----------------</option>";
    foreach($notes_opt as $key=>$field){
        $id = $field["notes_id"];
        $notes = $field["notes"];
    	echo "<option value='$id'>$notes</option>";
        //stores notes in an array for reference display of notes in the table
    }


  echo "</SELECT></br><input type='submit' name='generate' value='GENERATE BILL'/></td></tr>";
  echo "<tr><td bgcolor='05123E'>LIST OF COMPANIES</td></tr></thead><tbody>";

  $comp_package = array();

  foreach($companies as $orgId=>$indexes){

       $info_participant = array();
       echo "<tr><td>";
       echo "<table style='width:100%;'>";
       $orgname = $comp_names[$orgId];

       echo "<tr><th colspan='5'><input type='checkbox' name='orgId' value='$orgId'>$orgname</th></tr>";
       echo "<tr>";
       echo "<td bgcolor='#0B2161'>Participant Id</td>";
       echo "<td bgcolor='#0B2161'>Name</td>";
       echo "<td bgcolor='#0B2161'>Event Name</td>";
       echo "<td bgcolor='#0B2161'>Status</td>";
       echo "<td bgcolor='#0B2161'>Fee</td>";
       echo "</tr>";

       foreach($indexes as $key=>$participants){

               $participant_id = $participants['participant_id'];

	       echo "<tr>";
	       echo "<td><input type='checkbox' name='participantIds[]' value='".$participant_id."'>".$participant_id."</td>";
	       echo "<td>".$participants['sort_name']."</td>";
	       echo "<td>".$participants['event_name']."</td>";
	       echo "<td>".$participants['status']."</td>";
	       echo "<td>".$participants['fee_amount']."</td>";
	       echo "</tr>";
               
               $info_participant[$participant_id] = $participants;
       }

       echo "</table>";
       echo "</td></tr>";
       echo "<tr><td bgcolor='#2EFEF7'></td></tr>";

       $comp_package[$orgId] = $info_participant;
  }
?>

<?php
  echo "</tbody></table>";

?>

</body>
</html>
