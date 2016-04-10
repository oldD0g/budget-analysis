<?php
include 'db.php';

if (! ($myDB = mysql_connect("localhost","$username","$password"))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
}

if (! mysql_select_db($database)) {
                die("Unable to select $database database\n");
} 

$query="select year(transdate) as year, month(transdate) as month from commvisa group by year,month order by year,month;";

if (! ($result = mysql_query($query,$myDB))) {
  die("Failed to run $query against database\n");
}

while ($row = mysql_fetch_array($result,1)) {
	$year = $row['year'];
	$month = $row['month'];
	print "Got data for year $year, month $month\n<br>";
	$query = "select sum(transamt) as amount from commvisa where year(transdate)='$year' AND month(transdate)='$month' and transcat='Groceries';";
	$result2 = mysql_query($query,$myDB);
	while ($row2 = mysql_fetch_array($result2,1)) {
		$total = $row2['amount'];
		print "Got $total as total\n<br>";
	}
	$datestring="$year" . "-" . "$month";
	$datearray[] = $datestring;
	$amtarray[] = abs(round($total,2));
}

print "Array of values is:\n<br>";

print_r($datearray);
print_r($amtarray);

?>