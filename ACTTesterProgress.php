<html>
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
#  Function to display the day-to-day data Categorized by Username			#
#                                                                                       #
#########################################################################################
function dtdUserName($flag, $strtDate) {
include 'db_connect.php';
include 'common.php';
include("Chart/Code/PHP/Includes/FusionCharts.php");
shuffle($chartColors);

if ($flag == "Y") {
  $date = "$_POST[year]-$_POST[month]-$_POST[date]";
  $strtDate = $date;
  if (strtotime($strtDate) <= strtotime('2015-10-01')) {
  $strtDate = '2015-10-11';
  }
  #echo "Start Date is : $strtDate";
} else {
  list($start_date, $end_date) = x_week_range(date('Y-m-01'));
  $strtDate = $start_date;
}

foreach ($_POST['act_user_name'] as $act_user_name) {
  #echo "<br>$act_user_name <br>";
}


## XML Common Tags
  $graph_open = "<graph caption='Tester Progress' subcaption='' hovercapbg='FFECAA' hovercapborder='F47E00' formatNumberScale='0' decimalPrecision='0' showvalues='1' numdivlines='5' numVdivlines='0' yaxisminvalue='0' yaxismaxvalue='$yaxismaxvalue' rotateNames='1' showAlternateHGridColor='1' AlternateHGridColor='ff5904' divLineColor='ff5904' divLineAlpha='20' alternateHGridAlpha='5'> \n";
  $category_open = "<categories >\n";
  $category_close = "</categories>\n";
  $data_set_close= "</dataset>\n";
  $graph_close= "</graph>\n";
#
### Writing XML File to render Overall Chart
  $myFile = "Data/ACTTesterProgress-$today.xml";
  unlink($myFile);
  $fh = fopen($myFile, 'w') or die("Can't open file");


##### Printing Week-Week Dates ######
#echo "Start is $strtDate and end is $today <br>";
$days_between = ceil(abs(strtotime($today) - strtotime($strtDate)) / 86400);

$currentDay = date("Y-m-d", strtotime("+0 day", strtotime($strtDate)));
$totalScripted = 0;
$overallScripted = 0;

echo "<br><br><br><center>";
echo "<b><font size=2>Tester Progress - From $strtDate till $today</font></b><br><br>";
echo "<table border=1 align=auto>\n";
printf ("<tr>
                <td class=a>Day</td>
                <td class=a>Date</td>");

$userCount = 0;
foreach ($_POST['act_user_name'] as $act_user_name) {
  printf ("<td class=a>$act_user_name</td>");
  $userCount++;
}
echo "<td class=a align=center width=50>Total</td><td class=a align=center width=50>Average</td><td align=center width=50 class=a>Overall</td></tr>";

## Loop starts here
for ($day = 1; $day <= $days_between+1; $day++) {

    printf("<td align=center>$day</td><td>$currentDay</td>");

    ## Getting the user List
    $x_axis_dates = $x_axis_dates."<category name='$currentDay' />\n";

    foreach ($_POST['act_user_name'] as $act_user_name) {

        $dataSet_HDR[$act_user_name] = "<dataset seriesName='$act_user_name' color='$chartColors[$day]' anchorBorderColor='$chartColors[$day]' anchorBgColor='$chartColors[$day]'>\n";
        shuffle($chartColors);
        $countScripted = getDetail("select count(*) as value from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_scripted_date='$currentDay'");
        $data[$act_user_name] =	$data[$act_user_name]."<set value='$countScripted' />\n"; 
        $overallScripted = $overallScripted + $countScripted;
        $totalScripted = $totalScripted + $countScripted;
        $averageCount = ceil($totalScripted / $userCount);

      if ($countScripted >= $day_target) {
         $class="pass";
      } else {
         $class="fail";
      }
      echo "<td align=center class=$class>$countScripted</td>";
    }
      echo "<td align=center>$totalScripted</td>";
      echo "<td align=center>$averageCount</td>";
      echo "<td align=center>$overallScripted</td>";
      echo "</tr>";
      $totalScripted = 0;

$currentDay = date("Y-m-d", strtotime("+1 day", strtotime($currentDay)));
}

echo "<tr><td colspan=2 align=center class=a><b>Total</td>";
foreach ($_POST['act_user_name'] as $act_user_name) {
      $countScripted = getDetail("select count(*) as value from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_scripted_date between '$strtDate' AND '$currentDay'");
      echo "<td align=center>$countScripted</td>";
}

echo "<td class=a></td><td class=a></td><td class=a></td></tr></table><br><br>";


#Writing data to file
  fwrite($fh, $graph_open);
  fwrite($fh, $category_open);
  fwrite($fh, $x_axis_dates);
  fwrite($fh, $category_close);
  foreach ($_POST['act_user_name'] as $act_user_name) {
      fwrite($fh, $dataSet_HDR[$act_user_name]);
      fwrite($fh, $data[$act_user_name]);
      fwrite($fh, $data_set_close);
  }
  fwrite($fh, $graph_close);
  fclose($fh);

echo "<center>";
echo renderChartHTML("Chart/Charts/FCF_MSLine.swf", "Data/ACTTesterProgress-$today.xml", "", "myFirst", 900, 500, false);

}



#########################################################################################
#                                                                                       #
#  Function to display the WoW data Categorized by Username				#
#                                                                                       #
#########################################################################################
function wowUserName($flag, $strtDate) {
include 'db_connect.php';
include 'common.php';
include("Chart/Code/PHP/Includes/FusionCharts.php");
shuffle($chartColors);

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

foreach ($_POST['act_user_name'] as $act_user_name) {
  #echo "<br>$act_user_name <br>";
}


## XML Common Tags
  $graph_open = "<graph caption='Tester Progress' subcaption='' hovercapbg='FFECAA' hovercapborder='F47E00' formatNumberScale='0' decimalPrecision='0' showvalues='1' numdivlines='5' numVdivlines='0' yaxisminvalue='0' yaxismaxvalue='$yaxismaxvalue' rotateNames='1' showAlternateHGridColor='1' AlternateHGridColor='ff5904' divLineColor='ff5904' divLineAlpha='20' alternateHGridAlpha='5'> \n";
  $category_open = "<categories >\n";
  $category_close = "</categories>\n";
  $data_set_close= "</dataset>\n";
  $graph_close= "</graph>\n";
#
### Writing XML File to render Overall Chart
  $myFile = "Data/ACTTesterProgress-$today.xml";
  unlink($myFile);
  $fh = fopen($myFile, 'w') or die("Can't open file");


##### Printing Week-Week Dates ######
$endDate = date('Y/m/d', time());
$startDateWeekCnt = round(floor( date('d',strtotime($strtDate)) / 7)) ;
$endDateWeekCnt = round(ceil( date('d',strtotime($endDate)) / 7)) ;
$datediff = strtotime(date('Y-m',strtotime($endDate))."-01") - strtotime(date('Y-m',strtotime($strtDate))."-01");
$totalnoOfWeek = round(floor($datediff/(60*60*24)) / 7) + $endDateWeekCnt - $startDateWeekCnt ;
$weekStart = $strtDate;
$totalScripted = 0;
$overallScripted = 0;

echo "<br><br><br><center>";
echo "<b><font size=2>Tester Progress - From $strtDate till $today</font></b><br><br>";
echo "<table border=1 align=auto>\n";
printf ("<tr>
                <td class=a>Week Number</td>
                <td class=a>Week From-To</td>");

$userCount = 0;
foreach ($_POST['act_user_name'] as $act_user_name) {
  printf ("<td class=a>$act_user_name</td>");
  $userCount++;
}
echo "<td class=a align=center width=50>Total</td><td class=a align=center width=50>Average</td><td align=center width=50 class=a>Overall</td></tr>";

## Loop starts here
for ($week = 1; $week <= $totalnoOfWeek; $week++) {
    ## echo "$weekStart to ";
    $weekEnd = date("Y-m-d", strtotime("+6 day", strtotime($weekStart)));

    printf("<td align=center>$week</td><td>$weekStart - $weekEnd </td>");

    ## Getting the user List
    $x_axis_dates = $x_axis_dates."<category name='$weekStart - $weekEnd' />\n";

    foreach ($_POST['act_user_name'] as $act_user_name) {

        $dataSet_HDR[$act_user_name] = "<dataset seriesName='$act_user_name' color='$chartColors[$week]' anchorBorderColor='$chartColors[$week]' anchorBgColor='$chartColors[$week]'>\n";
        shuffle($chartColors);
        $countScripted = getDetail("select count(*) as value from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_scripted_date between '$weekStart' AND '$weekEnd'");
        $data[$act_user_name] =	$data[$act_user_name]."<set value='$countScripted' />\n"; 
        $overallScripted = $overallScripted + $countScripted;
        $totalScripted = $totalScripted + $countScripted;
        $averageCount = ceil($totalScripted / $userCount);

      if ($countScripted >= $target) {
         $class="pass";
      } else {
         $class="fail";
      }
      echo "<td align=center class=$class>$countScripted</td>";
    }
      echo "<td align=center>$totalScripted</td>";
      echo "<td align=center>$averageCount</td>";
      echo "<td align=center>$overallScripted</td>";
      echo "</tr>";
      $totalScripted = 0;

$weekStart = date("Y-m-d", strtotime("+1 day", strtotime($weekEnd)));
}

echo "<tr><td colspan=2 align=center class=a><b>Total</td>";
foreach ($_POST['act_user_name'] as $act_user_name) {
      $countScripted = getDetail("select count(*) as value from $test_report_db where act_test_assigned_to='$act_user_name' and act_test_scripted_date between '$strtDate' AND '$weekEnd'");
      echo "<td align=center>$countScripted</td>";
}

echo "<td class=a></td><td class=a></td><td class=a></td></tr></table><br><br>";


#Writing data to file
  fwrite($fh, $graph_open);
  fwrite($fh, $category_open);
  fwrite($fh, $x_axis_dates);
  fwrite($fh, $category_close);
  foreach ($_POST['act_user_name'] as $act_user_name) {
      fwrite($fh, $dataSet_HDR[$act_user_name]);
      fwrite($fh, $data[$act_user_name]);
      fwrite($fh, $data_set_close);
  }
  fwrite($fh, $graph_close);
  fclose($fh);

echo "<center>";
echo renderChartHTML("Chart/Charts/FCF_MSLine.swf", "Data/ACTTesterProgress-$today.xml", "", "myFirst", 900, 500, false);

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

echo "<br><br><br><center>";
echo "<b><font size=2>Tester Progress</font></b><br><br>";

$sql = "SELECT act_user_name FROM act_user_account where act_active='Y' order by act_user_no";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
echo "<table border=1 align=auto><tr><td>\n";
echo "<select name=\"act_user_name[]\" multiple size=8>";
     while($row = $result->fetch_assoc()) {
        printf("<OPTION SELECTED VALUE=\"%s\">%s &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</OPTION>\n",
                $row["act_user_name"],
                $row["act_user_name"]);
    }
echo "</select></td><td>";
} else {
    echo "0 users found";
}


echo "<select name=date>";
for($i=1;$i<=31;$i++){
echo "<option name='$i'>$i</option>";
}
echo "</select>";

$selOption="";

echo "<select name=month>";
for($i=1;$i<=12;$i++){
$month = date('F', mktime(0, 0, 0, $i, 10));
$currentMonth=date('F');
if ($month == $currentMonth) {
  $selOption="selected";
} else  {
  $selOption="";
}
echo "<option $selOption value=$i>$month</option> ";
}
echo "</select>";

echo "<select name=year>";
echo "<option name=2015>2015</option>";
echo "<option name=2016>2016</option>";
echo "<option selected name=2017>2017</option>";
echo "<option name=2018>2018</option>";
echo "<option name=2019>2019</option>";
echo "<option name=2020>2020</option>";
echo "</select></td></tr>";

echo "<tr><td colspan=2 class=a><font size=2><center>Print Daily Progress &nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name=dailyProgress value=yes /></td></tr>";
echo "</table>";

echo "&nbsp; <br><font size=1><input type=\"Submit\" name=\"submit\" value=\"Start Date\"><br><br>";

}

#########################################################################################
#											#
# Code to check whether Delete View, Modify View or Normal View Needs to be shown       #
#											#
#########################################################################################
if ($_REQUEST[submit]) {
    if(isset($_REQUEST["dailyProgress"])) {
      dtdUserName("Y", "$_POST[year]-$_POST[month]-$_POST[date]");
    } else {
      wowUserName("Y", "$_POST[year]-$_POST[month]-$_POST[date]");
    }
} else {
      dispTrend("N");
}

?>
</body>
</html>

