<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!-- Show all transactions, or optionally all transactions in a category -->
    
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    
	<title>All transactions<?php if ( isset ($_GET['category'])) {echo " for " . $_GET['category']; } ?>
	</title>

	<link rel="stylesheet" type="text/css" href="BudgetStyle.css">
	<link href="simpleCSSDropDownMenu.css" rel="stylesheet" type="text/css" media="screen">
	
    <!-- Don't include jquery twice, it's added at the bottom for bootstrap 
	<script type="text/javascript" language="javascript" src="jquerysrc/jquery-2.1.4.js"></script>
    -->
    
	<script type="text/javascript">
	function showNewCategory(value)
	{
	    alert(value);
	}
	
	function setNewCategory(dropdownname, newCategory)
	    {
			// This match extracts the transaction ID, cleverly encoded into the name
			// of the dropdown, so that the function can update the database.
			transdigits = dropdownname.match(/\d+/gi);
			transactionID = transdigits[0];
			
	            $.ajax({
	                 type: "GET",
	                 url: "setCategory-ajax.php",
	                 data: { category: newCategory, transID: transactionID },
	                 success: function(result){
						//message = "Changed to " + newCategory + ":" + result;
	                         document.getElementById(dropdownname).innerHTML = result;
	                       },
						error: function(result){
								document.getElementById(dropdownname).innerHTML = "Failed";
							}
	                     });
	    };
	
	</script>
</head>
<body>

<?php

include 'navbar.php';

include 'functionsv2.php';
include 'db.php';

global $dbuser, $dbpassword, $database, $transactionTable;

if (! ($handle = mysql_connect("localhost",$dbuser,$dbpassword))) {
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
		$query = "select transid,transdate,transamt,transtext,transcat from " . $transactionTable . " where transcat IS NULL $order;";
	} else {
		$query = "select transid,transdate,transamt,transtext,transcat from " . $transactionTable . " where transcat='$cat' $order;";
	}
} else { // Show all transactions
	if (isset($_GET['startdate'])) {
		$where="where transdate > '" . $_GET['startdate'] . "'";
		if (isset($_GET['enddate'])) {
			$where="$where" . "and transdate < '" . $_GET['enddate']. "'";
		}
	}
	$query = "select transid,transdate,transamt,transtext,transcat from " . $transactionTable . " $where $order;";
}


  echo '<br/><br/><form method="post" action="' . $_SERVER['PHP_SELF'] . '?category=' . $cat . '">';
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

$rowCount = mysql_query("select transid from $transactionTable $where");

if ($result = mysql_query($query)) {
  ?>
  <table id="transactions" border="1">

  <?php
     
  echo "<tr><th>Date</th><th>ID</th><th>Amount</th><th>Text</th><th>Category</th></tr>\n\n";

	$i=1;
  while ($row=mysql_fetch_array($result)) {
  		$index=sprintf("%04d",$i);

		// This used to be type=hidden but I don't remember why
        print "<tr><td><input type=\"hidden\" name=\"transid" .  $index . '" value="' . $row['transid'] . '">';
        print $row['transdate'] . "</td>\n". 
			"<td>" . $row['transid'] . "</td>\n" .
        	"<td class=\"amount\">\$"  . $row['transamt'] . "</td>" .
        	"<td><a href=\"updateSingleTransactionbyID.php?transactionID=" . $row['transid'] . "\">" . $row['transtext'] . 
        	 "</a></td>\n";
        // Generate a dropdown name that includes the transaction ID for processing by the AJAX function
		$dropdownName = "category" . $row['transid'];
		
		print "<td id = \"" . $dropdownName . "\">";
 		dropdown($dropdownName, transactions, transcat, $row['transcat'] );
		print "</td>\n";
		print "</tr>\n";
        $i++;
  }
  print "</table>\n";
 
} else {
  print "Failed to execute query: : ". mysql_error();
}

include 'footer.php';
?>
    
<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
      
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</body>
</html>
