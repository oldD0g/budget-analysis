<!doctype html public "-//w3c//dtd html 4.0 transitional//en"><html><head>   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">   <meta name="Author" content="Ivan Dean">   <title>Create a new category</title></head><body><h1>Create a new category</h1><?phpinclude 'functions.php';  $create = $_POST['create'];  $newcat = $_POST['newcat'];  if (isset ($create) && isset($newcat) && ($newcat != "")) {	newcat($newcat);  } ?><form method="post" action ="<?php echo $_SERVER['PHP_SELF'] ?>">New category: <input type="text" name="newcat"><input type="submit" name="create" value="Create"></form><?php  // Print out all the current categories for reference  echo "<h3>Current categories:</h3>";  printcats();  echo "<br>";  include 'footer.php';  ?>  </body></html>