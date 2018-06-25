<html>
<link href="style1.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
function validateForm() {
    var x = document.forms["import"]["file"].value;
    if (x == null || x == "") {
        alert("Filename Blank. Select the CSV file to import");
        return false;
    }
}
</script>

<body bgcolor=#a8a8a8>
<form name="import" method="post" onsubmit="return validateForm()" enctype="multipart/form-data">

<?php
include 'db_connect.php';
include 'common.php';

if(isset($_POST["submit"])) {
  if(isset($_REQUEST["testCase"])) {
    #echo "Check mark status $_REQUEST[testCase]";
        $file = $_FILES['file']['tmp_name'];
        $handle = fopen($file, "r");
        $c = 0;
          while(($filesop = fgetcsv($handle, 5000, ",")) !== false)
          {
                $act_test_module        = $filesop[0];
                $act_test_case_id       = $filesop[1];
                $act_test_regression    = $filesop[2];

                $sql = "update $test_report_db set act_test_regression='$act_test_regression' where act_test_case_id='$act_test_case_id' and act_test_module='$act_test_module'";
                //print $sql;
                  $stmt = $conn->prepare($sql);
                  $stmt->execute();
                 $c = $c + 1;
          }

        if($sql) {
                echo "<br><br><b><center>CVS Imported to Database successful. You have inserted ". $c ." records";
        } else {
                echo "<br><br><b><center>Sorry! Inserting values to database failed. Please check the details.";
        }
  } else { 

        $file = $_FILES['file']['tmp_name'];
        $handle = fopen($file, "r");
	$c = 0;
	  while(($filesop = fgetcsv($handle, 5000, ",")) !== false)
	  {
		$act_mod_name      = $filesop[0];
		$act_plan_start    = $filesop[1];
		$act_plan_end      = $filesop[2];
		$act_act_start     = $filesop[3];
		$act_act_end       = $filesop[4];
		$act_created_by    = $filesop[5];
		$act_release_scope = $filesop[6];
		$act_target        = $filesop[7];
		$act_func_area     = $filesop[8];
		$act_mod_pri       = $filesop[9];
		$act_mod_owner     = $filesop[10];
		$act_mod_reviewer  = $filesop[11];
		$act_auto_owner    = $filesop[12];
		$act_fs_loc        = $filesop[13];
		$act_tp_loc        = $filesop[14];   
		$act_repo_path     = $filesop[15];
		$act_topo_det      = $filesop[16];
		$act_ext_dep       = $filesop[17];
		$act_force_hold    = $filesop[18];
		$act_is_visible    = $filesop[19];
		$act_remarks       = $filesop[20];
		
		$sql = "insert into $module_db values ('',
							'$act_mod_name',
							'$act_plan_start',
							'$act_plan_end',
							'$act_act_start',
							'$act_act_end',
							'$act_created_by',
							'$act_release_scope',
							'$act_target',
							'$act_func_area',
							'$act_mod_pri',
							'$act_mod_owner',
							'$act_mod_reviewer',
							'$act_auto_owner',
							'$act_fs_loc',  
							'$act_tp_loc',
							'$act_repo_path',
							'$act_topo_det',
							'$act_ext_dep',
							'$act_force_hold',
							'$act_is_visible',
							'$act_remarks')";
		  $stmt = $conn->prepare($sql);
		 // $stmt->execute();
		 $c = $c + 1;
	  } 

	if($sql) {
		echo "<br><br><b><center>CVS Imported to Database successful. You have inserted ". $c ." records";
	} else {
		echo "<br><br><b><center>Sorry! Inserting values to database failed. Please check the details.";
        }
     }
}
$conn->close();
?>

<br><br><br>
<center><input type="file" name="file" /><br/><br> 
<center><font size=2><b>Check this to import Testcase <input type="checkbox" name="testCase" value="yes" /><br><br>
<center><input type="submit" name="submit" value="Submit" onclick="return validateForm()" />
</form>
</body>
</html>
