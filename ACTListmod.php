<html>
<body>
<link href="style1.css" rel="stylesheet" type="text/css">
<form method="post" name="frm" action="<?php echo $PHP_SELF?>">
<?php

#########################################################################################
#											#
#  Function to display the Modules from Database, Also sets appropriate del/mod flags	#
#											#
#########################################################################################

function dispModule($viewType) {
include 'db_connect.php';
include 'common.php';

if ($viewType == "D") {
  $reqAction="delete";
  $actFlag="yes";
  $cnfrmFlag="onClick";
  $formDirect=$PHP_SELF;
} elseif ($viewType == "M") {
  $reqAction="modify";
  $actFlag="yes";
  $cnfrmFlag="dummy";
  $formDirect=$PHP_SELF;
} elseif ($viewType == "remCase") {
  $reqAction="delCase";
  $actFlag="yes";
  $cnfrmFlag="dummy";
  $formDirect="ACTListCase.php";
} elseif ($viewType == "modCase") {
  $reqAction="modCase";
  $actFlag="yes";
  $cnfrmFlag="dummy";
  $formDirect="ACTListCase.php";
} elseif ($viewType == "assignCase") {
  $reqAction="assignCase";
  $actFlag="yes";
  $cnfrmFlag="dummy";
  $formDirect="ACTAssignCase.php";
} else {
  $reqAction="showcases";
  $actFlag="yes";
  $cnfrmFlag="dummy";
  $formDirect="ACTListCase.php";
}

$c=0;
if ($_GET[sort_report]) {
  $sql = "select * from $module_db order by $_GET[sort_by]";
} else {
  $sql = "select  * from $module_db";
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {
echo "<br><br><br><center>";
echo "<b><font size=2>Listing Automation Modules</font></b><br><br>";
echo "<table border=1>\n";
echo "<tr>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_mod_name>Module Name</a></td>
        <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_act_start>Actual Start Date</a></td>
	<td class=a>Created By</td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_release_scope>Release Scope</a></td>
        <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_func_area>Area</a></td>
        <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_mod_pri>Priority</a></td>
        <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_mod_owner>Owner</a></td>
        <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_auto_owner>Scripter</a></td>
        <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_topo_det>Topology</a></td>
        <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_remarks>Status</a></td>
       </tr>\n";
     while($row = $result->fetch_assoc()) {
        printf("<tr>
		<td class=$row[act_remarks]><a href=\"%s?act_mod_no=%s&act_mod_name=%s&$reqAction=$actFlag\" $cnfrmFlag=\"return confirm('Sure? Once deleted, the record CANNOT be reverted.')\">%s</td>
		<td class=$row[act_remarks]>%s</td>
		<td class=$row[act_remarks]>%s</td>
		<td class=$row[act_remarks]>%s</td>
		<td class=$row[act_remarks]>%s</td>
		<td class=$row[act_remarks]>%s</td>
		<td class=$row[act_remarks]>%s</td>
		<td class=$row[act_remarks]>%s</td>
		<td class=$row[act_remarks]>%s</td>
		<td class=$row[act_remarks]>%s</td>
		</tr>\n",
		$formDirect, $row["act_mod_no"], $row["act_mod_name"], $row["act_mod_name"], 
		$row["act_act_start"], 
		$row["act_created_by"], 
		$row["act_release_scope"], 
		$row["act_func_area"], 
		$row["act_mod_pri"], 
		$row["act_mod_owner"], 
		$row["act_auto_owner"], 
		$row["act_topo_det"], 
		$row["act_remarks"] 
	       );
     $c = $c + 1;
    }
} else {
    // echo "<center><b>0 Modules found";
}
echo "</table><br><br><b><center><font size=2>Total Modules: ". $c ."<br><br><br <br><br><br>";
$conn->close();
}

#########################################################################################
#											#
#  Function to delete the given module from database					#
#											#
#########################################################################################
function remModule($act_mod_no) {
include 'db_connect.php';
include 'common.php';

$sql = "DELETE FROM $module_db WHERE act_mod_no='$act_mod_no'";
 if (mysqli_query($conn, $sql)) {
      echo "<br><br><b><center><font size=2>Module deleted successfully - Record Number ($act_mod_no) <br><br>";
      dispModule(D);
 } else {
     echo "<br><br><b><center>Error deleting record: " . mysqli_error($conn);
 }
}

#########################################################################################
#                                                                                       #
#  Function to modify the given module from database                                    #
#                                                                                       #
#########################################################################################
function modModule($act_mod_no) {
include 'db_connect.php';
include 'common.php';
echo "<br><br><center><b><font size=2>Edit Module : $act_mod_no </b><br><br>";

$sql = "select * from $module_db where act_mod_no='$act_mod_no'";
$result = $conn->query($sql);
echo "<table align=center border=1>\n";
if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
        echo "<input type=hidden name=act_mod_no value=\"$row[act_mod_no]\">";
        echo "<tr><td class=a>Module Name</td>		<td><input size=50 type=Text name=act_mod_name value=\"$row[act_mod_name]\"></td></tr>";
        echo "<tr><td class=a>Plan Start</td>		<td><input size=50 type=Text name=act_plan_start value=\"$row[act_plan_start]\"></td></tr>";
        echo "<tr><td class=a>Plan End</td>		<td><input size=50 type=Text name=act_plan_end value=\"$row[act_plan_end]\"></td></tr>";
        echo "<tr><td class=a>Actual Start</td>		<td><input size=50 type=Text name=act_act_start value=\"$row[act_act_start]\"></td></tr>";
        echo "<tr><td class=a>Actual End</td>		<td><input size=50 type=Text name=act_act_end value=\"$row[act_act_end]\"></td></tr>";
        echo "<tr><td class=a>Created By</td>		<td><input size=50 type=Text name=act_created_by value=\"$row[act_created_by]\"></td></tr>";
        echo "<tr><td class=a>Release Scope</td>	<td><input size=50 type=Text name=act_release_scope value=\"$row[act_release_scope]\"></td></tr>";
        echo "<tr><td class=a>Target</td>		<td><input size=50 type=Text name=act_target value=\"$row[act_target]\"></td></tr>";
        echo "<tr><td class=a>Functional Area</td>	<td><input size=50 type=Text name=act_func_area value=\"$row[act_func_area]\"></td></tr>";
        echo "<tr><td class=a>Priority</td>		<td><input size=50 type=Text name=act_mod_pri value=\"$row[act_mod_pri]\"></td></tr>";
        echo "<tr><td class=a>Owner</td>		<td><input size=50 type=Text name=act_mod_owner value=\"$row[act_mod_owner]\"></td></tr>";
        echo "<tr><td class=a>Reviewer</td>		<td><input size=50 type=Text name=act_mod_reviewer value=\"$row[act_mod_reviewer]\"></td></tr>";
        echo "<tr><td class=a>Automation Owner</td>	<td><input size=50 type=Text name=act_auto_owner value=\"$row[act_auto_owner]\"></td></tr>";
        echo "<tr><td class=a>FS Location</td>		<td><input size=50 type=Text name=act_fs_loc value=\"$row[act_fs_loc]\"></td></tr>";
        echo "<tr><td class=a>TP Location</td>		<td><input size=50 type=Text name=act_tp_loc value=\"$row[act_tp_loc]\"></td></tr>";
        echo "<tr><td class=a>Repo Path</td>		<td><input size=50 type=Text name=act_repo_path value=\"$row[act_repo_path]\"></td></tr>";
        echo "<tr><td class=a>Topology Detail</td>	<td><input size=50 type=Text name=act_topo_det value=\"$row[act_topo_det]\"></td></tr>";
        echo "<tr><td class=a>Ext Dependency</td>	<td><input size=50 type=Text name=act_ext_dep value=\"$row[act_ext_dep]\"></td></tr>";
        echo "<tr><td class=a>Force Hold</td>		<td><input size=50 type=Text name=act_force_hold value=\"$row[act_force_hold]\"></td></tr>";
        echo "<tr><td class=a>Is Visible</td>		<td><input size=50 type=Text name=act_is_visible value=\"$row[act_is_visible]\"></td></tr>";
        echo "<tr><td class=a>Remarks</td>		<td><input size=50 type=Text name=act_remarks value=\"$row[act_remarks]\"></td></tr>";
     }
}
echo "</table>";
echo "<br><center><input type=\"Submit\" name=\"mod_submit\" value=\"Update Module\">";
}

