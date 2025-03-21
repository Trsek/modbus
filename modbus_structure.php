<?php
require_once("crc/crc.php");
require_once("funct/funct.php");
require_once("funct/force_coil.php");
require_once("objects.php");

/********************************************************************
 * @brief Is time format
 */
function isValidDateTimeString($str_dt)
{
    $str_dateformat = 'H:i:s';
    $str_timezone = date_default_timezone_get();
    
    $date = DateTime::createFromFormat($str_dateformat, $str_dt, new DateTimeZone($str_timezone));
    $laste = DateTime::getLastErrors();
    return $date && $laste["warning_count"] == 0 && $laste["error_count"] == 0;
}

/********************************************************************
* @brief Remove noncorrect chars.
*/
function MODBUS_NORMALIZE($FRAME, $strict)
{
	$FRAME = strtoupper($FRAME);
	$FRAME_OUT = [];
	foreach(explode("\n", $FRAME) as $FRAME_LINE)
	{
	    // od prasete
		if( ( $poz = strpos($FRAME_LINE, 'X:')) > 0 ) {
			$poz += 2;
			$FRAME_LINE = substr($FRAME_LINE, $poz, strlen($FRAME_LINE) - $poz);
		}
		
		// od noveho jadra Telves
		if( ( $poz = strpos($FRAME_LINE, 'W:')) > 0 ) {
		    $poz += 2;
		    $FRAME_LINE = substr($FRAME_LINE, $poz, strlen($FRAME_LINE) - $poz);
		}
		if( ( $poz = strpos($FRAME_LINE, 'R:')) > 0 ) {
		    $poz += 2;
		    $FRAME_LINE = substr($FRAME_LINE, $poz, strlen($FRAME_LINE) - $poz);
		}
		
		// s casovou znackou od telvesu
		if( isValidDateTimeString( substr($FRAME_LINE, 1, 8)))
		    $FRAME_LINE = substr($FRAME_LINE, 9);
		    
		// je prisny rezim, kde zacina 0x
		if( $strict && ($poz = strpos($FRAME_LINE, '0X')) > 0)
		    $FRAME_LINE = substr($FRAME_LINE, $poz, strlen($FRAME_LINE) - $poz);
		
		// strip all spaces
		$FRAME_LINE = str_replace("Read:", '', $FRAME_LINE);
		$FRAME_LINE = str_replace("READ:", '', $FRAME_LINE);
		$FRAME_LINE = str_replace("Write:", '', $FRAME_LINE);
		$FRAME_LINE = str_replace("WRITE:", '', $FRAME_LINE);
		$FRAME_LINE = str_replace(' ', '', $FRAME_LINE);
		$FRAME_LINE = str_replace(':', '', $FRAME_LINE);
		$FRAME_LINE = str_replace('.', '', $FRAME_LINE);
		$FRAME_LINE = str_replace('-', '', $FRAME_LINE);
		$FRAME_LINE = str_replace("\r", '', $FRAME_LINE);
		$FRAME_LINE = str_replace("\t", '', $FRAME_LINE);
		$FRAME_LINE = str_replace("0x", '', $FRAME_LINE);
		$FRAME_LINE = str_replace("0X", '', $FRAME_LINE);
		
		// je prisny rezim, len pakety obsahujuce hex znaky
		if( $strict && !ctype_xdigit($FRAME_LINE))
		    $FRAME_LINE = "";

		// reamain something
		if( !empty($FRAME_LINE))
			$FRAME_OUT[] = $FRAME_LINE;
	}
	
	if( is_array($FRAME_OUT))
		$FRAME = implode("\n", $FRAME_OUT);
	
	return $FRAME;
}

/********************************************************************
 * @brief Added space every $len
 */
function add_soft_space($DATI, $len)
{
	$answer = "";
	while( strlen($DATI))
	{	
		$answer .= substr($DATI, 0, $len) ." ";
		$DATI = substr($DATI, $len, strlen($DATI));
	}   

	return $answer;
}

/********************************************************************
 * @brief Is direct to device?
 */
function ElgasToDevice($type)
{
    if( !is_numeric($type))
        $type = hexdec(substr($type, 0, 2));
        
    if(( $type == 0x84 )
    || ( $type == 0x85 ))
        return true;
            
    return false;
}

/********************************************************************
 * @brief Make directory of packet
 */
