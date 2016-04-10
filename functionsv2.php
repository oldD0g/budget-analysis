<?php
// This set of functions is used by other routines in the budget routines.
include 'db.php';  
// Set the timezone here...should really obtain this from system environment?
date_default_timezone_set("Australia/ACT");

// This function allows a routine to exit cleanly with a valid HTML page
// Note that most routines create an HTML intro consisting of a <head> portion
// before this routine can be called so it does not create that part of the page

function budgetFail($errorMessage)
{
	?> 
	<h1>Sorry</h1>
	<p class="error">Sorry, I could not complete that operation. The error was: <b>
	<?php echo "$errorMessage"; ?>
	</b></p>
	</body>
	</html>
	<?php die();
	
}

function create_cat_array()
/* Grab all the category guess strings from the database and return them as an array.
  This can then be used when processing entries in the database or when they are uploaded
  to see what category they should be put into.
  */
  
{
	global $dbuser, $dbpassword, $database, $transactionTable;

if (! ($myDB = mysql_connect("localhost","$dbuser","$dbpassword"))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

if (! mysql_select_db("$database",$myDB)) {
                die("function create_cat_array: Unable to select $database database\n");
}

$query="select * from catstrings";
 
if ($result = mysql_query($query,$myDB)) {
     while ($row = mysql_fetch_array($result,1)) { 
     	$category=$row['category'];
     	$guessid=$row['guessid'];
     	$guess=$row['guess'];
     	
     	//echo "Processing $category, $guessid, $guess<br>";
     	$guessarray["$category"][] =  $guess;

     }
     return $guessarray;
} else {
	print "Failed to execute query in create_cat_array!";
	mysql_error();
	die;
}     
        
}

function db_insert($description,$date,$amount)
/* Insert an entry into the database
*/
{

	global $dbuser, $dbpassword, $database, $transactionTable;

        if (! ($myDB = mysql_connect("localhost","$dbuser","$dbpassword"))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

        if (! mysql_select_db("$database",$myDB)) {
                die("function db_insert: Unable to select $database database\n");
        } 

  $query="INSERT into " . $transactionTable . " (transtext,transdate,transamt) values(\"$description\",\"$date\",\"$amount\");";
  //print "executing $query<br>";
  if ($result = mysql_query($query,$myDB)) {
	//print "Insert of $description successful<br>\n";
	return true;
  } else {
	//print "Insert of $description failed!\n<br>";
	return false;
  }
  mysql_close($myDB);
}


function dropdown($name,$table,$column,$initialvalue)
/* Create a drop down for a form to insert data from an ENUM
  The argument to this function is the field to create the dropdown from.
  Initialvalue is the choice in the dropdown to set to initially.  If it
  is blank we create an entry called "Not set" and set it to this.

  Note that this function has been updated to support AJAX callback to a function
  called setNewCategory, which sets the category of the transaction to that choice.
*/

{

	global $dbuser, $dbpassword, $database, $transactionTable;
	

        if (! ($myDB = mysql_connect("localhost","$dbuser","$dbpassword"))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

        if (! mysql_select_db("$database",$myDB)) {
                die("function dropdown: Unable to select $database database\n");
        } 
		
		// This query will return a description of the ENUM so that it can be parsed
		// and then used to make a dropdown containing each value.
		
		$query="show columns from $table like '$column'";

		
        if ($result = mysql_query($query,$myDB)) {
                while ($row = mysql_fetch_array($result,MYSQL_NUM)) {
                        /* The "Type" entry contains the list of options since that
                                defines this fields type.  Note that this probably fails
                                disastrously if the field is not an ENUM!  */                        
						$list = $row[1];
						//print "(dropdown function) Received \"$list\" from database...";
						// The output from the show columns command has all the quotes and commas that
						// we need to kill off with the string commands below...
						$enum = str_replace("enum('", "", $list); // Delete the enum( at the start
                        
                        $enum = ereg_replace("'\)$", "", $enum); // Delete the ) at the end

                        $enum = str_replace("','", ",", $enum); // Delete the single quotes, replace with comma

						$enum = str_replace("''","'",$enum); // Delete any double single quotes, replace with one
						
                        $enum = explode(",", $enum); // Use explode to turn the comma separated list into array
                        
				}
				//print "Sorting enum";
				sort($enum);
				$id = substr($name,-4);

				// Now work through and print the dropdown
				echo "<SELECT name=$name onchange=\"setNewCategory(this.name,this.value);\">\n"; 
                foreach ($enum as $option) {
					echo "\t" . "<OPTION ";
					if ($option == $initialvalue) {
						print "SELECTED ";
					}
					print 'value="'. $option.'">'. $option . "</OPTION>\n";
				}
				
				if ($initialvalue =="") {
					print "<OPTION SELECTED value=\"Not set\">Not set</OPTION>\n";
				}
        
				echo '</SELECT>' . "\n"; 
		} else {
			print "Could not run query against database\n<br>\n";
        }

mysql_close($myDB);
} 

function dropdowntext($name,$table,$column,$initialvalue)
/* This function is identical to the dropdown function above except that it returns the text of
  the dropdown.  This allows pages which need lots of dropdowns to perform this function once, then
  include the text as necessary without further database calls.
*/

{

	global $username, $password, $database;

        if (! ($myDB = mysql_connect("localhost","$dbuser","$dbpassword"))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

        if (! mysql_select_db("$database",$myDB)) {
                die("Unable to select $database database\n");
        } 
		
        $query="show columns from $table like '$column'";
        //print "Running $query<br>\n";

        if ($result = mysql_query($query,$myDB)) {
                if ($row = mysql_fetch_array($result,1)) {
                        /* The "Type" entry contains the list of options since that
                                defines this fields type.  Note that this probably fails
                                disastrously if the field is not an ENUM!  */

                        $list = $row['Type'];
                        // Strip the "enum(" from the front
              			$enum = str_replace("enum('", "", $list);
              			
                        //Strip the ) from the end of the line
                        $enum = ereg_replace("'\)$", "", $enum);
                        
                        //Replace all the ',' which end a category, separate it from the next and start a new one
                        //  with just a comma.
                        // e.g.  'Groceries','Clothes' becomes 'Groceries,Clothes'
                        $enum = str_replace("','", ",", $enum);
                        
                        // Replace any double quotes, returned whenever a value includes a quote, with a single
                        //  quote for display
                        //  e.g. Ivan''s clothes becomes Ivan's clothes
                        $enum = str_replace("''","'",$enum);
                        
                        // Use explode to break it up into separate components
                        $enum = explode(",", $enum); 
						sort($enum);
						$id = substr($name,-4);
                        $result="<SELECT name=$name onchange=\"settickon('$id')\">"; 
                        foreach ($enum as $option) {
                                $result .= "\t" . "<OPTION ";
                                if ($option == $initialvalue) {
                                	$result .= "SELECTED ";
                                }
                                $result .= 'value="'. $option.'">'. $option . "\n";
                        }
                        if ($initialvalue =="") {
                        	$result .= "<OPTION SELECTED value=\"Not set\">Not set\n";
                        }
        
                        $result .= '</SELECT>' . "\n"; 
                }
        } else {
        		$result = '<SELECT name=$name> <OPTION SELECTED value="Unknown">Database failed!</SELECT>';
                print "Could not run query against database\n<br>\n";
        }

mysql_close($myDB);
return $result;
} 

function modifycat($transid,$newcategory)
/* Alter the category for the given transaction ID to newcategory.
*/

{

	global $dbuser, $dbpassword, $database, $transactionTable;

        if (! ($myDB = mysql_connect("localhost","$dbuser","$dbpassword"))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

        if (! mysql_select_db("$database",$myDB)) {
                die("Unable to select $database database\n");
        } 
        
        $query="select transtext,transdate from " . $transactionTable . " where transid='$transid';";
        if ($result = mysql_query($query,$myDB)) {
                while ($row = mysql_fetch_array($result,MYSQL_BOTH)) {
                       $text=$row['transtext'];
                       $date=$row['transdate'];
                }
                $query="update " . $transactionTable . " set transcat='$newcategory' where transid='$transid';";
                if ($result = mysql_query($query,$myDB)) {
                	print "Updated $text on $date to category $newcategory (transid $transid)<br>\n";
                } else {
                	print "Unable to update category for transaction ID $transid - error was " . mysql_error() . "<br>\n";
                }
        } else {
                print "Could not run query against database\n<br>\n";
        }

    mysql_close($myDB);
} 

function modifycatAJAX($transid,$newcategory)
/* Alter the category for the given transaction ID to newcategory.
This version of the function is designed to run from an AJAX query and so doesn't produce
output - a later version of this should return a status and perhaps a message if an error
occurs.
*/

{

	global $dbuser, $dbpassword, $database, $transactionTable;

        if (! ($myDB = mysql_connect("localhost","$dbuser","$dbpassword"))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

        if (! mysql_select_db("$database",$myDB)) {
                die("Unable to select $database database\n");
        } 
        
        $query="select transtext,transdate from " . $transactionTable . " where transid='$transid';";
        if ($result = mysql_query($query,$myDB)) {
                while ($row = mysql_fetch_array($result,MYSQL_BOTH)) {
                       $text=$row['transtext'];
                       $date=$row['transdate'];
                }
                $query="update " . $transactionTable . " set transcat='$newcategory' where transid='$transid';";
                if ($result = mysql_query($query,$myDB)) {
                	//print "Updated $text on $date to category $newcategory (transid $transid)<br>\n";
                } else {
                	//print "Unable to update category for transaction ID $transid - error was " . mysql_error() . "<br>\n";
                }
        } else {
                //print "Could not run query against database\n<br>\n";
        }

    mysql_close($myDB);
}

function renamecat($oldcategory,$newcategory)
/* Alter the category name from oldcategory to newcategory.
This is a multistep process:
1.  Find all the transactions with this category.
2.  Alter the enum so that oldcategory becomes newcategory.  At this point all those
	transactions are given an empty category.
3.  For each transaction identified in 1., alter the category to newcategory.
*/

{

	global $dbuser, $dbpassword, $database, $transactionTable;


        if (! ($myDB = mysql_connect("localhost","$dbuser","$dbpassword"))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

        if (! mysql_select_db("$database",$myDB)) {
                die("Unable to select $database database\n");
        } 

        echo "Renaming category $oldcategory to new name $newcategory<br>\n";
        $query="select transid,transtext,transdate from " . $transactionTable . " where transcat='$oldcategory';";
        $affectedids=array();
        if ($result = mysql_query($query,$myDB)) {
                while ($row = mysql_fetch_array($result,1)) {
                       $id=$row['transid'];
                       $text=$row['transtext'];
                       $date=$row['transdate'];
                       echo "Identified ID $id on $date ($text) as in $oldcategory<br>\n";
                       array_push($affectedids,$id);
                       
                }
                // Now alter the ENUM definition
                
                $query="show columns from " . $transactionTable . " like 'transcat'";
        		if ( !$result = mysql_query($query,$myDB)) { die("Couldn't show columns to get category names!"); }
        		
                if (! $row = mysql_fetch_array($result,1)) { die ("Didn't find any rows in query result!"); }
                        /* The "Type" entry contains the list of options since that
                                defines this fields type.  */

                 $currentcatlist = $row['Type'];
                 $newcatlist = str_replace("$oldcategory", "$newcategory", $currentcatlist); 
                        
                $query="alter table commvisa modify transcat $newcatlist;";
                echo "Preparing alter table statement : $query<br>\n";

                if ($result = mysql_query($query,$myDB)) {
                	print "Updated table with new category definition<br>\n";
                } else {
                	print "Unable to update category definition, error was ". mysql_error() . "<br>\n";
                }
                // Now go through each affected transaction and modify the category to the new value
                foreach ($affectedids as $ID) {
                  echo "Setting category for transid $ID to $newcategory<br>\n";
                  $query="update " . $transactionTable . " set transcat='$newcategory' where transid='$ID';";
                  if (! $result = mysql_query($query,$myDB)) { echo("Failed to update category for ID $ID<br>\n"); }
                }
        } else {
                print "Could not run query against database\n<br>\n";
        }

    mysql_close($myDB);
}

function newcat($newcategory)
/* Create a new category by altering the ENUM definition
*/

{
	global $dbuser, $dbpassword, $database, $transactionTable;
	
        if (! ($myDB = mysql_connect("localhost","$dbuser","$dbpassword"))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

        if (! mysql_select_db("$database",$myDB)) {
                die("function newcat: Unable to select  database \"$database\"\n");
        } 

        echo "Creating new category $newcategory<br>\n";
        // Now alter the ENUM definition
                
        $query="show columns from " . $transactionTable . " like 'transcat'";
      	if ( !$result = mysql_query($query,$myDB)) { die("Couldn't show columns to get category names!"); }
        		
        if (! $row = mysql_fetch_array($result,1)) { die ("Didn't find any rows in query result!"); }
         /* The "Type" entry contains the list of options since that
                     defines this fields type.  */

         $currentcatlist = $row['Type'];
		print "(newcat function) Got $currentcatlist for current category list";
         $matchstr = "'" . $newcategory . "'";
         $matchstr = str_replace("'","''",$matchstr);
    
         $found = strpos($currentcatlist,$matchstr);
         if ($found !== false) { // This category already exists!?
           print "Category $newcategory already exists!";
           return;
         }

         $newcatlist = str_replace("')", "','$newcategory')", $currentcatlist); 
                        
         $query="alter table " . $transactionTable . " modify transcat $newcatlist;";
         echo "Preparing alter table statement : $query<br>\n";

         if ($result = mysql_query($query,$myDB)) {
                	print "Updated table with new category definition<br>\n";
         } else {
                	print "Unable to update category definition, error was ". mysql_error() . "<br>\n";
         }

    mysql_close($myDB);
}

function printcats()
/* Print out all the current categories for display
*/

{
	global $dbuser, $dbpassword, $database, $transactionTable;

        if (! ($myDB = mysql_connect("localhost","$dbuser","$dbpassword"))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

        if (! mysql_select_db("$database",$myDB)) {
                die("Unable to select $database database\n");
        } 

        
       $query="show columns from " . $transactionTable . " like 'transcat'";
        if ($result = mysql_query($query,$myDB)) {
         if (! $row = mysql_fetch_array($result,1)) { die("Couldn't get transcat description, fetch_array failed"); }
                
                        /* The "Type" entry contains the list of options since that
                                defines this fields type.  Note that this probably fails
                                disastrously if the field is not an ENUM!  */

                $list = $row['Type'];
              	$enum = str_replace("enum('", "", $list);
                 $enum = ereg_replace("'\)$", "", $enum);
                 $enum = str_replace("','", ",", $enum);
                 $enum = str_replace("''","'",$enum);
                 $enum = explode(",", $enum); 

                 foreach ($enum as $option) {
                    echo "$option&nbsp;\n";
                 }
        } else {
          echo "Failed to show columns!";
        }
}
  
  function startandenddate()
/* Return the first and last dates in the database
*/

{
	global $dbuser, $dbpassword, $database, $transactionTable;
	
        if (! ($myDB = mysql_connect("localhost",$dbuser,$dbpassword))) {
          die("Connection to MySQL server 'localhost' failed!<br>\n");
        }

        if (! mysql_select_db($database,$myDB)) {
                die("(startandenddatefunction) Unable to select $database database\n");
        } 

       $query="select min(transdate) as mindate,max(transdate) as maxdate from " . $transactionTable;
        if ($result = mysql_query($query,$myDB)) {
         if (! $row = mysql_fetch_array($result,1)) { die("Couldn't get results from query, fetch_array failed"); }

                $startdate = $row['mindate'];
              	$enddate = $row['maxdate'];
                 $result = array($startdate,$enddate);
                 return($result);
        } else {
          echo "Failed to show columns!";
        }
}
                                  
?>