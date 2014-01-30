<html>
<head>
<title>Company Billing With Vat</title>
<style>
#main{
  border: 3px solid #0000FF;
  width: 875px;
  height: 1050px;
  padding: 2px;
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
  include 'login_functions.php';
 
  $dbh = civicrmConnect();
  /**session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }**/

  @$companyBillingNo = $_GET["companyBillingRef"];
  @$eventId = $_GET["eventId"];
  @$companyId = $_GET["orgId"];

  $billingDate = getCompanyBillingDate($dbh,$companyId,$eventId);
  $billingDate = date("F j Y",strtotime($billingDate));

  $billingDetails = getCompanyBillingDetails($dbh,$companyBillingNo,$eventId);
  $orgName = $billingDetails["organization_name"];
  $totalAmount = $billingDetails["total_amount"];
  $currencyFormat = number_format($totalAmount,2);
  
  //$tax = round($totalAmount/9.3333,2);
  //$netVat = round($totalAmount - $tax, 2);

  $tax = number_format($billingDetails["vat"],2);
  $netVat = number_format($billingDetails["subtotal"],2);

  $eventDetails = getEventDetails($dbh,$eventId);
  $eventName = $eventDetails["event_name"];
  $eventEndDate = $eventDetails["end_date"];
  $eventEndDate = date("F j Y",strtotime($eventEndDate));
  $dueDate = $eventDetails["start_date"];
  $dueDate = date("F j Y", strtotime($dueDate));

  $locationDetails = getEventLocation($dbh,$eventId);
  $eventLocation = formatEventLocation($locationDetails);

  $billingParticipantDetails = getCompanyBillingParticipants($dbh,$companyBillingNo,$eventId);
  $billingAddress = getCompanyBillingAddress($dbh,$companyId);
  $street = $billingAddress["street"];
  $city = $billingAddress["city"];

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
     <table align="left">
      <tr>
         <td colspan="4"><b><font style="font-size:16px">BILLED TO:</font></b></td>
      </tr>
      <tr>
         <td width="20px"></td>
         <td width="500px"><font style="font-size:13px"><?=$orgName?></font></td>
         <td align="right" width="150px" style="border-right:2px solid black"><font style="font-size:13px"><b>BILLING NUMBER</b></font></td>
         <td width="132.28px"><font style="font-size:19px"><b><?=$companyBillingNo?></b></font></td>
      </tr>
      <tr>
         <td></td>
         <td><font style="font-size:13px"><?=$street?></font></td>
         <td align="right" style="border-right:2px solid black"><font style="font-size:13px"><b>BILLING DATE</b></font></td>
         <td><font style="font-size:13px"><?=$billingDate?></font></td>
      </tr>
      <tr>
         <td></td>
         <td><font style="font-size:13px"><?=$city?></font></td>
         <td align="right" style="border-right:2px solid black"><font style="font-size:13px"><b>DUE DATE</b></font></td>
         <td><font style="font-size:13px"><?=$dueDate?></font></td>
      </tr>
      <tr>
       <td colspan="4"><br></td>
      </tr>
     </table>
     <!--end of billed to-->
    </div>
    <!--<div id="billedTo">-->
     <!--particulars-->
     <table align="left" style="border-collapse:collapse;width:100%;">
      <tr>
        <td colspan="2" align="center" bgcolor="#D8D8D8" style="border:2px solid black;"><font style="font-size:13px"><b>PARTICULARS</b></font></td>
        <td width="195.78px" align="center" bgcolor="#D8D8D8" style="border:2px solid black"><font style="font-size:13px"><b>AMOUNT</b></font></td>
      </tr>   
      <tr>
        <!--THE PARTICIPANTS INCLUDED IN THE COMPANY BILLING -->
        <td colspan="2" height="90px" style="border-right:2px solid black;border-left:2px solid black;vertical-align:top;" align="left">
          <?=$eventName?><br>
          On&nbsp;<?=$dueDate?>&nbsp;to&nbsp;<?=$eventEndDate?><br>
          <?=$eventLocation?>
        </td>
        <td style='border-right:2px solid black;'></td>
      </tr>
      <tr>
       <td colspan='2' height='430px' style="border-right:2px solid black;border-left:2px solid black;vertical-align:top;">
        <?php
           echo "<div align = 'left'>&nbsp;&nbsp;Billing for the ff. participants:<br><br>";
           foreach($billingParticipantDetails as $participant => $details){
              $participantName = $details["participant_name"];
              $participantId = $details["participant_id"];
              $participantId = formatParticipantId($participantId);
              echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$participantName." / Participant No.-$participantId<br>";
            }
           echo "</div>";
         ?>
        </td>
         <!-- EACH PARTICIPANT FEE INCLUDED IN THE COMPANY BILLING -->
        <td style="border-right:2px solid black;vertical-align:top;" align="center"><br><br>
        <?php
           foreach($billingParticipantDetails as $participant => $details){
              $feeAmount = $details["fee_amount"];
              $feeAmount = number_format($feeAmount,2);
              echo "$feeAmount<br>";
           }
        ?>
        </td> 
      </tr>
      <tr>
        <td style="border:2px solid black;" rowspan="2" align="center">
         <font style="font-size:19px"><b><i>THANK YOU FOR YOUR BUSINESS!</b></i></font><br>
         <font style="font-size:13px"><b>(NOT VALID FOR INPUT TAX CLAIM)</b></font>
        </td>
        <td width="165px" style="border:2px solid black;" align="right" rowspan="2">SUBTOTAL<br>VAT - 12%</td>
        <td width="195.78px" height="26.84px" style="border:2px solid black;" align="center"><?=$netVat?></td>
      </tr>
      <tr>
        <td height="26.84px" style="border:2px solid black;" align="center"><?=$tax?></td>
      </tr>
      <tr>
        <td height="15px"></td>
        <td style="border:2px solid black;" align="center" bgcolor="#C8C8C8"><b><font style="font-size:12px;">TOTAL<br>AMOUNT DUE</b></td>
        <td width="195.78px" style="border-bottom:2px solid black;" rowspan="2" align="center" bgcolor="#D8D8D8"><?=$currencyFormat?>&nbsp;PHP</td>
      </tr>
     </table>

     <!--<div id = "billedTo">
      <table>
      <tr>
        <td colspan="2"><b><font style="font-size:13px;font-family:Arial">DIRECT ALL INQUIRIES TO:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            PAYMENT INSTRUCTION:</font></b>
        </td>
      </tr>
      <tr>
        <td colspan="2"><b><i><font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i></b>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         &Oslash;&nbsp;If by check, <font color="red"><b><u>should be</u></b></font> made payable to:</font>
        </td>
        <td align="center" width="195.78px" style="border:2px solid black;" bgcolor="#D8D8D8"><font style="font-size:11px"><b>TOTAL AMOUNT DUE</b></font></td>
      </tr>
      <tr>
       <td colspan="3"><font style="font-size:13px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(+632) 940-9554</font></td>
      </tr>
      <tr>
       <td colspan="3"><font style="font-size:13px;font-family:Arial">email: ar_finance@iia-p.org</font>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         <font style="font-size:22px"><b><i>Institute of Internal Auditors Philippines, Inc.</i></b></font>
       </td>
      </tr>
      <tr><td><br></td></tr>
      <tr>
       <td colspan="3"><font style="font-size:13px;font-family:Arial">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;
          &Oslash;&nbsp;If thru bank telegraphic transfer, include <b><u>P250 /$ 6.50,</b></u> in your payment to cover for bank charges.
        </font>
       </td>
      </tr>
      <tr><td></td></tr>
      <tr>
       <td colspan="3"><font style="font-size:13px;font-family:Arial">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;
          &Oslash;&nbsp;By direct deposit to:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Institute of Intenal Auditors Philippines, Inc.</b>
       </font>
       </td>
      </tr>
      <tr>
       <td colspan="3"><font style="font-size:13px;font-family:Arial">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <b>Acct No. 691-002-1745</b>
          </font>
       </td>
      <tr>
      <tr>
       <td colspan="3"><font style="font-size:13px;font-family:Arial">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <b>Banco de Oro - Rufino Branch</b>
          </font>
       </td>
      <tr>
      <tr>
       <td colspan="3"><font style="font-size:13px;font-family:Arial">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          Please indicate the BDO branch where you are transacting and present your <b>ORIGINAL</b>
          </font>
       </td>
      <tr>
      <tr>
       <td colspan="3"><font style="font-size:13px;font-family:Arial">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <b>COPY</b> of the bank validated deposit slip to claim your OFFICIAL RECEIPT.
          </font>
       </td>
      <tr>
     </table>
    </div>-->
   <br><table align='left'>
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
          <font style="font-size:15px"><b><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Institute of Internal Auditors Philippines, Inc.</i></b></font><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&Oslash;&nbsp;If thru bank telegraphic transfer, include <b><u>P250 /$ 6.50,</b></u> in your 
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;payment to cover for bank charges.</font><br>
            <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&Oslash;&nbsp;If thru SM Department Store Bills Payment Center,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;To facilitate identification of your payment,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;Please indicate this registration\billing reference number in the 
            &nbsp;&nbsp;&nbsp;&nbsp;payment slip form.
         </font>
       </td>
      </tr>
    </table>
  </center>
</div>
<script>
  window.print();
</script>
</body>
</html>
