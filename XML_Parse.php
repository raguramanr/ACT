<body>
<link href="style1.css" rel="stylesheet" type="text/css">

<?php 
#####################################################
function xl_parse() {
$c=0;
$myfile = fopen("Data/tmp.txt", "r") or die("Unable to open file!");
$arr = array(
             "<testsuite name=",
             "</testsuite><testsuite name=",
             "</details><testsuite name=",
             "</details></testsuite><testsuite name=","\" >",
             "<testcase internalid=",
             "<externalid><![CDATA[",
             "<execution_type><![CDATA[",
             "<keywords><keyword name=",
             "\"",
             "name=",
             "\" >",
             "]]></execution_type>",
             "</details>",
             "]]>",
             ">"
         );
$firstMatch=0;
echo "<table border=1><tr>";
while(!feof($myfile)) {
  $line = fgets($myfile);

    if (preg_match("/(testcase|testsuite)/i", $line)) {
      echo "</tr><tr>";
    }

    if (preg_match("/testcase/i", $line)) {
      $firstMatch=0;
    }

    if (preg_match("/(execution_type)/i", $line)) {
      if($firstMatch==0) {
        $firstMatch=1;
        $line = str_replace("</testsuite></testsuite><testsuite name=","","$line");
        $line = str_replace("</testsuite><testsuite","","$line");
        $line = str_replace("<testsuite name=","","$line");
        $line = str_replace("</details><testsuite name=","","$line");
        $line = str_replace("</details></testsuite><testsuite name=","","$line");
        $line = str_replace("\" >","","$line");
        $line = str_replace("<testcase internalid=","","$line");
        $line = str_replace("<externalid><![CDATA[","","$line");
        $line = str_replace("<execution_type><![CDATA[","","$line");
        $line = str_replace("<keywords><keyword name=","","$line");
        $line = str_replace("<keyword name=","","$line");
        $line = str_replace("\"","","$line");
        $line = str_replace("name=","","$line");
        $line = str_replace("\" >","","$line");
        $line = str_replace("]]></execution_type>","","$line");
        $line = str_replace("</details>","","$line");
        $line = str_replace("]]>","","$line");
        $line = str_replace(">","","$line");
        echo "<td>$line</td>"; 
       } else {
     } 
    } else {
        $line = str_replace("</testsuite></testsuite><testsuite name=","","$line");
        $line = str_replace("</testsuite><testsuite","","$line");
        $line = str_replace("<testsuite name=","","$line");
        $line = str_replace("</details><testsuite name=","","$line");
        $line = str_replace("</details></testsuite><testsuite name=","","$line");
        $line = str_replace("\" >","","$line");
        $line = str_replace("<testcase internalid=","","$line");
        $line = str_replace("<externalid><![CDATA[","","$line");
        $line = str_replace("<execution_type><![CDATA[","","$line");
        $line = str_replace("<keywords><keyword name=","","$line");
        $line = str_replace("<keyword name=","","$line");
        $line = str_replace("\"","","$line");
        $line = str_replace("name=","","$line");
        $line = str_replace("\" >","","$line");
        $line = str_replace("]]></execution_type>","","$line");
        $line = str_replace("</details>","","$line");
        $line = str_replace("]]>","","$line");
        $line = str_replace(">","","$line");
        echo "<td>$line</td>"; 
    }

      ##echo "<td>$line</td>";
      #echo "Line is $c -  $line <br>";

    $c++;
}

fclose($myfile);
echo "</tr></table>";
}

#####################################################


$target_dir = "Data/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    $uploadOk = 1;

$fName=basename( $_FILES[fileToUpload][name]);
if ($uploadOk == 0) {
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
       # echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

shell_exec("grep -e \"testsuite name\" -e \"testcase internalid\" -e \"execution_type\" -e \"\"Sanity\"\" -e \"externalid\"  Data/$fName > Data/tmp.txt");
xl_parse();

}


?> 
<br><br><br>
<form action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data">
    Select file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload XML file" name="submit">
</form></body>
</html>

