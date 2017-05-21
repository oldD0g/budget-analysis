<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Re-apply category guesses</title>
	<link href="simpleCSSDropDownMenu.css" rel="stylesheet" type="text/css" media="screen">
	<link href="BudgetStyle.css" rel="stylesheet" type="text/css" media="screen">
</head>
<body>
<h1>Re-applying strings to transactions that are not yet set</h1>
<?php
// Re-applies the current set of category guesses to the existing transaction data.
// Only changes the category of items that are unset.  This is a useful function when you have
// new data, and add some new strings for categories, and want to universally apply those guesses
// to all the new transactions in the database.

/*  The algorithm is:
	For each transaction, where the category is not yet set:
		Run through the entire catstrings table, and check if any of the guesses match the transaction
			description.  If so, set the category to the appropriate value.  This is only done if the category
			is NOT SET.
			
	Note that this is probably not a very efficient algorithm.  A more sophisticated approach would run
		through the catstrings table and build a set of arrays for faster matching.  That's version 2.
		
*/

include 'navbar.php';

include 'functionsv2.php';
include 'db.php';

global $dbuser, $dbpassword, $database, $transactionTable;

$debug=0;

$guessarray=create_cat_array();

if (! ($myDB = mysql_connect("localhost",$dbuser,$dbpassword))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
}

if (! mysql_select_db($database,$myDB)) {
                die("Unable to select $database database\n");
} 

// Extract all the transactions and process them
$query="select * from " . $transactionTable . " order by transtext";

// Note that extra whitespace inside transaction text is not unheard of
// so the check removes any extra whitespace from transtext before checking.
// If you put extra whitespace into your category guess strings, that's your
// problem!
        
if ($result = mysql_query($query,$myDB)) {
	echo '<table border="1">' . "\n";
	echo '<tr><th>ID</th><th>Date</th><th>Amount</th><th>Text</th><th>New category</th></tr>';
	echo "\n";
	while ($row = mysql_fetch_array($result,1)) {
		$category=$row['transcat'];
		$transid=$row['transid'];
		$transtext=$row['transtext'];
		$transamt=$row['transamt'];
		$transdate=$row['transdate'];
		if ($category === NULL) {
		  //print "<b>Processing transaction $transid, text $transtext</b><br>";
			// Print out transaction text in ASCII values to check glitches...
			//$dumparray=unpack('C*',$transtext);
			//print "Transaction text is ";
			//print_r($dumparray);
			//print "<br>\n";
			// Remove any unnecessary white space in transaction text by replacing
			// excess with single whitespace characters
			$transtext = preg_replace('/\s+/', ' ', $transtext);
			$newcat="Unchanged";
			// Go through all the category strings and see if one applies
			// Note: only the first matching string is used!
			foreach ($guessarray as $catname => $stringarray) {
				//print "Looking at category $catname...<br>";
				foreach ($stringarray as $id => $string) {
					//print "Checking for '$transtext' match with '$string'...";
					$pos = stripos($transtext,$string);
					if ($pos === false) {
						//print "No match!<br/>";
						
					} else {
						//print "<b>This transaction matches!</b><br>";
						// Set the transaction category to $catname
						modifycat($transid,$catname);
						$newcat="<b>$catname</b>";
						break;
					}
				}
			}
			
    		if ($newcat != "Unchanged") {
    			print "<tr><td>$transid</td><td>$transdate</td><td>$transamt</td><td>$transtext</td><td>$newcat</td></tr>\n";
    		}
    			
    	}
                        
        
   	}
    echo "</table>\n";
    echo "<hr>\n";
    
} else {
      print "Could not run query against database $database\n<br>\n";
      mysql_error();
}

mysql_close($myDB);

?>

</body>
</html>
