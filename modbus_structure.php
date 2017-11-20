<?php
require_once("crc/crc.php");
require_once("funct/funct.php");
require_once("objects.php");

/********************************************************************
* @brief Remove 0A/0D if have it. Remove SMS prefix if have it
*/
function MODBUS_NORMALIZE($FRAME)
{
	$FRAME = strtoupper($FRAME);
	
	// strip all spaces
	$FRAME = str_replace(' ', '', $FRAME);
	$FRAME = str_replace(':', '', $FRAME);
	$FRAME = str_replace("\r", '', $FRAME);
	$FRAME = str_replace("\n\n", '\n', $FRAME);
	$FRAME = str_replace("0x", '', $FRAME);
	return $FRAME;
}

/********************************************************************
 * @brief Added space every $len
 */
function add_soft_space($DATI, $len)
{
	$asnwer = "";
	while( strlen($DATI))
	{	
		$answer .= substr($DATI, 0, $len) ." ";
		$DATI = substr($DATI, $len, strlen($DATI));
	}   

	return $answer;
}

/********************************************************************
* @brief Make HTML format from array
* @retval HTML format divide by <br>
*/
function modbus_array_show($value)
{
	if( is_array($value) && count($value)==1)
		$value = $value[0];
	
	if( !is_array($value))
	{
		$space = "";
		while($value[0] == ' ')
		{
			$space .= "&nbsp;";
			$value = substr($value, 1, strlen($value));
		}
		return $space. $value;
	}

	$out = "";
	foreach ($value as $value_line)
	{
		$out .= modbus_array_show($value_line) . "<br>";
	}
	return $out;
}

/********************************************************************
 * @brief Show analyze FRAME
 * @retval HTML table format
 */
function modbus_show_packet($FRAME, &$disp)
{
	$FRAME_OUT = modbus_analyze_frame($FRAME);
	$disp = $FRAME_OUT['FUNCT'];

	$out  = "<table class='table-style-two'>\n";
	foreach ($FRAME_OUT as $name => $value)
	{
		$out .= "<tr>";
		$out .= "<td>". $name ."</td>";
		$out .= "<td>";
		$out .= modbus_array_show($value);
		$out .= "</td>";
		$out .= "</tr>";
	}
	$out .= "</table>\n";

	return $out;
}

/********************************************************************
* @brief Show analyze PACKET
* @retval HTML table format
*/
function modbus_show($FRAME)
{
	$FRAME = explode("\n", $FRAME);
	
	// single line
	if( count($FRAME) <= 1)
		return modbus_show_packet($FRAME[0]);
	
	// multi line
	$first = true;
	foreach ($FRAME as $FRAME_LINE)
	{
		$out_data = modbus_show_packet($FRAME_LINE, $disp);
		$out .= "<li><a href='index.php?MODBUS_FRAME=$FRAME_LINE' title='$disp'>+ $disp</a>";
		$out .= $first? "<ul class='hidden'>": "<ul>";
		$out .= $out_data;
		$out .= "<br></ul></li>";
		$first = false;
	}
	
	return "\n<ul class='menu'>". $out ."</ul>";
}

/********************************************************************
* @brief Check CRC
*/
function modbus_CRCCheck($crc_compute, $crc)
{
	if( $crc_compute == $crc )
		$answ = "$crc - OK";
	else
		$answ = "$crc - bad, correctly $crc_compute";
				
	return $answ;
}

/********************************************************************
* @brief MetaAnalyze frame name
*/
function modbus_analyze_frame(&$FRAME)
{
	$crc_compute = CRC_MODBUS(substr($FRAME, 0, strlen($FRAME)-4));
	
	$FRAME_DATI['ID']      = substr_cut($FRAME, 1);
	$FRAME_DATI['FUNCT']   = hexdec(substr_cut($FRAME, 1));
	$FRAME_DATI['ADDRESS'] = hexdec(substr_cut($FRAME, 2));	
	$FRAME_DATI['LENGTH']  = hexdec(substr_cut($FRAME, 2));	
	$FRAME_DATI['DATA']    = substr_cut($FRAME, (STRLEN($FRAME)-4)/2);
	$FRAME_DATI['CRC']     = modbus_CRCCheck($crc_compute, substr_cut($FRAME, 2));
	
	$FRAME_DATI['FUNCT']   = modbus_funct_name($FRAME_DATI['FUNCT']);
	if( empty($FRAME_DATI['DATA']))
		unset($FRAME_DATI['DATA']);
	
	return $FRAME_DATI;
}
/*----------------------------------------------------------------------------*/
/* END OF FILE */
