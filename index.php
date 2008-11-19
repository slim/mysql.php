<?php
	$db_name = $_GET['dbn']; unset($_GET['dbn']);
	$db_host = $_GET['dbh']; unset($_GET['dbh']);
	$db_user = $_SERVER['PHP_AUTH_USER'];
	$db_password = $_SERVER['PHP_AUTH_PW'];

	if (!@mysql_connect($db_host, $db_user, $db_password) || !@mysql_select_db($db_name)) {
    	Header("WWW-Authenticate: Basic realm=\"$db_name@$db_host\"");
    	Header("HTTP/1.0 401 Unauthorized");
		fatal_error("<b>CONNECTION ERROR</b> check your server permissions"); 
	}
	$query = trim(stripslashes($_GET['q']));
	unset($_GET['q']);
	foreach ($_GET as $key => $val) {
		$query = str_replace('{'. $key .'}', $val, $query); 
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">

table, tr, td, th {
       margin: 0px;
       border-width: 0px;
       border-spacing: 0px;
       border-collapse: collapse;
}
	table tr td {border: solid 1px silver; padding: 10px}
	table tr th {border: solid 1px grey; padding: 10px}
	#error {
		background-color: yellow;
		border: 2px solid red;
		padding: 10px;
		margin: 10px;
	}
</style>
<?php
	$result = mysql_query($query) or fatal_error('<b>SQL ERROR</b> ' . mysql_error()); 

	$rows = array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$rows []= $row;
	}
	if (count($rows) < 1) {
		die("<table><tr><th>Empty</th></tr></table>");
	}
	$columns = array_keys(current($rows));
	$title = sql_comment($query) ? sql_comment($query) : join($columns, ' ');

?>
<title><?php echo $title; ?></title>
</head>
<body>
<?php
	echo "<table><tr>";
	foreach ($columns as $c) {
		echo "<th>$c</th>";
	}
	echo "</tr>";
	foreach ($rows as $r) {
		echo "<tr>";
		foreach ($r as $value) {
			echo "<td><pre>$value</pre></td>";
		}
		echo "</tr>";
	}
	echo "</table>";
?>
</body>
</html>
<?php

function fatal_error($message)
{
	die("<div style='background-color: yellow; border: 2px solid red; padding: 10px; margin: 10px;'>$message</div>");
}

function sql_comment($sql)
{
	if (!preg_match('/\-\-([^\n]*\n)/', $sql, $comments)) {
		return false;
	}
	return trim($comments[1]);
}
