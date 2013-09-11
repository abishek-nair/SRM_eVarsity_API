<html>
<head>
	<title>Awesome Evarsity Scraper</title>
	<style>
		body {
			font-family: 'Verdana', 'Sans-serif';
		}
		.key {
			width: 150px;
			display: inline-block;
			font-weight: bold;
			margin-right: 10px;
			color: #2E8B57;
		}
		ul.details {
			width: 1000px;
			margin: 0 auto;
		}
		ul.details li {
			padding: 7px 20px;
			color: #228B22;
		}
		ul.details li:nth-child(even) {
			background-color: #F0F0F0;
		}
		ul.details li:nth-child(odd) {
			background-color: #F6F6F6;
		}		
		table.attTable {
			width: 95%;
			margin: 0 auto;
			font-family: 'Verdana';
			border-spacing: 0;
		}
		table.attTable th {
			background-color: #000;
			color: #FFF;
			font-size: 18px;
			padding: 10px;
			margin: 0;			
		}
		table.attTable td {
			margin: 0;
			border: none;
			border-bottom: 1px solid #505050;
			padding: 8px;
		}
		table.attTable tr {
			margin: 0;
			text-align: center;
			background-color: #3CB371;
		}
	</style>
</head>
<body>

<?php
	include("scripts/srmerpApi.php");
	set_time_limit(30);

	if(!isset($_GET['uname'], $_GET['pass']) OR empty($_GET['uname']) OR empty($_GET['pass'])) {
		echo "<font color='#DC143C'>Status</font> : <i>Username or Password not set</i>";
		die("");
	}
	else {
		$uname = $_GET['uname'];
		$password = $_GET['pass'];
	}

	$obj = new evarsityAPI(array(
			'uname' => $uname,
			'pass' => $password
		));
	$ret = $obj->evarsityLogin();
	if(isset($ret["ERROR"])) {
		echo "<font color='#DC143C''>Couldn't connect to eVarsity Server</font> - Proabably down for maintainence";
	}

	if($obj->isLoggedIn) {
		echo "<font color='#3CB371'> Status</font> : <i>Logged in </i><br />";
		$stuInfoArray = $obj->fetchStudentInfo();
		?>
 		<h1>Welcome, <font color='#27408B'><?php echo $stuInfoArray['name'];?></font><i> [<font color='#FFA500'><?php echo $stuInfoArray['register_no']; ?></font>] </i></h1>
		Your Details : <br />
		<?php
		echo "<ul class='details'>";
		foreach($stuInfoArray as $key => $temp) {
			echo "<li><span class='key'>".$key." : </span>  ".$temp."</li>";
		}
		echo "</ul>";
		$stuAttArray =  $obj->fetchStudentAttendance();
        ?>
        <br />
        <br />
        <?php
        	$stuPerfArray = $obj->fetchStudentPerformance();
        ?>
		<table class='attTable' style='width: 60%'>
        	<tr>
        		<th> Subject Code </th>
        		<th> Subject Desc </th>
        		<th> Marks / Total </th>        		        
        	</tr>
        	<?php
        		foreach($stuPerfArray as $key => $temp) {
    				echo "<tr>";
    				foreach($temp as $t) {
    						echo "<td>".$t."</td>"; 

    				}
    				echo "</tr>";        			
        		}
        	?>
        </table>
        <br />
        <br />        
        <table class='attTable'>
        	<tr>
        		<th> Code </th>
        		<th> Description </th>
        		<th> Max. Hours </th>
        		<th> Att. Hours </th>
        		<th> Absent Hours </th>
        		<th> Average % </th>
        		<th> OD/ML % </th>
        		<th> Total Percentage </th>
        	</tr>
    		<?php
    			foreach($stuAttArray as $key => $temp) {
    				echo "<tr>";
    				foreach($temp as $t) {
    					if($t == 'TOTAL')
    						echo "<td colspan='2'>".$t."</td>"; 
    					else
    						echo "<td>".$t."</td>"; 

    				}
    				echo "</tr>";
    			}
    		?>
        </table>
        <?php
	}
	else {
		echo "<br />Unable to log in <br />";
	}
?>


</body>
</html>