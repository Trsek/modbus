<?php
define(MODBUS_WRITE, "Write");
define(MODBUS_READ,  "Read");

$example_packet = array(
	MODBUS_WRITE => "F810019C0001020008C49E",
	MODBUS_READ  => "F8030000002851BD",
);


/********************************************************************
* @brief Example is need
*/
function is_example_time()
{
	if( empty($_POST) && empty($_GET))
		return true;
	
	return false;
}

/********************************************************************
* @brief Show links to example
*/
function show_example()
{
	if( !is_example_time())
		return;
	
	global $example_packet;
	$out = "Examples<br>";
	
	foreach ($example_packet as $example_name => $example_packet_list)
	{
		$link = "index.php?MODBUS_FRAME=". $example_packet_list ."";
		$out .= "<a href='$link'>[". $example_name."]</a>&nbsp;";	
	}
	$out .= "<br><br>";
	return $out; 
}

/*----------------------------------------------------------------------------*/
/* END OF FILE */