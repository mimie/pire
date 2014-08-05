<?php

function getIndividualParticipantsByEventId($eventId){

	$stmt = civicrmDB("SELECT cc.id as contact_id, cp.status_id,cc.sort_name,cc.organization_name, cp.fee_amount,cp.id as participant_id,
                     billing_type.billing_45 as bill_type,cps.label as status,
                     bd. street_address__company__3 as street_address, city__company__5 as city_address
                     FROM civicrm_participant cp, civicrm_value_billing_17 as billing_type, civicrm_participant_status_type cps, civicrm_contact cc
                     LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
                     WHERE cp.contact_id = cc.id
                     AND billing_type.entity_id = cp.id
                     AND cp.event_id = ?
                     AND billing_type.billing_45 = 'Individual'
                     AND cps.id = cp.status_id
                     AND cc.is_deleted = '0'
                     ORDER BY sort_name");
	$stmt->bindValue(1,$eventId,PDO::PARAM_INT);
        $stmt->execute();

	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $result;

}

function getCompanyParticipantsByEventId($eventId){

	$stmt = civicrmDB("SELECT cc.employer_id,cc.id as contact_id, cp.status_id,cc.sort_name,cc.organization_name, cp.fee_amount,cp.id as participant_id,
                     billing_type.billing_45 as bill_type,cps.label as status,
                     bd. street_address__company__3 as street_address, city__company__5 as city_address
                     FROM civicrm_participant cp, civicrm_value_billing_17 as billing_type, civicrm_participant_status_type cps, civicrm_contact cc
                     LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
                     WHERE cp.contact_id = cc.id
                     AND billing_type.entity_id = cp.id
                     AND cp.event_id = ?
                     AND billing_type.billing_45 = 'Company'
                     AND cps.id = cp.status_id
                     AND cp.fee_amount <> 0
                     AND cp.status_id NOT IN(15,17,7,4)
                     AND cc.is_deleted = '0'
                     AND cc.contact_type = 'Individual'
                     ORDER BY sort_name");
	$stmt->bindValue(1,$eventId,PDO::PARAM_INT);
        $stmt->execute();

	$result = $stmt->fetchAll(PDO::FETCH_GROUP);

	return $result;

}

function getOrganizationParticipantsByEventId($eventId){

	$stmt = civicrmDB("SELECT cc.id as employer_id,cp.contact_id,cp.status_id,cc.sort_name,cc.organization_name, cp.fee_amount,cp.id as participant_id,
                     billing_type.billing_45 as bill_type,cps.label as status,
                     bd. street_address__company__3 as street_address, city__company__5 as city_address
                     FROM civicrm_participant cp, civicrm_value_billing_17 as billing_type, civicrm_participant_status_type cps, civicrm_contact cc
                     LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
                     WHERE cp.contact_id = cc.id
                     AND billing_type.entity_id = cp.id
                     AND cp.event_id = ?
                     AND billing_type.billing_45 = 'Company'
                     AND cps.id = cp.status_id
                     AND cp.fee_amount <> 0
                     AND cp.status_id NOT IN(15,17,7,4)
                     AND cc.is_deleted = '0'
                     AND cc.contact_type = 'Organization'
                     ORDER BY sort_name");
	$stmt->bindValue(1,$eventId,PDO::PARAM_INT);
        $stmt->execute();

	$result = $stmt->fetchAll(PDO::FETCH_GROUP);

	return $result;

}


function getCompanyParticipantsByOrgId($eventId,$orgId){

	$stmt = civicrmDB("SELECT cp.id as participant_id,cc.employer_id,cc.id as contact_id, cp.status_id,cc.sort_name,cc.organization_name, cp.fee_amount,
			   billing_type.billing_45 as bill_type,cps.label as status,
			   bd. street_address__company__3 as street_address, city__company__5 as city_address
			   FROM civicrm_participant cp, civicrm_value_billing_17 as billing_type, civicrm_participant_status_type cps, civicrm_contact cc
			   LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
			   WHERE cp.contact_id = cc.id
			   AND billing_type.entity_id = cp.id
			   AND cp.event_id = ?
			   AND billing_type.billing_45 = 'Company'
			   AND cps.id = cp.status_id
			   AND cp.fee_amount <> 0
			   AND cp.status_id NOT IN(15,17,7,4)
			   AND cc.is_deleted = '0'
			   AND cc.employer_id = ?
			   ORDER BY sort_name");
	$stmt->bindValue(1,$eventId,PDO::PARAM_INT);
	$stmt->bindValue(2,$orgId,PDO::PARAM_INT);
	$stmt->execute();

	$result = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);

	return $result;

}

function getIndividualBilledParticipantsByEventId($eventId){

	$stmt = civicrmDB("SELECT participant_id,generated_bill,post_bill,billing_no,bir_no,bill_date,amount_paid,fee_amount,subtotal,vat,notes_id FROM billing_details
                           WHERE event_id = ?
                           AND billing_type = 'Individual'
                          ");
        $stmt->bindValue(1,$eventId,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $participants = array();
        foreach($result as $key=>$field){
        	$participant_id = $field["participant_id"];
                unset($field["participant_id"]);
                $participants[$participant_id] = $field;
        }

       return $participants;
}

function checkIndividualBillGenerated($participant_id,$eventId){

	$stmt = civicrmDB("SELECT * FROM billing_details WHERE participant_id=? AND event_id=?");
        $stmt->bindValue(1,$participant_id,PDO::PARAM_INT);
        $stmt->bindValue(2,$eventId,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;

}

function getBIRDetails($billing_no){

	$stmt = civicrmDB("SELECT cc.sort_name,ce.title as event_name,ce.start_date,ce.end_date,bill.bir_no,bill.fee_amount,
                           bill.subtotal,bill.vat,bill.bill_date,bill.nonvatable_type,bill.notes_id,bd. street_address__company__3 as street_address,
                           city__company__5 as city_address
                           FROM billing_details bill,billing_notes bn,civicrm_event ce,civicrm_contact cc
                           LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
                           WHERE bill.billing_no = ?
                           AND bill.event_id = ce.id
                           AND bill.contact_id = cc.id
                           ");
	$stmt->bindValue(1,$billing_no,PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
}

/*
 *get participant details to be inserted in the billing_details table
 */
function getInfoByParticipantId($participant_id){

    try{

	$stmt = civicrmDB("SELECT cp.contact_id, cp.event_id, cp.id as participant_id,cov.label as event_type,ce.title as event_name, cc.sort_name,
                           em.email,cc.organization_name, bd.street_address__company__3 as street_address, bd.city__company__5 as city_address,
                           cc.employer_id as org_contact_id, cp.fee_amount, cps.label as participant_status
                           FROM civicrm_participant cp, civicrm_event ce, civicrm_option_value cov, civicrm_participant_status_type cps,civicrm_contact cc
                           LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
                           LEFT JOIN civicrm_email em ON em.contact_id = bd.entity_id AND em.is_primary = '1'
                           WHERE cp.event_id = ce.id
                           AND cov.value = ce.event_type_id
                           AND cov.option_group_id = '14'
                           AND cp.contact_id = cc.id
                           AND cps.id = cp.status_id
                           AND cp.id = ?");
        $stmt->bindValue(1,$participant_id,PDO::PARAM_INT);
	$stmt->execute();

	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	return $result;
    }catch(PDOException $e){
         echo $e->getMessage();
     }

}

function getInfoByBillingNo($billing_id){

     try{

	$stmt = civicrmDB("SELECT cp.contact_id, cp.id as participant_id,cp.event_id, cov.label as event_type,ce.title as event_name, cc.sort_name,cc.employer_id,
                           em.email,cc.organization_name, bd.street_address__company__3 as street_address, bd.city__company__5 as city_address,
                           cc.employer_id as org_contact_id, bill.bir_no,bill.edit_bill,bill.notes_id,bn.notes,bill.subtotal,bill.vat,bill.fee_amount as current_amount,bill.nonvatable_type,cp.fee_amount as civicrm_amount, bill.is_cancelled,cps.label as participant_status
                           FROM civicrm_participant cp, civicrm_event ce, civicrm_option_value cov, civicrm_participant_status_type cps,civicrm_contact cc
                           LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
                           LEFT JOIN civicrm_email em ON em.contact_id = bd.entity_id AND is_primary = '1'
                           LEFT JOIN billing_details bill ON bill.contact_id = em.contact_id
                           LEFT JOIN billing_notes bn on bn.notes_id = bill.notes_id
                           WHERE cp.event_id = ce.id
                           AND cov.value = ce.event_type_id
                           AND cov.option_group_id = '14'
                           AND cp.contact_id = cc.id
                           AND cps.id = cp.status_id
                           AND bill.participant_id = cp.id
                           AND bill.id = ? ORDER BY bill.id DESC");
         $stmt->bindValue(1,$billing_id,PDO::PARAM_INT);
         $stmt->execute();
         $result = $stmt->fetch(PDO::FETCH_ASSOC);

         return $result;
     }catch(PDOException $e){
           echo $e->getMessage();
      }


}

function checkDuplicateIndividualBIRNo($bir_no){

   try{

	$stmt = civicrmDB("SELECT * FROM billing_details WHERE bir_no=? AND (billing_type = 'Individual' OR billing_type='Company')");
        $stmt->bindValue(1,$bir_no,PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $count = count($result);

        return $count;

      }catch(PDOException $error){
            echo $error->getMessage();
       }
}

/*
 * this will generate an individual bill
 * @participant_id
 * @details_ - bs_no,vatable,notes_id,nonvatable_type,billing_type,billing_id
 *
 */
function generateIndividualBill($participant_id,array $details){

    $generator_uid = $_GET["uid"];
    $participant_id = intval($participant_id);
    $info = getInfoByParticipantId($participant_id);
    $event_type = $info["event_type"];
    $orgId = $info["org_contact_id"];

    $bs_no = $details['bs_no'];
    $vatable = $details['vatable'];
    $notes_id = $details['notes_id'];
    $nonvatable_type = $details['nonvatable_type'];
    $billing_type = $details['billing_type'];
    $billing_id = $details['billing_id'];

    $is_birExist = $bs_no == NULL ? 0 : checkDuplicateIndividualBIRNo($bs_no);

    if($is_birExist == 0){
		try{

			$stmt = civicrmDB("INSERT INTO billing_details (participant_id,contact_id,event_id,event_type,event_name,participant_name,email, bill_address,organization_name,
									org_contact_id,billing_type,fee_amount,subtotal,vat,billing_no,generated_bill,view_bill,participant_status,
									generator_uid,bir_no,notes_id,nonvatable_type)
					  VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

			$bill_address = $info["street_address"]." ".$info["city_address"];

                        if($billing_type == 'Individual'){
                        	$fee_amount = $vatable == 1 ? $info["fee_amount"] : round($info["fee_amount"]/1.12,2);
				$subtotal = $vatable == 1 ? round($fee_amount/1.12,2) : $fee_amount;
				$vat = $fee_amount - $subtotal;
                         }                         

                        else{
                        	$fee_amount = $info["fee_amount"];
                                $subtotal = $fee_amount;
                                $vat = 0;
                        }

			$billing_no = $billing_type == 'Individual' ? $event_type."-".date("y")."-".formatBillingNo($participant_id) : $billing_id;

			$stmt->bindValue(1,$participant_id,PDO::PARAM_INT);
			$stmt->bindValue(2,$info["contact_id"],PDO::PARAM_INT);
			$stmt->bindValue(3,$info["event_id"],PDO::PARAM_INT);
			$stmt->bindValue(4,$event_type,PDO::PARAM_STR);
			$stmt->bindValue(5,$info["event_name"],PDO::PARAM_STR);
			$stmt->bindValue(6,$info["sort_name"],PDO::PARAM_STR);
			$stmt->bindValue(7,$info["email"],PDO::PARAM_STR);
			$stmt->bindValue(8,$bill_address,PDO::PARAM_STR);
			$stmt->bindValue(9,$info["organization_name"],PDO::PARAM_STR);
			$stmt->bindValue(10,$orgId,PDO::PARAM_INT);
			$stmt->bindValue(11,$billing_type,PDO::PARAM_STR);
			$stmt->bindValue(12,$fee_amount,PDO::PARAM_INT);
			$stmt->bindValue(13,$subtotal,PDO::PARAM_INT);
			$stmt->bindValue(14,$vat,PDO::PARAM_INT);
			$stmt->bindValue(15,$billing_no,PDO::PARAM_STR);
			$stmt->bindValue(16,1,PDO::PARAM_INT);
			$stmt->bindValue(17,1,PDO::PARAM_INT);
			$stmt->bindValue(18,$info["participant_status"],PDO::PARAM_STR);
			$stmt->bindValue(19,$generator_uid,PDO::PARAM_INT);
			$stmt->bindValue(20,$bs_no,PDO::PARAM_STR);
			$stmt->bindValue(21,$notes_id,PDO::PARAM_INT);
			$stmt->bindValue(22,$nonvatable_type,PDO::PARAM_STR);

			$stmt->execute();
     			echo "<div id='confirmation'><img src='images/confirm.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Successfully generated bill.</div>";
	       }

	       catch(PDOException $error){
			echo "<div id='confirmation'><img src='images/error.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;".$error->getMessage()."</div>";
	       }
	}

        else{
             echo "<div id='confirmation'><img src='images/error.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;You have entered an existing BS No. Billing cannot be generated.</div>";
        }
}

/*
 * Returns an executed sql to generate package bill
 * @contact_id - contact id of the participant in civicrm
 * @details_ - an of participant details
 * @bs_no - bir no of the bill to be generated
 * @vatable_ - value 1,0 for vatable and non-vatable bill
 * @notes_id - notes of the bill
 * @package_id - package of the bill
 */
function generatePackageBill($contact_id,$details,$bs_no,$vatable,$notes_id,$package_id,$nonvatable_type,$billing_type){

	$generator_uid = $_GET["uid"];
        $total_amount = 0;
        $billing_no = '';

        if($billing_type == 'Company'){
		$orgId = $contact_id;
        }


        foreach($details as $key=>$info){

		try{
		       $stmt = civicrmDB("INSERT INTO billing_details (participant_id,contact_id,event_id,event_type,event_name,participant_name,email, bill_address,organization_name,
					  org_contact_id,billing_type,fee_amount,billing_no,generated_bill,view_bill,participant_status,generator_uid,bir_no,notes_id,is_package,nonvatable_type)
					   VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

			$bill_address = $info["street_address"]." ".$info["city_address"];
			$total_amount = $total_amount + $info["fee_amount"];

			$billing_no = $key == 0 ? $info["event_type"]."-".date("y")."-".formatBillingNo($info["participant_id"]) : $billing_no;
                        $contact_id = $billing_type == 'Individual' ? $contact_id : $info["contact_id"];

			$stmt->bindValue(1,$info["participant_id"],PDO::PARAM_INT);
			$stmt->bindValue(2,$contact_id,PDO::PARAM_INT);
			$stmt->bindValue(3,$info["event_id"],PDO::PARAM_INT);
			$stmt->bindValue(4,$info["event_type"],PDO::PARAM_STR);
			$stmt->bindValue(5,$info["event_name"],PDO::PARAM_STR);
			$stmt->bindValue(6,$info["sort_name"],PDO::PARAM_STR);
			$stmt->bindValue(7,"",PDO::PARAM_STR);
			$stmt->bindValue(8,$bill_address,PDO::PARAM_STR);
			$stmt->bindValue(9,$info["organization_name"],PDO::PARAM_STR);
			$stmt->bindValue(10,$info["employer_id"],PDO::PARAM_INT);
			$stmt->bindValue(11,$billing_type,PDO::PARAM_STR);
			$stmt->bindValue(12,$info["fee_amount"],PDO::PARAM_INT);
			$stmt->bindValue(13,$billing_no,PDO::PARAM_STR);
			$stmt->bindValue(14,1,PDO::PARAM_INT);
			$stmt->bindValue(15,1,PDO::PARAM_INT);
			$stmt->bindValue(16,$info["status"],PDO::PARAM_STR);
			$stmt->bindValue(17,$generator_uid,PDO::PARAM_INT);
			$stmt->bindValue(18,$bs_no,PDO::PARAM_STR);
			$stmt->bindValue(19,$notes_id,PDO::PARAM_INT);
			$stmt->bindValue(20,$package_id,PDO::PARAM_INT);
			$stmt->bindValue(21,$nonvatable_type,PDO::PARAM_STR);

			$stmt->execute();
		  }

		  catch (PDOException $error) {
			echo "<div id='confirmation'><img src='images/error.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Error in inserting billing_details</br>"
                             . $error->getMessage()."</div>";
		  }
        }

        $total_amount = $nonvatable_type == NULL ? $total_amount : round(($total_amount/1.12),2);
	$subtotal = $vatable == 1 ? round(($total_amount/1.12),2) : $total_amount;
	$vat = $total_amount - $subtotal;

        if($billing_type == 'Company'){
             $contact_id = $orgId;
        }

        try{
		$sql_bir = civicrmDB("INSERT INTO billing_details_package(bir_no,contact_id,subtotal,vat,total_amount,pid,notes_id,billing_no,generator_uid,nonvatable_type,billing_type) 
                                      VALUES(:bs_no,:contact_id,:subtotal,:vat,:total_amount,:package_id,:notes_id,:billing_no,:generator_uid,:nonvatable_type,:billing_type)");
		$sql_bir->bindParam(':bs_no',$bs_no);
		$sql_bir->bindParam(':contact_id',$contact_id);
		$sql_bir->bindParam(':subtotal',$subtotal);
		$sql_bir->bindParam(':vat',$vat);
		$sql_bir->bindParam(':total_amount',$total_amount);
		$sql_bir->bindParam(':package_id',$package_id);
		$sql_bir->bindParam(':notes_id',$notes_id);
		$sql_bir->bindParam(':billing_no',$billing_no);
                $sql_bir->bindParam(':generator_uid',$generator_uid);
                $sql_bir->bindParam(':nonvatable_type',$nonvatable_type);
                $sql_bir->bindParam(':billing_type',$billing_type);
		$sql_bir->execute();
		echo "<div id='confirmation'><img src='images/confirm.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Successfully generated bill.</div>";
        }

        catch (PDOException $error){
		echo "<div id='confirmation'><img src='images/error.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Error in inserting billing_details_package</br>"
                     . $error->getMessage()."</div>";

        }

}


/*
 * Retursn a formatted 6 digit-number BS No.
 * @bs_no = numeric bs. no to be formatted
 */
function formatBSNo($bs_no){

  $count = strlen($bs_no);
  if($count < 6){
     $countZeros = 6-$count;
     $zeros = "";
     for($i=1; $i <= $countZeros; $i++){
        $zeros = $zeros."0";
     }

   return $zeros.$bs_no;

  }

  else{
    return $bs_no;
  }
}

function getParticipantsPerPackage($packageId){

	$stmt = civicrmDB("SELECT cp.contact_id,cc.id as civicrm_id,pac.pid as package_id,pac.package_name,cp.status_id,cc.sort_name,cc.organization_name, cc.employer_id,cp.fee_amount,cp.id as participant_id,cp.event_id,
                           ce.title as event_name,cov.label as event_type,billing_type.billing_45 as bill_type,cps.label as status,
                           bd. street_address__company__3 as street_address, city__company__5 as city_address
                           FROM billing_package pac,billing_package_events pac_events, civicrm_event ce, civicrm_option_value cov,
                           civicrm_participant cp, civicrm_value_billing_17 as billing_type, civicrm_participant_status_type cps, civicrm_contact cc
                           LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
                           WHERE cp.contact_id = cc.id
                           AND billing_type.entity_id = cp.id
                           AND pac.pid = ?
                           AND pac.pid = pac_events.pid
                           AND cp.event_id = pac_events.event_id
                           AND cp.event_id = ce.id
		           AND ce.event_type_id = cov.value
		           AND cov.option_group_id = '14'
                           AND billing_type.billing_45 = 'Individual'
                           AND cps.id = cp.status_id
                           AND cc.is_deleted = '0'
                           AND NOT EXISTS (SELECT participant_id FROM billing_details WHERE billing_details.participant_id = cp.id)
                           ORDER BY sort_name,cp.event_id");
         $stmt->bindValue(1,$packageId,PDO::PARAM_INT);
         $stmt->execute();
         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	 return $result;
}

function getParticipantsPackageCompanyByPackageId($packageId){

	$stmt = civicrmDB("SELECT cc.employer_id,cp.id as participant_id,bp.pid as package_id,cc.id as contact_id, cc.sort_name, cc.organization_name, 
                           bp.package_name, cp.status_id, cps.label as status, cp.fee_amount,cp.event_id,
                           ce.title as event_name, cov.label as event_type, billing_type.billing_45 as bill_type,
                           bd. street_address__company__3 as street_address, city__company__5 as city_address
		           FROM  civicrm_participant cp, billing_package_events bpe, billing_package bp, civicrm_participant_status_type cps, 
                           civicrm_event ce, civicrm_option_value cov, civicrm_value_billing_17 as billing_type,civicrm_contact cc
			   LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
			   WHERE cc.id = cp.contact_id
			   AND cp.event_id = bpe.event_id
			   AND bpe.pid = bp.pid
			   AND bpe.pid = ?
			   AND cp.status_id = cps.id
			   AND ce.id = cp.event_id
		           AND cov.option_group_id = '14'
			   AND ce.event_type_id = cov.value
			   AND billing_type.billing_45 = 'Company'
			   AND billing_type.entity_id = cp.id
			   AND cc.is_deleted = '0'
                           AND NOT EXISTS (SELECT participant_id FROM billing_details WHERE billing_details.participant_id = cp.id)
                           ORDER BY sort_name,cp.event_id
");
         $stmt->bindValue(1,$packageId,PDO::PARAM_INT);
         $stmt->execute();
         $result = $stmt->fetchAll(PDO::FETCH_GROUP);

	 return $result;
}


function searchParticipantsPerPackage($packageId,$name){

	$stmt = civicrmDB("SELECT cc.id as contact_id,pac.pid as package_id,pac.package_name,cp.status_id,cc.sort_name,cc.organization_name,
                           cc.employer_id,cp.fee_amount,cp.id as participant_id,cp.event_id,
			   ce.title as event_name,cov.label as event_type,billing_type.billing_45 as bill_type,cps.label as status,
			   bd. street_address__company__3 as street_address, city__company__5 as city_address
			   FROM billing_package pac,billing_package_events pac_events, civicrm_event ce, civicrm_option_value cov,
			   civicrm_participant cp, civicrm_value_billing_17 as billing_type, civicrm_participant_status_type cps, civicrm_contact cc
			   LEFT JOIN civicrm_value_business_data_1 bd ON bd.entity_id = cc.id
			   WHERE cp.contact_id = cc.id
			   AND billing_type.entity_id = cp.id
			   AND pac.pid = ?
			   AND pac.pid = pac_events.pid
			   AND cp.event_id = pac_events.event_id
			   AND cp.event_id = ce.id
			   AND ce.event_type_id = cov.value
			   AND cov.option_group_id = '14'
			   AND billing_type.billing_45 = 'Individual'
			   AND cps.id = cp.status_id
			   AND cc.is_deleted = '0'
			   AND cc.sort_name LIKE ?
                           AND NOT EXISTS (SELECT participant_id FROM billing_details WHERE billing_details.participant_id = cp.id)
			   ORDER BY sort_name,cp.event_id");

	$stmt->bindValue(1,$packageId,PDO::PARAM_INT);
	$stmt->bindValue(2,"%".$name."%",PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_GROUP);

	return $result;
}

function getCompanyNames(){

	$stmt = civicrmDB("SELECT id, display_name FROM civicrm_contact WHERE contact_type='Organization' AND is_deleted = 0");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return $result;
}

function generateCompanyBill(array $bill){

	try{
		$stmt = civicrmDB("INSERT INTO billing_company
				  (event_id,event_type,event_name,org_contact_id,organization_name,bill_address,billing_no,total_amount,subtotal,vat,bir_no,notes_id,generator_uid,nonvatable_type)
				  VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$stmt->bindValue(1,$bill['event_id'],PDO::PARAM_INT);
		$stmt->bindValue(2,$bill['event_type'],PDO::PARAM_STR);
		$stmt->bindValue(3,$bill['event_name'],PDO::PARAM_STR);
		$stmt->bindValue(4,$bill['org_id'],PDO::PARAM_INT);
		$stmt->bindValue(5,$bill['org_name'],PDO::PARAM_STR);
		$stmt->bindValue(6,$bill['address'],PDO::PARAM_STR);
		$stmt->bindValue(7,$bill['billing_no'],PDO::PARAM_STR);
		$stmt->bindValue(8,$bill['total_amount'],PDO::PARAM_INT);
		$stmt->bindValue(9,$bill['subtotal'],PDO::PARAM_INT);
		$stmt->bindValue(10,$bill['vat'],PDO::PARAM_INT);
		$stmt->bindValue(11,$bill['bir_no'],PDO::PARAM_STR);
		$stmt->bindValue(12,$bill['notes_id'],PDO::PARAM_INT);
		$stmt->bindValue(13,$bill['generator_uid'],PDO::PARAM_INT);
		$stmt->bindValue(14,$bill['nonvatable_type'],PDO::PARAM_INT);
		$stmt->execute();
	}catch(PDOException $error){
		echo $error->getMessage();
         }
}

function updateAmountCancelledBill($billing_no,$participant_id){

	$stmt = civicrmDB("UPDATE billing_details
                           SET fee_amount='0',subtotal='0',vat='0',is_cancelled='1'
                           WHERE billing_no = ?
                ");
        $stmt->bindValue(1,$billing_no,PDO::PARAM_STR);
     	$stmt->execute();
}

function getTIN($orgId){

	$stmt = civicrmDB("SELECT tin_no__54 as tin FROM civicrm_value_tin_no__20 WHERE entity_id=?");
        $stmt->bindValue(1,$orgId,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

	return $result["tin"];
}
?>
