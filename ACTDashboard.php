<html>
<body>
<link href="style1.css" rel="stylesheet" type="text/css">
<?php

#########################################################################################
#                                                                                       #
#  Function to return data from database   		                                #
#                                                                                       #
#########################################################################################
function getDetail($sql) {
include 'db_connect.php';
include 'common.php';
    //echo "<br> $sql";
    $result = $conn->query($sql);
       if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              $value = $row['value'];
          }
          mysql_close();
       } else {
        $errmsg = "connection failed.";
        $value = 0;
    }
    return $value;
    //echo "<br>Called function returning value $value to mainfunction getCount";
}


#########################################################################################
#                                                                                       #
#  Function with queries to get the counter                                             #
#                                                                                       #
#########################################################################################
function getCount($db, $act_test_module, $user, $act_test_status) {
   $count = getDetail("select count(*) as value from $db where act_test_module='$act_test_module' and act_test_status='$act_test_status'");
   //echo "<br><br>Count $act_test_module, $act_test_status is $count";
   return $count;
}

########################################################################################
#                                                                                       #
#  Function to display the Modules from Database, Also sets appropriate del/mod flags   #
#                                                                                       #
#########################################################################################
function dispModule($viewType) {
include 'db_connect.php';
include 'common.php';

if ($viewType == "D") {
  $reqAction="delete";
  $actFlag="yes";
  $cnfrmFlag="onClick";
  $formDirect=$PHP_SELF;
} else {
  $reqAction="showcases";
  $actFlag="yes";
  $cnfrmFlag="dummy";
  $formDirect=$PHP_SELF;
}

$countTotal 	     = getDetail("select count(*) as value from $test_report_db");
$countExisting       = getDetail("select count(*) as value from $test_report_db where act_test_status='$existing'");
$countNotAssigned    = getDetail("select count(*) as value from $test_report_db where act_test_status=''");
$countNotAutomatable = getDetail("select count(*) as value from $test_report_db where act_test_status='$notAutomatable'");
$countAssigned       = getDetail("select count(*) as value from $test_report_db where act_test_status='$assigned'");
$countScripted       = getDetail("select count(*) as value from $test_report_db where act_test_status='$scripted'");
$countReview         = getDetail("select count(*) as value from $test_report_db where act_test_status='$review'"); 
$countReWork         = getDetail("select count(*) as value from $test_report_db where act_test_status='$rework'");
$countCheckin        = getDetail("select count(*) as value from $test_report_db where act_test_status='$checkedIn'");
$countCompleted      = $countScripted + $countReview + $countReWork + $countCheckin;
$debugCount 	     = getDetail("select count(*) as value from $test_report_db where act_test_regression='DEBUG'");
$quickCount 	     = getDetail("select count(*) as value from $test_report_db where act_test_regression='QUICK'");
$ITdebugCount 	     = getDetail("select count(*) as value from $test_report_db where act_test_regression='DEBUG' and act_test_priority like '%Sanity%'");
$ITquickCount 	     = getDetail("select count(*) as value from $test_report_db where act_test_regression='QUICK' and act_test_priority like '%Sanity%'");

echo "<br><br><center>";
echo "<b><font size=2>Automation Dashboard</font></b><br><br>";
echo "<table border=1 align=auto>\n";

printf ("<tr>
                <td class=a>Total Test cases</td>
                <td class=a>Existing Scripts</td>
                <td class=a>Completed</td>
                <td class=a>In-Progress</td>
                <td class=a>Not Automatable</td>
                <td class=a>Scripted</td>
                <td class=a>Review</td>
                <td class=a>Rework</td>
                <td class=a>Checked-In</td>
                <td class=a>Debug</td>
                <td class=a>Quick</td>
                <td class=a>IT-Debug</td>
                <td class=a>IT-Quick</td>
		<tr>
                <td align=center>$countTotal</td>
                <td align=center>$countExisting</td>
                <td align=center>$countCompleted</td>
                <td align=center>$countAssigned</td>
                <td align=center>$countNotAutomatable</td>
                <td align=center>$countScripted</td>
                <td align=center>$countReview</td>
                <td align=center>$countReWork</td>
                <td align=center>$countCheckin</td>
                <td align=center>$debugCount</td>
                <td align=center>$quickCount</td>
                <td align=center>$ITdebugCount</td>
                <td align=center>$ITquickCount</td>
          </table><br><br>");


echo "<table border=1 align=auto width=95%>\n";
echo "<tr>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_mod_name>Module Name</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_release_scope>Release Scope</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_func_area>Area</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_mod_pri>Priority</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_mod_owner>Owner</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_auto_owner>Scripter</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=total>Total Cases</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=existing>Existing</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=notautomatable>Not Automatable</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=tobeautomated>Can be Automated</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=automated>Automated</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=pending>Pending</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=scripted>Scripted</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=review>Review</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=rework>Rework</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=checkin>CheckIn</a></td>
	<td class=a><a xhref=$PHP_SELF?sort_report=yes&sort_by=sanity>Sanity T/A/C</a></td>
	<td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_remarks>Status</a></td>
       </tr>\n";
 
$c=0;
if ($_GET[sort_report]) {
    if ($_GET[sort_by] == "total") {
          $sort_field="coalesce(sum($test_report_db.act_test_status like '%'),0) desc";
    } elseif ($_GET[sort_by] == "existing") {
          $sort_field="coalesce(sum($test_report_db.act_test_status = 'Existing'),0) desc";
    } elseif ($_GET[sort_by] == "notautomatable") {
          $sort_field="coalesce(sum($test_report_db.act_test_status = 'Not-Automatable'),0) desc";
    } elseif ($_GET[sort_by] == "tobeautomated") {
          $sort_field="coalesce(sum($test_report_db.act_test_status = 'Scripted') + sum($test_report_db.act_test_status = 'Review') + sum($test_report_db.act_test_status = 'Rework') + sum($test_report_db.act_test_status = 'Checked-In') + sum($test_report_db.act_test_status = 'Assigned'),0) desc";
    } elseif ($_GET[sort_by] == "itcases") {
          $sort_field="coalesce(sum($test_report_db.act_test_priority like '%Sanity%'),0) desc";
    } elseif ($_GET[sort_by] == "automated") {
          $sort_field="coalesce(sum($test_report_db.act_test_status = 'Scripted') + sum($test_report_db.act_test_status = 'Review') + sum($test_report_db.act_test_status = 'Rework') + sum($test_report_db.act_test_status = 'Checked-In'),0) desc";
    } elseif ($_GET[sort_by] == "pending") {
          $sort_field="coalesce(sum($test_report_db.act_test_status = 'Assigned'),0) desc";
    } elseif ($_GET[sort_by] == "scripted") {
          $sort_field="coalesce(sum($test_report_db.act_test_status = 'Scripted'),0) desc";
    } elseif ($_GET[sort_by] == "review") {
          $sort_field="coalesce(sum($test_report_db.act_test_status = 'Review'),0) desc";
    } elseif ($_GET[sort_by] == "rework") {
          $sort_field="coalesce(sum($test_report_db.act_test_status = 'Rework'),0) desc";
    } elseif ($_GET[sort_by] == "checkin") {
          $sort_field="coalesce(sum($test_report_db.act_test_status = 'Checked-In'),0) desc";
    } else {
          $sort_field="$module_db.$_GET[sort_by]";
    }
} else {
    $sort_field="($module_db.act_remarks = 'In-Progress') DESC, $module_db.act_remarks ASC, $module_db.act_mod_name ASC";
}
  $sql = " select  
           $module_db.act_mod_name,
           $module_db.act_release_scope,
           $module_db.act_func_area,
           $module_db.act_mod_pri,
           $module_db.act_mod_owner,
           $module_db.act_auto_owner,
           coalesce(sum($test_report_db.act_test_status like '%'),0) Total,
           coalesce(sum($test_report_db.act_test_status = 'Existing'),0) Existing,
           coalesce(sum($test_report_db.act_test_status = 'Not-Automatable'),0) \"Not-Automatable\",
           coalesce(sum($test_report_db.act_test_status = 'Scripted') + sum($test_report_db.act_test_status = 'Review') + 
           sum($test_report_db.act_test_status = 'Rework') + sum($test_report_db.act_test_status = 'Checked-In') + 
           sum($test_report_db.act_test_status = 'Assigned'),0) \"To be Automated\",
           coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and (act_test_status!='test' && act_test_status!='test')),0) ITCases,
           coalesce(sum($test_report_db.act_test_status = 'Scripted') + sum($test_report_db.act_test_status = 'Review') + 
           sum($test_report_db.act_test_status = 'Rework') + sum($test_report_db.act_test_status = 'Checked-In'),0) Automated,
           coalesce(sum($test_report_db.act_test_status = 'Assigned'),0) Pending,
           coalesce(sum($test_report_db.act_test_status = 'Scripted'),0) Scripted,
           coalesce(sum($test_report_db.act_test_status = 'Review'),0) Review,
           coalesce(sum($test_report_db.act_test_status = 'Rework'),0) Rework,
           coalesce(sum($test_report_db.act_test_status = 'Checked-In'),0) \"Checked-In\",
           coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and (act_test_status!='Not-Automatable' && act_test_status!='Existing')),0) San_Total,
           coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and (act_test_status='Review' || act_test_status='Rework' || act_test_status='Scripted' || act_test_status='Checked-In')),0) San_Completed,
           $module_db.act_topo_det,
           $module_db.act_remarks
           from $module_db 
           LEFT join $test_report_db 
           on $test_report_db.$module_db= $module_db.act_mod_name
           group by $module_db.act_mod_name
           order by $sort_field";

