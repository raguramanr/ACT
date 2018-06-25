<html>
<body>
<link href="style1.css" rel="stylesheet" type="text/css">
<form method="post" action="<?php echo $PHP_SELF?>">
<?php
include 'common.php';

#########################################################################################
#                                                                                       #
#  Function to return difference in working dates					#
#                                                                                       #
#########################################################################################
function getWorkingDays($startDate,$endDate,$holidays) {
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);

    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;
    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)
        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;
            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }
    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
    //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }
    //We subtract the holidays
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }
    return $workingDays;
}

#########################################################################################
#                                                                                       #
#  Function to modify the given module from database                                    #
#                                                                                       #
#########################################################################################
function editReview($rt_mod_no) {
include 'db_connect.php';
include 'common.php';


echo "<br><br><center><b><font size=2>Edit Review: $rt_mod_no</b><br><br>";
$sql = "select * from $tracker_db where rt_mod_no='$rt_mod_no'";
$result = $conn->query($sql);
echo "<table align=center border=1>\n";
if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
        echo "<input type=hidden name=rt_mod_no 	value=\"$row[rt_mod_no]\">";
        echo "<tr><td class=a>Module Name</td>          <td><input size=50 type=Text readonly style=\"background-color:#E5E7E9;\" name=rt_mod_name value=\"$row[rt_mod_name]\"></td></tr>";
        echo "<tr><td class=a>Request Name</td>           <td><input size=50 type=Text name=rt_req_name value=\"$row[rt_req_name]\"></td></tr>";
        echo "<tr><td class=a>Functional Owner</td>             <td><input size=50 type=Text name=rt_func_owner value=\"$row[rt_func_owner]\"></td></tr>";
        echo "<tr><td class=a>Automation Owner</td>         <td><input size=50 type=Text name=rt_auto_owner value=\"$row[rt_auto_owner]\"></td></tr>";
        echo "<tr><td class=a>Scripted By</td>           <td><input size=50 type=Text name=rt_script_owner value=\"$row[rt_script_owner]\"></td></tr>";
        echo "<tr><td class=a>Assigned On</td>           <td><input size=50 type=Text name=rt_assigned_on value=\"$row[rt_assigned_on]\"></td></tr>";
        echo "<tr><td class=a>Scripting Start Date</td>        <td><input size=50 type=Text name=rt_script_start value=\"$row[rt_script_start]\"></td></tr>";
        echo "<tr><td class=a>Scripting End</td>               <td><input size=50 type=Text name=rt_script_end value=\"$row[rt_script_end]\"></td></tr>";
        echo "<tr><td class=a>Review Requested	On</td>      <td><input size=50 type=Text name=rt_review_req value=\"$row[rt_review_req]\"></td></tr>";
        echo "<tr><td class=a>Total Scripts</td>             <td><input size=50 type=Text name=rt_number_scripts value=\"$row[rt_number_scripts]\"></td></tr>";
        echo "<tr><td class=a>Automation First Response</td>                <td><input size=50 type=Text name=rt_auto_init_resp value=\"$row[rt_auto_init_resp]\"></td></tr>";
        echo "<tr><td class=a>Functional Owner First Resp</td>             <td><input size=50 type=Text name=rt_func_init_resp value=\"$row[rt_func_init_resp]\"></td></tr>";
        echo "<tr><td class=a>Automation Review Complete</td>     <td><input size=50 type=Text name=rt_auto_rev_end value=\"$row[rt_auto_rev_end]\"></td></tr>";
        echo "<tr><td class=a>Functional Review Complete</td>          <td><input size=50 type=Text name=rt_func_rev_end value=\"$row[rt_func_rev_end]\"></td></tr>";
        echo "<tr><td class=a>CheckIn Date</td>          <td><input size=50 type=Text name=rt_mod_checkin value=\"$row[rt_mod_checkin]\"></td></tr>";
        echo "<tr><td class=a>Remarks</td>      <td><input size=50 type=Text name=rt_review_remarks value=\"$row[rt_review_remarks]\"></td></tr>";
     }
}
echo "</table>";
echo "<br><center><input type=\"Submit\" name=\"editSubmit\" value=\"Update Review\">";

}

