 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Show a Transaction by ID</title>
	<link rel="stylesheet" type="text/css" href="BudgetStyle.css">
</head>
<body>
<?php
// This routine shows a single transaction passed in via GET referenced by the
//  transaction ID.
include 'db.php';


if (isset($_GET['transactionID'])) {
  $transactionID=$_GET['transactionID'];
  $query = "SELECT * from " . $transactionTable . " where transid = " . $transactionID . ";";
  
$handle = mysql_connect("localhost","$dbuser","$dbpassword") || die("Failed to connect to db");

  mysql_select_db("$database") || die("Failed to select $database DB");

if ($result = mysql_query($query)) {
    print "Searched for <b>" . $transactionID . "<b><br><br>\n";
    print "<table border=\"1\">\n";
    print "<tr><th>Amount</th><th>Date</th><th>Text</th><th>Category</th></tr>\n";
   while ($row=mysql_fetch_array($result)) {
  		$rowsreturned++;
  		$amount = $row['transamt'];
  		$category = $row['transcat'];
  		
  		$transtext = $row['transtext'];
  		$transdate = $row['transdate'];
  		print "<tr><td>" . $amount . "</td><td>" . $transdate . "</td><td>" . $transtext . "</td>\n";
		if ($category == NULL) {
			print "<td>Not set</td>";
		} else {
			print "<td>" . $category . "</td>";
		}
  		print "</tr>\n";
  		
  }
  print "</table>\n";
} else {
  print "Failed to return any rows\!" . mysql_error();
}
}
  print "<hr>"; 
include 'footer.php';
?>
</body>
</html>