#echo "$sql";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
        printf("<tr>
                <td class=$row[act_remarks]><a href=\"%s?act_mod_no=%s&act_mod_name=%s&$reqAction=$actFlag\" $cnfrmFlag=\"return confirm('Sure? Once deleted, the record CANNOT be reverted.')\">%s</td>
                <td class=$row[act_remarks]>%s</td>
                <td class=$row[act_remarks]>%s</td>
                <td class=$row[act_remarks]>%s</td>
                <td class=$row[act_remarks]>%s</td>
                <td class=$row[act_remarks]>%s</td>
                <td class=$row[act_remarks]><center><a href=\"%s?act_mod_name=%s&showcases=yes\">%s</td>
                <td class=$row[act_remarks]><center><a href=\"%s?act_mod_name=%s&act_test_status=%s\">%s</td>
                <td class=$row[act_remarks]><center><a href=\"%s?act_mod_name=%s&act_test_status=%s\">%s</td>
                <td class=$row[act_remarks]><center>%s</td>
                <td class=$row[act_remarks]><center>%s</td>
                <td class=$row[act_remarks]><center><a href=\"%s?act_mod_name=%s&act_test_status=%s\">%s</td>
                <td class=$row[act_remarks]><center><a href=\"%s?act_mod_name=%s&act_test_status=%s\">%s</td>
                <td class=$row[act_remarks]><center><a href=\"%s?act_mod_name=%s&act_test_status=%s\">%s</td>
                <td class=$row[act_remarks]><center><a href=\"%s?act_mod_name=%s&act_test_status=%s\">%s</td>
                <td class=$row[act_remarks]><center><a href=\"%s?act_mod_name=%s&act_test_status=%s\">%s</td>
                <td class=$row[act_remarks]>%s/%s/%s</td>
                <td class=$row[act_remarks]>%s</td>
                </tr>\n",
                $formDirect, $row["act_mod_no"], $row["act_mod_name"], $row["act_mod_name"],
                $row["act_release_scope"],
                $row["act_func_area"],
                $row["act_mod_pri"],
                $row["act_mod_owner"],
                $row["act_auto_owner"],
                $formDirect, $row["act_mod_name"], $row["Total"],
                $formDirect, $row["act_mod_name"], $existing, $row["Existing"],
                $formDirect, $row["act_mod_name"], $notAutomatable, $row["Not-Automatable"],
                $row["To be Automated"],
                $row["Automated"],
                $formDirect, $row["act_mod_name"], $assigned,  $row["Pending"],
                $formDirect, $row["act_mod_name"], $scripted,  $row["Scripted"],
                $formDirect, $row["act_mod_name"], $review,    $row["Review"],
                $formDirect, $row["act_mod_name"], $rework,    $row["Rework"],
                $formDirect, $row["act_mod_name"], $checkedIn, $row["Checked-In"],
                $row["ITCases"], $row["San_Total"], $row["San_Completed"],
                $row["act_remarks"]
               );
     $c = $c + 1;
    }
} else {
    echo "<br><br><center><b>0 Testcases found<br><br></center>";
}


