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
  include '../notes/notes_functions.php';
  include '../packages/packagebill_functions.php';
  include '../billing_functions.php';
  include '../editbill_functions.php';
  include '../company_functions.php';

  $dbh = civicrmConnect();
  @$eventId = $_GET["event_id"];
  @$bir_no = $_GET["bir_no"];
  @$billing_no = $_GET["billing_no"];
  @$orgId = $_GET["orgId"];
  @$uid = $_GET["uid"];
  $generator = getGeneratorName($uid);

  $bill = getCurrentCompanyBillByEvent($orgId,$eventId,$bir_no,$billing_no);
  $ref_no = $bir_no != NULL ? "BS-".$bir_no."/".$billing_no : $billing_no;
  $address = getCompanyAddress($dbh,$orgId);
  $complete_address = $address["street_address"]." ".$address["city"];
  $nonvatable_type = $bill["nonvatable_type"];

  $participants = getCompanyBilledParticipants($dbh,$billing_no,$eventId);
  
  //update edit status of bill
  $stmt = civicrmDB("UPDATE billing_company SET edit_bill = '0' WHERE billing_no=?");
  $stmt->bindValue(1,$billing_no,PDO::PARAM_STR);
  $stmt->execute();
  $tin = getTin($orgId);

?>
<p class="myname"><?=$bill['organization_name']?></p>
<p class="myaddress"><?=wordwrap($complete_address,57,"<br>\n<font color='white'>",FALSE)?></font></p>
<p class="mytin"><?=$tin?></p>
<p class="lbltxn">Txn. No:</p>
<p class="myrefno"><?=$ref_no?></p>
<p class="mybilldate"><?=date("F j, Y",strtotime($bill['bill_date']))?></p>
<p class="myduedate"><?=date("F j, Y",strtotime($bill['start_date']))?></p>
<p class="myparticulars">
<?php
       echo $bill["event_name"]."<br/>";
       if($bill["start_date"] != $bill["end_date"]){
		$date_range = "On ".date("F j,Y",strtotime($bill["start_date"]))." to ".date("F j,Y",strtotime($bill["end_date"]));
       }

       else{
 		$date_range = "On ".date("F j,Y",strtotime($bill["start_date"]));
       }
       $location = formatEventLocation(getEventLocation($dbh,$eventId));
       $location = $location == NULL ? '' : "At ".$location;
?>
       <?=$date_range?></br>
       <?=$location?></br></br>
       &nbsp;&nbsp;&nbsp;Billing of the ff. participants:</br></br>
<?php
        $amounts = array();
	foreach($participants as $participant_id=>$field){
		$amounts[] = $field["fee_amount"];
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                echo $field["participant_name"]." / Participant No. ".$participant_id."</br>";
        }

?>
</p>
<p class="notes">
<?php
	$notes = getNoteById($dbh,$bill['notes_id']);
        if($notes != NULL){
	     echo "<b><font color='#0A0A2A'><i>Notes: </i>".wordwrap($notes['notes'],57,"<br>\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",TRUE)."</font></b>";
	     echo "</br>";
        }
?>
</p>
<p class="myamount">
</br></br></br></br></br></br>
<?php
	foreach($amounts as $fee){
                if($nonvatable_type != NULL){
                   $fee = round($fee/1.12,2);
                }
		echo number_format($fee,2)."</br>";
        }

?>
</p>
<p class="vatsales"><?=$subtotal = $nonvatable_type == NULL ? number_format($bill['subtotal'],2) : ''?></p>
<p class="vatexempt"><?=$subtotal = $nonvatable_type=='vat_exempt' ? number_format($bill['subtotal'],2) : ''?></p>
<p class="vatzero"><?=$subtotal = $nonvatable_type=='vat_zero' ? number_format($bill['subtotal'],2) : ''?></p>
<p class="vatamount"><?=number_format($bill['vat'],2)?></p>
<p class="totalamount">PHP&nbsp;<?=number_format($bill['total_amount'],2)?></p>
<p class="issuedby"><?=$generator?></p>

</body>

</html>
<script>
  window.print();
</script>
