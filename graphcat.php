<?php
// Generate a graph of expenditure in one category - the inital call generates a chooser
//  to select the category.  The graph is done on monthly totals.



include "db.php";
include 'functions.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', true);


if (! isset($_POST['category'])) {
	print "<title>Graph expenditure by category</title>\n";
	echo "<p>Choose a category to graph by month&nbsp;</p>";
	
  echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
  dropdown(category,commvisa,transcat,Groceries);
  echo "<br>";
  echo "<em>Graph width: </em>";  
  echo '<input type="text" size="5" name="width" value="800">';
  echo "<br>";
  echo "<em>Graph height: </em>";
  echo '<input type="text" size="5" name="height" value="600">';
  echo '<input type="submit" name="submit" value="Graph it!">';
  echo '</form>';
  include 'footer.php';
} else {
  /* Produce the graph using jpgraph */
  $graphwidth = $_POST['width'];
  $graphheight = $_POST['height'];

	$category=$_POST['category'];
if (! ($myDB = mysql_connect("localhost",$username,$password))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
}

if (! mysql_select_db($database,$myDB)) {
                die("Unable to select $database database\n");
} 

include ("../jpgraph-1.11/src/jpgraph.php");
include ("../jpgraph-1.11/src/jpgraph_line.php");
include ("../jpgraph-1.11/src/jpgraph_bar.php");

// This query returns two colums - the years and months in the database in chronological order
// to be used to loop through the database again for finding the sum of the selected
// category
$query="select year(transdate) as year, month(transdate) as month from commvisa group by year,month order by year,month;";

if (! ($result = mysql_query($query,$myDB))) {
  die("Failed to run $query against database\n");
}

while ($row = mysql_fetch_array($result,1)) {
	$year = $row['year'];
	$month = $row['month'];
	// Use the year and the month in order and query the total for the given category.
	//print "Got data for year $year, month $month\n<br>";
	$query = "select sum(transamt) as amount from commvisa where year(transdate)='$year' AND month(transdate)='$month' and transcat='$category';";
	$result2 = mysql_query($query,$myDB);
	while ($row2 = mysql_fetch_array($result2,1)) {
		$total = $row2['amount'];
		//print "Got $total as total\n<br>";
	}
	$datestring="$year" . "-" . "$month";
	
	$datearray[] = $datestring;
	$amtarray[] = abs(round($total,2));
}
//exit;
$graph = new Graph($graphwidth,$graphheight,"auto");     

$graph->SetScale("textlin");

$plot = new BarPlot($amtarray);

$plot->SetWeight(2);
// Since there is only one thing on the graph we don't really need a legend
//$plot->SetLegend("Expenditure on $category");
// $graph->legend->Pos(0.02,0.1);

// Show the dollar values at each plot mark
$plot->value->Show(); 
$graph->Add($plot);
$graph->xaxis->SetTickLabels($datearray);
$graph->xaxis->SetLabelAngle(90);
$graph->img->SetMargin(80,100,30,100);


$graph->title->Set("Expenditure on $category");
$graph->xaxis->title->Set("Date");
$graph->yaxis->title->Set("Amount");



$graph->yaxis->SetColor("red");
$graph->yaxis->SetWeight(2);

 

// Display the graph
$graph->Stroke();


mysql_close($myDB);
}
?>
