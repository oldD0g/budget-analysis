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

modifycatAJAX($transactionID,$cat);
print "Set transaction " . $transactionID . " to " . $cat;
?>