<html>
<head>
<title>Billing With Vat</title>
<style>
#main{
  border: 3px solid #0000FF;
  width:875px;
  height: 1050px;
  padding: 4px;
  background-image:url('images/watermark.png');
  background-repeat:no-repeat;
  background-position:center;
  background-size:350px 300px;

}

#header{
  width: 860px;
  height: 49.51px;
  background-color: #08088A;
  font-family: Arial;
  color: white;
  font-weight: bold;
}

#logo{
  width:48.38px;
  height:43.09px;
  padding: 2px 2px 2px 2px;
  margin-left: auto;
  margin-right: auto;
}

#tin{
  width: 860px;
  height: 31.75px;
  font-family: Arial;
  padding: 4px 4px 2px 2px;
  margin: margin 0 auto;
  
}
#billedTo{
  width: 856.44px;
  font-family: Arial;
  margin: margin 0 auto;
  
}
</style>
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

  $dbh = civicrmConnect();
 
  /**session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }**/

  @$billingNo = $_GET["billingRef"];
  @$eventId = $_GET["eventId"];
  //@$userId = $_GET["user"];
  //$generator = getUserFullName($dbh,$userId);
  //$billingNo = '3154';
  //$eventId = '233';
  $billingDetails = getIndividualBillingDetails($dbh,$billingNo,$eventId);

  $eventType = getEventTypeName($dbh,$eventId);
  
  $participantId = $billingDetails["participant_id"];
  $contactId = getParticipantContactId($dbh, $participantId, $eventId);
  $participantName = $billingDetails["participant_name"];
  $orgId = getEmployerId($dbh,$contactId);
  $orgName = getEmployerName($dbh,$orgId);
  $billAddress = getContactAddress($dbh,$contactId);
  $feeAmount = $billingDetails["fee_amount"];
  $currencyFormat = number_format($feeAmount,2);

  $billDate = $billingDetails["bill_date"];
  $billDate = date("F j Y",strtotime($billDate));
  
  $eventDetails = getEventDetails($dbh,$eventId);
  $eventName = $eventDetails["event_name"];
  $dueDate = $eventDetails["start_date"];
  $dueDate = date("F j Y", strtotime($dueDate));

  $eventEndDate = $eventDetails["end_date"];
  $eventEndDate = date("F j Y", strtotime($eventEndDate));
  $locationDetails = getEventLocation($dbh,$eventId);
  $eventLocation = formatEventLocation($locationDetails);


  //$tax = round($feeAmount/9.3333,2);
  //$netVat = round($feeAmount - $tax,2);
  $tax = number_format($billingDetails["vat"],2);
  $netVat = number_format($billingDetails["subtotal"],2);

