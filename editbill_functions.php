<?php

function getParticipantWithSameAmount($eventId,$amount,$employer_id){

	$stmt = civicrmDB("SELECT cp.id as participant_id, cc.sort_name, cc.employer_id,cp.fee_amount
                           FROM civicrm_participant cp, civicrm_contact cc, civicrm_value_billing_17 as billing_type
                           WHERE event_id = ?
                           AND cp.fee_amount = ?
                           AND cp.status_id <> '4'
                           AND cc.employer_id = ?
                           AND cc.id = cp.contact_id
                           AND billing_type.billing_45 = 'Individual'
                           AND billing_type.entity_id = cp.id
                           AND NOT EXISTS(SELECT bd.participant_id FROM billing_details bd WHERE bd.event_id=? AND bd.participant_id = cp.id)
                           ORDER BY cc.sort_name;
                          ");
        $stmt->bindValue(1,$eventId,PDO::PARAM_INT);
        $stmt->bindValue(2,$amount,PDO::PARAM_INT);
        $stmt->bindValue(3,$employer_id,PDO::PARAM_INT);
        $stmt->bindValue(4,$eventId,PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
}

function getParticipantWithSameCompany($eventId,$employer_id){

	$stmt = civicrmDB("SELECT cp.id as participant_id, cc.sort_name, cc.employer_id,cp.fee_amount
                           FROM civicrm_participant cp, civicrm_contact cc, civicrm_value_billing_17 as billing_type
                           WHERE event_id = ?
                           AND cp.status_id <> '4'
                           AND cp.fee_amount <> '0'
                           AND cc.employer_id = ?
                           AND cc.id = cp.contact_id
                           AND billing_type.billing_45 = 'Individual'
                           AND billing_type.entity_id = cp.id
                           AND NOT EXISTS(SELECT bd.participant_id FROM billing_details bd WHERE bd.event_id=? AND bd.participant_id = cp.id)
                           ORDER BY cc.sort_name;
                          ");
        $stmt->bindValue(1,$eventId,PDO::PARAM_INT);
        $stmt->bindValue(2,$employer_id,PDO::PARAM_INT);
        $stmt->bindValue(3,$eventId,PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
}

/*
 * $info - details of the participant
 */
function updateParticipant($new_birno,$bir_no,array $info,$is_vat,$nonvatable_type){

   $new_birno = $new_birno == $bir_no ? $bir_no : $new_birno;

   try{
	$stmt = civicrmDB("UPDATE billing_details
                           SET participant_id = ?,
                           contact_id = ?,
                           participant_name = ?,
                           email = ?,
                           bill_address = ?,
                           organization_name = ?,
                           org_contact_id = ?,
                           billing_no = ?,
                           participant_status = ?,
                           generator_uid = ?,
                           fee_amount = ?,
                           subtotal = ?,
                           vat = ?,
                           nonvatable_type = ?,
                           edit_bill = '1',
                           bir_no = ?
                           WHERE bir_no = ?
                          ");

        $billing_no = $info['event_type']."-".date("y")."-".formatBillingNo($info['participant_id']);
        $fee_amount = $is_vat == 1 ? $info['fee_amount'] : round($info['fee_amount']/1.12,2);
        $subtotal = $is_vat == 0 ? $fee_amount : $fee_amount/1.12;
        $subtotal = round($subtotal,2);
        $tax = $fee_amount - $subtotal;

        $address = $info['street_address']." ".$info['city_address'];
        $stmt->bindValue(1,$info['participant_id'],PDO::PARAM_INT);
        $stmt->bindValue(2,$info['contact_id'],PDO::PARAM_INT);
        $stmt->bindValue(3,$info['sort_name'],PDO::PARAM_STR);
        $stmt->bindValue(4,$info['email'],PDO::PARAM_STR);
        $stmt->bindValue(5,$address,PDO::PARAM_STR);
        $stmt->bindValue(6,$info['organization_name'],PDO::PARAM_STR);
        $stmt->bindValue(7,$info['org_contact_id'],PDO::PARAM_INT);
        $stmt->bindValue(8,$billing_no,PDO::PARAM_STR);
        $stmt->bindValue(9,$info['participant_status'],PDO::PARAM_STR);
        $stmt->bindValue(10,$_GET['uid'],PDO::PARAM_INT);
        $stmt->bindValue(11,$fee_amount,PDO::PARAM_INT);
        $stmt->bindValue(12,$subtotal,PDO::PARAM_INT);
        $stmt->bindValue(13,$tax,PDO::PARAM_INT);
        $stmt->bindValue(14,$nonvatable_type,PDO::PARAM_STR);
        $stmt->bindValue(15,$new_birno,PDO::PARAM_STR);
        $stmt->bindValue(16,$bir_no,PDO::PARAM_STR);

        $stmt->execute();
    }

    catch(PDOException $e){

	echo $e->getMessage();
    }
}


function insertBillingHistory(array $hist_details){

	try{

		$stmt = civicrmDB("INSERT INTO billing_history(billing_no,action_taken,generator_uid,bir_no)
                                   VALUES(?,?,?,?)
                                  ");
                $stmt->bindValue(1,$hist_details['billing_no'],PDO::PARAM_STR);
                $stmt->bindValue(2,$hist_details['action'],PDO::PARAM_STR);
                $stmt->bindValue(3,$_GET['uid'],PDO::PARAM_INT);
                $stmt->bindValue(4,$hist_details['bir_no'],PDO::PARAM_STR);
                $stmt->execute();
	   }

        catch(PDOException $e){
                echo $e->getCode();
        }
}

function updateAmountByBIRNo($bir_no,$amount){

	try{

                $subtotal = round($amount/1.12);
                $tax = $amount-$subtotal;
        	$stmt = civicrmDB("UPDATE billing_details SET fee_amount = ?, subtotal = ?, vat = ?
                                   WHERE bir_no = ?");
                $stmt->bindValue(1,$amount,PDO::PARAM_INT);
                $stmt->bindValue(2,$subtotal,PDO::PARAM_INT);
                $stmt->bindValue(3,$tax,PDO::PARAM_INT);
                $stmt->bindValue(4,$bir_no,PDO::PARAM_STR);
                $stmt->execute();
        }

        catch(PDOException $e){
        	echo $e->getMessage();
        }
}

function getCompanyNameByOrgId($orgId){

	$stmt = civicrmDB("SELECT organization_name FROM civicrm_contact
                           WHERE civicrm_contact.id = ?");
	$stmt->bindValue(1,$orgId,PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
        $orgName = $result["organization_name"];

	return $orgName;
}

function getCurrentCompanyBillByEvent($orgId,$eventId,$bir_no,$billing_no){

	try{

	      if($bir_no == NULL){
		$stmt = civicrmDB("SELECT cbid, cc.organization_name,ce.start_date,ce.end_date,ce.title as event_name,bc.billing_no, bc.bir_no, bc.total_amount, bc.subtotal,bc.vat, bc.bill_date, bc.edit_bill,bc.is_cancelled,bc.notes_id,bc.nonvatable_type,bn.notes
				   FROM civicrm_contact cc, civicrm_event ce,billing_company bc
                                   LEFT JOIN billing_notes bn ON bn.notes_id = bc.notes_id
				   WHERE bc.event_id = ?
				   AND bc.org_contact_id = ?
                                   AND bc.billing_no = ?
                                   AND bc.event_id = ce.id
                                   AND bc.org_contact_id = cc.id
                                   AND cc.contact_type = 'Organization'
                                   AND cc.is_deleted = 0
                                  ");
		$stmt->bindValue(1,$eventId,PDO::PARAM_INT);
		$stmt->bindValue(2,$orgId,PDO::PARAM_INT);
                $stmt->bindValue(3,$billing_no,PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
              }

              else{

		$stmt = civicrmDB("SELECT cbid, cc.organization_name,ce.end_date,ce.start_date,ce.title as event_name,bc.billing_no, bc.bir_no, bc.total_amount, bc.subtotal,bc.vat, bc.bill_date, bc.edit_bill,bc.is_cancelled,bc.notes_id,bc.nonvatable_type,bn.notes
				   FROM civicrm_contact cc,civicrm_event ce,billing_company bc
                                   LEFT JOIN billing_notes bn ON bn.notes_id = bc.notes_id
				   WHERE bc.event_id = ?
				   AND bc.org_contact_id = ?
                                   AND bc.bir_no = ?
                                   AND ce.id = bc.event_id
                                   AND bc.org_contact_id = cc.id
                                   AND cc.contact_type = 'Organization'
                                   AND cc.is_deleted = 0
                                  ");
		$stmt->bindValue(1,$eventId,PDO::PARAM_INT);
		$stmt->bindValue(2,$orgId,PDO::PARAM_INT);
                $stmt->bindValue(3,$bir_no,PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
            }

		return $result;
        }catch(PDOException $error){
         	$error->getMessage();
         }
}

function updateIncludedNameByParticipantId($participant_id,$bir_no,$new_amount,$new_birno,$billing_no){


	try{
                $new_birno = $bir_no == $new_birno ? $bir_no : $new_birno;
		$stmt = civicrmDB("UPDATE billing_details SET fee_amount=?,vat='0',subtotal='0',vat='0',bir_no=?
                                   WHERE billing_no=?
                                   AND participant_id=?
                                   AND billing_type = 'Company'
                                   ");
                $stmt->bindValue(1,$new_amount,PDO::PARAM_INT);
                $stmt->bindValue(2,$new_birno,PDO::PARAM_STR);
                $stmt->bindValue(3,$billing_no,PDO::PARAM_STR);
                $stmt->bindValue(4,$participant_id,PDO::PARAM_INT);
                $stmt->execute();

        }catch(PDOException $error){
		echo $error->getMessage();
        }
}

function updateCompanyBillByBillingNo($bir_no,$is_vat,$new_amount,$nonvatable_type,$notes_id,$new_birno,$billing_no){

        $new_amount = $is_vat == 1 ? $new_amount : round($new_amount/1.12,2);
        $subtotal = $is_vat == 1 ? $new_amount/1.12 : $new_amount;
        $vat = $new_amount - $subtotal;

        $new_birno = $bir_no == $new_birno ? $bir_no : $new_birno;

	try{
		$stmt = civicrmDB("UPDATE billing_company SET total_amount=?,subtotal=?,vat=?,nonvatable_type=?,notes_id=?,bir_no=?
                                   WHERE billing_no=?");
                $stmt->bindValue(1,$new_amount,PDO::PARAM_INT);
                $stmt->bindValue(2,$subtotal,PDO::PARAM_INT);
                $stmt->bindValue(3,$vat,PDO::PARAM_INT);
                $stmt->bindValue(4,$nonvatable_type,PDO::PARAM_STR);
                $stmt->bindValue(5,$notes_id,PDO::PARAM_INT);
                $stmt->bindValue(6,$new_birno,PDO::PARAM_STR);
                $stmt->bindValue(7,$billing_no,PDO::PARAM_STR);
                $stmt->execute();
        echo "<div id='confirmation'><img src='images/confirm.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Successfully updated company bill.</div>";
        }catch(PDOException $error){
		echo "<div id='error'><img src='images/error.png' style='float:left;' height='28' width='28'>".$error->getMessage()."</div>";
         }
}

function cancelCompanyBill($bir_no){

	try{
		$stmt = civicrmDB("UPDATE billing_company SET is_cancelled = '1',edit_bill = '0' WHERE bir_no=?");
                $stmt->bindValue(1,$bir_no,PDO::PARAM_STR);
                $stmt->execute();

                $cancel_stmt = civicrmDB("UPDATE billing_details SET is_cancelled = '1',edit_bill = '0' WHERE bir_no=?");
                $cancel_stmt->bindValue(1,$bir_no,PDO::PARAM_STR);
                $cancel_stmt->execute();
	}
        catch(PDOException $error){
		echo $error->getMessage();
        }
}

function updateIndividualParticipant(array $details){

        $fee_amount = $details['withVat'] == 1 ? $details['amount'] : round($details['amount']/1.12,2);
	$subtotal = $details['withVat'] == 1 ? $fee_amount/1.12 : $fee_amount;
	$vat = $details['amount'] - round($subtotal,2);
	$subtotal = number_format($subtotal,2,'.','');
	$vat = number_format($vat,2,'.','');

	try{
		$stmt = civicrmDB("UPDATE billing_details
			                 SET bir_no=?,fee_amount=?,subtotal=?,vat=?,nonvatable_type=?,notes_id=?
											 WHERE billing_no=?");
		$stmt->bindValue(1,$details['new_birno'],PDO::PARAM_STR);
		$stmt->bindValue(2,$fee_amount,PDO::PARAM_INT);
		$stmt->bindValue(3,$subtotal,PDO::PARAM_INT);
		$stmt->bindValue(4,$vat,PDO::PARAM_INT);
		$stmt->bindValue(5,$details['nonvatable_type'],PDO::PARAM_STR);
		$stmt->bindValue(6,$details['notes_id'],PDO::PARAM_INT);
		$stmt->bindValue(7,$details['billing_no'],PDO::PARAM_STR);
		$stmt->execute();
	}

	catch(PDOException $error){
		echo $error->getMessage();
	}
}

function getTotalAmountByBillingNo($billing_no){

	$stmt = civicrmDB("SELECT fee_amount FROM billing_details WHERE billing_no=? AND billing_type='Company'");
        $stmt->bindValue(1,$billing_no,PDO::PARAM_STR);
        $stmt->execute();
        $amounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        foreach($amounts as $key=>$field){
        	$total = $total + $field["fee_amount"];
        }

        return $total;
}

?>
