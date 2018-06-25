<html>
<head>
<script>
function toggleTable() {
    var lTable = document.getElementById("plotTable");
    lTable.style.display = (lTable.style.display == "table") ? "none" : "table";
}
</script>
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
#  Function to display overall WoW data							#
#                                                                                       #
#########################################################################################
function wowOverall($flag, $date) {
include 'db_connect.php';
include 'common.php';

if ($flag == "Y") {
  $date = "$_POST[year]-$_POST[month]-$_POST[date]";
  list($start_date, $end_date) = x_week_range($date);
  $strtDate = $start_date;
  #echo "Start Date is : $strtDate";
  if (strtotime($strtDate) <= strtotime('2015-10-01')) {
  $strtDate = '2015-10-11';
  }
} else {
  list($start_date, $end_date) = x_week_range(date('Y-m-01'));
  $strtDate = $start_date;
  #$strtDate = '2015-10-11';
}

$totAverage = 0;

##### Printing Week-Week Dates ######
$endDate = date('Y/m/d', time());
$startDateWeekCnt = round(floor( date('d',strtotime($strtDate)) / 7)) ;
//echo "<br>startDateWeekCnt is $startDateWeekCnt <br>";
$endDateWeekCnt = round(ceil( date('d',strtotime($endDate)) / 7)) ;
//echo "endDateWeekCnt is $endDateWeekCnt <br>";
$datediff = strtotime(date('Y-m',strtotime($endDate))."-01") - strtotime(date('Y-m',strtotime($strtDate))."-01");
$totalnoOfWeek = round(floor($datediff/(60*60*24)) / 7) + $endDateWeekCnt - $startDateWeekCnt ;
$weekStart = $strtDate;

$userCount=7;

echo "<table border=1 align=auto>\n";
printf ("<tr><td class=a>Week Number</td>
                <td class=a>Week From-To</td>
                <td class=a>Scripted</td>
                <td class=a>Review</td>
                <td class=a>Rework</td>
                <td class=a>Checked-In</td>
                <td class=a>Average</td>
		<td class=a>Tester Count</td>
		</tr>");

for ($week = 1; $week <= $totalnoOfWeek; $week++) {
    ## echo "$weekStart to ";
    $weekEnd = date("Y-m-d", strtotime("+6 day", strtotime($weekStart)));
    printf("<td align=center>$week</td><td>$weekStart - $weekEnd </td>");

    ## Getting the user List
    $countScripted = getDetail("select count(*) as value from $test_report_db where act_test_scripted_date between '$weekStart' AND '$weekEnd'");
    $countReview   = getDetail("select count(*) as value from $test_report_db where act_test_review_date between '$weekStart' AND '$weekEnd'");
    $countReWork   = getDetail("select count(*) as value from $test_report_db where act_test_rework_date between '$weekStart' AND '$weekEnd'");
    $countCheckin  = getDetail("select count(*) as value from $test_report_db where act_test_checkin_date between '$weekStart' AND '$weekEnd'");
    if (strtotime($weekStart) <= strtotime('2015-12-31')) {
      $userCount=1;
    } elseif (strtotime('$weekStart') <= strtotime('2016-01-01') && strtotime($weekStart) <= strtotime('2016-02-13')) {
      $userCount=4;
    } else {
      $userCount=7;
    }
    $countAverage  = round($countScripted / $userCount);
     echo "<td align=center>$countScripted</td>";
     echo "<td align=center>$countReview</td>";
     echo "<td align=center>$countReWork</td>";
     echo "<td align=center>$countCheckin</td>";
     echo "<td align=center>$countAverage</td>";
     echo "<td align=center>$userCount</td>";
     $totAverage = $totAverage + $countAverage;
     echo  "</tr>";

$weekStart = date("Y-m-d", strtotime("+1 day", strtotime($weekEnd)));
} 
     $aveAverage = round(($totAverage / ($totalnoOfWeek)), 0);
     echo "<tr>	
           <td align=center class=a colspan=6>Total</td> 
	   <td align=center>$aveAverage</td>
           <td></td>
           </tr>";
     echo "</table><br><br>";
}


#########################################################################################
#                                                                                       #
#  Function to display the WoW data Categorized by Username				#
#                                                                                       #
#########################################################################################
function wowUserName($flag, $date) {
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
  list($start_date, $end_date) = x_week_range(date('Y-m-01'));
  $strtDate = $start_date;
}

##### Printing Week-Week Dates ######
$endDate = date('Y/m/d', time());
$startDateWeekCnt = round(floor( date('d',strtotime($strtDate)) / 7)) ;
$endDateWeekCnt = round(ceil( date('d',strtotime($endDate)) / 7)) ;
$datediff = strtotime(date('Y-m',strtotime($endDate))."-01") - strtotime(date('Y-m',strtotime($strtDate))."-01");
$totalnoOfWeek = round(floor($datediff/(60*60*24)) / 7) + $endDateWeekCnt - $startDateWeekCnt ;
$weekStart = $strtDate;

echo "<table border=1 align=auto>\n";
printf ("<tr><td class=a>Week Number</td>
                <td class=a>Week From-To</td>
                <td class=a>Tester Name</td>
                <td class=a>Scripted</td>
                <td class=a>Review</td>
                <td class=a>Rework</td>
                <td class=a>Checked-In</td></tr>");


$userCount = getDetail("select count(*) as value from $user_db");
##$userCount=11;

for ($week = 1; $week <= $totalnoOfWeek; $week++) {
    ## echo "$weekStart to ";
    $weekEnd = date("Y-m-d", strtotime("+6 day", strtotime($weekStart)));

    printf("<td align=center rowspan=$userCount>$week</td><td rowspan=$userCount>$weekStart - $weekEnd </td>");

    ## Getting the user List
    $sql = "SELECT act_user_name from $user_db";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
	    ## For each user get the data for each Week and Print
            $countScripted = getDetail("select count(*) as value from $test_report_db where act_test_assigned_to='$row[act_user_name]' and act_test_scripted_date between '$weekStart' AND '$weekEnd'");
            $countReview = getDetail("select count(*) as value from $test_report_db   where act_test_assigned_to='$row[act_user_name]' and act_test_review_date between '$weekStart' AND '$weekEnd'");
            $countReWork = getDetail("select count(*) as value from $test_report_db   where act_test_assigned_to='$row[act_user_name]' and act_test_rework_date between '$weekStart' AND '$weekEnd'");
            $countCheckin = getDetail("select count(*) as value from $test_report_db  where act_test_assigned_to='$row[act_user_name]' and act_test_checkin_date between '$weekStart' AND '$weekEnd'");
	    echo "<td align=left>$row[act_user_name]</td>";
	    echo "<td align=center>$countScripted</td>";
	    echo "<td align=center>$countReview</td>";
	    echo "<td align=center>$countReWork</td>";
	    echo "<td align=center>$countCheckin</td></tr>";
        }
    } else {
        echo "0 users found";
    }

## echo "$weekEnd <br>";
$weekStart = date("Y-m-d", strtotime("+1 day", strtotime($weekEnd)));
} 
echo "</table><br><br>";
$conn->close();
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
echo "<b><font size=2>Execution Trend</font></b><br><br>";


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
		<td class=a>In-Progress</td>
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
	

