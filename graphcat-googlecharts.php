<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!-- Generate a graph of expenditure in one category - the inital call generates a chooser -->
<!--  to select the category.  The graph is done on monthly totals. -->
<!-- Show all transactions, or optionally all transactions in a category -->
<head>
	<title>All transactions<?php if ( isset ($_GET['category'])) {echo " for " . $_GET['category']; } ?>
	</title>

	<link rel="stylesheet" type="text/css" href="BudgetStyle.css">
	<!-- Google Charts scripts: -->
	<!--Load the AJAX API-->
	    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
	    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	    <script type="text/javascript">

	    // Load the Visualization API.
	    google.load('visualization', '1', {'packages':['corechart']});

	    // Set a callback to run when the Google Visualization API is loaded.
	    google.setOnLoadCallback(drawChart);

	    function drawChart() {
	      var jsonData = $.ajax({
	          url: "getGraphData.php",
	          dataType:"json",
	          async: false
	          }).responseText;

	      // Create our data table out of JSON data loaded from server.
	      var data = new google.visualization.DataTable(jsonData);

	      // Instantiate and draw our chart, passing in some options.
	      var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
	      chart.draw(data, {width: 800, height: 400});
	    }

	    </script>
</head>
<body>

<?php
include "db.php";
include 'functions.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', true);


if (! isset($_POST['category'])) {
	print "<title>Graph expenditure by category</title>\n";
	echo "<p>Choose a category to graph by month&nbsp;</p>";
	
  echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
  dropdown(category,transactions,transcat,Groceries);
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
  /* Produce the graph using Google Charts */
?>
	<!--Div that will hold the column chart-->
    <div id="chart_div"></div>
<?php
}
?>
</body>
</html>