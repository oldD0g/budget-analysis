<!DOCTYPE html>

<head>
	<title>Transactions summarised by category</title>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<link href="BudgetStyle.css" rel="stylesheet" type="text/css" media="screen">
<link href="simpleCSSDropDownMenu.css" rel="stylesheet" type="text/css" media="screen">
</head>
<body>

<?php
/* This routine summarises transactions by category, listing them in reverse spending order.
  It also prints out the monthly cost in each category for this year and the last. */
    
include 'navbar.php';

include 'functionsv3.php';
include 'db.php';

global $dbuser, $dbpassword, $database, $transactionTable;

//print "Connecting with user $dbuser...";

$myDB = mysqli_connect("localhost",$dbuser,$dbpassword,$database);
   
if ($myDB->connect_error) {
          budgetFail("Connection to MySQL server on 'localhost' failed! Maybe the database is not running?");
}

if (isset($_GET['startdate']) && isset($_GET['enddate'])) {
	$dates[0]=$_GET['startdate'];
	$dates[1]=$_GET['enddate'];
	//print "Start date is $dates[0], end date is $dates[1]";
} else {
  $dates = startandenddate();
}
print "<br/>";
print "<h1>Transactions summarised by category</h1>";

//print "<p>start and end dates are: $dates[0]</p>";
if ($dates[0] == "empty") {
    print "Empty database! You probably need to import some data!";
    exit;
}
    
  list($startyear,$startmonth,$startday) = explode("-",$dates[0]);
  list($endyear,$endmonth,$endday) = explode("-",$dates[1]);
  
	date_default_timezone_set("Australia/ACT");

  //mktime doesn't like leading zeroes on the month!
  $startmonth=sprintf("%d",$startmonth);
  $endmonth=sprintf("%d",$endmonth);
  //echo "Start dates are $startday,$startmonth,$startyear<br>\n";
  //echo "End dates are $endday,$endmonth,$endyear<br>\n";
  $starttime=mktime(0,0,0,$startmonth,$startday,$startyear);
  $endtime=mktime(0,0,0,$endmonth,$endday,$endyear);

  $duration= $endtime-$starttime;
    $numweeks=(int)($duration / (7 * 3600 * 24));
    $nummonths=(int)($duration / (3600 * 24 * 31));
	
    $currenttime=localtime();
    $thisyear=$currenttime[5]+1900;
    $thismonth=$currenttime[4]+1;
    $startthisyear=$thisyear . "-01-01";
    $lastyear=$thisyear-1;
    $startlastyear=$lastyear . "-01-01";
    $endlastyear = $lastyear . "-12-31";
    
  //echo "<h4>Time from $starttime to $endtime, $duration seconds, $numweeks weeks</h4>\n";
  echo "<h3>Data from $dates[0] to $dates[1], $numweeks weeks or approx. $nummonths months</h3>\n";
  
  $query = "select transid,transdate,transamt,transtext,transcat from " . $transactionTable .
	 " where transdate > '" . $dates[0] . "' and transdate < '" . $dates[1] . "';";
  //print "Executing $query<br>";
  
echo '<table border="1">';


if ($result = mysqli_query($myDB,$query)) {
     
  echo "<thead><tr><th>Category</th><th>Amount</th><th>Weekly cost</th><th>Monthly Cost</th><th>This year</th>" .
  	"<th>Last year</th></tr></thead>\n\n";
  $rowsreturned=0;
  while ($row=mysqli_fetch_array($result)) {
	// This loop stores the data into the $row array for processing and printing afterwards
	// This allows totals and such to be easily calculated for printing.
	
  		$rowsreturned++;
  		$amount = $row['transamt'];
  		$category = $row['transcat'];
  		
  		$transtext = $row['transtext'];
  		if ($category == "") {
  			$total{"Uncategorised"} += $amount;
  			//print "$transtext is Uncategorised<br>";
  		} else {
			// Add running totals by category and store them into $total
  			$total{$category} += $amount;
  		}
  }
  
  		
  if ($rowsreturned != 0) {
  	// Array multisort sorts the array and maintains key associations, which the plain
  	// "sort" doesn't seem to do.
	// This sorts the array into order by total spent on the category.
  	array_multisort($total,SORT_NUMERIC);
    $rowNumber = 0;
  	foreach ($total as $key => $value) {
		// Print out a summary line for each category of expenditure.
		// Include a link to all the transactions in the category first,then
		// show weekly and monthly costs, and finish with
		// a comparison of how much was spent this year vs last year.
		
    	print "<tr><td><a href=\"showtransv2.php?category=$key\">$key</a></td> <td> $value</td>\n";
    	echo "<td> $" . (int)($value/$numweeks) . "</td>\n";
    	echo "<td> $" . (int)($value/$nummonths) . "</td>\n";
    	
    	// Check how much was spent on this category during this year
  		$thisyearquery='select sum(transamt) as totalthisyear from ' . $transactionTable . ' where transcat="' . 
  			$key . '" and transdate > "' . $startthisyear . '";';
  			//echo "Executing $thisyearquery";
  		if ($thisyearresult=mysqli_query($thisyearquery)) {
  			$thisyearrow=mysqli_fetch_array($thisyearresult);
  			$totalthisyear=$thisyearrow['totalthisyear'];
  			//print "Set totalthisyear to " . $totalthisyear. "\n";
    		echo "<td> $" . (int)($totalthisyear/$thismonth) . "</td>\n";
    	} else {
    		print "Failed to execute this year query : " . mysql_error();
    	}
    	// Also print out how much was spent per month last year
    	$lastyearquery='select sum(transamt) as totallastyear from ' . $transactionTable . ' where transcat="' . 
  			$key . '" and transdate > "' . $startlastyear . '" and transdate < "' . $endlastyear . '";';
  			//echo "Executing $lastyearquery";
  		if ($lastyearresult=mysqli_query($myDB,$lastyearquery)) {
  			$lastyearrow=mysqli_fetch_array($lastyearresult);
  			$totallastyear=$lastyearrow['totallastyear'];
  			//print "Set totallastyear to " . $totallastyear. "\n";
    		echo "<td> $" . (int)($totallastyear/12) . "</td>\n";
    	} else {
    		print "Failed to execute last year query : " . mysql_error();
    	}
    	
    	echo "</tr>\n";
		$rowNumber++;
		// If we have hit more than 20 rows, reprint the table headings
		if ($rowNumber % 15 == 0) {
			echo "<tr class=\"repeatheader\"><td>Category</td><td>Amount</td><td>Weekly cost</td><td>Monthly Cost</td><td>This year</td>" .
			  	"<td>Last year</td></tr>\n\n";
		}
  	}
  } else { // No data for these dates was found
  	print "<tr><td><b>No data found for this period, $rowsreturned rows returned!</b></td></tr>"; 
  }
  print "</table>\n";
 
} else {
  print "Failed to execute query: : ". mysqli_error();
}

?>
    <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</body>
</html>
