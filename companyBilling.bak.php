<html>
<head>
<title>Billing List</title>
<link rel="stylesheet" type="text/css" href="billingStyle.css">
<link rel="stylesheet" type="text/css" href="menu.css">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
<script>
function reloadPage()
  {
  location.reload();
  }
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#billings').jPaginate({
                'max': 5,
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

  include 'dbcon.php';
  include 'pdo_conn.php';
  include 'badges_functions.php';
  include 'weberp_functions.php';
  include 'billing_functions.php';
  include 'send_functions.php';
  include 'login_functions.php';
  include 'company_functions.php';

   $dbh = civicrmConnect();
 
   /**session_start();
   //if the user has not logged in
   if(!isLoggedIn())
   {
     header('Location: login.php');
     die();
   }**/

   @$uid = $_GET["uid"];
  
   $logout = logoutDiv($dbh,$userId);
   echo $logout;
   echo "<br>";
   @$eventId = $_GET["eventId"];

   $eventDetails = getEventDetails($dbh,$eventId);
   $eventName = $eventDetails["event_name"];
   $eventStartDate = $eventDetails["start_date"];
   $eventEndDate = $eventDetails["end_date"];
   $eventTypeName = getEventTypeName($dbh,$eventId);
   $locationDetails = getEventLocation($dbh,$eventId);
   $eventLocation = formatEventLocation($locationDetails);
   //navigation
   echo "<div id = 'navigation'>";
   echo "<a href='events2.php?&uid=".$uid."'><b>Event List</b></a>";
   echo "&nbsp;&nbsp;<b>&gt;</b>&nbsp;";
   echo "<i>$eventName</i>";
   echo "</div>";

   echo "<div id='eventDetails'>";
   echo "<table border = '1'>";
   echo "<tr>";
   echo "<th>Event Name</th><td><b><i>$eventName</i></b></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Start Date</th><td><i>$eventStartDate</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>End Date</th><td><i>$eventEndDate</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Event Type</th><td><i>$eventTypeName</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Event Location</th><td><i>$eventLocation</i></td>";
   echo "</tr>";
   echo "</table>";
   echo "</div>";

?>

<?php  

   echo "<div id='billingNav'>";
   echo "<table width='100%'>";
   echo "<tr>";
   echo "<td align='center' bgcolor='#084B8A'><a href='individualBilling.php?eventId=$eventId&billingType=individual&uid=".$uid."'>INDIVIDUAL BILLING</a></td>";
   echo "<td align='center'><a href='companyBilling.php?eventId=$eventId&billingType=company&uid=".$uid."'>COMPANY BILLING</td>";
   echo "</tr>";
   echo "</table>";
   echo "</div>";
 
   $customGroupDetails = getCustomGroupDetails($dbh,"Billing");
   $customGroupId = $customGroupDetails["id"];
   $tableName = $customGroupDetails["table_name"];
   
   $column = getColumnNameStoredValues($dbh,$customGroupId);
   $columnName = $column["column_name"];

   $billingType =  getTypeOfBilling($dbh,$tableName,$columnName);
   $orgs = getOrganization($dbh);
   $participants = getEventParticipantIds($dbh, $eventId);
   $eventBillingTypes = getParticipantsBillingType($billingType,$participants);
   $companyBillingTypes = $eventBillingTypes["Company"];
   //var_dump($companyBillingTypes);
   $companyBillAmounts = array();

