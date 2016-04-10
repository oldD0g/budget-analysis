<?php

if (isset($_POST['listtrans'])) {
  $startdate=$_POST['startdate'];
  $enddate=$_POST['enddate'];
  $url="showtrans.php?startdate=" . $startdate . "&enddate=" . $enddate;
  header("Location: $url"); /* Redirect to showtrans with date arguments */
  //echo "List transactions";
  exit;
 }
 if (isset($_POST['summarisetrans'])) {
  $startdate=$_POST['startdate'];
  $enddate=$_POST['enddate'];
  $url="summarise.php?startdate=" . $startdate . "&enddate=" . $enddate;
  header("Location: $url"); /* Redirect to showtrans with date arguments */
  //echo "List transactions";
  exit;
 }
 
 ?>
 
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Choose Period</title>
	<meta name="generator" content="BBEdit 7.1.1" />
	<script language="javascript" src="datepicker/overlib_mini.js"></script>
   <script language="javascript" src="datepicker/datepicker.head.new"></script>
</head>
<body>

<h1>Choose a Period to Analyse</h1>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<?php echo '<form name="sample" method="post" action="' . $_SERVER['PHP_SELF'] . '">'; ?>

<p>Beginning date: <input type="text" name="startdate" size="20">  
        <!-- ggPosX and ggPosY not set, so popup will autolocate to the right of the graphic -->
        <a href="javascript:show_calendar('sample.startdate');" 
        	onMouseOver="window.status='Date Picker'; 
			overlib('Click here to choose a date from a one month pop-up calendar.'); 
        	return true;" 
        	onMouseOut="window.status=''; nd(); return true;">
        	<img src="datepicker/show-calendar.gif" width=24 height=22 border=0></a>
        </p>
        
<p>End date: <input type="text" name="enddate" size="20">  
        <!-- ggPosX and ggPosY not set, so popup will autolocate to the right of the graphic -->
        <a href="javascript:show_calendar('sample.enddate');" onMouseOver="window.status='Date Picker'; 
        overlib('Click here to choose a date from a one month pop-up calendar.'); 
        return true;" onMouseOut="window.status=''; nd(); return true;">
        	<img src="datepicker/show-calendar.gif" width=24 height=22 border=0></a>
        </p>
        <p><input type="submit" value="Submit" name="B1"><input type="reset" value="Reset" name="B2"></p>
        <p><input type="submit" value="List transactions" name="listtrans">&nbsp;
        <input type="submit" value="Summarise transactions" name="summarisetrans"></p>


</form>

<?php 
include 'footer.php';
?>
</body>
</html>
