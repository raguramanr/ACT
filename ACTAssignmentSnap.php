<html>
<head>
<script language="JavaScript" src="Chart/JSClass/FusionCharts.js"></script>
<body>
<link href="style1.css" rel="stylesheet" type="text/css">
<form method="post" name="frm" action="<?php echo $PHP_SELF?>">
<?php
#########################################################################################
#                                                                                       #
#  Function to return start and end date of the week from specified date		#
#                                                                                       #
#########################################################################################
function x_week_range($date) {
    $ts = strtotime($date);
    $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
    return array(date('Y-m-d', $start),
                 date('Y-m-d', strtotime('next saturday', $start)));
}

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
    // echo "<br>Called function returning value $value to mainfunction getCount";
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
#  Function to display the Testcase from Database, Also sets appropriate del/mod flags  #
#                                                                                       #
#########################################################################################

function dispTCase($act_user_name, $start_date, $act_test_status) {
include 'db_connect.php';
include 'common.php';

//echo "Got a request to print testcase details for $act_user_name from $start_date with status as $act_test_status";
$c=0;
if ($_GET[sort_tcase] == "yes") {
  $filter = $_GET[sort_by];
} else {
  $filter = "act_test_case_no";
}

#echo "Here filter is $filter";

if ($act_test_status == "scripted") {
    $sql = "select * from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_status='$scripted' and act_test_scripted_date>='$start_date' order by $filter desc";
    $reqAction="nothing";
    $actFlag="dummy";
} elseif ($act_test_status == "review") {
    $sql = "select * from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_status='$review' and act_test_review_date>='$start_date' order by $filter desc";
    $reqAction="nothing";
    $actFlag="dummy";
} elseif ($act_test_status == "rework") {
    $sql = "select * from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_status='$rework' and act_test_rework_date>='$start_date' order by $filter desc";
    $reqAction="nothing";
    $actFlag="dummy";
} elseif ($act_test_status == "checkin") {
    $sql = "select * from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_status='$checkedIn' and act_test_checkin_date>='$start_date' order by $filter desc";
    $reqAction="nothing";
    $actFlag="dummy";
} elseif ($act_test_status == "total") {
    $sql = "select * from $test_report_db where act_test_assigned_to='$act_user_name' order by $filter desc";
    $reqAction="nothing";
    $actFlag="dummy";
} elseif ($act_test_status == "complete") {
    $sql = "select * from $test_report_db where act_test_assigned_to='$act_user_name' and (act_test_status='scripted' OR act_test_status='review' OR act_test_status='rework' OR act_test_status='checked-in') and (act_test_scripted_date>='$start_date' OR act_test_review_date>='$start_date' OR act_test_rework_date>='$start_date' OR act_test_checkin_date>='$start_date') order by $filter desc";
    $reqAction="nothing";
    $actFlag="dummy";
} else {
    $sql = "select * from $test_report_db where act_user_name='$act_user_name' order by $filter desc";
    $reqAction="act_test_status";
    $actFlag="$_GET[act_test_status]";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
echo "<br><br><br><center>";
echo "<b><font size=2>Listing Test cases for \"$act_user_name\" </font></b><br><br>";

$act_nmame = urlencode($act_mod_name);
echo "<br><table border=1>\n";
echo "<tr>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_case_id&$reqAction=$actFlag>Testcase ID</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_project&$reqAction=$actFlag>Project</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_module&$reqAction=$actFlag>Module</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_suite&$reqAction=$actFlag>Test Suite</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_title&$reqAction=$actFlag>Test Title</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_release_id&$reqAction=$actFlag>Release ID</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_priority&$reqAction=$actFlag>Priority</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_status&$reqAction=$actFlag>Status</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_topology&$reqAction=$actFlag>Toplogy</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_scripted_date&$reqAction=$actFlag>Scripted On</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_review_date&$reqAction=$actFlag>Reviewed On</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_rework_date&$reqAction=$actFlag>Reworked On</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_checkin_date&$reqAction=$actFlag>Checkin On</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_script_name&$reqAction=$actFlag>Script Name</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_defect_id&$reqAction=$actFlag>CR Details</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_result&$reqAction=$actFlag>Test Result</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_comments&$reqAction=$actFlag>Comments</a></td>
        <td class=a><a href=$PHP_SELF?act_user_name=$act_user_name&start_date=$start_date&act_test_status=$act_test_status&dispTCase=yes&sort_tcase=yes&sort_by=act_test_assigned_to&$reqAction=$actFlag>Assigned To</a></td>
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
                $row["act_test_assigned_to"]
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
#                                                                                       #
#  Function to display modules allotted per tester					#
#                                                                                       #
#########################################################################################
function printModules($flag, $date) {
include("Chart/Code/PHP/Includes/FusionCharts.php");
include 'db_connect.php';
include 'common.php';


if ($flag == "Y") {
  $date = "$_POST[year]-$_POST[month]-$_POST[date]";
  list($start_date, $end_date) = x_week_range($date);
  $strtDate = $start_date;
  if (strtotime($strtDate) <= strtotime('2015-10-01')) {
  $strtDate = '2015-10-11';
  }
  #echo "Start Date is : $strtDate";
} else {
  $strtDate = '2015-10-11';
}

##### Printing Week-Week Dates ######
#$strtDate = '2015-10-11';
$endDate = date('Y/m/d', time());
$startDateWeekCnt = round(floor( date('d',strtotime($strtDate)) / 7)) ;
$endDateWeekCnt = round(ceil( date('d',strtotime($endDate)) / 7)) ;
$datediff = strtotime(date('Y-m',strtotime($endDate))."-01") - strtotime(date('Y-m',strtotime($strtDate))."-01");
$totalnoOfWeek = round(floor($datediff/(60*60*24)) / 7) + $endDateWeekCnt - $startDateWeekCnt ;
$weekStart = $strtDate;

## Declarations for Graphs
## For Column2D Chart
## XML Common Tags
  $graph_open = "<graph xAxisName='' yAxisName='Scripts' caption='Allotted Vs Completed' decimalPrecision='0' rotateNames='1' numDivLines='3' numberPrefix='' showValues='0' showAlternateHGridColor='1' AlternateHGridColor='ff5904' divLineColor='ff5904' divLineAlpha='20' alternateHGridAlpha='5' canvasBorderThickness='0' showColumnShadow='0'> \n";
  $category_open = "<categories >\n";
  $category_close = "</categories>\n";
  $data_set_close= "</dataset>\n";
  $graph_close= "</graph>\n";
  $dataSet_HDR[Allotted] = "<dataset seriesName='Allotted' color='B22222' showValues='1'>\n";
  $dataSet_HDR[Complete] = "<dataset seriesName='Complete' color='8BBA00' showValues='0'>\n";

### Writing XML File to render Overall Chart
  $myFile = "Data/ACTAllottedVsCompleted-$today.xml";
  unlink($myFile);
  $fh = fopen($myFile, 'w') or die("Can't open file");

## For Pie Chart
$strXML = "<graph caption='Scripts Allotted per Tester' decimalPrecision='1' showPercentageValues='0' showNames='1' showValues='1' showPercentageInLabel='1' pieYScale='60' pieBorderAlpha='40' pieFillAlpha='70' pieSliceDepth='15' pieRadius='130'>";

echo "<table border=1 align=auto>\n";
printf ("<tr>
    	     <td class=a>Tester Name</td>
    	     <td class=a>Allotted</td>
             <td class=a>Scripted</td>
             <td class=a>Review</td>
             <td class=a>Rework</td>
             <td class=a>Checked-In</td>
             <td class=a>Completed</td>
	</tr>");

$totalnoOfWeek=2;
for ($week = 1; $week < $totalnoOfWeek; $week++) {
    ## echo "$weekStart to ";
    $weekEnd = date("Y-m-d", strtotime("+6 day", strtotime($weekStart)));

    ## Getting the user List
    $sql = "SELECT act_user_name from $user_db";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
            ## For each user get the data Print
            $countOverall = getDetail("select count(*) as value from $test_report_db where act_test_assigned_to='$row[act_user_name]'");
            $countScripted = getDetail("select count(*) as value from $test_report_db where act_test_status='$scripted' and act_test_assigned_to='$row[act_user_name]' and act_test_scripted_date>='$weekStart'");
            $countReview = getDetail("select count(*) as value from $test_report_db where act_test_status='$review' and act_test_assigned_to='$row[act_user_name]' and act_test_review_date>='$weekStart'");
            $countReWork = getDetail("select count(*) as value from $test_report_db where act_test_status='$rework' and act_test_assigned_to='$row[act_user_name]' and act_test_rework_date>='$weekStart'");
            $countCheckin = getDetail("select count(*) as value from $test_report_db where act_test_status='$checkedIn' and act_test_assigned_to='$row[act_user_name]' and act_test_checkin_date>='$weekStart'");
	    $countComplete = $countScripted + $countReview + $countReWork + $countCheckin;
            echo "<td align=left><a href=$PHP_SELF?show_detail=yes&act_user_name=$row[act_user_name]&start_date=$weekStart>$row[act_user_name]</td>";
            echo "<td align=center><a href=$PHP_SELF?dispTCase=yes&act_user_name=$row[act_user_name]&start_date=$weekStart&act_test_status=total>$countOverall</td>";
            echo "<td align=center><a href=$PHP_SELF?dispTCase=yes&act_user_name=$row[act_user_name]&start_date=$weekStart&act_test_status=scripted>$countScripted</td>";
            echo "<td align=center><a href=$PHP_SELF?dispTCase=yes&act_user_name=$row[act_user_name]&start_date=$weekStart&act_test_status=review>$countReview</td>";
            echo "<td align=center><a href=$PHP_SELF?dispTCase=yes&act_user_name=$row[act_user_name]&start_date=$weekStart&act_test_status=rework>$countReWork</td>";
            echo "<td align=center><a href=$PHP_SELF?dispTCase=yes&act_user_name=$row[act_user_name]&start_date=$weekStart&act_test_status=checkin>$countCheckin</td>";
            echo "<td align=center><a href=$PHP_SELF?dispTCase=yes&act_user_name=$row[act_user_name]&start_date=$weekStart&act_test_status=complete>$countComplete</td>";
	    echo "</tr>";
	    $x_act_user_name = $x_act_user_name."<category name='$row[act_user_name]' />\n";
	    $dataAllotted = $dataAllotted."<set value='$countOverall' />\n";
	    $dataComplete = $dataComplete."<set value='$countComplete' />\n";
	    $strXML .= "<set name='" . $row['act_user_name'] . "' value='" . $countOverall . "' />";
        }
    } else {
        echo "0 users found";
    }

## echo "$weekEnd <br>";
$weekStart = date("Y-m-d", strtotime("+1 day", strtotime($weekEnd)));
}
$conn->close();
echo "</table><br><br>";
$strXML .= "</graph>";
echo "<br><br>";

#Writing data to file
  fwrite($fh, $graph_open);
  fwrite($fh, $category_open);
  fwrite($fh, $x_act_user_name);
  fwrite($fh, $category_close);
  fwrite($fh, $dataSet_HDR[Complete]);
  fwrite($fh, $dataComplete);
  fwrite($fh, $data_set_close);
  fwrite($fh, $dataSet_HDR[Allotted]);
  fwrite($fh, $dataAllotted);
  fwrite($fh, $data_set_close);
  fwrite($fh, $graph_close);
  fclose($fh);

echo "<table border=0 align=auto>\n";
echo "<tr><td>";
echo renderChart("Chart/Charts/FCF_Pie3D.swf", "", $strXML, "Assignment", 500, 300);
echo "</td><td>";
echo renderChart("Chart/Charts/FCF_StackedColumn2D.swf", "Data/ACTAllottedVsCompleted-$today.xml", "", "Overall", 500, 300);
echo "</td></tr></table>";
}

#########################################################################################
#                                                                                       #
#  Function to display the WoW data Categorized by Username				#
#                                                                                       #
#########################################################################################
function wowUserName($act_user_name, $strtDate) {
include 'db_connect.php';
include 'common.php';

##### Printing Week-Week Dates ######
$endDate = date('Y/m/d', time());
$startDateWeekCnt = round(floor( date('d',strtotime($strtDate)) / 7)) ;
$endDateWeekCnt = round(ceil( date('d',strtotime($endDate)) / 7)) ;
$datediff = strtotime(date('Y-m',strtotime($endDate))."-01") - strtotime(date('Y-m',strtotime($strtDate))."-01");
$totalnoOfWeek = round(floor($datediff/(60*60*24)) / 7) + $endDateWeekCnt - $startDateWeekCnt ;
$weekStart = $strtDate;

echo "<br><br><br><center>";
echo "<b><font size=2>Printing WoW for Username : $act_user_name</font></b><br><br>";
echo "<table border=1 align=auto>\n";
printf ("<tr><td class=a>Week Number</td>
                <td class=a>Week From-To</td>
                <td class=a>Scripted</td>
                <td class=a>Review</td>
                <td class=a>Rework</td>
                <td class=a>Checked-In</td></tr>");


for ($week = 1; $week <= $totalnoOfWeek; $week++) {
    ## echo "$weekStart to ";
    $weekEnd = date("Y-m-d", strtotime("+6 day", strtotime($weekStart)));

    printf("<td align=center>$week</td><td>$weekStart - $weekEnd </td>");

    ## Getting the user List
    $countScripted = getDetail("select count(*) as value from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_scripted_date between '$weekStart' AND '$weekEnd'");
    $countReview = getDetail("select count(*) as value from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_review_date between '$weekStart' AND '$weekEnd'");
    $countReWork = getDetail("select count(*) as value from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_rework_date between '$weekStart' AND '$weekEnd'");
    $countCheckin = getDetail("select count(*) as value from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_checkin_date between '$weekStart' AND '$weekEnd'");
    echo "<td align=center>$countScripted</td>";
    echo "<td align=center>$countReview</td>";
    echo "<td align=center>$countReWork</td>";
    echo "<td align=center>$countCheckin</td></tr>";

## echo "$weekEnd <br>";
$weekStart = date("Y-m-d", strtotime("+1 day", strtotime($weekEnd)));
} 
echo "</table><br><br>";
}

########################################################################################
#											#
#  Function to display the Modules from Database, Also sets appropriate del/mod flags	#
#											#
#########################################################################################
function dispTrend($viewType) {
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

$countTotal = getDetail("select count(*) as value from $test_report_db");
$countNotAssigned    = getDetail("select count(*) as value from $test_report_db where act_test_status=''");
$countNotAutomatable = getDetail("select count(*) as value from $test_report_db where act_test_status='$notAutomatable'");
$countAssigned       = getDetail("select count(*) as value from $test_report_db where act_test_status='$assigned'");
$countScripted       = getDetail("select count(*) as value from $test_report_db where act_test_status='$scripted'");
$countReview         = getDetail("select count(*) as value from $test_report_db where act_test_status='$review'"); 
$countReWork         = getDetail("select count(*) as value from $test_report_db where act_test_status='$rework'");
$countCheckin        = getDetail("select count(*) as value from $test_report_db where act_test_status='$checkedIn'");
$countCompleted      = $countScripted + $countReview + $countReWork + $countCheckin;

echo "<br><br><br><center>";
echo "<b><font size=2>Assignment Snap</font></b><br><br>";


echo "<select name=date>";
for($i=1;$i<=31;$i++){
echo "<option name='$i'>$i</option>";
}
echo "</select>";

echo "<select name=month>";
for($i=1;$i<=12;$i++){
$month = date('F', mktime(0, 0, 0, $i, 10));
// $month=date('F',strtotime("first day of -$i month"));
echo "<option value=$i>$month</option> ";
}
echo "</select>";

echo "<select name=year>";
echo "<option name=2015>2015</option>";
echo "<option name=2016>2016</option>";
echo "<option selected name=2017>2017</option>";
echo "<option name=2018>2018</option>";
echo "<option name=2019>2019</option>";
echo "<option name=2020>2020</option>";
echo "</select>";

echo "&nbsp; <font size=1><input type=\"Submit\" name=\"submit\" value=\"Start Date\"><br><br>";

#### Printing overall status ####
echo "<table border=1 align=auto>\n";
printf ("<tr><td class=a>Total Test cases</td>
	     <td class=a>Completed</td>
		<td class=a>Pending</td>
		<td class=a>Not Automatable</td> 
		<td class=a>Scripted</td>
		<td class=a>Review</td>
		<td class=a>Rework</td>
		<td class=a>Checked-In</td><tr>
		<td align=center>$countTotal</td>
		<td align=center>$countCompleted</td>
		<td align=center>$countAssigned</td>
		<td align=center>$countNotAutomatable</td>
		<td align=center>$countScripted</td>
		<td align=center>$countReview</td>
		<td align=center>$countReWork</td>
		<td align=center>$countCheckin</td>
	  </table><br><br>");
	

echo "<b><font size=2>Tester - Overall Progress</font></b>";
if ($_REQUEST[submit]) {
      printModules("Y", "$_POST[year]-$_POST[month]-$_POST[date]");
} else {
      printModules("N");
}
}

#########################################################################################
#											#
# Code to check whether Delete View, Modify View or Normal View Needs to be shown       #
#											#
#########################################################################################
if ($_REQUEST[submit]) {
      #echo "$_POST[date]-$_POST[month]-$_POST[year]";
      dispTrend("Y", "$_POST[year]-$_POST[month]-$_POST[date]");
} elseif ($_GET[show_detail] == "yes") {
     #echo "Got a show trend from username $_GET[act_user_name] from starting date $_GET[start_date]"; 
     wowUserName($_GET[act_user_name],$_GET[start_date]);
} elseif ($_GET[dispTCase] == "yes") {
     dispTCase($_GET[act_user_name], $_GET[start_date], $_GET[act_test_status]);
} else {
      dispTrend("N");
}

?>
</html>
