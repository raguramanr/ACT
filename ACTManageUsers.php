<html>
<body>
<link href="style1.css" rel="stylesheet" type="text/css">
<form method="post" action="<?php echo $PHP_SELF?>">
<?php
include 'common.php';

#########################################################################################
#                                                                                       #
#  Function to return list of users from database					#
#                                                                                       #
#########################################################################################
function listUser($flag) {
include 'db_connect.php';
include 'common.php';

$sql = "SELECT act_user_name, act_active, act_user_priv FROM $user_db";
$result = $conn->query($sql);

$userCount=0;
if ($result->num_rows > 0) {
echo "<br><br><br><center>";
echo "<b><font size=2>Manage Users  <a href=$PHP_SELF?addUser=yes> [Click here to add New User] </a></b><br><br>";
echo "<table border=1>\n";
echo "<tr><td class=a>User Name</td>
	<td class=a>Active</td>
	<td class=a>Previlege</td>
	<td class=a>Remove</td>
	<td class=a>Modify</td>
	<td class=a>Reset Password</td>
	</tr>\n";
     while($row = $result->fetch_assoc()) {
        printf("<tr><td>%s</td>
                <td align=center>%s</td>
                <td align=center>%s</td>
                <td><font color=blue><a href=\"%s?user_name=%s&delUser=yes\">Remove</a></font></td>
                <td><font color=blue><a href=\"%s?user_name=%s&modUser=yes\">Modify</a></font></td>
                <td><font color=blue><a href=\"%s?user_name=%s&act_user_priv=%s&resetPass=yes\">Reset Pass</a></font></td>
		</tr>\n",
                $row["act_user_name"],
                $row["act_active"],
                $row["act_user_priv"],
                $PHP_SELF,$row["act_user_name"],
                $PHP_SELF,$row["act_user_name"],
                $PHP_SELF,$row["act_user_name"],$row["act_user_priv"]);
	$usercount++;
    }
} else {
    echo "0 users found";
}
 echo "</tr></table><b><br><center>Total Users : $usercount";
 $conn->close();
}

#########################################################################################
#                                                                                       #
#  Function to remove the user from database						#
#                                                                                       #
#########################################################################################
function delUser($act_user_name) {
include 'db_connect.php';
include 'common.php';

//echo "Request to delete $act_user_name from $user_db";
$sql = "DELETE FROM $user_db WHERE act_user_name='$act_user_name'";
if (mysqli_query($conn, $sql)) {
    //echo "Record deleted successfully";
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
$cmd = "/usr/bin/htpasswd -D $admin_pwd_path $_GET[user_name]";
$cmd1 = "/usr/bin/htpasswd -D $user_pwd_path $_GET[user_name]";
system("$cmd", $ret);
system("$cmd1", $ret);

if($ret == "0") {
   //echo "User deletion Success..!!";
} else {
  echo "<center><b>Login Could not be set. Contact system Administrator !!</b></center>";
}
 echo "<br><br><b><center>User $act_user_name deleted Successfully!<p></b>";
}

#########################################################################################
#                                                                                       #
#  Function to modify the user from database                                            #
#                                                                                       #
#########################################################################################
function modUser($act_user_name) {
include 'db_connect.php';
include 'common.php';

$sql = "SELECT * FROM $user_db where act_user_name='$act_user_name'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
        echo "<br><center><b><font size=2>Modify User - $act_user_name</b><br>";
        echo "<table border=1>\n";
          while($row = $result->fetch_assoc()) {
          printf("
                  <tr><td class=b>Username</td><td><input type=\"Text\" name=\"user_name\" value=%s readonly></td><br>
                  </tr><tr><td class=b>Previlege</td><td><input type=Text name=user_priv value=%s></td><br>
                  </tr><tr><td class=b>Activation</td><td><input type=Text name=activity_flag value=%s></td>
                  </tr></table>
                  <br><center><input type=\"Submit\" name=\"modSubmit\" value=\"Update Information\"></form>",
    $row["act_user_name"],
    $row["act_user_priv"],
    $row["act_active"]);
    }
} else {
    echo "<br><br><b><center>0 users found<p></b>";
}
$conn->close();
exit();
}

