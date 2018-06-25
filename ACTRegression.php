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

#########################################################################################
#                                                                                       #
#  Function to display the Modules and its Regression status from Database		#
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
echo "<b><font size=2>Regression Dashboard</font></b><br><br>";
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



echo "<table border=1>\n";
echo "<tr>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_mod_name>Module Name</a></td>
       <td class=a colspan=6 align=center><a href=$PHP_SELF?sort_report=yes&sort_by=totPass>Overall Status</a></td>
       <td class=a colspan=6 align=center><a href=$PHP_SELF?sort_report=yes&sort_by=ITtotPass>IT Status</a></td>
       <td class=a></td>
       </tr>
       <tr>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_mod_name>Name</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=Total>Total Cases</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=CheckedIn>CheckedIn</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=TotalDebug>Dev/Debug</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=TotalQuick>Quick/AllPass</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=totPass>% Pass</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=totNoFlag>No Flag</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=ITCases>IT Cases</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=ITCheckedin>IT CheckedIn</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=ITDebug>IT Dev/Debug</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=ITQuick>IT Quick/AllPass</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=ITtotPass>% Pass</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=ITtotNoFlag>No Flag</a></td>
       <td class=a><a href=$PHP_SELF?sort_report=yes&sort_by=act_remarks>Status</a></td>
       </tr>\n";

