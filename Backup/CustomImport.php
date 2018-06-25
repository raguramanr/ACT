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
                
                $act_test_case_id       = $filesop[0];
                $act_test_priority	= $filesop[1];
		$act_test_module	= $_REQUEST[act_mod_name];
		##Uncheck this for Updating Records
		$sql = "update $test_report_db set act_test_priority='$act_test_priority' where act_test_case_id='$act_test_case_id' and act_test_module='$act_test_module'";

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
<?php
$sql = "select act_mod_name from $module_db order by act_mod_no asc";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
echo "<table border=1>\n";
echo "<tr><td class=a>Module to Import</td>";
echo "<td><SELECT name=act_mod_name>";
     while($row = $result->fetch_assoc()) {
        printf("<OPTION SELECTED VALUE=\"%s\">%s</OPTION>\n",
                $row["act_mod_name"],
                $row["act_mod_name"]);
    }
echo "</td></tr></SELECT>";
} else {
    echo "0 users found";
}

echo "<tr><td class=a>Release ID</td><td><input type=Text name=act_test_release_id></td></tr></table>";

?>
<center><input type="submit" name="submit" value="Submit" onclick="return validateForm()" />
</form>
</body>
</html>