//-------------------FOR COMPANY BILLING------------------------------------------------------------------------------------
   $participantPerCompanyBill = array();
   $participantDetails = array();
   $details = array();
   $totalAmount = array();

   foreach($companyBillingTypes as $participantId){
     		
	if(!$participantPerCompanyBill){
       	   $contact = $participants[$participantId];
       	   $contact_id = $contact["contact_id"];
           $contact_address = $eventTypeName == 'SIH' ? getCompleteCompanyAddress($dbh,$contact_id) : getContactAddress($dbh,$contact_id);
       	   $fee_amount = $contact["fee_amount"];
       	   $contactDetails = getContactDetails($dbh, $contact_id);
           $participant_name = $contactDetails["name"];
           $email = getContactEmail($dbh,$contact_id);
       	   $organization_name = $contactDetails["companyName"];
       	   //$orgId = $orgs[$organization_name];
           $orgId = $eventTypeName == 'SIH' ? $contact_id : getEmployerId($dbh,$contact_id);
       	   $billingId = "";
           $billingNo = $eventTypeName.$billingId.$participantId;
           $status = getStatusType($dbh,$participantId);

       	   $details["participant_id"] = $participantId;
       	   $details["event_id"] = $eventId;
           $details["event_name"] = $eventName;
       	   $details["participant_name"] = $participant_name;
           $details["email"] = $email;
           $details["bill_address"] = $contact_address;
       	   $details["organization_name"] = $organization_name;
       	   $details["org_contact_id"] = $orgId;
       	   $details["billing_type"] = 'Company';
       	   $details["fee_amount"] = $fee_amount;
       	   $details["billingNo"] = $billingNo;
           $details["status"] = $status;

       	   $participantDetails[$participantId] = $details;
       	   $participantPerCompanyBill[$orgId] = $participantDetails;

       	   unset($details);
       	   unset($participantDetails);
      }

     else{
       $contact = $participants[$participantId];
       $contact_id = $contact["contact_id"];
       $fee_amount = $contact["fee_amount"];
       $contactDetails = getContactDetails($dbh, $contact_id);
       $participant_name = $contactDetails["name"];
       $email = getContactEmail($dbh,$contact_id);
       $contact_address = $eventTypeName == 'SIH' ? getCompleteCompanyAddress($dbh,$contact_id) : getContactAddress($dbh,$contact_id);
       $organization_name = $contactDetails["companyName"];
       //$orgId = $orgs[$organization_name];
       $orgId = $eventTypeName == 'SIH' ? $contact_id : getEmployerId($dbh,$contact_id);
       $billingId = "";
       $billingNo = $eventTypeName.$billingId.$participantId;
       $status = getStatusType($dbh,$participantId);

       $details["participant_id"] = $participantId;
       $details["event_id"] = $eventId;
       $details["event_name"] = $eventName;
       $details["participant_name"] = $participant_name;
       $details["email"] = $email;
       $details["bill_address"] = $contact_address;
       $details["organization_name"] = $organization_name;
       $details["org_contact_id"] = $orgId;
       $details["billing_type"] = 'Company';
       $details["fee_amount"] = $fee_amount;
       $details["billingNo"] = $billingNo;
       $details["status"] = $status;
       

       $participantDetails[$participantId] = $details;

       	if(array_key_exists($orgId,$participantPerCompanyBill)){
          array_push($participantPerCompanyBill[$orgId],$details);
       	}
      
       	else{
         $participantPerCompanyBill[$orgId] = $participantDetails;
       	}

       unset($details);
       unset($participantDetails);
      
     }
   }//end foreach

 //-------------------------------------FOR COMPANY BILLING-------------------------------------------------------------------------------

?>
<?
      foreach($participantPerCompanyBill as $orgIdKey => $value){

         $updateValue = $value;
         $totalAmount = 0;
         $amountPerParticipant = 0;
         foreach($value as $billingDetails){
            $amountPerParticipant = $amountPerParticipant + $billingDetails["fee_amount"];
         }
         $totalAmount = $amountPerParticipant;
         $companyBillAmounts[$orgIdKey] = $totalAmount; 
      }

      echo "<b>Select action to process:</b>";
      echo "<form name='companyBill' method='Post'>";
      echo "<select name='companyProcessType'>";
      echo "<option value='select'>Select action type</option>";
      echo "<option value='Generate Bill'>Generate Bill</option>";
      echo "<option value='Send Bill'>Send Bill</option>";
      //For weberp function
      //echo "<option value='Post to Weberp'>Post to Weberp</option>";
      echo "</select>";
      echo "<input type='submit' name='process' value='Process Action'><br><br>";
      echo "<input type='button' value='Reload page' onclick='reloadPage()'>";

      echo "<br><br>";
      echo "<table id='billings' border='1' width='100%'>";
      echo "<thead>";
      echo "<tr><th colspan='12'>Company Billing</th></tr>";
      echo "<tr>";
      echo "<th>Organization Name</th>";
      echo "<th>Total Billing Amount</th>";
      echo "<th>Subtotal</th>";
      echo "<th>12% VAT</th>";
      echo "<th><input type='checkbox' id='check'>Generate Bill</th>";
      echo "<th>Send Bill</th>";
