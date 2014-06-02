<!--Print New Billing Form --!>

<html>

<head>

<link rel="stylesheet" type="text/css" href="css/check.css" media="screen" />
<title>Print Check</title>
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


</style>
<body onload="printTkt()">
<?php

include('connectDb.php');

$sql="";


?>
<p class="myname">Name</p>
<p class="myaddress">Address</p>
<p class="mytin">Tin</p>
<p class="lbltxn">Txn. No:</p>
<p class="myrefno">RefNo</p>
<p class="mybilldate">Bill date</p>
<p class="myduedate">Due Date</p>
<p class="myparticulars">Particulars<br>Particulars</p>
<p class="myamount">Amount</p>
<p class="vatsales">VatSales</p>
<p class="vatexempt">VatExempt</p>
<p class="vatzero">VatZero</p>
<p class="vatamount">VatAmount</p>
<p class="totalamount">TotalAmount</p>

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