echo "</table><br><br><b><center><font size=2>Total Modules : ". $c ." <br><br><br>";
$conn->close();
}

#########################################################################################
#                                                                                       #
#  Function to display the Testcase from Database, Also sets appropriate del/mod flags  #
#                                                                                       #
#########################################################################################

function dispTCase($viewType, $act_mod_name) {
include 'db_connect.php';
include 'common.php';

$c=0;
if ($_GET[sort_tcase] == "yes") {
  $filter = $_GET[sort_by];
} else {
  $filter = "act_test_case_no";

}

#echo "Here filter is $filter";

if ($_GET[act_test_status] != "") {
    $sql = "select * from $test_report_db where act_test_module=\"$_GET[act_mod_name]\" and act_test_status=\"$_GET[act_test_status]\" order by $filter";
    $reqAction="act_test_status";
    $actFlag="$_GET[act_test_status]";
} else {
    $sql = "select * from $test_report_db where act_test_module=\"$_GET[act_mod_name]\" order by $filter";
    $reqAction="nothing";
    $actFlag="dummy";
}
 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
echo "<br><br><br><center>";
echo "<b><font size=2>Listing Test cases for \"$act_mod_name\" feature</font></b><br><br>";

$countTotal          = getDetail("select count(*) as value from $test_report_db where act_test_module='$act_mod_name'");
$countExisting       = getCount($test_report_db, $act_mod_name, $User, $existing);
$countNotAssigned    = getCount($test_report_db, $act_mod_name, $User, $notAssigned);
$countNotAutomatable = getCount($test_report_db, $act_mod_name, $User, $notAutomatable);
$countAssigned       = getCount($test_report_db, $act_mod_name, $User, $assigned);
$countScripted       = getCount($test_report_db, $act_mod_name, $User, $scripted);
$countReview         = getCount($test_report_db, $act_mod_name, $User, $review);
$countReWork         = getCount($test_report_db, $act_mod_name, $User, $rework);
$countCheckin        = getCount($test_report_db, $act_mod_name, $User, $checkedIn);
$countCompleted      = $countScripted + $countReview + $countReWork + $countCheckin;
$debugCount          = getDetail("select count(*) as value from $test_report_db where act_test_regression='DEBUG' and act_test_module='$act_mod_name'");
$quickCount          = getDetail("select count(*) as value from $test_report_db where act_test_regression='QUICK' and act_test_module='$act_mod_name'");
$ITdebugCount        = getDetail("select count(*) as value from $test_report_db where act_test_regression='DEBUG' and act_test_priority like '%Sanity%' and act_test_module='$act_mod_name'");
$ITquickCount        = getDetail("select count(*) as value from $test_report_db where act_test_regression='QUICK' and act_test_priority like '%Sanity%' and act_test_module='$act_mod_name'");

echo "<table border=1 align=center>";
printf ("<tr>
                <td class=a>Total Test cases</td>
                <td class=a>Existing Scripts</td>
                <td class=a>Completed</td>
                <td class=a>In-Progress</td>
                <td class=a>Not Automatable</td>
                <td class=a>Scripted</td>
                <td class=a>Review</td>
                <td class=a>Rework</td>
                <td class=a>Checked-In</td>
                <td class=a>Debug</td>
                <td class=a>Quick</td>
                <td class=a>IT-Debug</td>
                <td class=a>IT-Quick</td>
		<tr>
                <td align=center>$countTotal</td>
                <td align=center>$countExisting</td>
                <td align=center>$countCompleted</td>
                <td align=center>$countAssigned</td>
                <td align=center>$countNotAutomatable</td>
                <td align=center>$countScripted</td>
                <td align=center>$countReview</td>
                <td align=center>$countReWork</td>
                <td align=center>$countCheckin</td>
                <td align=center>$debugCount</td>
                <td align=center>$quickCount</td>
                <td align=center>$ITdebugCount</td>
                <td align=center>$ITquickCount</td>
          </table><br>");

$act_nmame = urlencode($act_mod_name);
echo "<br><table border=1>\n";
echo "<tr>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_case_id&$reqAction=$actFlag>Testcase ID</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_project&$reqAction=$actFlag>Project</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_module&$reqAction=$actFlag>Module</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_suite&$reqAction=$actFlag>Test Suite</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_title&$reqAction=$actFlag>Test Title</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_release_id&$reqAction=$actFlag>Release ID</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_priority&$reqAction=$actFlag>Priority</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_status&$reqAction=$actFlag>Status</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_topology&$reqAction=$actFlag>Toplogy</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_scripted_date&$reqAction=$actFlag>Scripted On</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_review_date&$reqAction=$actFlag>Reviewed On</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_rework_date&$reqAction=$actFlag>Reworked On</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_checkin_date&$reqAction=$actFlag>Checkin On</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_script_name&$reqAction=$actFlag>Script Name</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_defect_id&$reqAction=$actFlag>CR Details</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_result&$reqAction=$actFlag>Test Result</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_comments&$reqAction=$actFlag>Comments</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_assigned_to&$reqAction=$actFlag>Assigned To</a></td>
        <td class=a><a href=$PHP_SELF?act_mod_name=$act_nmame&showcases=yes&sort_tcase=yes&sort_by=act_test_regression&$reqAction=$actFlag>Regression</a></td>
       </tr>\n";
     while($row = $result->fetch_assoc()) {
        printf("<tr>
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
                <td class=$row[act_test_status]>%s</td>
                <td class=$row[act_test_status]>%s</td>
                <td class=$row[act_test_status]>%s</td>
                <td class=$row[act_test_status]>%s</td>
                <td class=$row[act_test_status]>%s</td>
                </tr>\n",
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
# Code to check whether Delete View, Modify View or Normal View Needs to be shown       #
#											#
#########################################################################################
if ($_GET[showcases] == "yes") {
      dispTCase("N",$_GET[act_mod_name]); 
} elseif ($_GET[act_test_status] != "") {
      dispTCase("N",$_GET[act_mod_name]); 
} else {
      dispModule("Y");
}

?>
</body>
</html>
