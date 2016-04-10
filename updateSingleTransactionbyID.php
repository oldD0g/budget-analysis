 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Show a Transaction by ID</title>
	<link rel="stylesheet" type="text/css" href="BudgetStyle.css">
	<link rel="stylesheet" type="text/css" href="DynamicElements.css">
	
	<script type="text/javascript" language="javascript" src="jquerysrc/jquery-2.1.4.js"></script>
	<script type="text/javascript">
	function processNewCategory(value)
	{
	    alert(value);
	}
	
	function setNewCategory(newCategory)
	    {
			transactionID = $.urlParam('transactionID');
			
	            $.ajax({
	                       type: "GET",
	                       url: "setCategory-ajax.php",
	                       data: { category: newCategory, transID: transactionID },
	                       success: function(result){
	                         $("#returnedStatus").html(result);
	                       }
	                     });
	    };
	// This function is from http://www.sitepoint.com/url-parameters-jquery/
		$.urlParam = function(name){
		    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
		    if (results==null){
		       return null;
		    }
		    else{
		       return results[1] || 0;
		    }
		}
	
	</script>
	
</head>
<body>
<?php
// This routine shows a single transaction passed in via GET referenced by the
//  transaction ID.
include 'db.php';
include 'functionsv2.php';


if (isset($_GET['transactionID'])) {
  $transactionID=$_GET['transactionID'];
  $query = "SELECT * from " . $transactionTable . " where transid = " . $transactionID . ";";
  
$handle = mysql_connect("localhost","$dbuser","$dbpassword") || die("Failed to connect to db");

  mysql_select_db("$database") || die("Failed to select $database DB");

print '<FORM ACTION="setCategorybyID.php" METHOD="POST">';
if ($result = mysql_query($query)) {
    //print "Searched for transaction D <b>" . $transactionID . "<b><br><br>\n";
    print "<table border=\"1\">\n";
    print "<tr><th>Amount</th><th>Date</th><th>Text</th><th>Category</th><th>Transaction ID</th></tr>\n";
   while ($row=mysql_fetch_array($result)) {
  		$rowsreturned++;
  		$amount = $row['transamt'];
  		$category = $row['transcat'];
  		
  		$transtext = $row['transtext'];
  		$transdate = $row['transdate'];
		$transid = $row['transid'];
		
  		print "<tr><td>" . $amount . "</td><td>" . $transdate . "</td><td>" . $transtext . "</td>\n";
		
		print "<td>\n";
		// the dropdown function will return a dropdown for me but it won't contain the necessary
		// javascript to activate when the dropdown changes.  So this is a dummy version untl I
		// work out the best way around that issue.
		//include 'category-dropdown.php';
		dropdown("category",transactions,transcat,$row['transcat']);
		
		//<select id ="ddl" name="ddl" onmousedown="this.value='';" onchange="processNewCategory(this.value);">
		 // <option value='Groceries'>Groceries</option>
		 // <option value='Cash Withdrawals'>Cash Withdrawals</option>
		//  <option value='Eating Out'>Eating Out</option>
		//</select>
		
		print "</td>\n";
		print "<td>" . $transid . "</td>\n";
	
  		print "</tr>\n";
		// Put the Submit button in a second row of the table so it is easily accessible after selecting
		// the dropdown
		print '<tr><td>';
		// Print a hidden field so it can be submitted to the form and used in setCategorybyID.php
		print '<input type="hidden" name="transID" value="' .  $transactionID . '">';
		print '</td><td>&nbsp;</td><td>&nbsp;</td><td class=\"right-align\">';
  		print '<div id="returnedStatus"></div>';
		print "</td></tr>";
  }
  print "</table>\n";
 	
	print '</form>';
} else {
  print "Failed to return any rows\!" . mysql_error();
}
}
  print "<hr>"; 
include 'footer.php';
?>
</body>
</html>