#########################################################################################
#                                                                                       #
# Handle Submit Information and Update the record accordingly                           #
#                                                                                       #
#########################################################################################
if ($_REQUEST[mod_submit]) {
include 'db_connect.php';
include 'common.php';
$sql = "update $module_db set act_mod_name='$_REQUEST[act_mod_name]',
                              act_plan_start='$_REQUEST[act_plan_start]',
                              act_plan_end='$_REQUEST[act_plan_end]',
                              act_act_start='$_REQUEST[act_act_start]',
                              act_act_end='$_REQUEST[act_act_end]',
                              act_created_by='$_REQUEST[act_created_by]',
                              act_release_scope='$_REQUEST[act_release_scope]',
                              act_target='$_REQUEST[act_target]',
                              act_func_area='$_REQUEST[act_func_area]',
                              act_mod_pri='$_REQUEST[act_mod_pri]',
                              act_mod_owner='$_REQUEST[act_mod_owner]',
                              act_mod_reviewer='$_REQUEST[act_mod_reviewer]',
                              act_auto_owner='$_REQUEST[act_auto_owner]',
                              act_fs_loc='$_REQUEST[act_fs_loc]',
                              act_tp_loc='$_REQUEST[act_tp_loc]',
                              act_repo_path='$_REQUEST[act_repo_path]',
                              act_topo_det='$_REQUEST[act_topo_det]',
                              act_ext_dep='$_REQUEST[act_ext_dep]',
                              act_force_hold='$_REQUEST[act_force_hold]',
                              act_is_visible='$_REQUEST[act_is_visible]',
                              act_remarks='$_REQUEST[act_remarks]' where act_mod_no='$_REQUEST[act_mod_no]'";
                              
       if ($conn->query($sql) === TRUE) {
         echo "<b><br><br><center><font size=2> Details for Module  $_REQUEST[act_mod_name] updated Successfully</font>";
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
if ($_GET[remView]) {
      dispModule("D"); 
} elseif ($_GET[modView]) {
      dispModule("M"); 
} elseif ($_GET[delete] == "yes" ) { 
      remModule($_REQUEST[act_mod_no]);
} elseif ($_GET[modify] == "yes" ) { 
      modModule($_REQUEST[act_mod_no]);
} elseif ($_GET[remCase] == "yes" ) { 
      dispModule("remCase"); 
} elseif ($_GET[modCase] == "yes" ) {
      dispModule("modCase");
} elseif ($_GET[assignCase] == "yes" ) {
      dispModule("assignCase");
} else {
      dispModule("N"); 
}

?>
</body>
</html>