#########################################################################################
#                                                                                       #
# Handle Submit Information and Update the record accordingly                           #
#                                                                                       #
#########################################################################################
if ($_REQUEST[editSubmit]) {
include 'db_connect.php';
include 'common.php';
$sql = "update $tracker_db set
                              rt_mod_name='$_REQUEST[rt_mod_name]',
                              rt_req_name='$_REQUEST[rt_req_name]',
                              rt_func_owner='$_REQUEST[rt_func_owner]',
                              rt_auto_owner='$_REQUEST[rt_auto_owner]',
                              rt_script_owner='$_REQUEST[rt_script_owner]',
                              rt_assigned_on='$_REQUEST[rt_assigned_on]',
                              rt_script_start='$_REQUEST[rt_script_start]',
                              rt_script_end='$_REQUEST[rt_script_end]',
                              rt_review_req='$_REQUEST[rt_review_req]',
                              rt_number_scripts='$_REQUEST[rt_number_scripts]',
                              rt_auto_init_resp='$_REQUEST[rt_auto_init_resp]',
                              rt_func_init_resp='$_REQUEST[rt_func_init_resp]',
                              rt_auto_rev_end='$_REQUEST[rt_auto_rev_end]',
                              rt_func_rev_end='$_REQUEST[rt_func_rev_end]',
                              rt_mod_checkin='$_REQUEST[rt_mod_checkin]',
                              rt_review_remarks='$_REQUEST[rt_review_remarks]' where  rt_mod_no='$_REQUEST[rt_mod_no]'";

       if ($conn->query($sql) === TRUE) {
	 $url=strtok($_SERVER["REQUEST_URI"],'?');
         echo "<b><br><br><center><font size=2> Details for Review \"$_REQUEST[rt_mod_name]\" updated Successfully. <a href=$url>Click here to go back</a>";
       } else {
        echo "Error updating record: " . $conn->error;
       }
$conn->close();
}


#########################################################################################
#                                                                                       #
#  Function to add a new acitivity to database						#       
#                                                                                       #
#########################################################################################
function addReview() {
include 'db_connect.php';
include 'common.php';
        echo "<br><center><b><font size=2>Add Review<br><br>";
        echo "<table border=1 align=center>";

	$sql = "select act_mod_name from $module_db order by act_mod_no asc";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
	echo "<tr><td class=a>Module Name</td>";
	echo "<td><SELECT name=rt_mod_name>";
	     while($row = $result->fetch_assoc()) {
	        printf("<OPTION SELECTED VALUE=\"%s\">%s</OPTION>\n",
	                $row["act_mod_name"],
	                $row["act_mod_name"]);
	    }
	echo "</td></tr></SELECT>";
	} else {
	    echo "0 users found";
	}

printf("
        <tr><td class=b>Request Name</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_req_name\" value=\"\"></td></tr>
        <tr><td class=b>Functional Owner</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_func_owner\" value=\"\"></td></tr>
        <tr><td class=b>Automation Owner</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_auto_owner\" value=\"\"></td></tr>
        <tr><td class=b>Scripted By</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_script_owner\" value=\"\"></td></tr>
        <tr><td class=b>Assigned On</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_assigned_on\" value=\"0000-00-00\"></td></tr>
        <tr><td class=b>Scripting Start Date</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_script_start\" value=\"0000-00-00\"></td></tr>
        <tr><td class=b>Scripting End  Date</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_script_end\" value=\"0000-00-00\"></td></tr>
        <tr><td class=b>Review Requested  On</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_review_req\" value=\"0000-00-00\"></td></tr>
        <tr><td class=b>Total Scripts</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_number_scripts\" value=\"0\"></td></tr>
        <tr><td class=b>Automation First Response</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_auto_init_resp\" value=\"0000-00-00\"></td></tr>
        <tr><td class=b>Functional Owner First Resp</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_func_init_resp\" value=\"0000-00-00\"></td></tr>
        <tr><td class=b>Automation Review Complete</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_auto_rev_end\" value=\"0000-00-00\"></td></tr>
        <tr><td class=b>Functional Review Complete</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_func_rev_end\" value=\"0000-00-00\"></td></tr>
        <tr><td class=b>CheckIn Date</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_mod_checkin\" value=\"0000-00-00\"></td></tr>
        <tr><td class=b>Remarks</td><td><input type=\"Text\" size=35 maxlength=100 name=\"rt_review_remarks\" value=\"\"></td></tr>
        </table>
        <br><input type=\"Submit\" name=\"addReview\" value=\"Add Review\">
       ");

}