?>
<div id="main">
  <div style="width:896.5px;height:7.93px;"></div>
   <center>
    <div id="header">
     <table id="header">
       <tr>
        <td rowspan="2"><img id="logo" src="iiap_logo.png"></td>
        <td>Institute of Internal Auditors Philippines, Inc.</td>
        <td rowspan="2" align="center"><font style="font-size:35px">BILLING</font></td>
       </tr>
      <tr>
        <td><font size="2">Unit 702 Corporate Center, 139 Valero St., Salcedo Village, Makati City 1227</font></td>
      </tr>
    </table>
   </div>
  </center>
  <center>
    <div id="tin">
      <table align="left">
       <tr>
         <td><font style="font-size:12px"><b>TIN No. 001-772-403-000 : (+632) 940-9551 /940-9554 : Fax (+632) 325-0414</b></font></td>
       </tr>
     </table>
    </div>
    <!--billed to-->
    <div id="billedTo">
     <center>
     <table align="left">
      <tr>
         <td colspan="4"><b><font style="font-size:16px">BILLED TO:</font></b></td>
      </tr>
      <tr>
         <td width="20px"></td>
         <td width="500px"><font style="font-size:17px"><?=$participantName?></font></td>
         <td align="right" width="150px" style="border-right:2px solid black"><font style="font-size:17px"><b>BILLING NUMBER</b></font></td>
         <td width="132.28px"><font style="font-size:19px"><b><?=$billingNo?></b></font></td>
      </tr>
      <tr>
         <td></td>
         <!--This line for the organizatio name  and address-->
         <td><font style="font-size:17px"><?=$orgName?></font></td>
         <td align="right" style="border-right:2px solid black"><font style="font-size:17px"><b>BILLING DATE</b></font></td>
         <td><font style="font-size:17px"><?=$billDate?></font></td>
      </tr>
      <tr>
         <td></td>
         <td><font width="329.95px" style="font-size:17px"><?=$billAddress?></font></td>
         <td align="right" style="border-right:2px solid black"><font style="font-size:17px"><b>DUE DATE</b></font></td>
         <td><font style="font-size:17px"><?=$dueDate?></font></td>
      </tr>
      <tr>
       <td colspan="4"><br></td>
      </tr>
     </table>
     </center>
     <!--end of billed to-->
    </div>
    <div id="billedTo">
     <!--particulars-->
     <table align="left" style="border-collapse:collapse;">
      <tr>
        <td colspan="2" width="580px"align="center" bgcolor="#D8D8D8" style="border:2px solid black;"><font style="font-size:13px"><b>PARTICULARS</b></font></td>
        <td align="center" bgcolor="#D8D8D8" style="border:2px solid black"><font style="font-size:13px"><b>AMOUNT</b></font></td>
      </tr>   
      <tr>
        <td colspan="2" height="500px" style="border:2px solid black; vertical-align:top;" align="left"><?=$eventName?>
               <br>On&nbsp;<?=$dueDate?>&nbsp;to&nbsp;<?=$eventEndDate?>
               <br>At&nbsp;<?=$eventLocation?><br>
               <div style="height:50%"></div><br>
               <div>
               <?php
                 $sql = $dbh->prepare("SELECT notes FROM billing_notes bn,billing_notes_category bnc
                                       WHERE bn.notes_category_id = bnc.notes_category_id
                                       AND bnc.category_name = 'Individual Event Billing'
                                       AND bn.notes_status = '1'
                                      ");
                 $sql->execute();
                 $result = $sql->fetch(PDO::FETCH_ASSOC);
                 $notes = $result["notes"];
                 echo $notes;
               ?>
                
               </div>
        </td>
        <td style="border:2px solid black; vertical-align:top;" align="center"><br><?=$currencyFormat?></td> 
      </tr>
      <tr>
        <td style="border:2px solid black;" rowspan="2" align="center">
         <font style="font-size:19px"><b><i>THANK YOU FOR YOUR BUSINESS!</b></i></font><br>
         <font style="font-size:13px"><b>(NOT VALID FOR INPUT TAX CLAIM)</b></font>
        </td>
        <td style="border:2px solid black;" align="right" rowspan="2">SUBTOTAL<br>VAT - 12%</td>
        <td width="" height="26.84px" style="border:2px solid black;" align="center"><?=$netVat?></td>
      </tr>
      <tr>
        <td height="26.84px" style="border:2px solid black;" align="center"><?=$tax?></td>
      </tr>
      <tr>
        <td height="15px"></td>
        <td style="border:2px solid black;" align="center" bgcolor="#C8C8C8"><b>TOTAL<br>AMOUNT DUE</b></td>
        <!--<td width="" style="border:1px solid black;" rowspan="2" align="center"><?//=$currencyFormat?>&nbsp;PHP</td>-->
        <td style="border:2px solid black;" align="center" bgcolor="#D8D8D8">
           <?=$currencyFormat?>&nbsp;PHP</td>
        </td>
      </tr>
    </table><br>
     <table align='left'>
      <tr>
       <td width="279.61px" style="vertical-align:top">
          <br><font style="font-size:13px;font-family:Arial"><b>DIRECT ALL INQUIRIES TO:</b></font><br>
          <b><i><font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?//=$generator?></i></b><br>
          <font style="font-size:13px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(+632) 940-9554</font><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;email: ar_finance@iia-p.org</font>
       </td>
       <td width="419.61px">
          <br><font style="font-size:13px;font-family:Arial"><b>PAYMENT INSTRUCTION:</b></font><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&Oslash;&nbsp;If by check, <font color="red"><b><u>should be</u></b></font> made payable to:</font><br>
          <font style="font-size:15px"><b><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Institute of Internal Auditors Philippines, Inc.</i></b></font><br><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&Oslash;&nbsp;If thru bank telegraphic transfer, include <b><u>P250 /$ 6.50,</b></u> in your 
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;payment to cover for bank charges.</font><br><br>
            <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&Oslash;&nbsp;If thru SM Department Store Bills Payment Center,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;To facilitate identification of your payment,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;Please indicate this registration\billing reference number in the 
            &nbsp;&nbsp;&nbsp;&nbsp;payment slip form.
         </font>
       </td>
      </tr>
    </table>
    </div>
  </center>
</div>
<script>
  window.print();
</script>
</body>
</html>
