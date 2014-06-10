<!--Print New Billing Form --!>

<html>

<head>

<link rel="stylesheet" type="text/css" href="css/check.css" media="screen" />
<title>Print Bill</title>
</head>
<style>
p.myname{
  position:fixed;
  top:65px;
  left:66px;
  font-size: 10pt;
  font-family: Calibri;
}

p.myaddress{
  position:fixed;
  top:70px;
  left:65px;
  font-size: 10pt;
  font-family: Calibri;
}

p.mytin{
  position:fixed;
  top:90px;
  left:65px;
  font-size: 10pt;
  font-family: Calibri;
}


p.lbltxn{
  position:fixed;
  top:63px;
  left:750px;
  font-size: 10pt;
  font-family: Calibri;
}


p.myrefno{
  position:fixed;
  top:63px;
  left:870px;
  font-size: 10pt;
  font-family: Calibri;
}


p.mybilldate{
  position:fixed;
  top:80px;
  left:870px;
  font-size: 10pt;
  font-family: Calibri;
}

p.myduedate{
  position:fixed;
  top:100px;
  left:870px;
  font-size: 10pt;
  font-family: Calibri;
}


p.myparticulars{
  position:fixed;
  top:150px;
  left:10px;
  font-size: 10pt;
  font-family: Calibri;
}

p.notes{
  position:fixed;
  top:500px;
  left:10px;
  font-size: 10pt;
  font-family: Calibri;
}



p.myamount{
  position:fixed;
  top:150px;
  left:870px;
  font-size: 10pt;
  font-family: Calibri;
}


p.vatsales{
  position:fixed;
  top:560px;
  left:870px;
  font-size: 10pt;
  font-family: Calibri;
}

p.vatexempt{
  position:fixed;
  top:600px;
  left:870px;
  font-size: 9pt;
  font-family: Calibri;
}
p.vatzero{
  position:fixed;
  top:630px;
  left:870px;
  font-size: 10pt;
  font-family: Calibri;
}

p.vatamount{
  position:fixed;
  top:660px;
  left:870px;
  font-size: 10pt;
  font-family: Calibri;
}

p.totalamount{
  position:fixed;
  top:690px;
  left:870px;
  font-size: 10pt;
  font-family: Calibri;
}

p.issuedby{
  position:fixed;
  top:720px;
  left:870px;
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


?>
<p class="myname"><?=$bill['sort_name']?></p>
<p class="myaddress"><?=$address?></p>
<p class="mytin">Tin</p>
<p class="lbltxn">Txn. No:</p>
<p class="myrefno"><?=$billing_no?>/<?=$bill['bir_no']?></p>
<p class="mybilldate"><?=date("F j, Y",strtotime($bill['bill_date']))?></p>
<p class="myduedate"><?=date("F j, Y",strtotime($bill['start_date']))?></p>
<p class="myparticulars">
   <?=$bill['event_name']?></br>
   On <?=date("F j, Y",strtotime($bill['start_date']))?> to <?=date("F j, Y",strtotime($bill['end_date']))?></br>
   <?=$location?>
</p>
<p class="notes">
<?php
	$notes = getNoteById($dbh,$bill['notes_id']);
        echo $notes['notes'];
?>
</p>
<p class="myamount"><?=number_format($bill['fee_amount'],2)?></p>
<p class="vatsales"><?=number_format($bill['subtotal'],2)?></p>
<p class="vatexempt">VatExempt</p>
<p class="vatzero">VatZero</p>
<p class="vatamount"><?=number_format($bill['vat'],2)?></p>
<p class="totalamount"><?=number_format($bill['fee_amount'],2)?></p>
<p class="issuedby"><?=$generator?></p>

<?php
include('myFunctions.php');
?>


</body>

</html>


<script>
 function printTkt(){
 //window.print();

}
</script>