#########################################################################################
#                                                                                       #
#  Function to handle Submit information for adding the user                         #
#                                                                                       #
#########################################################################################

if(isset($_POST["addReview"])) {
include 'db_connect.php';
include 'common.php';
$sql = "insert into $tracker_db values ('',
                              '$_REQUEST[rt_mod_name]',
                              '$_REQUEST[rt_req_name]',
                              '$_REQUEST[rt_func_owner]',
                              '$_REQUEST[rt_auto_owner]',
                              '$_REQUEST[rt_script_owner]',
                              '$_REQUEST[rt_assigned_on]',
                              '$_REQUEST[rt_script_start]',
                              '$_REQUEST[rt_script_end]',
                              '$_REQUEST[rt_review_req]',
                              '$_REQUEST[rt_number_scripts]',
                              '$_REQUEST[rt_auto_init_resp]',
                              '$_REQUEST[rt_func_init_resp]',
                              '$_REQUEST[rt_auto_rev_end]',
                              '$_REQUEST[rt_func_rev_end]',
                              '$_REQUEST[rt_mod_checkin]',
                              '$_REQUEST[rt_review_remarks]')";


       if ($conn->query($sql) === TRUE) {
         $url=strtok($_SERVER["REQUEST_URI"],'?');
         echo "<b><br><br><center><font size=2> Review \"$_REQUEST[rt_mod_name]\" added Successfully. <a href=$url>Click here to go back</a>";
       } else {
        echo "Error updating record: " . $conn->error;
       }
$conn->close();

}

