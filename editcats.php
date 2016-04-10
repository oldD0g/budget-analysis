<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Edit categories</title>
</head>
<body>
<?php
include 'db.php';
include 'functionsv2.php';
global $dbuser, $dbpassword, $database, $transactionTable;

// Lists all available categories and allows their names to be changed.

if (! ($myDB = mysql_connect("localhost",$dbuser,$dbpassword))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

        if (! mysql_select_db($database,$myDB)) {
                die("Unable to select budget database\n");
        } 

        $query="show columns from " . $transactionTable . " like 'transcat'";
        if ($result = mysql_query($query,$myDB)) {
                while ($row = mysql_fetch_array($result,1)) {
                        /* The "Type" entry contains the list of options since that
                                defines this fields type.  Note that this probably fails
                                disastrously if the field is not an ENUM!  */

                        $list = $row['Type'];
                        //$enum = str_replace("enum(", "", $list); 
              			$enum = str_replace("enum('", "", $list);
                        //$enum = ereg_replace("\)$", "", $enum); 
                        $enum = ereg_replace("'\)$", "", $enum);
                        //$enum = str_replace("'", "", $enum); 
                        $enum = str_replace("','", ",", $enum);
                        $enum = str_replace("''","'",$enum);
                        $enum = explode(",", $enum); 
						sort($enum);

                        echo '<form method="post" action="processcats.php">';
                        echo '<table border="1">' . "\n";
                        echo '<tr><th>Category</th><th>New name</th></tr>' . "\n";
                        
                        $i=1; 
                        foreach ($enum as $option) {
                        	$index=sprintf("%03d",$i); $catoldname=cat . $index; $catnewname =newcat . $index;
                            echo '<tr><td><input name= "'. $catoldname . '" type="text" readonly value="' . $option . '"></td>';
                            echo '<td><input type="text" name="' . $catnewname . '"></td>';
                            echo "</tr>\n";
                            $i++;
                                
                        }
                        echo "</table>\n";
                        ?>
                        <input type="submit" name="submit" value="Submit"></form>
                        <?php
        
                }
        } else {
                print "Could not run query against database\n<br>\n";
        }

mysql_close($myDB);

include 'footer.php';
?>

</body>
</html>
