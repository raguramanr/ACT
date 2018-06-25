<html>
<body>
<link href="style1.css" rel="stylesheet" type="text/css">
<form method="post" name="frm" action="<?php echo $PHP_SELF?>">
<?php
include 'common.php';

#########################################################################################
#											#
#  Function to display the Testcase from Database, Also sets appropriate del/mod flags	#
#											#
#########################################################################################

function dispTCase($viewType, $act_mod_name) {
include 'db_connect.php';
include 'common.php';

if ($viewType == "delView") {
  $reqAction="delete";
  $actFlag="yes";
  $cnfrmFlag="onClick";
  $formDirect=$PHP_SELF;
} elseif ($viewType == "modView") {
  $reqAction="editCase";
  $actFlag="yes";
  $cnfrmFlag="dummy";
  $formDirect=$PHP_SELF;
} else {
  $reqAction="action";
  $actFlag="nothing";
  $cnfrmFlag="dummy";
  $formDirect=$PHP_SELF;
}

if ($_GET[delCase] == "yes" || $_GET[modCase] == "yes" || $act_mod_name != "" || $_GET[action] == "nothing" ) {
 $sql = "select * from $test_report_db where act_test_module=\"$_GET[act_mod_name]\"";
} else {
 $sql = "select * from $test_report_db";
}


$c=0;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
echo "<br><br><br><center>";
echo "<b><font size=2>Listing Test cases</font></b><br><br>";
echo "<table border=1>\n";
echo "<tr>
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
	<td class=a>Regression</td>
       </tr>\n";
     while($row = $result->fetch_assoc()) {
        printf("<tr>
		<td class=$row[act_test_status]><a href=\"%s?act_mod_name=%s&act_test_case_no=%s&$reqAction=$actFlag\" $cnfrmFlag=\"return confirm('Sure? Once deleted, the record CANNOT be reverted.')\">%s</td>
		<td class=$row[act_test_status]><a href=\"%s?act_mod_name=%s&act_test_case_no=%s&$reqAction=$actFlag\" $cnfrmFlag=\"return confirm('Sure? Once deleted, the record CANNOT be reverted.')\">%s</td>
		<td class=$row[act_test_status]><a href=\"%s?act_mod_name=%s&act_test_case_no=%s&$reqAction=$actFlag\" $cnfrmFlag=\"return confirm('Sure? Once deleted, the record CANNOT be reverted.')\">%s</td>
		<td class=$row[act_test_status]><a href=\"%s?act_mod_name=%s&act_test_case_no=%s&$reqAction=$actFlag\" $cnfrmFlag=\"return confirm('Sure? Once deleted, the record CANNOT be reverted.')\">%s</td>
		<td class=$row[act_test_status]><a href=\"%s?act_mod_name=%s&act_test_case_no=%s&$reqAction=$actFlag\" $cnfrmFlag=\"return confirm('Sure? Once deleted, the record CANNOT be reverted.')\">%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		<td class=$row[act_test_status]>%s</td>
		</tr>\n",
		$formDirect, $_GET[act_mod_name], $row["act_test_case_no"], $row["act_test_case_id"], 
		$formDirect, $_GET[act_mod_name], $row["act_test_case_no"], $row["act_test_project"],
		$formDirect, $_GET[act_mod_name], $row["act_test_case_no"], $row["act_test_module"],
		$formDirect, $_GET[act_mod_name], $row["act_test_case_no"], $row["act_test_suite"],
		$formDirect, $_GET[act_mod_name], $row["act_test_case_no"], $row["act_test_title"],
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
		$row["act_test_assigned_to"],
		$row["act_test_regression"] 
	       );
     $c = $c + 1;
    }
} else {
    // echo "<center><b>0 Testcases found";
}
echo "</table><br><br><b><center><font size=2>Total Testcases : ". $c ." <br><br><br <br><br><br>";
$conn->close();
}

#########################################################################################
#											#
#  Function to delete the given module from database					#
#											#
#########################################################################################
function remCase($act_test_case_no) {
include 'db_connect.php';
include 'common.php';

$sql = "DELETE FROM $test_report_db WHERE act_test_case_no='$act_test_case_no'";
 if (mysqli_query($conn, $sql)) {
      echo "<br><br><b><center><font size=2>Testcase deleted successfully - Record Number ($act_test_case_no) <br><br>";
      dispTCase(D,$_GET[act_mod_name]);
 } else {
     echo "<br><br><b><center>Error deleting record: " . mysqli_error($conn);
 }
}

