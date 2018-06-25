<html>
<body>
<link href="style1.css" rel="stylesheet" type="text/css">
<form method="post" name="frm" action="<?php echo $PHP_SELF?>">

<script language="JavaScript">
function toggle(source) {
  checkboxes = document.getElementsByName('chk[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>

<?php
include 'db_connect.php';
include 'common.php';

#########################################################################################
#											#
#  Function to display the Testcase from Database, Also sets appropriate del/mod flags	#
#											#
#########################################################################################

function dispTCase($viewType, $act_mod_name) {
include 'db_connect.php';
include 'common.php';

if ($viewType == "assignCase") {
  $formDirect=$PHP_SELF;
}

echo "<br><br><br><center>";
echo "<b><font size=2>Listing Test cases</font></b><br><br></center>";

# Displaying the Users List as Option
$sql = "SELECT act_user_name FROM act_user_account order by act_user_name";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
echo "<table border=1>\n";
echo "<tr><td class=a>Assign To</td>";
echo "<td><SELECT name=assignTo>";
echo "<OPTION SELECTED VALUE=\"\"></OPTION>";
     while($row = $result->fetch_assoc()) {
        printf("<OPTION VALUE=\"%s\">%s</OPTION>\n",
                $row["act_user_name"],
                $row["act_user_name"]);
    }
echo "</td></tr></SELECT></table><br><br>";
} else {
    echo "0 users found";
}

# Displaying the Testcase with Checkbox
$sql = "select * from $test_report_db where act_test_module=\"$_GET[act_mod_name]\" order by act_test_case_no";
$result = $conn->query($sql);

$count=0;
$c=0;
if ($result->num_rows > 0) {
echo "<table border=1>\n";
echo "<tr>
	<td class=a><input type=\"checkbox\" onClick=\"toggle(this)\" /> <br/></td>
	<td class=a>Testcase ID</td>
	<td class=a>Project</td>
	<td class=a>Module</td>
	<td class=a>Testsuite</td>
	<td class=a>Title</td>
	<td class=a>Release ID</td>
	<td class=a>Priority</td>
	<td class=a>Status</td>
	<td class=a>Toplogy</td>
	<td class=a>Scripted On</td>
	<td class=a>Reviewed On</td>
	<td class=a>Reworked On</td>
	<td class=a>Checkin On</td>
	<td class=a>Script Name</td>
	<td class=a>CR Details</td>
	<td class=a>Test Result</td>
	<td class=a>Comments</td>
	<td class=a>Assigned To</td>
       </tr>\n";
     while($row = $result->fetch_assoc()) {
        printf("<tr>
		<td><input type=checkbox name=chk[] value=%s>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		</tr>\n",
		$row["act_test_case_no"], 
		$row["act_test_case_id"], 
		$row["act_test_project"], 
		$row["act_test_module"], 
		$row["act_test_suite"], 
		$row["act_test_title"], 
		$row["act_test_release_id"], 
		$row["act_test_priority"], 
		$row["act_test_status"], 
		$row["act_test_topology"], 
		$row["act_test_scripted_date"], 
		$row["act_test_review_date"], 
		$row["act_test_rework_date"], 
		$row["act_test_checkin_date"], 
		$row["act_test_script_name"], 
		$row["act_test_defect_id"], 
		$row["act_test_result"], 
		$row["act_test_comments"], 
		$row["act_test_assigned_to"] 
	       );
     $c = $c + 1;
     $count++;
    }
} else {
}
echo "</table><br><br><b><center><font size=2>Total Testcases : ". $c ." <br><br>";
printf("<input type=hidden name=totalCase value=%s\n", $count);
  if ($c != 0) {
	echo "<br><center><input type=\"Submit\" name=\"submit\" value=\"Assign Testcase\" onClick=\"return confirm('All dates will be reset for the selected Testcases. Continue?')\">";
  } else  {
  }
$conn->close();
}

#########################################################################################
#                                                                                       #
# Handle Submit Information and Update the user record accordingly                      #
#                                                                                       #
#########################################################################################

if ($_REQUEST[submit]) {
   
if(!empty($_POST['chk'])) {
    foreach($_POST['chk'] as $tCaseNo) {
       $sql = "update $test_report_db set act_test_assigned_to='$_POST[assignTo]', act_test_checkin_date='0000-00-00', act_test_rework_date='0000-00-00', act_test_scripted_date='0000-00-00', act_test_review_date='0000-00-00' where act_test_case_no='$tCaseNo'";
       // echo $sql;
       if ($conn->query($sql) === TRUE) {
          //echo "<br><BR><b><center>User $_REQUEST[user_name] Modified Successfully!<p></b>";
       } else {
          echo "Error updating record: " . $conn->error;
       }
    }
}
$conn->close();
}

#########################################################################################
#											#
# Code to check whether Delete View, Modify View or Normal View Needs to be shown       #
#											#
#########################################################################################
if ($_GET[assignCase == "yes"]) {
      dispTCase("assignCase",$_GET[act_mod_name]); 
} else {
      dispTCase("dummy"); 
}

?>
</body>
</html>