#########################################################################################
#                                                                                       #
#  Function to handle Submit information for modifying the user				#
#                                                                                       #
#########################################################################################

include 'db_connect.php';
include 'common.php';
if(isset($_POST["modSubmit"])) {
$sql = "SELECT act_user_priv FROM $user_db where act_user_name='$_POST[user_name]'";
$result = $conn->query($sql);
$myrow=mysqli_fetch_assoc($result);

if ( $myrow[act_user_priv] == "A" && $_REQUEST[user_priv] == "U" ) {
        // echo "Removing Admin Prevlieges for the User";
        $cmd = "/usr/bin/htpasswd -D $admin_pwd_path $_REQUEST[user_name]";
        system("$cmd", $ret);
        if($ret == "0") {
         } else {
           echo "<center><b>Login Could not be set. Contact system Administrator !!</b></center>";
        }
} elseif ( $myrow[act_user_priv] == "U" &&  $_REQUEST[user_priv] == "A" ) {
      // echo "Enabling Admin Previlege along with User Previlege";
      $cmd = "/usr/bin/htpasswd -b $admin_pwd_path $_REQUEST[user_name] $_REQUEST[user_name]";
      system("$cmd", $ret);
      if($ret == "0") {
        } else {
          echo "<center><b>Login Could not be set. Contact system Administrator !!</b></center>";
      }
} else {
echo "<br><BR><b><center>No updation request seen. Just updating the SQL record";
}

$sql = "update $user_db set act_user_priv='$_REQUEST[user_priv]',act_active='$_REQUEST[activity_flag]' WHERE act_user_name='$_REQUEST[user_name]'";
if ($conn->query($sql) === TRUE) {
      echo "<br><BR><b><center>User $_REQUEST[user_name] Modified Successfully!<p></b>";
} else {
      echo "Error updating record: " . $conn->error;
}

}

