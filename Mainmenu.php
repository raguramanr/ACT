<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>ACT Login</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link href="style1.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY>
<?php
echo "<TABLE width=\"100%\" border=1 bgcolor=#A0B0E0>";
echo "<TR>";
echo "<TD class=menu>";
echo "<font color=black>";
$User = $_SERVER["REMOTE_USER"];
echo "<A href=\"ACTDashboard.php\" target=body>Welcome: $User </a>";
echo "</strong></font>";
echo "</TD>";
if ($_GET[ACTManageusers]) {
   echo "<TD class=menu><A href=\"ACTUserview.php?listUser=yes\" target=body>View Users</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTUseradd.php\" target=body>Add User</A></TD>";
   echo "<TD class=menu><A href=\"ACTUserrem.php\" target=body>Remove User</A></TD>";
   echo "<TD class=menu><A href=\"ACTUsermod.php\" target=body>Modify User</A></TD>";
   echo "<TD class=menu><A href=\"ACTChangeuserpass.php\" target=body>Reset User Password</A></TD>";
   echo "<TD class=menu><A href=\"Mainmenu.php\" target=_self>Mainmenu</A></TD>";
} elseif ($_GET[ACTManageModules]) {
   echo "<TD class=menu><A href=\"ACTListmod.php\" target=body>List Module</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTImportmod.php\" target=body>Import Module</A></TD>";
   echo "<TD class=menu><A href=\"ACTListmod.php?remView=yes\" target=body>Remove Module</A></TD>";
   echo "<TD class=menu><A href=\"ACTListmod.php?modView=yes\" target=body>Modify Module</A></TD>";
   echo "<TD class=menu><A href=\"Mainmenu.php\" target=_self>Mainmenu</A></TD>";
} elseif ($_GET[ACTManageTestcases]) {
   echo "<TD class=menu><A href=\"ACTListCase.php\" target=body>View Test case</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTListmod.php?remCase=yes\" target=body>Remove Test case</A></TD>";
   echo "<TD class=menu><A href=\"ACTListmod.php?modCase=yes\" target=body>Modify Test case</A></TD>";
   echo "<TD class=menu><A href=\"ACTListmod.php?assignCase=yes\" target=body>Assign Owners</A></TD>";
   echo "<TD class=menu><A href=\"Mainmenu.php\" target=_self>Mainmenu</A></TD>";
} elseif ($_GET[ACTDashboard]) {
   echo "<TD class=menu><A href=\"ACTDashboard.php\" target=body>Status Dash</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTExecutionTrend.php\" target=body>Execution Trend</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTExecutionTrend.php?plotTrend=yes\" target=body>Overall Trend</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTAssignmentSnap.php\" target=body>Assignment Snap</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTTesterProgress.php\" target=body>Tester Progress</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTRegression.php\" target=body>Regression Progress</A></font></TD>";
   echo "<TD class=menu><A href=\"Mainmenu.php\" target=_self>Mainmenu</A></TD>";
} else {
  if ($User == "guest") {
   ##echo "<TD class=menu><A href=$PHP_SELF?ACTDashboard=yes target=_self>Dashboard </A></TD>";
   echo "<TD class=menu><A href=\"ACTDashboard.php\" target=body>Status Dash</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTExecutionTrend.php\" target=body>Execution Trend</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTExecutionTrend.php?plotTrend=yes\" target=body>Overall Trend</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTAssignmentSnap.php\" target=body>Assignment Snap</A></font></TD>";
   echo "<TD class=menu><A href=\"ACTTesterProgress.php\" target=body>Tester Progress</A></font></TD>";
  } else {
   echo "<TD class=menu><font color=\"\#00FF00\"><A href=ACTManageUsers.php target=body>Manage Users</A></font></TD>";
   echo "<TD class=menu><A href=$PHP_SELF?ACTManageModules=yes target=_self>Manage Modules</A></TD>";
   echo "<TD class=menu><A href=$PHP_SELF?ACTManageTestcases=yes target=_self>Manage Testcases</A></TD>";
   echo "<TD class=menu><A href=$PHP_SELF?ACTDashboard=yes target=_self>Dashboard</A></TD>";
   echo "<TD class=menu><A href=ACTReviewTracker.php target=body>Review Tracker</A></TD>";
  // echo "<TD class=menu><A href=\"ACTPassmod.php\" target=body>Change My Password</A></TD>";
  // echo "<TD class=menu><A href=\"#\" onclick=\"ActiveBrowser.close();\">Logout</A></TD>";

 } 
}
?>
</TR>
</TABLE>
</BODY>
</HTML>
