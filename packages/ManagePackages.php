<html>
<head>
<title>Manage CIA Events</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../billingStyle.css">
<link rel="stylesheet" type="text/css" href="../menu.css">
</head>
<body>
<?php

   include '../login_functions.php';
   include '../pdo_conn.php';

   $dbh = civicrmConnect();
   $menu = logoutDiv($dbh);

   echo $menu;
?>
  <div>
    <input type='text' name='package' placeholder='Package name here...' required>
    <input type='submit' name='create' value='Create CIA Package'>
  </div>
</body>
</html>