echo "<b><font size=2>Week on Week Progress</font></b>";

if ($_REQUEST[submit]) {
      wowOverall("Y", "$_POST[year]-$_POST[month]-$_POST[date]");
} else {
      WowOverall("N");
}
echo "<b><font size=2>Week on Week Progress - Categorized by Tester </font></b>";
if ($_REQUEST[submit]) {
      wowUserName("Y", "$_POST[year]-$_POST[month]-$_POST[date]");
} else {
      wowUserName("N");
}
}



#########################################################################################
#                                                                                       #
#  Function to display the Testcase from Database, Also sets appropriate del/mod flags  #
#                                                                                       #
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
  $reqAction="modCase";
  $actFlag="yes";
  $cnfrmFlag="dummy";
  $formDirect=$PHP_SELF;
} else {
  $reqAction="action";
  $actFlag="nothing";
  $cnfrmFlag="dummy";
  $formDirect=$PHP_SELF;
}

$c=0;
if ($_GET[act_test_status] != "") {
  $sql = "select * from $test_report_db where act_test_module=\"$_GET[act_mod_name]\" and act_test_status=\"$_GET[act_test_status]\"";
} else {
  $sql = "select * from $test_report_db where act_test_module=\"$_GET[act_mod_name]\"";
}



$result = $conn->query($sql);

