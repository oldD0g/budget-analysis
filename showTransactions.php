<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!-- Show all transactions, or optionally all transactions in a category -->
<head>
	<title>All transactions<?php if ( isset ($_GET['category'])) {echo " for " . $_GET['category']; } ?>
	</title>
	<link href="BudgetStyle.css" rel="stylesheet" type="text/css" media="screen">

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

$mysqli = new mysqli("localhost", $username, $password, $database);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

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
		$query = "select transid,transdate,transamt,transtext,transcat from commvisa where transcat IS NULL $order";
	} else {
		$query = "select transid,transdate,transamt,transtext,transcat from commvisa where transcat='$cat' $order";
	}
} else { // Show all transactions
	if (isset($_GET['startdate'])) {
		$where="where transdate > '" . $_GET['startdate'] . "'";
		if (isset($_GET['enddate'])) {
			$where="$where" . "and transdate < '" . $_GET['enddate']. "'";
		}
	}
	$query = "select transid,transdate,transamt,transtext,transcat from commvisa $where $order";
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


// Yes, I have no stylesheets yet, cellpadding is a kludge!
// Which doesn't work...?
if ($result = $mysqli->query($query . ";")) {
	/* determine number of rows result set */
	    $row_cnt = $result->num_rows;
	
	// Now we can paginate the results to show a limited number of transactions per
	// page. This really helps browsers which struggle with tables several thousand lines
	// long.
	
		$rowsperpage = 20;
		// find out total pages
		$totalpages = ceil($row_cnt / $rowsperpage);
		
		// get the current page or set a default
		if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
		   // cast var as int
		   $currentpage = (int) $_GET['currentpage'];
		} else {
		   // default page num
		   $currentpage = 1;
		} // end if
		
		// the offset of the list, based on current page 
		$offset = ($currentpage - 1) * $rowsperpage;
		
		// This next query pulls out the data for this page only so that the loop below only
		// shows that data.
		
		if (!($result = $mysqli->query("$query LIMIT $offset, $rowsperpage;"))) {
			printf("Error trying to query database! Error was %s\n",$mysqli->error);
			printf("Query was $query LIMIT $offset, $rowsperpage\n");
			exit();
		}
		
		printf("Showing results from query: %s\n",$query);
	
  ?> <form method="post" action ="processupdates.php">
   
  <table border="1" cellpadding=\"15\">
  <?php
     
  echo "<tr><th>Date</th><th>ID</th><th>Amount</th><th>Text</th><th>Category</th></tr>\n\n";

$i=1;
  while ($row=mysqli_fetch_array($result, MYSQLI_ASSOC)) {
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
	
	/******  build the pagination links ******/
	// if not on page 1, don't show back links
	echo "Jump to page: ";
	if ($currentpage > 1) {
	   // show << link to go back to page 1
	   echo " <a href='{$_SERVER['REQUEST_URI']}?currentpage=1'><<</a> ";
	   // get previous page num
	   $prevpage = $currentpage - 1;
	   // show < link to go back to 1 page
	   echo " <a href='{$_SERVER['REQUEST_URI']}?currentpage=$prevpage'><</a> ";
	} // end if
	
	// range of num links to show
	$range = 3;

	// loop to show links to range of pages around current page
	for ($x = ($currentpage - $range); $x < (($currentpage + $range)  + 1); $x++) {
	   // if it's a valid page number...
	   if (($x > 0) && ($x <= $totalpages)) {
	      // if we're on current page...
	      if ($x == $currentpage) {
	         // 'highlight' it but don't make a link
	         echo " [<b>$x</b>] ";
	      // if not current page...
	      } else {
	         // make it a link
	         echo " <a href='{$_SERVER['REQUEST_URI']}?currentpage=$x'>$x</a> ";
	      } // end else
	   } // end if 
	} // end for
	
} else {
  print "Failed to execute query: : ". mysql_error();
}

include 'footer.php';
?> 
</body>
</html>
