<?php
// This AJAX routine fetches data from the database and returns it as a JSON array suitable
//  for putting into a column graph using Google Charts
// The data returned is monthly cost of all transactions, by month.
// This allows an ordered column graph to be presented of the costs per category

include "db.php";
include 'functionsv2.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', true);

if (isset($_POST['year'])) {
	$year = $_POST['year'];
} else {
	$year = '2016';
}

if (isset($_POST['minimum'])) {
	$minimumValue = $_POST['minimum'];
} else {
	$minimumValue = '100';
}

if (isset($_POST['maximum'])) {
	$maximumValue = $_POST['maximum'];
} else {
	$maximumValue = '10000';
}

//print "Got year as $year";
//print "<br/>\n";

if (! ($myDB = mysql_connect("localhost",$dbuser,$dbpassword))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
}

if (! mysql_select_db($database,$myDB)) {
                die("Unable to select $database database\n");
} 

// This query returns the categories in the transaction database

$query="select transcat from " . $transactionTable . " group by transcat;";

if (! ($result = mysql_query($query,$myDB))) {
  die("Failed to run $query against database\n");
}

// This table stores the resulting data for later encoding into JSON
$table = array();
$table['cols'] = array(
    array('id' => "", 'label' => 'Category', 'type' => 'string'),
	array('id' => "", 'label' => 'Amount', 'type' => 'number')
);

$rows = array();

// For each category in the transaction table, collate the relevant spend
//  for the nominated year and store it in the table
while ($row = mysql_fetch_array($result,1)) {
	$category = $row['transcat'];
	//print "Fetched category " . $category;	
	//print "</p>\n";
	
	$query2 = "select sum(transamt) as totalValue from " . $transactionTable . " where transcat = '" .
		$category . "' AND year(transdate)='" . $year . "';";
	$result2 = mysql_query($query2,$myDB);
	
	if ($row2 = mysql_fetch_array($result2,1)) {
		$totalCost = abs(round($row2['totalValue'],2));
		// If totalCost is zero for this query, don't add it to the data, as it just clutters
		// the graph.
		if (($totalCost >= $minimumValue) && ($totalCost <= $maximumValue)){
		//print "Fetched total spend on category '" . $category . "' as " . $totalCost;
		//print "<br/>\n";

		// Create an initial array of the data, that can then be sorted, and after sorting
		// it can be encoded into a DataTable type format suitable for JSON encoding and
		// use by Google charts...not 100% sure that all the ''v' and 'c' stuff is needed though
		$categoryValues[$category] = $totalCost;
		
		}
		
	} else {
		//print "mysql_fetch_array failed!";
	}
}

// Sort the table to put highest expenditure first

     arsort($categoryValues, SORT_NUMERIC);
// Now re-encode the entries from categoryValues into an array suitable for JSON encoding
//  by making it into a DataTable format that Google Chart likes.


foreach ($categoryValues as $categoryName => $dollarTotal) {
	$temp = array();
	$temp[] = array('v' => $categoryName);
	$temp[] = array('v' => $dollarTotal);
	$rows[] = array('c' => $temp);
}
	//print "<pre>\n";
	//print_r($rows);
	//print "</pre>\n";

	   
$table['rows'] = $rows;

$jsonTable = json_encode($table);
//$jsonTable = json_encode($categoryValues);


echo $jsonTable;

mysql_close($myDB);
?>
