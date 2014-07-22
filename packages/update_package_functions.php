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
		echo $error->getMessage();
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

        }

        catch(PDOException $error){
	      echo $error->getMessage();
        }
}

?>
