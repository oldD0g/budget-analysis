<html>
<head>
<title>Edit strings for category</title>
</head>
<body>
<?php 
include 'functionsv2.php';
include 'db.php';

global $dbuser, $dbpassword, $database, $transactionTable;

/* Edits strings for a nominated category.  This script is called as the form
action from editguesses.php
*/

if (isset ($_POST['category'])) {
  $cat=$_POST['category'];
} else if (isset ($_GET['category'])) {
	$cat=$_GET['category'];
} else {
	$cat = "Groceries"; // A useful default category
}

//echo "Editing strings for category $cat\n";

/* Collect all the entries from the catstrings table where category matches,
and present in a form that allows adding a new string or editing of the existing
ones.  */

$handle = mysql_connect("localhost",$dbuser,$dbpassword) || die("Failed to connect to db");

mysql_select_db($database) || die("Failed to select $database DB");

$query = "select guessid,guess from catstrings where category='$cat';";

if ($result = mysql_query($query)) {
  ?> <form method="post" action ="processguessupdates.php">

<h2>Add an entry</h2>
<input type="hidden" name="thiscat" value="<?php echo $cat ?>">
New string to match category <?php echo $cat ?>: <input type="text" name="newstring">
<input type="submit" name="addnewstring" value="Add">

  <h2>Existing entries</h2>
  <table border="1">
  <?php
     
  echo "<tr><th>GuessId</th><th>Guess</th></tr>\n\n";

  $i=1;
  while ($row=mysql_fetch_array($result)) {
  	$id=$row['guessid'];
  	$string=$row['guess'];
  	echo "<tr><td>" . $id . "</td><td>" . $string . "</td></tr>\n";
  }
  echo "</table>\n";
} else {
	print "Database query failed!\n";
	mysql_error();
}
?>

</form>
<?php include 'footer.php' ?>
</body>
</html>