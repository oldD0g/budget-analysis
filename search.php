<?php

include 'db.php';

if (isset($_POST['searchstring'])) {
  $searchstring=$_POST['searchstring'];
  $query = "SELECT * from commvisa where transtext regexp '.*" . $searchstring . ".*';";
  
$handle = mysql_connect("localhost","$username","$password") || die("Failed to connect to db");

  mysql_select_db("$database") || die("Failed to select $database DB");

if ($result = mysql_query($query)) {
    print "Searched for <b>" . $searchstring . "<b><br><br>\n";
    print "<table border=\"1\">\n";
    print "<tr><th>Amount</th><th>Date</th><th>Text</th></tr>\n";
   while ($row=mysql_fetch_array($result)) {
  		$rowsreturned++;
  		$amount = $row['transamt'];
  		$category = $row['transcat'];
  		
  		$transtext = $row['transtext'];
  		$transdate = $row['transdate'];
  		print "<tr><td>" . $amount . "</td><td>" . $transdate . "</td><td>" . $transtext . "</td>\n";
  		print "</tr>\n";
  		
  }
  print "</table>\n";
} else {
  print "Failed to return any rows\!" . mysql_error();
}
}
 
 ?>
 
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Search Transactions</title>
	<meta name="generator" content="BBEdit 7.1.1" />
</head>
<body>

<h1>Search Transactions</h1>

<?php echo '<form name="sample" method="post" action="' . $_SERVER['PHP_SELF'] . '">'; ?>

<p>Transaction text: <input type="text" name="searchstring" size="30">  
        </p>
        
<p>Amount: <input type="text" name="searchamount" size="20">  
       
        </p>
        
<p><input type="submit" value="Search" name="B1"></p>


</form>

<?php 
include 'footer.php';
?>
</body>
</html>
