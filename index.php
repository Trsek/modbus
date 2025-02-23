<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE);

	require("time_meas.php");
	require("example.php");
	require("modbus_structure.php");
	
	$debug_cesta = "";
	// some example for debug mode
	if(IsSet($_REQUEST["XDEBUG_SESSION_START"]))
	{
		$debug_cesta = "/modbus/";
		$_REQUEST["MODBUS_FRAME"] = "TX: 01-10-00-78-00-04-08-00-00-00-00-00-00-00-00-16-DA";
		$_REQUEST["MODBUS_FRAME"] = "F810019C0001020008C49E";
		$_REQUEST["MODBUS_FRAME"] = "F8050010FF009996";
		$_REQUEST["MODBUS_FRAME"] = "F885049362";
		$_REQUEST["MODBUS_FRAME"] = "F81001280003063611140317037C50

F8030000002851BD";
		$_REQUEST["MODBUS_FRAME"] = "12:35:43.78 TX: F8 10 00 9F 00 02 04 00 00 00 01 56 DC 
12:35:48.79 RX: 
14:02:05.89 TX: F8 03 01 2B 00 02 A1 96 
14:02:10.89 RX: 
14:02:47.40 TX: F8 03 01 2B 00 02 A1 96 
14:02:47.50 RX: F8 03 04 03 04 05 00 D1 E9 
14:03:22.95 TX: F8 10 01 2B 00 01 02 9E 39 76 AD 
14:03:23.00 RX: F8 90 04 9D F2 
14:17:41.07 TX: F8 10 01 2B 00 01 02 0A 4A 59 88 
14:17:41.12 RX: F8 90 04 9D F2 
14:18:07.07 TX: F8 10 01 2B 00 02 04 00 04 05 05 12 99 
14:18:10.02 RX: F8 10 01 2B 00 02 24 55";
		$_REQUEST["MODBUS_FRAME"] = "F0050011 FF00 C91E";
		$_REQUEST["MODBUS_FRAME"] = "Read: 00 01 00 00 00 06 01 03 10 1E 00 01 
Write: 00 01 00 00 00 05 01 03 02 07 E3 ";
		$_REQUEST["MODBUS_FRAME"] = "0001000000060103101E0001";
		$_REQUEST["MODBUS_FRAME"] = "000C00000008010F10010002";
		$_REQUEST["MODBUS_FRAME"] = "000C00000008010F100100020103";
		$_REQUEST["MODBUS_FRAME"] = "00010000000401010100";
		$_REQUEST["MODBUS_FRAME"] = "F8531F0086070000501F0000521F0065541F000000000000C21F0030C31F0000000000235D";
		//$_REQUEST["TCP"] = "1";
		//$_REQUEST["Strict"] = "1";
	}

	if( isset($_REQUEST["FRAME"]))
		$_REQUEST["MODBUS_FRAME"] = $_REQUEST["FRAME"];

	$strict = !empty($_REQUEST["Strict"]);
	$tcp = !empty($_REQUEST["TCP"]);
	$MODBUS_FRAME = MODBUS_NORMALIZE($_REQUEST["MODBUS_FRAME"], $strict);
	
	// pre json
	if( isset($_REQUEST["JSON"])) {
		$TCP = !empty($_REQUEST["TCP"]);
		$DIR = !empty($_REQUEST["DIR"]);
		echo json_encode( mb_convert_encoding( modbus_analyze_frame($MODBUS_FRAME, $TCP, $DIR), 'UTF-8', 'UTF-8'));
		return;
	}

	// pre app
	if( isset($_REQUEST["FLAT"])) {
		echo "<head><meta HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'><link rel='stylesheet' href='modbus_flat.css'></head>";
		echo modbus_show($MODBUS_FRAME, $tcp);
		return;
	}
?>

<!doctype HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>
	<meta name='author' lang='en' content='Zdeno Sekerák, www.trsek.com'>
	<link rel='shortcut icon' href='favicon.ico'>
	<link rel='stylesheet' href='<?php echo $debug_cesta?>modbus.css'>
	<title>MODBUS online</title>
	<script type="text/javascript" src="<?php echo $debug_cesta?>togglemenu.js"></script>
</head>
<body>
<h1>MODBUS Encrypt online</h1>

<?php echo show_example();?>
<form action='index.php' method='post' ENCTYPE='multipart/form-data' class='form-style-two'>
	Packet (hex format)<br>
	<textarea name='MODBUS_FRAME' rows="9" cols="63"><?php echo $MODBUS_FRAME;?></textarea><br>
	Strict Mode <input type='checkbox' name='Strict' <?php if($strict) echo 'checked=on';?>'>
	TCP <input type='checkbox' name='TCP' <?php if($tcp) echo 'checked=on';?>'><br><br>
	<input type='submit' name='analyze' value='analyze'><br>
</form>
	
<br>Frame<br>
<?php
//var_dump($GLOBALS);
echo modbus_show($MODBUS_FRAME, $tcp);
?>

<?php
	require("paticka.php");
?>