#########################################################################################
#                                                                                       #
#  Function to return list of acitivites from database					#
#                                                                                       #
#########################################################################################
function listReview($sortReq) {
include 'db_connect.php';
include 'common.php';

$holidays=array("2015-08-28","2015-09-17","2015-10-02","2015-10-21","2015-11-10","2015-11-11","2015-12-02","2015-12-03","2015-12-04","2015-12-07","2015-12-21","2015-12-22","2015-223","2015-12-24","2015-12-25","2015-12-28","2015-12-29","2015-12-30","2015-12-31","2016-01-01","2016-01-14","2016-01-15","2016-01-26","2016-03-25","2016-04-08","2016-04-14","2016-07-07","2016-08-15","2016-09-05","2016-09-14","2016-10-10","2016-10-28","2016-12-26","2016-12-27","2016-12-28","2016-12-29","2016-12-30");

$sql = "select * from act_rt order by $sortReq";
$result = $conn->query($sql);

$reportCount=0;
if ($result->num_rows > 0) {
echo "<br><br><br><center>";
$url=strtok($_SERVER["REQUEST_URI"],'?');
echo "<b><font size=2><a href=$url>Listing Review Items</a><a href=$PHP_SELF?addReview=yes> [Click here to add New Review] </a></b><br><br>";
echo "<table border=1>\n";
echo "<tr>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_mod_name>Module Name</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_req_name>Request Name</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_func_owner>Functional Owner</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_auto_owner>Automation Owner</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_script_owner>Scripted By</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_assigned_on>Assigned On</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_script_start>Scripting Start</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_script_end>Scripting End</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_review_req>Review Requested</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_number_scripts>Total Scripts</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_auto_init_resp>Auto First Resp</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_func_init_resp>Func First Resp</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_auto_rev_end>Auto Review Comp</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_func_rev_end>Func Review Comp</a></td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_mod_checkin>Checkin-On</a></td>
	<td class=a>Scripting Window</td>
	<td class=a>Script-Review Request</td>
	<td class=a>FirstResponse-Auto</td>
	<td class=a>FirstResponse-Func</td>
	<td class=a>Review Period</td>
	<td class=a>Overall LifeCycle</td>
	<td class=a><a href=$PHP_SELF?sortReq=yes&sort_by=rt_review_remarks>Remarks</td>
	</tr>\n";
     while($row = $result->fetch_assoc()) {

	echo "<tr>";
        echo "<td><a href=$PHP_SELF?editReview=yes&rt_mod_no=$row[rt_mod_no]>$row[rt_mod_name]</a></td>";
        echo "<td>$row[rt_req_name]</td>";
        echo "<td>$row[rt_func_owner]</td>";
        echo "<td>$row[rt_auto_owner]</td>";
        echo "<td>$row[rt_script_owner]</td>";
        if ( $row[rt_assigned_on] == "0000-00-00") {
	  echo "<td class=b>$row[rt_assigned_on]</td>";
        } else {
	  echo "<td>$row[rt_assigned_on]</td>";
	}

        if ( $row[rt_script_start] == "0000-00-00") {
          echo "<td class=b>$row[rt_script_start]</td>";
        } else {
          echo "<td>$row[rt_script_start]</td>";
	}

        if ( $row[rt_script_end] == "0000-00-00") {
          echo "<td class=b>$row[rt_script_end]</td>";
        } else {
          echo "<td>$row[rt_script_end]</td>";
        }


        if ( $row[rt_review_req] == "0000-00-00") {
          echo "<td class=b>$row[rt_review_req]</td>";
        } else {
          echo "<td>$row[rt_review_req]</td>";
        }

        echo "<td align=center>$row[rt_number_scripts]</td>";

        if ( $row[rt_auto_init_resp] == "0000-00-00") {
          echo "<td class=b>$row[rt_auto_init_resp]</td>";
        } else {
          echo "<td>$row[rt_auto_init_resp]</td>";
        }

        if ( $row[rt_func_init_resp] == "0000-00-00") {
          echo "<td class=b>$row[rt_func_init_resp]</td>";
        } else {
          echo "<td>$row[rt_func_init_resp]</td>";
        }

        if ( $row[rt_auto_rev_end] == "0000-00-00") {
          echo "<td class=b>$row[rt_auto_rev_end]</td>";
        } else {
          echo "<td>$row[rt_auto_rev_end]</td>";
        }

        if ( $row[rt_func_rev_end] == "0000-00-00") {
          echo "<td class=b>$row[rt_func_rev_end]</td>";
        } else {
          echo "<td>$row[rt_func_rev_end]</td>";
        }

        if ( $row[rt_mod_checkin] == "0000-00-00") {
          echo "<td class=b>$row[rt_mod_checkin]</td>";
        } else {
          echo "<td>$row[rt_mod_checkin]</td>";
        }

	## Calcuate the Scripting Window ##
    if ( $row[rt_script_start] == "0000-00-00") {
	echo "<td class=fail align=center>0</td>";
	echo "<td class=fail align=center>0</td>";
	echo "<td class=fail align=center>0</td>";
	echo "<td class=fail align=center>0</td>";
	echo "<td class=fail align=center>0</td>";
	echo "<td class=fail align=center>0</td>";
    } else {

	## Calcuate the Scriping Window - Scripting Start to End ##
	if ( $row[rt_script_start] == "0000-00-00" || $row[rt_script_end] == "0000-00-00" ) {
	   echo "<td class=fail align=center>0</td>";
	} else {
	   echo "<td align=center class=$class>";
	   echo getWorkingDays("$row[rt_script_start]","$row[rt_script_end]",$holidays);
	   echo "</td>";
	}

	## Calcuate the Scripted to Review Request - Scripting End to Review Request Start ##
        if ( $row[rt_script_end] == "0000-00-00" && $row[rt_review_req] == "0000-00-00" ) {
           echo "<td class=fail align=center>0</td>";
        } elseif ( $row[rt_script_end] != "0000-00-00" && $row[rt_review_req] == "0000-00-00") {
           echo "<td class=fail align=center>Pending-";
           echo getWorkingDays("$row[rt_script_end]","$today",$holidays);
           echo "</td>";
	} else {
           echo "<td align=center>";
           echo getWorkingDays("$row[rt_script_end]","$row[rt_review_req]",$holidays);
           echo "</td>";
	}

	## Calcuate the First Response  (Automation Owner) ##
        if ($row[rt_review_req] == "0000-00-00" && $row[rt_auto_init_resp] == "0000-00-00") {
           echo "<td align=center class=fail>0</td>";
        } elseif ( $row[rt_review_req] != "0000-00-00" && $row[rt_auto_init_resp] == "0000-00-00") {
           echo "<td align=center class=fail>Pending-"; 
           echo getWorkingDays("$row[rt_review_req]","$today",$holidays);
           echo "</td>";
	} else {
           echo "<td align=center>";
           echo getWorkingDays("$row[rt_review_req]","$row[rt_auto_init_resp]",$holidays);
           echo "</td>";
	}

	## Calcuate the First Response  (Functional Owner) ##
        if ( $row[rt_review_req] == "0000-00-00" && $row[rt_func_init_resp] == "0000-00-00" ) {
           echo "<td align=center class=fail>0</td>";
        } elseif ( $row[rt_review_req] != "0000-00-00" && $row[rt_func_init_resp] == "0000-00-00") {
           echo "<td align=center class=fail>Pending-"; 
           echo getWorkingDays("$row[rt_review_req]","$today",$holidays);
           echo "</td>";
        } else {
           echo "<td align=center>";
           echo getWorkingDays("$row[rt_review_req]","$row[rt_func_init_resp]",$holidays);
           echo "</td>";
	}

	## Calcuate the Review Period, Review-Rework Window ##
        if ( $row[rt_review_req] == "0000-00-00" || $row[rt_mod_checkin] == "0000-00-00" ) {
           echo "<td align=center class=fail>Pending</td>";
        } else {
           echo "<td align=center>";
           echo getWorkingDays("$row[rt_review_req]","$row[rt_mod_checkin]",$holidays);
           echo "</td>";
	}

	## Calcuate the Overall Lifecycle ##
        if ( $row[rt_script_start] == "0000-00-00" || $row[rt_mod_checkin] == "0000-00-00" ) {
           echo "<td align=center class=fail>0</td>";
        } else {
           echo "<td align=center>";
           echo getWorkingDays("$row[rt_script_start]","$row[rt_mod_checkin]",$holidays);
           echo "</td>";
	}
   }

        echo "<td>$row[rt_review_remarks]</td>";
	echo "</tr>";

	$recordCount++;
    }
} else {
    echo "0 Records found";
}
 echo "</tr></table><b><br><center>Total Records : $recordCount";
 $conn->close();
}

#########################################################################################
#                                                                                       #
# Code to check whether Delete View, Modify View or Normal View Needs to be shown       #
#                                                                                       #
#########################################################################################
if ($_GET[editReview] == "yes") {
      editReview("$_GET[rt_mod_no]");
} elseif ($_GET[addReview] == "yes") {
      addReview();
} elseif ($_GET[sortReq] == "yes") {
      listReview("$_GET[sort_by]");
} else {
      listReview("rt_mod_no");
}

?>
</body>
</html>
