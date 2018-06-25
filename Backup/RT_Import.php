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
    echo "Importing the Module to module - $_REQUEST[act_mod_name] <br><br>";
        $file = $_FILES['file']['tmp_name'];
        $handle = fopen($file, "r");
        $c = 0;
          while(($filesop = fgetcsv($handle, 5000, ",")) !== false)
          {
                
                $rt_mod_name 		= $filesop[0];
                $rt_req_name 		= $filesop[1];
                $rt_func_owner 		= $filesop[2];
                $rt_auto_owner 		= $filesop[3];
                $rt_script_owner 	= $filesop[4];
                $rt_assigned_on 	= $filesop[5];
                $rt_script_start	= $filesop[6];
                $rt_script_end 		= $filesop[7];
                $rt_review_req 		= $filesop[8];
                $rt_number_scripts 	= $filesop[9];
                $rt_auto_init_resp 	= $filesop[10];
                $rt_func_init_resp 	= $filesop[11];
                $rt_auto_rev_end 	= $filesop[12];
                $rt_func_rev_end 	= $filesop[13];
                $rt_mod_checkin 	= $filesop[14];
                $rt_review_remarks	= $filesop[15];
		##Uncheck this for Updating Records
		$sql = "insert into $tracker_db values ('','$rt_mod_name','$rt_req_name','$rt_func_owner','$rt_auto_owner','$rt_script_owner','$rt_assigned_on','$rt_script_start','$rt_script_end','$rt_review_req','$rt_number_scripts','$rt_auto_init_resp','$rt_func_init_resp','$rt_auto_rev_end','$rt_func_rev_end','$rt_mod_checkin','$rt_review_remarks')";

                // echo "$sql <br>";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $c = $c + 1;
          }

        if($sql) {
                echo "<br><br><b><center>CVS Imported to Database successful. You have inserted ". $c ." records";
        } else {
                echo "<br><br><b><center>Sorry! Inserting values to database failed. Please check the details.";
        }
$conn->close();
}
?>

<br><br><br>
<center><input type="file" name="file" /><br/><br> 
<center><input type="submit" name="submit" value="Submit" onclick="return validateForm()" />
</form>
</body>
</html>
