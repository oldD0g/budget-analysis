<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!-- Show all transactions, or optionally all transactions in a category -->
<head>
	<title>All transactions<?php if ( isset ($_GET['category'])) {echo " for " . $_GET['category']; } ?>
	</title>

	<script type="text/javascript">
  	function settickon(field)
  	// This function turns on the "Update" tickbox if the user changes the category for
  	//  a transaction.  This allows the form processing script to recognise which items
  	//  have modifications to be put in the database.
  	{
  		updatefieldname="update" + field;
  		document.forms[0][updatefieldname].checked=1;
  		// alert('The field name was ' + updatefieldname);
  	}
</script>
</head>
<body>

<?php
include 'functions.php';
include 'db.php';

if (! ($handle = mysql_connect("localhost",$username,$password))) {
          budgetFail("Connection to MySQL server 'localhost' failed! Maybe the database is not running?");
}

mysql_select_db("$database") || die("Failed to select $database DB");

if (isset ($_POST['sortby'])) {
  // Sort transactions by this field
  switch ($_POST['sortby']) {
    case 'date': $order = "order by transdate DESC";
    	break;
    case 'amount': $order="order by transamt DESC";
    	break;
    case 'text': $order="order by transtext";
    	break;
    case 'category': $order = "order by transcat";
    	break;
    }
 }
    
if (isset ($_GET['category'])) { // Only show transactions from this category
	$cat=$_GET['category'];
	if ($cat == "Uncategorised") { // Alter the query to show all transactions with no category
		$query = "select transid,transdate,transamt,transtext,transcat from commvisa where transcat IS NULL $order;";
	} else {
		$query = "select transid,transdate,transamt,transtext,transcat from commvisa where transcat='$cat' $order;";
	}
} else { // Show all transactions
	if (isset($_GET['startdate'])) {
		$where="where transdate > '" . $_GET['startdate'] . "'";
		if (isset($_GET['enddate'])) {
			$where="$where" . "and transdate < '" . $_GET['enddate']. "'";
		}
	}
	$query = "select transid,transdate,transamt,transtext,transcat from commvisa $where $order;";
}


  echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?category=' . $cat . '">';
  ?>
  <span>Sort by: </span>
  <select name="sortby">
  	<option selected value="date">Date
  	<option value="amount">Amount
  	<option value="text">Text
  	<option value="category">Category
  	
  </select>
  <input type="submit" name="submit" value="Sort">
  </form>
  <hr>
  <?php

$rowCount = mysql_query("select transid from commvisa $where");
echo "Row count is " . $rowCount;

// Yes, I have no stylesheets yet, cellpadding is a kludge!
// Which doesn't work...?
if ($result = mysql_query($query)) {
  ?> <form method="post" action ="processupdates.php">
   
  <table border="1" cellpadding=\"15\">
  <?php
     
  echo "<tr><th>Date</th><th>ID</th><th>Amount</th><th>Text</th><th>Category</th></tr>\n\n";

$i=1;
  while ($row=mysql_fetch_array($result)) {
  		$index=sprintf("%04d",$i);
  		$dropdownname=sprintf("category%04d",$i);
		// This used to be type=hidden but I don't know why
        print "<tr><td><input type=\"hidden\" name=\"transid" .  $index . '" value="' . $row['transid'] . '">';
        print $row['transdate'] . "</td>\n". 
			"<td padding=\"3px\">" . $row['transid'] . "</td>\n" .
        	"<td>\$"  . $row['transamt'] . "</td>" .
        	"<td>" . $row['transtext'] . 
        	 "</td><td>\n";
        dropdown($dropdownname,commvisa,transcat,$row['transcat']);
        print '</td><td><input type="checkbox" name="update' . $index . '">Update</td></tr>' . "\n";
        $i++;
  }
  print "</table>\n";
  ?>
  <input type="submit" name="submit" value="Submit">
     
	</form>
	<?php
} else {
  print "Failed to execute query: : ". mysql_error();
}

include 'footer.php';
?> 
</body>
</html>
