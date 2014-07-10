<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content="Microsoft Excel 12">
<link rel=File-List
href="IIAP%20Billing%20Form%20(rev2_2014%20ATP)_files/filelist.xml">
<link rel="stylesheet" type="text/css" href="newBill.css">
</head>

<body>
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

?>

<div id="IIAP Billing Form (rev2_2014 ATP)_2552" align=center
x:publishsource="Excel">

<table border=0 cellpadding=0 cellspacing=0 width=979 class=xl655352552
 style='border-collapse:collapse;table-layout:fixed;width:735pt'>
 <col class=xl655352552 width=64 style='width:48pt'>
 <col class=xl655352552 width=19 style='mso-width-source:userset;mso-width-alt:
 694;width:14pt'>
 <col class=xl655352552 width=81 style='mso-width-source:userset;mso-width-alt:
 2962;width:61pt'>
 <col class=xl655352552 width=19 style='mso-width-source:userset;mso-width-alt:
 694;width:14pt'>
 <col class=xl655352552 width=125 style='mso-width-source:userset;mso-width-alt:
 4571;width:94pt'>
 <col class=xl655352552 width=90 style='mso-width-source:userset;mso-width-alt:
 3291;width:68pt'>
 <col class=xl655352552 width=71 style='mso-width-source:userset;mso-width-alt:
 2596;width:53pt'>
 <col class=xl655352552 width=90 style='mso-width-source:userset;mso-width-alt:
 3291;width:68pt'>
 <col class=xl655352552 width=12 style='mso-width-source:userset;mso-width-alt:
 438;width:9pt'>
 <col class=xl655352552 width=159 style='mso-width-source:userset;mso-width-alt:
 5814;width:119pt'>
 <col class=xl655352552 width=166 style='mso-width-source:userset;mso-width-alt:
 6070;width:125pt'>
 <col class=xl655352552 width=19 style='mso-width-source:userset;mso-width-alt:
 694;width:14pt'>
 <col class=xl655352552 width=64 style='width:48pt'>
 <tr height=2 style='mso-height-source:userset;height:2.1pt'>
  <td height=2 width=64 style='height:2.1pt;width:48pt' align=left valign=top>
  <span style='mso-ignore:vglayout;
  position:absolute;z-index:1;margin-left:63px;margin-top:0px;width:16px;
  height:9px'><img width=16 height=9
  src="IIAP%20Billing%20Form%20(rev2_2014%20ATP)_files/IIAP%20Billing%20Form%20(rev2_2014%20ATP)_2552_image002.gif"
  v:shapes="HideTemplatePointer"></span><![endif]><span style='mso-ignore:vglayout2'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td height=2 class=xl655352552 width=64 style='height:2.1pt;width:48pt'></td>
   </tr>
  </table>
  </span></td>
  <td class=xl655352552 width=19 style='width:14pt'><a name="RANGE!B1:L50"></a></td>
  <td class=xl655352552 width=81 style='width:61pt'></td>
  <td class=xl655352552 width=19 style='width:14pt'></td>
  <td class=xl655352552 width=125 style='width:94pt'></td>
  <td class=xl655352552 width=90 style='width:68pt'></td>
  <td class=xl655352552 width=71 style='width:53pt'></td>
  <td class=xl655352552 width=90 style='width:68pt'></td>
  <td class=xl655352552 width=12 style='width:9pt'></td>
  <td class=xl655352552 width=159 style='width:119pt'></td>
  <td class=xl655352552 width=166 style='width:125pt'></td>
  <td class=xl655352552 width=19 style='width:14pt'></td>
  <td class=xl655352552 width=64 style='width:48pt'></td>
 </tr>
 <tr height=8 style='mso-height-source:userset;height:6.0pt'>
  <td height=8 class=xl655352552 style='height:6.0pt'></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=30 style='mso-height-source:userset;height:23.1pt'>
  <td height=30 class=xl655352552 style='height:23.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=6 height=30 width=476 style='height:23.1pt;width:358pt'
	  align=left valign=top>
	  
	  
   
   
   <![if !vml]><span style='mso-ignore:vglayout;
  position:absolute;z-index:2;margin-left:2px;margin-top:2px;width:50px;
  height:44px'><img width=50 height=44
  src="IIAP%20Billing%20Form%20(rev2_2014%20ATP)_files/IIAP%20Billing%20Form%20(rev2_2014%20ATP)_2552_image004.gif"
  alt=IIA v:shapes="Picture_x0020_2"></span><![endif]><span style='mso-ignore:
  vglayout2'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td class=xl1672552 style='height:23.1;width:358'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Institute of Internal Auditors Philippines Inc. 
    </td>
   </tr>
  </table>
  </span></td>
  <td class=xl962552></td>
  <td class=xl972552></td>
  <td rowspan=2 class=xl1512552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=18 style='mso-height-source:userset;height:14.1pt'>
  <td height=18 class=xl655352552 style='height:14.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=6 class=xl1662552><span style='mso-spacerun:yes'> </span>Unit 702
  Corporate Center, 139 Valero St., Salcedo Village, Makati City 1227</td>
  <td class=xl962552></td>
  <td class=xl972552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=20 style='mso-height-source:userset;height:15.0pt'>
  <td height=20 class=xl655352552 style='height:15.0pt'></td>
  <td class=xl655352552></td>
  <td colspan=9 class=xl1652552>TIN No. 001-772-403-000<span
  style='mso-spacerun:yes'>  </span>:<span style='mso-spacerun:yes'>  </span><font
  class="font62552"></font><font class="font52552"> (+632) 940-9551 /
  940-9554<span style='mso-spacerun:yes'>   </span>:<span
  style='mso-spacerun:yes'>  </span></font><font class="font72552"></font><font
  class="font52552"> Fax (+632) 325-0414<span style='mso-spacerun:yes'> 
  </span>: www.iia-p.org</font></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=20 style='mso-height-source:userset;height:15.0pt'>
  <td height=20 class=xl655352552 style='height:15.0pt'></td>
  <td class=xl655352552></td>
  <td class=xl982552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl982552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td colspan=2 rowspan=2 class=xl1522552>BILLING STATEMENT</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=18 style='mso-height-source:userset;height:14.1pt'>
  <td height=18 class=xl655352552 style='height:14.1pt'></td>
  <td class=xl655352552></td>
  <td class=xl1002552 colspan=2>BILLED TO:</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=24 style='mso-height-source:userset;height:18.0pt'>
  <td height=24 class=xl655352552 style='height:18.0pt'></td>
  <td class=xl655352552></td>
  <td class=xl1012552>Name</td>
  <td class=xl992552>:</td>
  <td colspan=5 class=xl1632552>&nbsp;<?=$bill['sort_name']?></td>
  <td class=xl1022552>REFERENCE NO.</td>
  <td class=xl1242552><?=$reference_no = $bill['bir_no'] == NULL ? $billing_no : "BS-".$bill['bir_no']."/".$billing_no?></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=24 style='mso-height-source:userset;height:18.0pt'>
  <td height=24 class=xl655352552 style='height:18.0pt'></td>
  <td class=xl655352552></td>
  <td class=xl1012552>Address</td>
  <td class=xl992552>:</td>
  <td colspan=5 class=xl1632552>&nbsp;<?=$address?></td>
  <td class=xl1022552>BILLING DATE</td>
  <td class=xl1242552>&nbsp;<?=date("F j,Y",strtotime($bill['bill_date']))?></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=24 style='mso-height-source:userset;height:18.0pt'>
  <td height=24 class=xl655352552 style='height:18.0pt'></td>
  <td class=xl655352552></td>
  <td class=xl1012552>TIN#</td>
  <td class=xl992552>:</td>
  <td colspan=5 class=xl1632552>&nbsp;</td>
  <td class=xl1022552>DUE DATE</td>
  <td class=xl1242552>&nbsp;<?=date("F j, Y",strtotime($bill['start_date']))?></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=8 style='mso-height-source:userset;height:6.0pt'>
  <td height=8 class=xl655352552 style='height:6.0pt'></td>
  <td class=xl655352552></td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl655352552></td>
  <td rowspan=2 class=xl1532552 width=64 style='width:48pt'></td>
 </tr>
 <tr height=24 style='mso-height-source:userset;height:18.0pt'>
  <td height=24 class=xl655352552 style='height:18.0pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1542552>PARTICULARS</td>
  <td class=xl1032552>AMOUNT</td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1582552 style='border-right:.5pt solid black'>
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
  </td>
  <td class=xl1272552><?=number_format($bill['fee_amount'],2)?></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'></td>
  <td class=xl1282552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'></td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>
  <?php
     $notes = getNoteById($dbh,$bill['notes_id']);
     if($notes != NULL){
             echo "<div align='center'>";
	     echo "<div class='notes'>";
	     echo "<b><font color='#0A0A2A'><i>Notes: </i>".wordwrap($notes['notes'],67,"<br>\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",TRUE)."</font></b>";
	     echo "</div></br></div>";
     }
  ?>
  </td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=8 class=xl1572552 style='border-right:.5pt solid black'>&nbsp;

  </td>
  <td class=xl1282552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.95pt'>
  <td height=21 class=xl655352552 style='height:15.95pt'></td>
  <td class=xl655352552></td>
  <td class=xl1042552 colspan=3>PAYMENT OPTIONS &amp; INSTRUCTION/S:</td>
  <td class=xl1052552>&nbsp;</td>
  <td class=xl1052552>&nbsp;</td>
  <td class=xl1052552>&nbsp;</td>
  <td class=xl1062552>&nbsp;</td>
  <td rowspan=2 class=xl1562552>VAT-ABLE SALES</td>
  <td rowspan=2 class=xl1422552 style='border-bottom:.5pt solid black'>&nbsp;<?=$subtotal = $nonvatable_type == NULL ? $bill_subtotal : ''?></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.95pt'>
  <td height=21 class=xl655352552 style='height:15.95pt'></td>
  <td class=xl655352552></td>
  <td class=xl1072552 colspan=3>&#9679; CHECKS, should be made payable to:<span
  style='mso-spacerun:yes'> </span></td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl1092552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.95pt'>
  <td height=21 class=xl655352552 style='height:15.95pt'></td>
  <td class=xl655352552></td>
  <td colspan=6 class=xl1382552>INSTITUTE OF INTERNAL AUDITORS PHILIPPINES INC.</td>
  <td class=xl1102552>&nbsp;</td>
  <td rowspan=2 class=xl1402552>VAT-EXEMPT SALES</td>
  <td rowspan=2 class=xl1422552 style='border-bottom:.5pt solid black;border-top:none'><?=$subtotal = $nonvatable_type == 'vat_exempt' ? $bill_subtotal : ''?> </td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.95pt'>
  <td height=21 class=xl655352552 style='height:15.95pt'></td>
  <td class=xl655352552></td>
  <td colspan=6 class=xl1442552 width=476 style='width:358pt'>Ø BANK
  TELEGRAPHIC TRANSFERS, include P250/$ 6.50, to cover for bank charges</td>
  <td class=xl1102552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.95pt'>
  <td height=21 class=xl655352552 style='height:15.95pt'></td>
  <td class=xl655352552></td>
  <td colspan=6 class=xl1442552 width=476 style='width:358pt'>Ø SM BILLS
  PAYMENT CENTER, pls. indicate BS reference number in the payment slip.</td>
  <td class=xl1102552>&nbsp;</td>
  <td rowspan=2 class=xl1402552>VAT-ZERO RATED SALES</td>
  <td rowspan=2 class=xl1422552 style='border-bottom:.5pt solid black;border-top:none'><?=$subtotal = $nonvatable_type == 'vat_zero' ? $bill_subtotal : ''?></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.95pt'>
  <td height=21 class=xl655352552 style='height:15.95pt'></td>
  <td class=xl655352552></td>
  <td colspan=7 rowspan=3 class=xl1442552 width=488 style='border-right:.5pt solid black;
  border-bottom:.5pt solid black;width:367pt'>Ø BIR 2306/2307 certificate
  should be attached along with the payment, if withholding tax deduction is
  made. Otherwise, this entire billing shall be considered unpaid and may be
  subject to 2% interest for every 30-days of delay.</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.95pt'>
  <td height=21 class=xl655352552 style='height:15.95pt'></td>
  <td class=xl655352552></td>
  <td rowspan=2 class=xl1402552 style='border-bottom:.5pt solid black'>VAT-AMOUNT</td>
  <td rowspan=2 class=xl1422552 style='border-bottom:.5pt solid black;
  border-top:none'>&nbsp;<?=number_format($bill['vat'],2)?></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.95pt'>
  <td height=21 class=xl655352552 style='height:15.95pt'></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=33 style='mso-height-source:userset;height:24.95pt'>
  <td height=33 class=xl655352552 style='height:24.95pt'></td>
  <td class=xl655352552></td>
  <td colspan=6 class=xl1312552>THANK YOU FOR YOUR BUSINESS!</td>
  <td class=xl1112552>&nbsp;</td>
  <td class=xl1122552 style='border-top:none'>TOTAL AMOUNT DUE</td>
  <td class=xl1122552 style='border-top:none'>PHP&nbsp;<?=number_format($bill['fee_amount'],2)?></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=18 style='mso-height-source:userset;height:14.1pt'>
  <td height=18 class=xl655352552 style='height:14.1pt'></td>
  <td class=xl655352552></td>
  <td class=xl1132552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>Issued by:</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 class=xl655352552 style='height:16.5pt'></td>
  <td class=xl655352552></td>
  <td align=left valign=top>
	  
   
   <![if !vml]><span style='mso-ignore:vglayout;position:absolute;z-index:3;margin-left:0px;margin-top:1px;width:439px;height:120px'>

