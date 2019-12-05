<?php
	// write to log
	define("_BBC_PAGE_NAME", substr(":".$MODBUS_FRAME, 0, 30));
	define("_BBCLONE_DIR", "bbclone/");
	define("_BBC_ROOT_PATH", "bbclone/");
	define("COUNTER", _BBCLONE_DIR."mark_page.php");

	if (is_readable(COUNTER))
		include_once(COUNTER);

	// write to log
	file_put_contents("log/packets.txt", $MODBUS_FRAME. PHP_EOL , FILE_APPEND);
	
	// make link for customer
	if( !empty($_REQUEST["MODBUS_FRAME"])) {
		$link = "?MODBUS_FRAME=". $_REQUEST["MODBUS_FRAME"];
		echo("<div align='right'><a href='$link'>Copy link of packet</a></div>");
	} 
	
	echo("<br><br>\n");
	echo("<div style='clear: both'>\n");
	echo("<div style='font-size: 9pt;' align=center>\n");
	echo("designed by <a href='https://trsek.com/curriculum' style='font-size: 12pt; font-weight: bold; color: rgb(73, 85, 120)'>Zdeno Sekerak</a> (c) 2019<br>\n");
	echo("</div>\n");

	require("bbclone/var/access.php");
	$dayvisit = 0;
	foreach ($access['time']['hour'] as $val) $dayvisit += $val;

	echo("<table cellpadding='1' cellspacing='10' style='font-size: 9pt;' align=center>");
	echo("<tr>");
	echo("<td> Generated: ".date('d.m.y, h:i a').", duration: " .stop_meas()."s </td>");
	echo("<td> Number of visitors: <a href='bbclone/index.php'>". $access['stat']['totalvisits']."</a> </td>");
	echo("<td> Days visitors: ". $dayvisit." </td>");
	echo("<td> Counter started: ". date("d.m.y", $access['time']['reset'])." </td>");
	echo("</tr>");
	echo("</table>");

	echo("</div>\n");
	echo("</body>\n");
	echo("</html>\n");
?>
