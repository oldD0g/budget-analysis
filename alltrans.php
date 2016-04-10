<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>All Visa transactions</title>
	<link rel="stylesheet" type="text/css" href="BudgetStyle.css">
</head>
<body>

<?php
include 'functionsv2.php';

if (!($handle = mysql_connect("localhost",$dbuser,$dbpassword))) {
	budgetFail("Database is not running!");
	
}

mysql_select_db("$database") || budgetFail("Failed to select budget DB after connecting. Is the database set up?");

$query = "select transid,transdate,transamt,transtext,transcat from " . $transactionTable . " order by transdate DESC;";

if ($result = mysql_query($query)) {
  ?> <form method="post" action ="processupdates.php">
  <?php
   echo "<table border=\"1\">\n" ; 
  echo "<tr><th>Date</th><th>Amount</th><th>Text</th><th>Category</th></tr>\n\n";

$i=1;
/* Currmonth and lastmonth are used to detect when the month changes, so a new HTML table
can be started - the very very long table can cause browsers to struggle otherwise */

$currmonth='190001';
/* $lastmonth='190001'; */
$dropdownstring = dropdowntext(dropdown001,transactions,transcat,"Unknown");
  while ($row=mysql_fetch_array($result)) {
  		$index=sprintf("%04d",$i);
  		$thiscategory=$row['transcat'];
  		$thisdate=$row['transdate'];
  		list($thisyear,$thismonth,$thisday) = split('-',$thisdate);
  		$currmonth=$thisyear . $thismonth;
  		if (isset($lastmonth)) {
  			if ($lastmonth != $currmonth) {
  				/* Close the table and start a new one */
  				print "</table>\n";
  				?>
  				<table border="1">
  				<tr><th>Date</th><th>Amount</th><th>Text</th><th>Category</th></tr>
  				<?php
  				$lastmonth=$currmonth;
  			}
  		}
  		
  		$dropdownname=sprintf("category%04d",$i);
        print "<tr><td><input type=\"hidden\" name=\"transid" .  $index . '" value="' . $row['transid'] . '">';
        print $row['transdate'] . "</td>\n". 
        	"<td>"  . $row['transamt'] . "</td>" .
        	"<td>" . $row['transtext'] . 
        	 "</td><td>\n";
       	// Set the name of this dropdown
      	$mydropdownstring = str_replace("dropdown001",$dropdownname,$dropdownstring);
      	// Set the selected value to the category of this item, but only if this item has a category!
      	if ($thiscategory != "") { // Set the initial value of the dropdown to match the category of the item
      		$mydropdownstring = ereg_replace("OPTION value=\"$thiscategory","OPTION SELECTED VALUE=\"$thiscategory",$mydropdownstring);
        } else {  // This item has no category so insert a special value for the dropdown
        	$mydropdownstring = str_replace("</SELECT>","<OPTION SELECTED value=\"Not set\">Not set</SELECT>",$mydropdownstring);
        }
        echo $mydropdownstring;
        // dropdown($dropdownname,transactions,transcat,$row['transcat']);
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