#########################################################################################
#                                                                                       #
#  Function to modify the given module from database                                    #
#                                                                                       #
#########################################################################################
function ediTCase($act_mod_name, $act_test_case_no) {
include 'db_connect.php';
include 'common.php';

echo "<br><br><center><b><font size=2>Edit TestCase : $act_test_case_no,  $act_mod_name</b><br><br>";
$sql = "select * from $test_report_db where act_test_case_no='$act_test_case_no'";
$result = $conn->query($sql);
echo "<table align=center border=1>\n";
if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
        echo "<input type=hidden name=act_test_case_no value=\"$row[act_test_case_no]\">";
        echo "<tr><td class=a>Test case ID</td>          <td><input size=50 type=Text name=act_test_case_id value=\"$row[act_test_case_id]\"></td></tr>";
        echo "<tr><td class=a>Test Project</td>           <td><input size=50 type=Text name=act_test_project value=\"$row[act_test_project]\"></td></tr>";
        echo "<tr><td class=a>Test Module</td>             <td><input size=50 type=Text name=act_test_module value=\"$row[act_test_module]\"></td></tr>";
        echo "<tr><td class=a>Test Suite</td>         <td><input size=50 type=Text name=act_test_suite value=\"$row[act_test_suite]\"></td></tr>";
        echo "<tr><td class=a>Test Title</td>           <td><input size=50 type=Text name=act_test_title value=\"$row[act_test_title]\"></td></tr>";
        echo "<tr><td class=a>Release ID</td>           <td><input size=50 type=Text name=act_test_release_id value=\"$row[act_test_release_id]\"></td></tr>";
        echo "<tr><td class=a>Priority</td>        <td><input size=50 type=Text name=act_test_priority value=\"$row[act_test_priority]\"></td></tr>";
        echo "<tr><td class=a>Test Status</td>               <td><input size=50 type=Text name=act_test_status value=\"$row[act_test_status]\"></td></tr>";
        echo "<tr><td class=a>Topology</td>      <td><input size=50 type=Text name=act_test_topology value=\"$row[act_test_topology]\"></td></tr>";
        echo "<tr><td class=a>Scripted Date</td>             <td><input size=50 type=Text name=act_test_scripted_date value=\"$row[act_test_scripted_date]\"></td></tr>";
        echo "<tr><td class=a>Review Date</td>                <td><input size=50 type=Text name=act_test_review_date value=\"$row[act_test_review_date]\"></td></tr>";
        echo "<tr><td class=a>Rework Date</td>             <td><input size=50 type=Text name=act_test_rework_date value=\"$row[act_test_rework_date]\"></td></tr>";
        echo "<tr><td class=a>check-In Date</td>     <td><input size=50 type=Text name=act_test_checkin_date value=\"$row[act_test_checkin_date]\"></td></tr>";
        echo "<tr><td class=a>Script Name</td>          <td><input size=50 type=Text name=act_test_script_name value=\"$row[act_test_script_name]\"></td></tr>";
        echo "<tr><td class=a>Defect ID</td>          <td><input size=50 type=Text name=act_test_defect_id value=\"$row[act_test_defect_id]\"></td></tr>";
        echo "<tr><td class=a>Test Result</td>            <td><input size=50 type=Text name=act_test_result value=\"$row[act_test_result]\"></td></tr>";
        echo "<tr><td class=a>Test Comments</td>      <td><input size=50 type=Text name=act_test_comments value=\"$row[act_test_comments]\"></td></tr>";
        echo "<tr><td class=a>Owner</td>       <td><input size=50 type=Text name=act_test_assigned_to value=\"$row[act_test_assigned_to]\"></td></tr>";
        echo "<tr><td class=a>Regression</td>       <td><input size=50 type=Text name=act_test_regression value=\"$row[act_test_regression]\"></td></tr>";
     }
}
echo "</table>";
echo "<br><center><input type=\"Submit\" name=\"tcase_submit\" value=\"Update Testcase\">";

}

#########################################################################################
#                                                                                       #
# Handle Submit Information and Update the record accordingly                           #
#                                                                                       #
#########################################################################################
if ($_REQUEST[tcase_submit]) {
include 'db_connect.php';
include 'common.php';
$sql = "update $test_report_db set 
                              act_test_case_id='$_REQUEST[act_test_case_id]',
                              act_test_project='$_REQUEST[act_test_project]',
                              act_test_module='$_REQUEST[act_test_module]',
                              act_test_suite='$_REQUEST[act_test_suite]',
                              act_test_title='$_REQUEST[act_test_title]',
                              act_test_release_id='$_REQUEST[act_test_release_id]',
                              act_test_priority='$_REQUEST[act_test_priority]',
                              act_test_status='$_REQUEST[act_test_status]',
                              act_test_topology='$_REQUEST[act_test_topology]',
                              act_test_scripted_date='$_REQUEST[act_test_scripted_date]',
                              act_test_review_date='$_REQUEST[act_test_review_date]',
                              act_test_rework_date='$_REQUEST[act_test_rework_date]',
                              act_test_checkin_date='$_REQUEST[act_test_checkin_date]',
                              act_test_script_name='$_REQUEST[act_test_script_name]',
                              act_test_defect_id='$_REQUEST[act_test_defect_id]',
                              act_test_result='$_REQUEST[act_test_result]',
                              act_test_comments='$_REQUEST[act_test_comments]',
                              act_test_assigned_to='$_REQUEST[act_test_assigned_to]',
			      act_test_regression='$_REQUEST[act_test_regression]'
			      where act_test_case_no='$_REQUEST[act_test_case_no]'";
       if ($conn->query($sql) === TRUE) {
         echo "<b><br><br><center><font size=2> Details for Testcase $_REQUEST[act_test_case_no] updated Successfully</font>";
       } else {
        echo "Error updating record: " . $conn->error;
       }
$conn->close();
}

#########################################################################################
#											#
# Code to check whether Delete View, Modify View or Normal View Needs to be shown       #
#											#
#########################################################################################
if ($_GET[delCase]) {
      dispTCase("delView"); 
} elseif ($_GET[editCase] == "yes" ) {
       ediTCase("$_GET[act_mod_name]", "$_GET[act_test_case_no]"); 
} elseif ($_GET[modCase]) {
      dispTCase("modView"); 
} elseif ($_GET[delete] == "yes" ) { 
      remCase($_REQUEST[act_test_case_no]);
} elseif ($_GET[modify] == "yes" ) { 
      modCase($_REQUEST[act_test_case_no]);
} elseif ($_GET[showcases] == "yes" ) {
      dispTCase("dummy",$_GET[act_mod_name]);
} else {
      dispTCase("dummy"); 
}

?>
</body>
</html>
