<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!-- Show all transactions, or optionally all transactions in a category -->
    
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    
	<title>Set up database tables...
	</title>

	<link rel="stylesheet" type="text/css" href="BudgetStyle.css">
	<link href="simpleCSSDropDownMenu.css" rel="stylesheet" type="text/css" media="screen">
	
    <!-- Don't include jquery twice, it's added at the bottom for bootstrap 
	<script type="text/javascript" language="javascript" src="jquerysrc/jquery-2.1.4.js"></script>
    -->
    
</head>
<body>
<h1>Attempting to create tables...</h1>
<?php

include 'navbar.php';

include 'functionsv2.php';
include 'db.php';

echo "<br/>";
echo "<hr/>";
global $dbuser, $dbpassword, $database, $transactionTable, $servername;
$servername = 'localhost';

// Create connection
$conn = new mysqli($servername, $dbuser, $dbpassword, $database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// sql to create transactions table
$sql = "CREATE TABLE $transactionTable (
    transid INTEGER auto_increment primary key,
    transdate DATE,
    transamt FLOAT,
    transtext VARCHAR(255),
    transcat ENUM('Groceries','Cash withdrawals')
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>";
    echo "Table " . $transactionTable . " created successfully";
    echo "</p>";
} else {
    echo "Error creating table " . $transactionTable . ":" . $conn->error;
}
    
    // sql to create category strings table
$sql = "CREATE TABLE $categoryTable (
    guessid INTEGER auto_increment primary key,
    category TEXT,
    guess TEXT
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>";
    echo "Table " . $categoryTable . " created successfully";
    echo "</p>";
} else {
    echo "<p>Error creating table " . $categoryTable . ":" . $conn->error . "</p>";
}

$conn->close();

?>
    
<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
      
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</body>
</html>
