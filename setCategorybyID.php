<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Show a Transaction by ID</title>
	<link rel="stylesheet" type="text/css" href="BudgetStyle.css">
	<link rel="stylesheet" type="text/css" href="DynamicElements.css">
	
</head>
<body>

<?php
// This routine sets the category for a single transaction passed in via POST referenced by the
//  transaction ID.
include 'db.php';
include 'functionsv2.php';

global $dbuser, $dbpassword, $database, $transactionTable;

if (isset ($_POST['category'])) {
  $cat=$_POST['category'];
} else if (isset ($_GET['category'])) {
	$cat=$_GET['category'];
} else {
	$cat = "Groceries"; // A useful default category
}
if (isset ($_POST['transID'])) {
  $transactionID=$_POST['transID'];
} else if (isset ($_GET['transID'])) {
	$transactionID=$_GET['transID'];
} else {
	$transactionID = "100"; // A dummy entry?
}

modifycat($transactionID,$cat);
?>
<p>
	<?php
	print "Category modified to " . $cat;
	?>
</p>
<?php
include 'footer.php';
?>
</body>
</html>