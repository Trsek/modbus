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
  		$_REQUEST["MODBUS_FRAME"] = "F81001280003063611140317037C50
  		F8030000002851BD";
	}

	$MODBUS_FRAME = MODBUS_NORMALIZE($_REQUEST["MODBUS_FRAME"]);
	
	// pre app
	if( isset($_REQUEST["FLAT"])) {
		echo "<head><link rel='stylesheet' href='modbus_flat.css'></head>";
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