<html>
<head>
<title>Manage CIA Events</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../billingStyle.css">
<link rel="stylesheet" type="text/css" href="../menu.css">
 <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
 <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<script>
$(function() {
  $( "#confirmation" ).dialog({
    resizable: false,
    width: 500,
    modal: true,
    buttons: {
       "OK": function(){
           $( this ).dialog("close");
       }
    }
    
  });
})
</script>
<style>
#package
{
  text-align:center;
  padding: 20px 20px 20px 20px;
}
</style>
</head>
<body>
<?php

   include '../login_functions.php';
   include '../pdo_conn.php';
   include 'package_functions.php';

   $dbh = civicrmConnect();
   $menu = logoutDiv($dbh);

   echo $menu;
?>
  <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
  <div id='package'>
    <input type='text' name='package' placeholder='Package name here...' required>
    <input type='submit' name='create' value='Create Event Package'>
  </div>
  </form>

<?php

  if($_POST['create']){
     $package = $_POST["package"];
     createPackage($package);

     echo "<div id='confirmation' title='confirmation'>";
     echo "<img src='../images/confirm.png' alt='confirm'  style='float:left;padding:5px;'i width='42' height='42'/>";
     echo "<p><b>$package</b> successfully created.</p>";
     echo "</div>";
  }

?>
</body>
</html>