//      echo "<th>Post Bill</th>";
      echo "<th>Payment Status</th>";
      echo "<th>Billing Reference No.</th>";
      echo "<th>Billing Date</th>";
      echo "<th>Billing Address</th>";
      echo "<th>Billed Participants</th>";
      echo "</tr>";
      echo "</thead>";

      echo "<tbody>";

      foreach($participantPerCompanyBill as $orgIdKey => $participant){
         
  	$companyId = $orgIdKey;
  	$organization_name = array_search($companyId,$orgs);
        $totalBill = $companyBillAmounts[$companyId];

        if($eventTypeName == 'CON' || $eventTypeName == 'MBA'){
           $subtotal = $totalBill;
           $subtotal = number_format($subtotal, 2, '.', '');
           $tax = 0.0;
         }

        else{
           //$tax = round($totalBill/9.3333,2);
           //$subtotal = round($totalBill - $tax,2);
           $subtotal = round($totalBill/1.12,2);
           $tax = $totalBill - $subtotal;

        }
        $totalBill = number_format($totalBill, 2, '.', '');
        echo "<tr>";
        echo "<td>$organization_name,$companyId</td>";
        echo "<td align='center'>$totalBill</td>";
        echo "<td align='center'>$subtotal</td>";
        echo "<td align='center'>$tax</td>";

        $isCompanyBillGenerated = checkCompanyBillGenerated($dbh,$companyId,$eventId);   
        if($isCompanyBillGenerated == 1){
  
          $companyBillingRefNo = getCompanyBillingNo($dbh,$companyId,$eventId);
          $companyBillingDate = getCompanyBillingDate($dbh,$companyId,$eventId);
          $companyBillingAddress = "";
          $participantsLink = participantsLink($companyBillingRefNo,$eventId,$companyId);
        
          if($eventTypeName == 'CON' || $eventTypeName == 'MBA'){
             echo "<td align='center'><a href='companyConvention.php?companyBillingRef=$companyBillingRefNo&eventId=$eventId&orgId=$companyId' style='text-decoration:none' target='_blank'>";
          }

          else{
            echo "<td align='center'><a href='companyBillingReference.php?companyBillingRef=$companyBillingRefNo&eventId=$eventId&orgId=$companyId' style='text-decoration:none' target='_blank'>";
          }
          echo "<img src='printer-icon.png' height='50' width='50'><br>Print</a></td>";
          echo "<td align='center'><a href='sendIndividualBilling.php?companyBillingRef=$companyBillingRefNo&eventId=$eventId&orgId=$companyId' style='text-decoration:none' target='_blank'>";
          echo "<img src='email.jpg' height='50' width='50'><br>Email</a></td>";
          //for weberp function
          //echo "<td align='center'><input type='checkbox' name='postIds[]' value='$companyId'></td>";
          echo "<td align='center'></td>";
          echo "<td align='center'>$companyBillingRefNo</td>";
          echo "<td align='center'>$companyBillingDate</td>";
          echo "<td align='center'>$companyBillingAddress</td>";
          echo "<td align='center'>$participantsLink</td>";
        }

        elseif($isCompanyBillGenerated == 0){
         
          echo "<td align='center'><input type='checkbox' name='companyIds[]' value='$companyId' class=checkbox></td>";
          echo "<td align='center'><input type='checkbox' name='companyIds2[]' value='$companyId' disabled></td>";
          //for weberp function
          //echo "<td align='center'><input type='checkbox' name='postIds[]' value='$companyId' disabled></td>";
          echo "<td align='center'></td>";
          echo "<td></td>";
          echo "<td></td>";
          echo "<td></td>";
          echo "<td></td>";
        }
        echo "</tr>";
        echo "</tbody>";

      }
      echo "</form>";
      echo "</table>";

   if(isset($_POST["companyProcessType"])){
     $companyProcessType = $_POST["companyProcessType"];

      
     if($companyProcessType == 'Generate Bill'){
       $companiesSelected = $_POST["companyIds"];
      
       foreach($companiesSelected as $companyId){
         $billedParticipants = $participantPerCompanyBill[$companyId];

         $organization_name = array_search($companyId,$orgs);
         $sqlMaxBillingId = $dbh->prepare("SELECT MAX(cbid) as prevBillingId FROM billing_company");
         $sqlMaxBillingId->execute();
  	 $maxBillingId = $sqlMaxBillingId->fetch(PDO::FETCH_ASSOC);
         $eventTypeName = getEventTypeName($dbh,$eventId);
         $companyBillingNo = $maxBillingId["prevBillingId"] + 1;
         $companyBillingNo = formatBillingNo($companyBillingNo);
         $currentYear = date("y");
         $companyBillingNo = $eventTypeName."-".$currentYear."-".$companyBillingNo;
            
         $sqlInsertCompanyBilling = $dbh->prepare("INSERT INTO billing_company
                                    (event_id,event_name,org_contact_id,organization_name,billing_no)
                                    VALUES('$eventId', '$eventName','$companyId',?,'$companyBillingNo')
                                     ");
         $sqlInsertCompanyBilling->bindValue(1,$organization_name,PDO::PARAM_STR);  
         $sqlInsertCompanyBilling->execute();
         $companyBillTotalAmount = 0;
        
         foreach($billedParticipants as $participant => $billDetails){

            $participant_id = $billDetails["participant_id"];
            $contactId = getParticipantContactId($dbh,$participant_id,$eventId);
            $email = getContactEmail($dbh,$contactId);
            $participant_name = $billDetails["participant_name"];
            $organization_name = $billDetails["organization_name"];
            $orgId = $companyId;
            $participantBillingType = $billDetails["billing_type"];
            $fee_amount = $billDetails["fee_amount"];
            $billingNo = $companyBillingNo;
            $status = getStatusType($dbh,$participant_id);
   
            $sql = $dbh->prepare("INSERT INTO billing_details
                   (participant_id,contact_id,event_id,event_type,event_name,participant_name,email,participant_status,organization_name,org_contact_id,billing_type,fee_amount,billing_no)
                   VALUES('$participant_id','$contactId','$eventId','$eventTypeName','$eventName','$participant_name','$email','$status',?,'$orgId','$participantBillingType','$fee_amount','$billingNo')");

             $sql->bindValue(1,$organization_name,PDO::PARAM_STR);
             $sql->execute();

            $companyBillTotalAmount = $companyBillTotalAmount + $fee_amount;
          }
             
              if($eventTypeName == 'CON'){
                   $subtotal = $totalBill;
                   $tax = 0.0;
              }

              else{
                //$tax = round($companyBillTotalAmount/9.3333,2);
                //$subtotal = round($companyBillTotalAmount - $tax,2);
                $subtotal = round($companyBillTotalAmount/1.12,2);
                $tax = $companyBillTotalAmount - $subtotal;

             }

         $sqlUpdateTotalAmount = $dbh->prepare("UPDATE billing_company
                                    SET total_amount = '$companyBillTotalAmount', subtotal = '$subtotal', vat = '$tax'
                                    WHERE event_id = '$eventId'
                                    AND  billing_no = '$billingNo'
                                    AND org_contact_id = '$orgId'
                                  ");

         $sqlUpdateTotalAmount->execute();
       }
     echo "<script type='text/javascript'>";
     echo "reloadPage()";
     echo "</script>";
       
    echo'<div id="confirmation" title="Confirmation">';
    echo "<img src='images/confirm.png' alt='confirm' style='float:left;padding:5px;'i width='42' height='42'/>";
    echo'<p>Billing is successfully generated.</p>';
    echo'</div>';


     }//end if Generate Bill


     elseif($companyProcessType == 'Post to Weberp'){

       $cids = $_POST["postIds"];
       $companyDetails = array();
       foreach($cids as $companyId){
          $orgName = array_search($companyId,$orgs);
          $street = "";
          $city = "";
          $companyDetails["contact_id"] = $companyId;
          $companyDetails["company_name"] = $orgName;
          $companyDetails["street"] = $street;
          $companyDetails["city"] = $city;
          $amount = getCompanyBillingAmount($dbh,$companyId,$eventId);
          insertCompanyCustomer($weberpConn,$companyDetails);
          unset($companyDetails);
          $companyDetails = array();
          myPost($eventTypeName,$eventName,$amount,$orgName);
       }
       


     }//end if Post to Weberp
   }//end elseif company processor type


?>
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