function show_dir($to_device)
{
    $smer = "";
    if( $to_device === true ) $smer = ">> ";
    if( $to_device === false ) $smer = "<< ";
    return $smer;
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
function modbus_show_packet($FRAME, $tcp, &$disp)
{
	$to_device = false;
	$FRAME_OUT = modbus_analyze_frame($FRAME, $tcp, $to_device);
	$disp = modbusDisp($FRAME_OUT, $to_device, $disp);

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
function modbus_show($FRAME, $tcp)
{
	$FRAME = explode("\n", $FRAME);
	$disp = "";
	
	// single line
	if( count($FRAME) <= 1)
		return modbus_show_packet($FRAME[0], $tcp, $disp);
	
	// multi line
	$first = true;
	foreach ($FRAME as $FRAME_LINE)
	{
		$out_data = modbus_show_packet($FRAME_LINE, $tcp, $disp);
		$out .= "<li><a href='index.php?MODBUS_FRAME=$FRAME_LINE&amp;TCP=$tcp' title='$disp'>+ $disp</a>";
		$out .= $first? "<ul class='hidden'>": "<ul>";
		$out .= $out_data;
		$out .= "<br></ul></li>";
		$first = false;
	}
	
	return "\n<ul class='menu'>". $out ."</ul>";
}

/********************************************************************
 * @brief Show disp information in short view
 */
function modbusDisp($FRAME_OUT, $to_device)
{
	global $funct_code;

	$answer = show_dir($to_device). $FRAME_OUT['FUNCT'][0];

	if( isset($FRAME_OUT['TransID']))
		$answer .= ' '. ltrim(substr($FRAME_OUT['TransID'], 0, 4), '0'). 'h ';

	if( isset($FRAME_OUT['ADDRESS']))
		$answer .= ' ('. substr($FRAME_OUT['ADDRESS'],0,5) .')';

	if(( strpos($FRAME_OUT['CRC'], 'OK') == false ) && isset($FRAME_OUT['CRC']))
		$answer .= ' (bad CRC)';

	if(( strpos($FRAME_OUT['FUNCT'][0], $funct_code[MODBUS_TUNEL][0]) > 0) && isset($FRAME_OUT['DATA'][0]['GROUP2']))
	{
		$answer .= ' ('. $FRAME_OUT['DATA'][0]['GROUP2'] .')';
		if (isset($FRAME_OUT['DATA'][0]['FUNCT2'][0]))
			$answer .= ' -> '. $FRAME_OUT['DATA'][0]['FUNCT2'][0];
	}

	if( isset($FRAME_OUT['DATA'][0]['GROUP']))
		$answer .= ' -> '. $FRAME_OUT['DATA'][0]['GROUP'];

	return $answer;
}

/********************************************************************
* @brief Check CRC
*/
function modbus_CRCCheck($crc_compute, $crc)
{
	if( $crc_compute === $crc )
		$answ = $crc .'h - OK';
	else
		$answ = $crc .'h - bad, correctly '. $crc_compute .'h';
				
	return $answ;
}

/********************************************************************
 * @brief Mozne formaty cisla
 */
function MODBUS_POSSIBLE($data)
{
    $len = strlen($data)/2;
    $answer = [];
    $answer[] = $data .'h - Raw data';
    
    switch ($len)
    {
        case 1: $answer[] = ' - byte = '. hexdec($data); break;
        case 2: $answer[] = ' - int  = '. hexdec($data); break;
        case 4: $answer[] = ' - ulong = '. hexdec($data); 
                $answer[] = ' - float = '. hexFloat(rotOrder($data,4));
                $answer[] = ' - dt3 = '. modbus_actTime($data);
                break;
        case 8: $answer[] = ' - dlong = '. hexdec($data);
                $answer[] = ' - double = '. hexDouble(rotOrder($data,8));
                break;
    }
    
    $answer[] = ' - str = '. htmlspecialchars(hexToStr($data), ENT_COMPAT,'ISO-8859-1', true);
    return $answer;
}

/********************************************************************
 * @brief Get offset of register, coil, input
 */
function GetOffset($funct_id)
{
    switch ($funct_id)
    {
        case MODBUS_READ_COIL:
        case MODBUS_FORCE_COIL:
            return 1;
        case MODBUS_READ_INPUT:
            return 10001;
        case MODBUS_READ_INPUT_REG:
            return 30001;
        case MODBUS_READ_HOLD_REG:
        case MODBUS_WRITE_REGISTER:
        case MODBUS_06:
        case MODBUS_22:
        case MODBUS_23:
        case MODBUS_24:
            return 40001;
    }
    return 1;
}

/********************************************************************
 * @brief Tunel obsahuje rychlost, port, ..
 */
function modbus_tunel_param(&$DATI)
{
    $answer = [];

    $protokol = hexdec(substr_cut($DATI, 2));
    $answer['PROTOCOL'] = "MODBUS";
    if (($protokol==2)
      ||($protokol==7))
        $answer['PROTOCOL'] = "ELGAS";

    $answer['PORT'] = hexdec(substr_cut($DATI, 1));
    $answer['SPEED'] = get_speed($DATI);
    
    return $answer;
}

/********************************************************************
* @brief MetaAnalyze frame name
*/
function modbus_analyze_frame(&$FRAME, $tcp, &$to_device)
{
	if($tcp)
	{
		$FRAME_DATI = [];
		$FRAME_DATI['TransID']  = substr_cut($FRAME, 2) .'h - TransID';
		$FRAME_DATI['Protocol'] = substr_cut($FRAME, 2) .'h - Protocol';
		$FRAME_DATI['TCPLen']   = substr_cut($FRAME, 2) .'h - Length';
		$FRAME_DATI['UNIT']     = substr_cut($FRAME, 1) .'h - Unit';
		$FRAME_DATI['FUNCT']    = hexdec(substr_cut($FRAME, 1));
	}
	else 
	{
		$crc         = substr_cut($FRAME, -2);
		$crc_compute = CRC_MODBUS($FRAME);

		$FRAME_DATI = [];
		$FRAME_DATI['ID']    = substr_cut($FRAME, 1) .'h - Device address';
		$FRAME_DATI['FUNCT'] = hexdec(substr_cut($FRAME, 1));
	}

	$to_device = 'none';
	$funct_id = $FRAME_DATI['FUNCT'];
	switch( $funct_id )
	{
		case MODBUS_FORCE_COIL:
		    $address = substr_cut($FRAME, 2);
		    $FRAME_DATI['ADDRESS'] = $address .'h';
		    $answer = analyze_force_coil($FRAME, $address);
			break;

		case MODBUS_FORCE_MULTI_COILS:
		    $address = substr_cut($FRAME, 2);
		    $count = substr_cut($FRAME, 2);
		    $FRAME_DATI['ADDRESS'] = $address .'h';
		    $FRAME_DATI['COUNT'] = $count .'h';
		    $answer = analyze_force_multi_coils($FRAME, $address, $count);
		    break;
		    
		case MODBUS_WRITE_REGISTER:
		    if( strlen($FRAME) != 8 )
		    {
		        $to_device = true;
		        $FRAME_DATI['ADDRESS'] = substr_cut($FRAME, 2) .'h';
		        $FRAME_DATI['LENGTH']  = hexdec(substr_cut($FRAME, 2)) .' word';
		        if( !empty($FRAME))
		            $answer[] = substr_cut($FRAME, 1) .'h - wool';
		    }
		    else
		    {
		        $to_device = false;
		        $FRAME_DATI['ADDRESS'] = substr_cut($FRAME, 2) .'h';
		        $FRAME_DATI['LENGTH']  = hexdec(substr_cut($FRAME, 2)) .' word';
		    }
			break;
			
		case MODBUS_READ_COIL:
		case MODBUS_READ_INPUT:
		case MODBUS_READ_HOLD_REG:
		case MODBUS_READ_INPUT_REG:
		case MODBUS_SERVER_ID:
		case MODBUS_READ_FILE_REC:
		    if( strlen($FRAME) == 8 )
		    {
		        $to_device = true;
		        $FRAME_DATI['ADDRESS'] = substr_cut($FRAME, 2) .'h';
		        $FRAME_DATI['LENGTH']  = hexdec(substr_cut($FRAME, 2)) .' word';
		    }
		    else
		    {
		        $to_device = false;
		        $FRAME_DATI['LENGTH']  = hexdec(substr_cut($FRAME, 1)) .' byte';
		        if ($funct_id == MODBUS_READ_COIL)
		            $answer = analyze_read_coils($FRAME);
		    }
		    break;

		case MODBUS_TUNEL:
			$FRAME_DATI['LENGTH'] = hexdec(rotOrder(substr_cut($FRAME, 2),2));
			$type  = hexdec( substr_cut($FRAME, 1));
			$group = hexdec( substr_cut($FRAME, 1));
			$to_device = ElgasToDevice($type);
			$answer[] = json_decode( file_get_contents('http://'. $_SERVER['HTTP_HOST']. '/elgas2/index.php?JSON&ELGAS_FRAME='. $FRAME. '&GROUP='. $group. '&TYPE='. $type. '&TCP='. $tcp), true);
			unset($FRAME);
			break;

		default:
			if( $funct_id & 0x80 )
				$answer = analyze_error($FRAME);
			break;
	}
	
	if( !empty($FRAME_DATI['ADDRESS']))
	    $FRAME_DATI['ADDRESS'] .= " -> ". (GetOffset($funct_id) + hexdec(substr($FRAME_DATI['ADDRESS'], 0, -1)));
	    
    if( !empty($FRAME))
        $answer[] = MODBUS_POSSIBLE($FRAME);
	
	if( !empty($answer))	
		$FRAME_DATI['DATA'] = $answer;
	
	$FRAME_DATI['FUNCT'] = modbus_funct_name($funct_id);

	if( isset($crc) && $crc != '????' && $crc != 'FF00')
		$FRAME_DATI['CRC'] = modbus_CRCCheck($crc_compute, $crc);
	
	return $FRAME_DATI;
}
/*----------------------------------------------------------------------------*/
/* END OF FILE */