<?php if($bir_no != NULL) { ?>
<img width=439 height=120 src="IIAP%20Billing%20Form%20(rev2_2014%20ATP)_files/IIAP%20Billing%20Form%20(rev2_2014%20ATP)_2552_image006.gif" v:shapes="TextBox_x0020_3">
<?php } ?>

</span><![endif]><span style='mso-ignore:vglayout2'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td height=22 class=xl1142552 width=81 style='height:16.5pt;width:61pt'>&nbsp;</td>
   </tr>
  </table>
  </span></td>
  <td class=xl1152552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl1162552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl1172552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td colspan=2 class=xl1632552>&nbsp;<b><font size='3'></br></br><?=$generator?></font></b></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 class=xl655352552 style='height:16.5pt'></td>
  <td class=xl655352552></td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl1182552>&nbsp;</td>
  <td class=xl1152552>&nbsp;</td>
  <td class=xl1192552>&nbsp;</td>
  <td class=xl1202552>&nbsp;</td>
  <td colspan=2 class=xl1642552>&nbsp;</td>
  <td class=xl1212552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 class=xl655352552 style='height:16.5pt'></td>
  <td class=xl655352552></td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl1222552>&nbsp;</td>
  <td class=xl1232552>&nbsp;</td>
  <td colspan=2 class=xl1332552>&nbsp;</td>
  <td class=xl1212552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td rowspan=2 class=xl1342552><?=$bir_label = $bill['bir_no'] == NULL ? '' : 'BS No.'?></td>
  <td rowspan=2 class=xl1352552><?=$bill['bir_no']?></td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl655352552></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl655352552 style='height:20.1pt'></td>
  <td class=xl655352552></td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl1142552>&nbsp;</td>
  <td class=xl655352552></td>
 </tr>
 <tr height=18 style='mso-height-source:userset;height:14.1pt'>
  <td height=18 class=xl655352552 style='height:14.1pt'></td>
  <td class=xl655352552></td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl992552>&nbsp;</td>
  <td class=xl1182552>&nbsp;</td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl655352552></td>
 </tr>
 <tr height=18 style='mso-height-source:userset;height:14.1pt'>
  <td height=18 class=xl655352552 style='height:14.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=9 class=xl1362552><?=$disclaimer = $bill['bir_no'] == NULL ? '' : "THIS DOCUMENT IS NOT VALID FOR CLAIMING INPUT TAXES"?></td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl655352552></td>
 </tr>
 <tr height=18 style='mso-height-source:userset;height:14.1pt'>
  <td height=18 class=xl655352552 style='height:14.1pt'></td>
  <td class=xl655352552></td>
  <td colspan=9 class=xl1372552><?=$disclaimer = $bill['bir_no'] == NULL ? '' : "THIS BILLING STATEMENT SHALL BE VALID FOR (5) YEARS FROM THE DATE OF ATP"?></td>
  <td class=xl1082552>&nbsp;</td>
  <td class=xl655352552></td>
 </tr>
 <tr height=19 style='height:14.25pt'>
  <td height=19 class=xl655352552 style='height:14.25pt'></td>
  <td class=xl655352552></td>
  <td class=xl1182552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <tr height=19 style='height:14.25pt'>
  <td height=19 class=xl655352552 style='height:14.25pt'></td>
  <td class=xl655352552></td>
  <td class=xl1182552>&nbsp;</td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td colspan=6 rowspan=2 class=xl1302552 width=517 style='width:388pt'>&nbsp;</td>
  <td class=xl655352552></td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl655352552 style='height:12.75pt'></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
  <td class=xl655352552></td>
 </tr>
 <![if supportMisalignedColumns]>
 <tr height=0 style='display:none'>
  <td width=64 style='width:48pt'></td>
  <td width=19 style='width:14pt'></td>
  <td width=81 style='width:61pt'></td>
  <td width=19 style='width:14pt'></td>
  <td width=125 style='width:94pt'></td>
  <td width=90 style='width:68pt'></td>
  <td width=71 style='width:53pt'></td>
  <td width=90 style='width:68pt'></td>
  <td width=12 style='width:9pt'></td>
  <td width=159 style='width:119pt'></td>
  <td width=166 style='width:125pt'></td>
  <td width=19 style='width:14pt'></td>
  <td width=64 style='width:48pt'></td>
 </tr>
 <![endif]>
</table>

</div>


<!----------------------------->
<!--END OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD-->
<!----------------------------->
</body>

</html>