#########################################################################################
#                                                                                       #
#  Function to handle password reset							#
#                                                                                       #
#########################################################################################
function resetPass($act_user_name, $act_user_priv) {

printf("
	<br><br><center><b>Reset User Password
	<table border=1 align=center>
	<tr><td class=b>Username</td><td><input type=\"Text\" disabled maxlength=20 name=\"user_name\" value=\"$act_user_name\"></td></tr>
	<tr><td class=b>Privilege</td><td><input type=\"Text\" disabled maxlength=20 name=\"act_user_priv\" value=\"$act_user_priv\"></td></tr>
	<tr><td class=b>New Password</td><td><input type=\"password\" maxlength=20 name=\"newpwd\" value=\"\"></td></tr>
	</table><br>
	<br><input type=\"Submit\" name=\"resetSubmit\" value=\"Change Password\">
       ");
}


#########################################################################################
#                                                                                       #
#  Function to handle Submit information for modifying the user                         #
#                                                                                       #
#########################################################################################

if(isset($_POST["resetSubmit"])) {
include 'db_connect.php';
include 'common.php';
$loop1 = "yes";
if($loop1) {
$flag="no";

if ( $_REQUEST[newpwd] != "" ) {
        $flag = "yes";
     } else {
        $flag = "error";
        echo "<br><b><center>Password field should not be Empty...!!</center></b>";
    }
}

if ($flag == "yes" ) {
   // echo "<br>Final Flag Value is $flag - User Priv is $_REQUEST[act_user_priv] - User is $_REQUEST[user_name] - Password is $_REQUEST[newpwd] <br>";
   if($_REQUEST["act_user_priv"] == "U") {
      $cmd = "/usr/bin/htpasswd -b $user_pwd_path $_REQUEST[user_name] $_REQUEST[newpwd]";
      system("$cmd", $ret);
   } else {
      $cmd = "/usr/bin/htpasswd -b $user_pwd_path $_REQUEST[user_name] $_REQUEST[newpwd]";
      system("$cmd", $ret);
      $cmd1 = "/usr/bin/htpasswd -b $admin_pwd_path $_REQUEST[user_name] $_REQUEST[newpwd]";
      system("$cmd1", $ret);
   }
   if($ret == "0") {
        echo "<br><br><center><font size=2><b>All authentication tokens updated successfully</b></center>";
   } else {
      echo "<center><b>Password Could not be Changed. Contact system Administrator !!</b></center>";
      exit;
   }
  } else {
 echo "<br><b><center> Password could not be changed  due to above reason ! <br>";
  }
}

#########################################################################################
#                                                                                       #
#  Function to add a new user								#
#                                                                                       #
#########################################################################################
function addUser() {

printf("
        <br><center><b><font size=2>Add User<br>
        <table border=1 align=center>
	<tr><td class=b>User name</td><td><input type=\"Text\" maxlength=20 name=\"username\" value=\"\"></td></tr>
        <tr><td class=b>Previlege</td><td><input type=\"Text\" maxlength=15 name=\"previlege\" value=\"U\"></td></tr>
        <tr><td class=b>Activate</td><td><input type=\"Text\" maxlength= 1 name=\"activate\" value=\"Y\"></td></tr>
        </table>
        <br><input type=\"Submit\" name=\"addSubmit\" value=\"Add User\">
       ");
}

#########################################################################################
#                                                                                       #
#  Function to handle Submit information for adding the user                            #
#                                                                                       #
#########################################################################################

if(isset($_POST["addSubmit"])) {
include 'db_connect.php';
include 'common.php';

//echo "Username is - $_REQUEST[username]  - Previlege is $_REQUEST[previlege] - Activation Flag is $_REQUEST[activate] <br>";
if($_REQUEST[username] == "" || $_REQUEST[previlege] == "" || $_REQUEST[activate] == "") {
 	echo "<br><br><b><center><font size=2 color=red>Error! Fields cannot be left blank <br></font>";
 } else {
	$sql = "insert into $user_db values ('','$_REQUEST[username]','$_REQUEST[previlege]','$_REQUEST[activate]')";
	$stmt = $conn->prepare($sql);
	$stmt->	execute();
	echo "<br><b><center>User Added Successfully!<p></b>";
	if($_REQUEST[previlege] == "A") {
		$cmd = "/usr/bin/htpasswd -b $admin_pwd_path $_REQUEST[username] $_REQUEST[username]";
		$cmd1 = "/usr/bin/htpasswd -b $user_pwd_path $_REQUEST[username] $_REQUEST[username]";
		system("$cmd", $ret);
		system("$cmd1", $ret);
		if($ret == "0") {
		} else {
   		      echo "<center><b>Login Could not be set. Contact system Administrator !!</b></center>";
		}
	} else {
		$cmd = "/usr/bin/htpasswd -b $user_pwd_path $_REQUEST[username] $_REQUEST[username]";
		system("$cmd", $ret);
		if($ret == "0") {
	        } else {
		      echo "<center><b>Login Could not be set. Contact system Administrator !!</b></center>";
		}
	}
 }
}

#########################################################################################
#                                                                                       #
# Code to check whether Delete View, Modify View or Normal View Needs to be shown       #
#                                                                                       #
#########################################################################################
if ($_GET[listUser] == "yes") {
      listUser("list");
} elseif ($_GET[addUser] == "yes") {
      addUser();
} elseif ($_GET[delUser] == "yes") {
      delUser("$_GET[user_name]");
} elseif ($_GET[modUser] == "yes") {
      modUser("$_GET[user_name]");
      listUser("modUser");
} elseif ($_GET[resetPass] == "yes") {
      resetPass("$_GET[user_name]","$_GET[act_user_priv]");
} else {
      listUser("list");
}

?>
</body>
</html>