if ($result->num_rows > 0) {
echo "<br><br><br><center>";
echo "<b><font size=2>Listing Test cases for \"$act_mod_name\" feature</font></b><br><br>";

$countTotal = getDetail("select count(*) as value from $test_report_db where act_test_module='$act_mod_name'");
$countNotAssigned    = getCount($test_report_db, $act_mod_name, $User, $notAssigned);
$countNotAutomatable = getCount($test_report_db, $act_mod_name, $User, $notAutomatable);
$countAssigned       = getCount($test_report_db, $act_mod_name, $User, $assigned);
$countScripted       = getCount($test_report_db, $act_mod_name, $User, $scripted);
$countReview         = getCount($test_report_db, $act_mod_name, $User, $review);
$countReWork         = getCount($test_report_db, $act_mod_name, $User, $rework);
$countCheckin        = getCount($test_report_db, $act_mod_name, $User, $checkedIn);
$countCompleted      = $countScripted + $countReview + $countReWork + $countCheckin;

echo "<table border=1 align=left>";
printf ("<tr><td class=a width=\"150\">Total Test cases</td>	<td width=\"80\" align=center> %s </font> </td></tr>", $countTotal);
printf ("<tr><td class=a>Completed</td>				<td align=center> %s </font></td></tr>", $countCompleted);
printf ("<tr><td class=a>Pending</td>				<td align=center> %s </font></td></tr>", $countAssigned);
printf ("<tr><td class=a>Not Automatable</td>			<td align=center> %s </font></td></tr>", $countNotAutomatable);
printf ("<tr><td class=a>Scripted</td>				<td align=center> %s </font></td></tr>", $countScripted);
printf ("<tr><td class=a>Review</td>				<td align=center> %s </font></td></tr>", $countReview);
printf ("<tr><td class=a>Rework</td>				<td align=center> %s </font></td></tr>", $countReWork);
printf ("<tr><td class=a>Checked-In</td>			<td align=center> %s </font></td></tr>", $countCheckin);
echo "</table>";
echo "<br><br><br>";
echo "<br><br><br>";
echo "<br><br><br>";
echo "<br><br><br>";
echo "<br><br><br>";


echo "<br><table border=1>\n";
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
#  Function to display the Testcase from Database, Also sets appropriate del/mod flags  #
#                                                                                       #
#########################################################################################
function plotTrend() {
include 'db_connect.php';
include 'common.php';
include("Chart/Code/PHP/Includes/FusionCharts.php");

$target = array
  (
  array("2015","October",50,"Oct"),
  array("2015","November",100,"Nov"),
  array("2015","December",150,"Dec"),
  array("2016","January",240,"Jan"),
  array("2016","February",480,"Feb"),
  array("2016","March",720,"Mar"),
  array("2016","April",960,"Apr"),
  array("2016","May",1200,"May"),
  array("2016","June",1500,"Jun"),
  array("2016","July",1800,"Jul"),
  array("2016","August",2100,"Aug"),
  array("2016","September",2400,"Sep"),
  array("2016","October",2600,"Oct"),
  array("2016","November",2800,"Nov"),
  array("2016","December",3000,"Dec"),
  array("2017","January",3200,"Jan"),
  array("2017","February",3400,"Feb"),
  array("2017","March",3600,"Mar"),
  array("2017","April",3800,"Apr"),
  array("2017","May",4000,"May"),
  array("2017","June",4200,"Jun"),
  array("2017","July",4400,"Jul"),
  array("2017","August",4500,"Aug"),
  array("2017","September",4600,"Sep"),
  array("2017","October",4700,"Oct"),
  array("2017","November",4800,"Nov"),
  array("2017","December",4900,"Dec"),
  array("2018","January",5000,"Jan"),
  array("2018","February",5050,"Feb"),
  array("2018","March",5100,"Mar"),
  array("2018","April",5150,"Apr"),
  array("2018","May",5200,"May"),
  array("2018","June",5250,"Jun"),
  array("2018","July",5300,"Jul"),
  array("2018","August",5350,"Aug"),
  array("2018","September",5400,"Sep"),
  array("2018","October",5450,"Oct"),
  array("2018","November",5500,"Nov"),
  array("2018","December",5550,"Dec")
  );

## XML Common Tags
  $graph_open = "<graph caption='' subcaption='' hovercapbg='FFECAA' hovercapborder='F47E00' formatNumberScale='0' decimalPrecision='0' showvalues='1' numdivlines='4' numVdivlines='0' yaxisminvalue='0' yaxismaxvalue='$yaxismaxvalue_overall' rotateNames='0' showAlternateHGridColor='1' AlternateHGridColor='ff5904' divLineColor='ff5904' divLineAlpha='20' alternateHGridAlpha='5'> \n";
  $category_open = "<categories >\n";
  $category_close = "</categories>\n";
  $data_set_close= "</dataset>\n";
  $graph_close= "</graph>\n";

### Writing XML File to render Overall Chart
  $myFile = "Data/ACToverallTrend-$today.xml";
  unlink($myFile);
  $fh = fopen($myFile, 'w') or die("Can't open file");

  $x_axis_dates = $x_axis_dates."<category name='$weekStart - $weekEnd' />\n";
  $dataSet_HDR[Target] = "<dataset seriesName='Target' color='B22222' anchorBorderColor='B22222' anchorBgColor='B22222'>\n";
  $dataSet_HDR[Actual] = "<dataset seriesName='Actual' color='006400' anchorBorderColor='006400' anchorBgColor='006400'>\n";

  echo "<br><center><font size=2><b> Target/Achieved Trend - ";
  echo "<a id=\"loginLink\" onclick=\"toggleTable();\" href=\"#\">[Show/Hide Data]</a>";
  echo "<table style=\"display:none;\" id=\"plotTable\" border=1 align=center><tr>";
  echo "<td class=a>Month</td><td class=a align=center width=60>Target</td><td class=a align=center width=60>Achieved</td></tr>";
  
  for ($row = 0; $row < count($target); $row++) {
    $year     = $target[$row][0];
    $month    = $target[$row][1];
    $planData = $target[$row][2];
    $shortMonth = $target[$row][3];
    $x_axis_month = $x_axis_month."<category name='$shortMonth' />\n";
    $dataTarget = $dataTarget."<set value='$planData' />\n";

    $currentMonth = strtotime($month."-".$day."-".$year);
    $today_date = date("Y-m-01");
    $nextMonth = strtotime(date('Y-F', strtotime('+1 month', strtotime($today_date))));
 
    if ($currentMonth < $nextMonth ) {
        $actualAchieved = $actualAchieved + getDetail("select count(*) as value from $test_report_db where YEAR(act_test_scripted_date)='$year' and MONTHNAME(act_test_scripted_date)='$month'");
        $dataActual = $dataActual."<set value='$actualAchieved' />\n";
    } else {
        $actualAchieved = "";
    }
    echo "<tr><td>$month/$year</td><td align=center>$planData</td><td align=center>$actualAchieved</td></tr>";
  }

echo "</tr></table><br><br><center>";
#Writing data to file
  fwrite($fh, $graph_open);
  fwrite($fh, $category_open);
  fwrite($fh, $x_axis_month);
  fwrite($fh, $category_close);
  fwrite($fh, $dataSet_HDR[Target]);
  fwrite($fh, $dataTarget);
  fwrite($fh, $data_set_close);
  fwrite($fh, $dataSet_HDR[Actual]);
  fwrite($fh, $dataActual);
  fwrite($fh, $data_set_close);
  fwrite($fh, $graph_close);
  fclose($fh);

echo renderChartHTML("Chart/Charts/FCF_MSLine.swf", "Data/ACToverallTrend-$today.xml", "", "myFirst", 800, 350, false);
$conn->close();
}


#########################################################################################
#											#
# Code to check whether Delete View, Modify View or Normal View Needs to be shown       #
#											#
#########################################################################################
if ($_REQUEST[submit]) {
      dispTrend("Y", "$_POST[year]-$_POST[month]-$_POST[date]");
} else if ($_REQUEST[plotTrend]) {
      plotTrend();
} else {
      dispTrend("N");
}

?>
</body>
</html>
