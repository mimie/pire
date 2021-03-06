<html>
<head>
	<title>Contacts</title>
	<link rel="stylesheet" type="text/css" href="../billingStyle.css">
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script src="../js/jquery-jPaginate.js"></script>
	<script src="../js/jquery.tablesorter.js"></script>
<script type='text/javascript' language='javascript'>
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#contacts').jPaginate({
                'max': 30,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} );
});
</script>
</head>
<body>
<?php
	include '../pdo_conn.php';
        include 'orreport_functions.php';

	 echo "<table width='100%'>";
	 echo "<tr>";
	 echo "<td align='center'><a href='display_contacts.php'>INDIVIDUAL CONTACTS</a></td>";
	 echo "<td align='center' bgcolor='#084B8A'><a href='display_organization.php'>ORGANIZATION</td>";
	 echo "</tr>";
	 echo "</table></br>";
?>
     <div align='center'>
	<form method='POST' action='display_contacts.php'>
		<input type='text' name='search_contact' placeholder='Search name or organization...'>
		<input type='submit' name='search' value='SEARCH'>
        </form>

<?php      
        if(isset($_POST['search'])){
                $searchValue = $_POST['search_contact'];
		$contacts = displayContactsWithEvents($searchValue);
        }else{
		$contacts = displayContactsWithEvents('');
	}

        echo "<table id='contacts'>";
        echo "<thead><tr><th colspan='2'>IIAP Contacts</th></tr></thead>";
        echo "<tbody>";

	foreach($contacts as $contact_id=>$info){
        	echo "<tr>";
                echo "<td><a href='display_billings.php?contact_id=$contact_id'>".mb_convert_encoding($info['sort_name'],"UTF-8")."</a></td>"; 
                echo "<td>".mb_convert_encoding($info['organization_name'],"UTF-8")."</td>";
                echo "</tr>";
	}
      
        echo "</tbody>";
        echo "</table>";
?>
      </div>
</body>
</html>
