<?php
require_once("crc/crc.php");
require_once("funct/funct.php");
require_once("funct/force_coil.php");
require_once("objects.php");

/********************************************************************
* @brief Remove 0A/0D if have it. Remove SMS prefix if have it
*/
function MODBUS_NORMALIZE($FRAME)
{
	$FRAME = strtoupper($FRAME);
	$FRAME_OUT = "";
	foreach(explode("\n", $FRAME) as $FRAME_LINE)
	{
		// od prasete
		if( ( $poz = strpos($FRAME_LINE, 'X:')) > 0 ) {
			$poz += 2;
			$FRAME_LINE = substr($FRAME_LINE, $poz, strlen($FRAME_LINE) - $poz);
		}
		
		// strip all spaces
		$FRAME_LINE = str_replace(' ', '', $FRAME_LINE);
		$FRAME_LINE = str_replace(':', '', $FRAME_LINE);
		$FRAME_LINE = str_replace("\r", '', $FRAME_LINE);
		$FRAME_LINE = str_replace("\t", '', $FRAME_LINE);
		$FRAME_LINE = str_replace("0x", '', $FRAME_LINE);
		
		// reamain something
		if( !empty($FRAME_LINE))
			$FRAME_OUT[] = $FRAME_LINE;
	}
	
	$FRAME = implode("\n", $FRAME_OUT);
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
	$disp = $FRAME_OUT['FUNCT'][0];

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
		return modbus_show_packet($FRAME[0], $disp);
	
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
	$crc         = substr_cut($FRAME, -2);
	$crc_compute = CRC_MODBUS($FRAME);
	
	$FRAME_DATI['ID']    = substr_cut($FRAME, 1);
	$FRAME_DATI['FUNCT'] = hexdec(substr_cut($FRAME, 1));	

	$funct_id = $FRAME_DATI['FUNCT'];
	switch( $funct_id )
	{
		case MODBUS_FORCE_COIL:
			$FRAME_DATI['ADDRESS'] = hexdec(substr_cut($FRAME, 2));
			$answer = analyze_force_coil($FRAME, $FRAME_DATI['ADDRESS']);
			break;
				
		case MODBUS_WRITE_REGISTER:
			$FRAME_DATI['ADDRESS'] = hexdec(substr_cut($FRAME, 2));
			$FRAME_DATI['LENGTH']  = hexdec(substr_cut($FRAME, 2));
			if( !empty($FRAME))
				$answer[] = substr_cut($FRAME, 1) .'h - wool';
			break;
			
		case MODBUS_READ_COIL:
		case MODBUS_READ_INPUT:
		case MODBUS_READ_HOLD_REG:
		case MODBUS_READ_INPUT_REG:
		case MODBUS_SERVER_ID:
		case MODBUS_READ_FILE_REC:
		case MODBUS_TUNEL:
			$FRAME_DATI['ADDRESS'] = hexdec(substr_cut($FRAME, 2));
			$FRAME_DATI['LENGTH']  = hexdec(substr_cut($FRAME, 2));
			break;
			
		default:
			if( $funct_id & 0x80 )
				$answer = analyze_error($FRAME);
			break;
	}
	
	if( !empty($FRAME))	
		$answer[] = $FRAME;
	
	if( !empty($answer))	
		$FRAME_DATI['DATA'] = $answer;
	
	$FRAME_DATI['FUNCT'] = modbus_funct_name($funct_id);
	$FRAME_DATI['CRC']   = modbus_CRCCheck($crc_compute, $crc);
	
	return $FRAME_DATI;
}
/*----------------------------------------------------------------------------*/
/* END OF FILE */
