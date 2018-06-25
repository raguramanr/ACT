<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script language="JavaScript" src="Chart/JSClass/FusionCharts.js"></script>
</head>
<body>
<?php
echo "<br><br>Test";
include("Chart/Code/PHP/Includes/FusionCharts.php");
echo renderChart("Chart/Charts/FCF_StackedColumn2D.swf", "Data/ACTAllottedVsCompleted-2016-05-20.xml", "", "Assignment", 600, 350);

?>
  
</body>
</html>
