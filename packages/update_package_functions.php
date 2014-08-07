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

        $stmt = civicrmDB("SELECT cp.id as participant_id,bp.pid as package_id,cc.id as contact_id, cc.sort_name, cc.organization_name, cc.employer_id,
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
         $result = $stmt->fetchAll(PDO::FETCH_UNIQUE);

         return $result;
}

function updatePackageAdditionalParticipants(array $info,$participant_id,$bir_no,$billing_no){

	try{
		$stmt = civicrmDB("INSERT INTO billing_details(participant_id,contact_id,event_id,event_type,event_name,
                                   participant_name,bill_address,organization_name,org_contact_id,billing_type,fee_amount,subtotal,vat,billing_no,bir_no)
                                   VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
                                  ");
                $address = $info['street_address']." ".$info['city'];
                $stmt->bindValue(1,$participant_id,PDO::PARAM_INT);
                $stmt->bindValue(2,$info['contact_id'],PDO::PARAM_INT);
                $stmt->bindValue(3,$info['event_id'],PDO::PARAM_INT);
                $stmt->bindValue(4,$info['event_type'],PDO::PARAM_STR);
                $stmt->bindValue(5,$info['event_name'],PDO::PARAM_STR);
                $stmt->bindValue(6,$info['sort_name'],PDO::PARAM_STR);
                $stmt->bindValue(7,$address,PDO::PARAM_STR);
                $stmt->bindValue(8,$info['organization_name'],PDO::PARAM_STR);
                $stmt->bindValue(9,$info['employer_id'],PDO::PARAM_INT);
                $stmt->bindValue(10,$info['billing_type'],PDO::PARAM_STR);
                $stmt->bindValue(11,$info['fee_amount'],PDO::PARAM_INT);
                $stmt->bindValue(12,0,PDO::PARAM_INT);
                $stmt->bindValue(13,0,PDO::PARAM_INT);
                $stmt->bindValue(14,$billing_no,PDO::PARAM_STR);
                $stmt->bindValue(15,$bir_no,PDO::PARAM_STR);
                $stmt->execute();
	}

        catch(PDOException $error){

        	echo "Error in inserting billing_details : ".$error->getMessage();
        }

}

function updateTotalPackageAmount($billing_id,$billing_no,$nonvatable_type){

	try{
	    $stmt = civicrmDB("SELECT fee_amount FROM billing_details WHERE billing_no=? AND fee_amount <> '0'");
            $stmt->bindValue(1,$billing_no,PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = 0;

            foreach($result as $key=>$info){
		$total = $total + $info['fee_amount'];
            }
        }

        catch(PDOException $error){
	    echo "Error in selecting billing_details : ".$error->getMessage();
        }

        try{

           $total = $nonvatable_type == 'vat_zero' || $nonvatable_type == 'vat_exempt' ? $total/1.12 : $total;
           $total = number_format((float)$total, 2, '.', '');
           $subtotal = $nonvatable_type == 'vat_zero' || $nonvatable_type == 'vat_exempt' ? $total : $total/1.12;
           $subtotal = number_format((float)$subtotal, 2, '.', '');
           $vat = $total - $subtotal;

           $stmt = civicrmDB("UPDATE billing_details_package
                              SET total_amount=?,subtotal=?,vat=?
                              WHERE bid=? AND billing_no=?");
           $stmt->bindValue(1,$total,PDO::PARAM_INT);
           $stmt->bindValue(2,$subtotal,PDO::PARAM_INT);
           $stmt->bindValue(3,$vat,PDO::PARAM_INT);
           $stmt->bindValue(4,$billing_id,PDO::PARAM_INT);
           $stmt->bindValue(5,$billing_no,PDO::PARAM_STR);
           $stmt->execute();
        }

        catch(PDOException $error){
            echo "Error in updating billing_details_package : ".$error->getMessage();          
        }
}


?>
