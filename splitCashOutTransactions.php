<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Split "Cash Out" transaction</title>
</head>
<body>
<h1>Splitting transactions containing "Cash Out" components</h1>
<?php
// This routine is designed to scan the database for transactions that have a "cash out"
// component. In around 2010(?) the bank started tagging these so you can tell when you have
// purchased some groceries and also obtained cash at the register. Previously they just
// showed up as a single grocery purchase.
// Splitting out the cash withdrawal is important to make it show up as cash out rather
// than mistakenly assuming that cash was spent on groceries.

// NOTE: This code is NOT finished. Currently I just pre-process the transactions
// before loading them in.

/*  The algorithm is:
	For each transaction, where the text is of the form "SomeSupermarketID Cash Out $80.00 Purchase $9.82"
		Adjust the existing transaction so the text only says Purchase,
		Create a new "synthetic" transaction for the cash amount described as "Cash Out at SomeSupermarketID"

		
*/
include 'functions.php';
include 'db.php';

$debug=0;


if (! ($myDB = mysql_connect("localhost",$username,$password))) {
          budgetFail("Connection to MySQL server 'localhost' failed!");
}

if (! mysql_select_db($database,$myDB)) {
                budgetFail("Unable to select $database database\n");
} 

// Extract all the transactions in date order and check them for the Cash Out string
$query="select * from transactions order by transdate";

// Note that extra whitespace inside transaction text is not unheard of
// so the check removes any extra whitespace from transtext before checking.
// If you put extra whitespace into your category guess strings, that's your
// problem!
        
if ($result = mysql_query($query,$myDB)) {
	echo '<table border="1">' . "\n";
	echo '<tr><th>ID</th><th>Date</th><th>Amount</th><th>Text</th><th>Comment</th></tr>';
	echo "\n";
	while ($row = mysql_fetch_array($result,1)) {
		$category=$row['transcat'];
		$transid=$row['transid'];
		$transtext=$row['transtext'];
		$transamt=$row['transamt'];
		$transdate=$row['transdate'];
		if (preg_match("/.*Cash Out/",$transtext,$matches)) {
		  // print out the transaction to check that it is matching correctly
    			print "<tr><td>$transid</td><td>$transdate</td><td>$transamt</td><td>$transtext</td><td>Cash Out</td></tr>\n";
    	}
                        
        
   	}
    echo "</table>\n";
    echo "<hr>\n";
    
} else {
      print "Could not run query against database\n<br>\n";
      mysql_error();
}

mysql_close($myDB);

include 'footer.php';
?>

</body>
</html>