$c=0;
if ($_GET[sort_report]) {
    if ($_GET[sort_by] == "Total") {
          $sort_field="coalesce(sum($test_report_db.act_test_status like '%'),0) desc";
    } elseif ($_GET[sort_by] == "CheckedIn") {
          $sort_field="coalesce(sum($test_report_db.act_test_status = 'Checked-In'),0) desc";
    } elseif ($_GET[sort_by] == "TotalDebug") {
          $sort_field="coalesce(sum($test_report_db.act_test_regression = 'DEBUG'),0) desc";
    } elseif ($_GET[sort_by] == "TotalQuick") {
          $sort_field="coalesce(sum($test_report_db.act_test_regression = 'QUICK'),0) desc";
    } elseif ($_GET[sort_by] == "ITCases") {
          $sort_field="coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and (act_test_status!='Not-Automatable' && act_test_status!='Existing')),0) desc";
    } elseif ($_GET[sort_by] == "ITCheckedin") {
          $sort_field="coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and act_test_status='Checked-In'),0) desc";
    } elseif ($_GET[sort_by] == "ITDebug") {
          $sort_field="coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and act_test_regression='DEBUG'),0) desc";
    } elseif ($_GET[sort_by] == "ITQuick") {
          $sort_field="coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and act_test_regression='QUICK'),0) desc";
    } elseif ($_GET[sort_by] == "totPass") {
          $sort_field="coalesce(sum($test_report_db.act_test_regression = 'QUICK') * 100 /  sum($test_report_db.act_test_status='Checked-In'),0) desc";
    } elseif ($_GET[sort_by] == "ITtotPass") {
          $sort_field="coalesce(sum($test_report_db.act_test_regression = 'QUICK' and $test_report_db.act_test_priority like '%Sanity%') * 100 /  sum($test_report_db.act_test_status='Checked-In' and $test_report_db.act_test_priority like '%Sanity%'),0) desc";
    } elseif ($_GET[sort_by] == "totNoFlag") {
          $sort_field="coalesce(sum($test_report_db.act_test_status='Checked-In') - sum($test_report_db.act_test_regression = 'QUICK')  - sum($test_report_db.act_test_regression = 'DEBUG'),0) desc";
    } elseif ($_GET[sort_by] == "ITtotNoFlag") {
          $sort_field="coalesce(sum($test_report_db.act_test_status='Checked-In' and $test_report_db.act_test_priority like '%Sanity%') - sum($test_report_db.act_test_regression = 'QUICK' and $test_report_db.act_test_priority like '%Sanity%')  - sum($test_report_db.act_test_regression = 'DEBUG' and $test_report_db.act_test_priority like '%Sanity%'),0) desc";
    } else {
          $sort_field="$module_db.$_GET[sort_by]";
    }
} else {
    $sort_field="coalesce(sum($test_report_db.act_test_status='Checked-In' and $test_report_db.act_test_priority like '%Sanity%') - sum($test_report_db.act_test_regression = 'QUICK' and $test_report_db.act_test_priority like '%Sanity%')  - sum($test_report_db.act_test_regression = 'DEBUG' and $test_report_db.act_test_priority like '%Sanity%'),0) desc";
}
  $sql = " select  
           $module_db.act_mod_name,
           coalesce(sum($test_report_db.act_test_status like '%'),0) Total,
           coalesce(sum(act_test_report.act_test_status='Checked-In') - sum(act_test_report.act_test_regression = 'QUICK') - sum(act_test_report.act_test_regression = 'DEBUG'),0) as totNoFlag,
           coalesce(sum($test_report_db.act_test_status = 'Checked-In'),0) \"Checked-In\",
           coalesce(sum($test_report_db.act_test_regression = 'DEBUG'),0) \"Total-Debug\",
           coalesce(sum($test_report_db.act_test_regression = 'QUICK'),0) \"Total-Quick\",
           coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and act_test_status='Checked-In'),0) \"ITChecked-In\",
           coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and act_test_regression='DEBUG'),0) \"IT-Debug\",
           coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and act_test_regression='QUICK'),0) \"IT-Quick\",
           coalesce(sum($test_report_db.act_test_priority like '%Sanity%' and (act_test_status!='Not-Automatable' && act_test_status!='Existing')),0) San_Total,
	   coalesce(sum($test_report_db.act_test_regression = 'QUICK') * 100 /  (sum(act_test_report.act_test_regression = 'QUICK') +  sum(act_test_report.act_test_regression = 'DEBUG')),0) totPass,
	   coalesce(sum($test_report_db.act_test_regression = 'QUICK' and $test_report_db.act_test_priority like '%Sanity%') * 100 /  (sum(act_test_report.act_test_regression = 'QUICK' and $test_report_db.act_test_priority like '%Sanity%') +  sum(act_test_report.act_test_regression = 'DEBUG' and $test_report_db.act_test_priority like '%Sanity%')),0) ITtotPass,
           coalesce(sum($test_report_db.act_test_status='Checked-In' and $test_report_db.act_test_priority like '%Sanity%') - sum($test_report_db.act_test_regression='QUICK' and $test_report_db.act_test_priority like '%Sanity%')  - sum($test_report_db.act_test_regression='DEBUG' and $test_report_db.act_test_priority like '%Sanity%'),0) ITtotNoFlag,
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
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks] align=center>%s</td>
                <td class=$row[act_remarks]>%s</td>
                </tr>\n",
                $formDirect, $row["act_mod_no"], $row["act_mod_name"], $row["act_mod_name"],
                $row["Total"],
                $row["Checked-In"],
                $row["Total-Debug"],
                $row["Total-Quick"],
                CEIL($row["totPass"]).'%',
		$row["totNoFlag"],
                $row["San_Total"],
                $row["ITChecked-In"],
                $row["IT-Debug"],
                $row["IT-Quick"],
                CEIL($row["ITtotPass"]).'%',
		$row["ITtotNoFlag"],
		$row["act_remarks"]
               );
     $c = $c + 1;
     $totalCases       = $totalCases + $row["Total"];
     $totalCheckedIn   = $totalCheckedIn + $row["Checked-In"];
     $totalDebug       = $totalDebug + $row["Total-Debug"];
     $totalQuick       = $totalQuick + $row["Total-Quick"];
     $ITtotalCases     = $ITtotalCases + $row["San_Total"];
     $ITtotalCheckedIn = $ITtotalCheckedIn + $row["ITChecked-In"];
     $ITtotalDebug     = $ITtotalDebug + $row["IT-Debug"];
     $ITtotalQuick     = $ITtotalQuick + $row["IT-Quick"];
     $counttotNoFlag   = $counttotNoFlag + $row["totNoFlag"];
     $countITtotNoFlag   = $countITtotNoFlag + $row["ITtotNoFlag"];
    }
} else {
    echo "<br><br><center><b>0 Testcases found<br><br></center>";
}


echo "</tr><tr>
	<td align=center></td>
	<td align=center><b>$totalCases</td>
	<td align=center><b>$totalCheckedIn</td>
	<td align=center><b>$totalDebug</td>
	<td align=center><b>$totalQuick</td>
	<td align=center><b>".FLOOR(($totalQuick / ($totalCheckedIn - $counttotNoFlag))*100).'%' ."</td>
	<td align=center><b>$counttotNoFlag</td>
	<td align=center><b>$ITtotalCases</td>
	<td align=center><b>$ITtotalCheckedIn</td>
	<td align=center><b>$ITtotalDebug</td>
	<td align=center><b>$ITtotalQuick</td>
	<td align=center><b>".FLOOR(($ITtotalQuick / ($ITtotalCheckedIn - $countITtotNoFlag))*100).'%' ."</td>
	<td align=center><b>$countITtotNoFlag</td>
	<td align=center></td>
	</tr>";
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
                <td class=a>Total Cases</td>
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
