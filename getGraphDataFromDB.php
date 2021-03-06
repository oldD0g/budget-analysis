<?php
// This AJAX routine fetches data from the database and returns it as a JSON array suitable
//  for putting into a column graph using Google Charts
// The data returned is monthly cost of a nominated transaction (passed in as a POST variable)

include "db.php";
include 'functionsv2.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', true);

$category=$_POST['category'];
//print "Got category of $category";

if (! ($myDB = mysql_connect("localhost",$dbuser,$dbpassword))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
}

if (! mysql_select_db($database,$myDB)) {
                die("Unable to select $database database\n");
} 

// This query returns two colums - the years and months in the database in chronological order
// to be used to loop through the database again for finding the sum of the selected
// category
// There is probably a way to do this in one SQL query (group by month?) but that's not how it
// has been written originally.
$query="select year(transdate) as year, month(transdate) as month from " . $transactionTable . 
	" group by year,month order by year,month;";

if (! ($result = mysql_query($query,$myDB))) {
  die("Failed to run $query against database\n");
}

$table = array();
$table['cols'] = array(
    array('id' => "", 'label' => 'Month', 'type' => 'string'),
	array('id' => "", 'label' => 'Amount', 'type' => 'number')
);

$rows = array();

while ($row = mysql_fetch_array($result,1)) {
	$year = $row['year'];
	$month = $row['month'];
	// Use the year and the month in order and query the total for the given category.
	
	// table will contain the array which converts to JSON
	
	
	$query = "select sum(transamt) as amount from " . $transactionTable . " where year(transdate)='$year' 
		AND month(transdate)='$month' and transcat='$category';";
	$result2 = mysql_query($query,$myDB);
	
	while ($row2 = mysql_fetch_array($result2,1)) {
		$total = $row2['amount'];
		$datestring="$year" . "-" . "$month";

		$datearray[] = $datestring;
		$amtarray[] = abs(round($total,2));
		
		//print "Got $total as total\n<br>";
		$temp = array();
		$temp[] = array('v' => $datestring);
		$temp[] = array('v' => abs(round($total,2)));
		
		$rows[] = array('c' => $temp);
	}	
}
$table['rows'] = $rows;
$jsonTable = json_encode($table);
echo $jsonTable;

mysql_close($myDB);
?>
