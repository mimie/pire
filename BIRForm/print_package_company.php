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
<body>
<?php

  include '../pdo_conn.php';
  include '../login_functions.php';
  include '../bir_functions.php';
  include '../notes/notes_functions.php';
  include '../packages/packagebill_functions.php';
  include '../packages/package_functions.php';
  include '../billing_functions.php';
  include '../shared_functions.php';
  include '../company_functions.php';

  $dbh = civicrmConnect();
  @$eventId = $_GET["event_id"];

  @$uid = $_GET["uid"];
  $generator = getGeneratorName($uid);
  @$billing_no = $_GET["billing_no"];
  $bill = getBillDetailsByBillingNo($billing_no);

  $orgId = $bill['contact_id'];
  $address = getCompanyAddress($dbh,$orgId);
  $complete_address = $address['street_address']." ".$address['city_address'];
  $tin = getTin($orgId);
  $package_name = getPackageName($bill['pid']);

  $nonvatable_type = $bill['nonvatable_type'];
  $bir_no = $bill['bir_no'];
  $ref_no = $bir_no == NULL ? $billing_no : "BS-".$bir_no."/".$billing_no;
  $infobill = getEventBillDetailsByBillingNo($billing_no);
  $firstkey = array_keys($infobill)[0];
  $due_date = date_standard($infobill[$firstkey][0]['start_date']);

  //update edit bill
  $update_stmt = civicrmDB("UPDATE billing_details_package SET edit_bill= '0' WHERE billing_no=?");
  $update_stmt->bindValue(1,$billing_no,PDO::PARAM_STR);
  $update_stmt->execute();

?>
<p class="myname"><?=$bill['sort_name']?></p>
<p class="myaddress"><?=wrapAddress($complete_address)?></font></p>
<p class="mytin"><?=$tin?></p>
<p class="lbltxn">Txn. No:</p>
<p class="myrefno"><?=$ref_no?></p>
<p class="mybilldate"><?=date_standard($bill['bill_date'])?></p>
<p class="myduedate"><?=$due_date?></p>
<p class="myparticulars">
  <?php
      echo $package_name."</br></br>";
  ?>
       &nbsp;&nbsp;&nbsp;Billing of the ff. participants:</br></br>
<?php
      $amounts = array();
      foreach($infobill as $contact_id=>$details){
              $fee_amount = 0;
              $name = getContactName($contact_id);
              echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
              echo $name."</br>";
	      foreach($details as $key=>$field){
              		$fee_amount = $fee_amount + $field["fee_amount"];
	      }

              $amounts[] = $fee_amount;
       }
?>
</p>
<p class="notes">
  <?php
     
     if($bill["notes"] != NULL){
	     echo "<b><font color='#0A0A2A'><i>Notes: </i>".wordwrap($bill['notes'],45,"<br>\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",TRUE)."</font></b>";
     }
  ?>
</p>
<p class="myamount">
  </br></br></br></br>
  <?php
      foreach($amounts as $display_amount){
        $display_amount = $nonvatable_type == NULL ? $display_amount : round(($display_amount/1.12),2);
        echo number_format($display_amount,2)."</br>";
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
