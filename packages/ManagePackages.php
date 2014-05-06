<html>
<head>
<title>Manage CIA Events</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../billingStyle.css">
<link rel="stylesheet" type="text/css" href="../menu.css">
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
  }

?>
</body>
</html>
