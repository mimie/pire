<?php

function date_standard($date){

   $date = date("F j,Y",strtotime($date));
   return $date;
}

?>
