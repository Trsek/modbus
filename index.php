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
  		$_REQUEST["MODBUS_FRAME"] = "F8 53 0C 00 86 6C 00 47 35 D5 26 03 18 0C 9B 1C 96 2D ";
	}

	$MODBUS_FRAME = MODBUS_NORMALIZE($_REQUEST["MODBUS_FRAME"]);
	
	// pre app
	if( isset($_REQUEST["FLAT"])) {
		echo "<head><meta HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'><link rel='stylesheet' href='modbus_flat.css'></head>";
		echo modbus_show($MODBUS_FRAME);
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
	<input type='submit' name='analyze' value='analyze'><br>
</form>
	
<br>Frame<br>
<?php
//var_dump($GLOBALS);
echo modbus_show($MODBUS_FRAME);
?>

<?php
	require("paticka.php");
?>