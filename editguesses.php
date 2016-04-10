<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Edit strings for categories</title>
	<link href="BudgetStyle.css" rel="stylesheet" type="text/css" media="screen">
</head>
<body>
<h1>Edit strings for categories</h1>
<?php
// Lists all available category guesses.  A "category guess" is a set of strings that are
// used when loading in transaction data to decide which category a transaction should be in.
// They're called guesses becauses they're not always right, but careful selection of the strings
// can save a lot of manual reallocation.  For instance "COLES" is a good guess for the groceries
// category - although it's usually safer to list a series of longer strings like "COLES SUPERMARKET JAMISON".

/*  The table looks like:
mysql> describe catstrings;
+----------+------+------+-----+---------+-------+
| Field    | Type | Null | Key | Default | Extra |
+----------+------+------+-----+---------+-------+
| category | text | YES  |     | NULL    |       |
| guesses  | text | YES  |     | NULL    |       |
+----------+------+------+-----+---------+-------+
*/
include 'functionsv2.php';
include 'db.php';

global $dbuser, $dbpassword, $database, $transactionTable;

  echo '<FORM ACTION="editstrings.php" METHOD="POST">';
    echo "Edit strings for category: ";
    dropdown(category,$transactionTable,transcat,Groceries);
    echo '<input type="submit" name="submit" value="Go">';
    echo '</form>';
    
// Also display the current set of categories and the user can just click on
//  the category to edit it as the drop down can become quite large.
if (! ($myDB = mysql_connect("localhost",$dbuser,$dbpassword))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
}

if (! mysql_select_db($database,$myDB)) {
                die("Unable to select $database database\n");
} 
echo "<h2>Existing strings</h2>\n";
$query="select category from catstrings group by category";
        
if ($result = mysql_query($query,$myDB)) {
    echo '<table border="1">' . "\n";
                
    while ($row = mysql_fetch_array($result,1)) {

       $catname = $row['category'];
       echo "<tr><td><a href=\"editstrings.php?category=" . $catname . "\">$catname</a></td>\n";
       
       echo "</tr>\n";        
 
   }
    echo "</table>\n";
    echo "<hr>\n";
  
} else {
      print "Could not run query against database\n<br>\n";
      mysql_error();
}

mysql_close($myDB);

include 'footer.php';
?>

</body>
</html>
