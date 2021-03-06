<!--Print New Billing Form --!>

<html>

<head>

<link rel="stylesheet" type="text/css" href="css/check.css" media="screen" />
<title>Print Bill</title>
</head>
<style>
p.myname{
  position:fixed;
  top:81px;
  left:30px;
  font-size: 10pt;
  font-family: Calibri;
}

p.myaddress{
  position:fixed;
  top:86px;
  left:30px;
  font-size: 10pt;
  font-family: Calibri;
}

p.mytin{
  position:fixed;
  top:106px;
  left:30px;
  font-size: 10pt;
  font-family: Calibri;
}


p.lbltxn{
  position:fixed;
  top:71px;
  left:365px;
  font-size: 10pt;
  font-family: Calibri;
}


p.myrefno{
  position:fixed;
  top:71px;
  left:427px;
  font-size: 10pt;
  font-family: Calibri;
}


p.mybilldate{
  position:fixed;
  top:88px;
  left:427px;
  font-size: 10pt;
  font-family: Calibri;
}

p.myduedate{
  position:fixed;
  top:108px;
  left:427px;
  font-size: 10pt;
  font-family: Calibri;
}


p.myparticulars{
  position:fixed;
  top:150px;
  left:1px;
  font-size: 10pt;
  font-family: Calibri;
}

p.notes{
  border-style: solid 1px;
  position:fixed;
  top:500px;
  left:10px;
  font-size: 10pt;
  font-family: Calibri;
}



p.myamount{
  position:fixed;
  top:150px;
  left:427px;
  font-size: 10pt;
  font-family: Calibri;
}


p.vatsales{
  position:fixed;
  top:578px;
  left:427px;
  font-size: 10pt;
  font-family: Calibri;
}

p.vatexempt{
  position:fixed;
  top:618px;
  left:427px;
  font-size: 9pt;
  font-family: Calibri;
}
p.vatzero{
  position:fixed;
  top:648px;
  left:427px;
  font-size: 10pt;
  font-family: Calibri;
}

p.vatamount{
  position:fixed;
  top:678px;
  left:427px;
  font-size: 10pt;
  font-family: Calibri;
}

p.totalamount{
  position:fixed;
  top:708px;
  left:427px;
  font-size: 10pt;
  font-family: Calibri;
}

p.issuedby{
  position:fixed;
  top:738px;
  left:427px;
  font-size: 10pt;
  font-family: Calibri;
}



</style>
<body onload="printTkt()">
<?php


  include '../pdo_conn.php';
  include '../login_functions.php';
  include '../bir_functions.php';
  include '../billing_functions.php';
  include '../notes/notes_functions.php';

  $dbh = civicrmConnect();
  @$eventId = $_GET["event_id"];

  @$uid = $_GET["uid"];
  $generator = getGeneratorName($uid);
  @$billing_no = $_GET["billing_no"];
  $bill = getBIRDetails($billing_no);
  $address = $bill['street_address']." ".$bill['city_address'];
  $location = formatEventLocation(getEventLocation($dbh,$eventId));
  $nonvatable_type = $bill['nonvatable_type'];
  $bill_subtotal = number_format($bill['subtotal'],2);

  $stmt = civicrmDB("UPDATE billing_details SET edit_bill = '0' WHERE billing_no=?");
  $stmt->bindValue(1,$billing_no,PDO::PARAM_STR);
  $stmt->execute();

?>
<p class="myname"><?=$bill['sort_name']?></p>
<p class="myaddress"><?=wordwrap($address,58,"<br>\n",TRUE)?></p>
<p class="mytin"></p>
<p class="lbltxn">Txn. No:</p>
<p class="myrefno"><?=$reference_no = $bill['bir_no'] == NULL ? $billing_no : "BS-".$bill['bir_no']."/".$billing_no?></p>
<p class="mybilldate"><?=date("F j, Y",strtotime($bill['bill_date']))?></p>
<p class="myduedate"><?=date("F j, Y",strtotime($bill['start_date']))?></p>
<p class="myparticulars">
  <?=$bill['event_name']?></br>
<?php

if($bill['start_date']==$bill['end_date']){
              $date_range = "On ".date("F j, Y",strtotime($bill['start_date']));
      }
      else{
  $date_range = "On ".date("F j, Y",strtotime($bill['start_date']))." to ".date("F j, Y",strtotime($bill['end_date']));
      }
      echo $date_range."</br>";
      $location = $location == NULL ? '' : $location;
      echo $location;

?>
</p>
<p class="notes">
<?php
	$notes = getNoteById($dbh,$bill['notes_id']);
     if($notes != NULL){
	     echo "<b><font color='#0A0A2A'><i>Notes: </i>".wordwrap($notes['notes'],94,"<br>\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",TRUE)."</font></b>";
	     echo "</br>";
     }
?>
</p>
<p class="myamount"></br></br><?=number_format($bill['fee_amount'],2)?></p>
<p class="vatsales"><?=$subtotal = $nonvatable_type == NULL ? $bill_subtotal : ''?></p>
<p class="vatexempt"><?=$subtotal = $nonvatable_type == 'vat_exempt' ? $bill_subtotal : ''?></p>
<p class="vatzero"><?=$subtotal = $nonvatable_type == 'vat_zero' ? $bill_subtotal : ''?></p>
<p class="vatamount"><?=number_format($bill['vat'],2)?></p>
<p class="totalamount"><?=number_format($bill['fee_amount'],2)?>&nbsp;PHP</p>
<p class="issuedby"><?=$generator?></p>

</body>

</html>
<script>
  window.print();
</script>
