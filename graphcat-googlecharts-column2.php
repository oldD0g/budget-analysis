<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!-- Generate a graph of expenditure in one category - the inital call generates a chooser -->
<!--  to select the category.  The graph is done on monthly totals. -->
<!-- Show all transactions, or optionally all transactions in a category -->
<head>
	<title>Monthly cost</title>

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
			
			var dd = document.getElementById("categoryDropdown");
			var categoryName = dd.options[dd.selectedIndex].text;
			
			var graphHeight =  document.getElementById("graphHeight");
			var chartHeight = graphHeight.value;
			
			console.log("height is ");
			console.log(chartHeight);
			
			var graphWidth =  document.getElementById("graphWidth");
			var chartWidth = graphWidth.value;
			
			var jsonData = $.ajax({
				type: "POST",
				url: "getGraphDataFromDB.php",
				data: ({category : categoryName }),
				dataType:"json",
	          async: false
	          }).responseText;
			
	      // Create our data table out of JSON data loaded from server.
		try {
			var data = new google.visualization.DataTable(jsonData);
		}
		catch (e) {
		    alert('DataTable construction failed' + e);
		}	
	      // Instantiate and draw our chart, passing in some options.


		var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
	      chart.draw(data, {width: chartWidth, height: chartHeight, 
			title: 'Monthly cost of ' + categoryName,
			hAxis: { title: 'Monthly cost of ' + categoryName}
			});
			
			newTitle = "Monthly cost for " + categoryName;
			document.title = newTitle;
	    }

	    </script>
</head>
<body>

	<!--Div that will hold the column chart-->
    <div id="chart_div" class="GoogleGraph"></div>
<div class="graphSize">Graph height: 
	<input type="text" id="graphHeight" size="5" value="600" onChange="drawChart()"></div>
<div class="graphSize">Graph width: 	
<input type-"text" id="graphWidth" size="5" value="800" onChange="drawChart()"></div>


	<div class="categorychooser">Choose category to graph: 
	<?php
	include 'functionsv2.php';
categoryDropdownWithChange("categoryDropdown","drawChart()");
	?>
	</div>

</body>
</html>