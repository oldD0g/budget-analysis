<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
      <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous"><link href="BudgetStyle.css" rel="stylesheet" type="text/css" media="screen">
    <link href="simpleCSSDropDownMenu.css" rel="stylesheet" type="text/css" media="screen">
    <title>Received data</title>
</head>
<body>


<?php
/* This code receives a file and inserts each transaction in the file into the database
 	if it does not match an existing record*/

include 'navbar.php';


include 'functionsv2.php';
include 'db.php';
global $dbuser, $dbpassword, $database, $transactionTable;

$uploaddir = '/private/tmp/';
$uploadfile = $uploaddir. $_FILES['userfile']['name'];

print "<pre>";
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    print "File is valid, and was successfully uploaded. ";
    //print "Here's some more debugging info:\n";
    //print_r($_FILES);
    echo "File " . $_FILES['userfile']['name'] . " successfully received<br>\n";
} else {
	#print "move_uploaded_file failed to move " . $_FILES['userfile']['tmp_name'] . " to " . $uploadfile;
	print "move_uploaded_file failed!<br>";
	print "Tried to move " . $_FILES['userfile']['tmp_name'];
	print "to the file " . $uploadfile . "<br>";
	print "Error code was " . $_FILES['userfile']['error'];
	print "<br>";
	print "Here's some debugging info:\n";
    print_r($_FILES);
    exit;
}

  $dbhandle = mysql_connect("localhost","$dbuser","$dbpassword") || die("Failed to connect to db");
  mysql_select_db("$database") || die("Failed to select budget DB");

print "</pre>";

if (!$data=fopen($uploadfile,"r")) {
		 die("Failed to open $uploadfile");
		}
print "<table border=\"1\">\n";
print "<tr><th>Transaction</th><th>Status</th><th>Action/Result</th></tr>\n";

while (!feof ($data)) {
    $buffer = fgets($data, 4096);
    //echo "Read \"" . $buffer . "\" from input file<br>";
    list($date,$amount,$description) = explode(",",$buffer);
    $amount=str_replace('"','',$amount);
    // Remove the quotes around the description field.
    $description=str_replace('"','',$description);

    if (! preg_match("/\d\d\/\d\d\/\d\d/", $date)) {
      echo "First field " . $date . " in " . $buffer . " does not appear to be in dd/mm/yy date format, skipping<br>";
    } else {
      //echo "<br>Read date=" . $date . ", amount=" . $amount .
      	// " description= " . $description . " from input file";
      	// Date in file is dd/mm/yyyy format, but MySQL likes yyyy-mm-dd format, so we switch it around
      	list($day,$month,$year) = explode("/",$date);
      	$mysqldate="$year-$month-$day";
      	// Occasionally the bank seems to give us a file where the spaces in some transaction descriptions
      	// have been removed.  This results in loading duplicate transactions.  To avoid this, the description has
      	//  all spaces removed before comparisons are done.
      	$searchtext=str_replace(' ','',$description);
      	//  Also, the search must have any single quotes turned into doubles so they don't signify the end of the string
      	$searchtext=str_replace("'","''",$searchtext);
      	
      	$query = "select transid,transdate,transamt,transtext,transcat from " . $transactionTable . " where " .
      		"transdate='$mysqldate' and " .
      		"replace(transtext,' ','')='$searchtext' " .
      		"HAVING ABS(transamt-$amount)<0.01";
      		// Since transamt is a float, we have to do this funny comparison to allow for floating point precision issues.
      	 //print "<br>Executing <br>$query<br>\n";
      	if ($result = mysql_query($query)) {
      		$num_rows=mysql_num_rows($result);
      		//print "<br>DB query successful, $num_rows rows returned<br>\n";
			if ($num_rows == 1) { // This transaction is already in the database
      			$row=mysql_fetch_array($result);
      			$ID=$row['transid'];
      			$foundamount=$row['transamt'];
      			print "<tr><td>$description date $date \$$foundamount</td><td> <font color=\"red\">already in database</font> - transaction ID is $ID</td>\n<td>Skipped</td></tr>\n";
      		} else if ($num_rows == 0) {
      			//print "<br>\nQuery for $description on $date succeeded, but no matching data found!<br>\n";
      			print "<tr><td>$description $date $amount</td><td> <font color=\"red\">not in database</font>\n</td>";
      			
      			if (db_insert($description,$mysqldate,$amount)) {
      				print "<td><font color=\"red\">Inserted</font></td></tr>\n";
      			} else {
      				print "<td><font color=\"red\">Insert failed:</font>" . mysql_error() . "</td></tr>\n";
      			}
      		} else {
      			print "<td>More than one match found for this transaction, skipping</td></tr>\n";
      		}
      	} else {
      		// At this point, mysql_query has returned false, but this seems to indicate the entry
      		//  doesn't exist, contrary to my expectation, which is that I was catching a syntax
      		//  error in my query.  Accordingly, we attempt to insert this entry.
      		//print "<br>Should insert $description on $date for $amount\n<br>";
      		if (db_insert($description,$mysqldate,$amount)) {
      			print "<td><font color=\"red\">Inserted</font></td></tr>\n";
      		} else {
      			print "<td><font color=\"red\">Insert failed:</font> " . mysql_error() . "</td></tr>\n";
      		}
      	}
      	  
    }
}
fclose ($data); 
print "</table>\n";

?>
      <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</body>
</html>