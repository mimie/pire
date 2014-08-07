<?php

function updateIndividualPackage($nonvatable_type,$amounts,$notes_id,$billing_no,$bir_no){

	foreach($amounts as $participant_id=>$fee_amount){

	   try{
	       $update_stmt = civicrmDB("UPDATE billing_details 
					 SET fee_amount = '$fee_amount',
					 bir_no = '$bir_no'
					 WHERE participant_id = '$participant_id'
					 AND billing_no = '$billing_no'
					 AND is_cancelled = '0'
				   ");
	       /*$update_stmt->bindValue(1,$fee_amount,PDO::PARAM_INT);
	       $update_stmt->bindValue(2,$bir_no,PDO::PARAM_STR);
	       $update_stmt->bindValue(3,$participant_id,PDO::PARAM_INT);
	       $update_stmt->bindValue(4,$billing_no,PDO::PARAM_STR);**/
	       $update_stmt->execute();
	     }
	   
	   catch(PDOException $error){
	        echo "<div id='confirmation'><img src='images/error.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Error in updating billing_details</br>"
                . $error->getMessage()."</div>";
	   }

	}

	$select_stmt = civicrmDB("SELECT fee_amount FROM billing_details WHERE billing_no=? AND is_cancelled='0'");
	$select_stmt->bindValue(1,$billing_no,PDO::PARAM_STR);
	$select_stmt->execute();

        $participant_amounts = $select_stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = 0;

        foreach($participant_amounts as $field){

               $total = $total + $field['fee_amount'];
        }

        $total = $nonvatable_type == NULL ? $total : round($total/1.12,2);
        $subtotal = $nonvatable_type == NULL ? round($total/1.12,2) : $total;
        $vat = $total - $subtotal;

        try{

        $update_package = civicrmDB("UPDATE billing_details_package
                                     SET total_amount = ?,
                                     subtotal = ?,
                                     vat = ?,
                                     bir_no = ?,
                                     notes_id = ?,
                                     nonvatable_type = ?
                                     WHERE billing_no = ?
                                     AND edit_bill = '1'
                                     AND is_cancelled = '0'
                                    ");
        $update_package->bindValue(1,$total,PDO::PARAM_INT);
        $update_package->bindValue(2,$subtotal,PDO::PARAM_INT);
        $update_package->bindValue(3,$vat,PDO::PARAM_INT);
        $update_package->bindValue(4,$bir_no,PDO::PARAM_STR);
        $update_package->bindValue(5,$notes_id,PDO::PARAM_INT);
        $update_package->bindValue(6,$nonvatable_type,PDO::PARAM_STR);
        $update_package->bindValue(7,$billing_no,PDO::PARAM_STR);
        $update_package->execute();
        echo "<div id='confirmation'><img src='images/confirm.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Successfully updated bill.</div>";

        }

        catch(PDOException $error){
	        echo "<div id='confirmation'><img src='images/error.png' style='float:left;' height='28' width='28'>&nbsp;&nbsp;Error in updating billing_details_package</br>"
                . $error->getMessage()."</div>";
        }
}

function getAdditionalCompanyParticipantsByPackageId($packageId,$employer_id){

        $stmt = civicrmDB("SELECT cp.id as participant_id,bp.pid as package_id,cc.id as contact_id, cc.sort_name, cc.organization_name, 
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
                           AND cc.employer_id = ?
                           AND cps.label <> 'status'
                           AND cp.fee_amount <> '0'
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
         $stmt->bindValue(2,$employer_id,PDO::PARAM_INT);
         $stmt->execute();
         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

         return $result;
}


?>
